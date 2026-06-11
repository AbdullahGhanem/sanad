# Deployment Checklist

Consolidated steps for deploying Sanad to production (Laravel Forge assumed). Work top to
bottom on a **first** deploy; subsequent deploys only need the **Standard deploy** section
plus any new env/migration changes.

---

## 0. First deploy only — reconcile rewritten git history

The `main` branch history was rewritten once (a sensitive document was scrubbed and
force-pushed). A server that cloned before that will diverge on `git pull`. Run **once** on
the server before the first normal deploy:

```bash
git fetch origin && git reset --hard origin/main
```

After this, normal `git pull` deploys work again.

---

## 1. Environment variables (Forge → Environment)

| Variable | Required | Purpose |
|---|---|---|
| `APP_KEY` | ✅ | Standard Laravel app key (`php artisan key:generate` if empty). |
| `AI_DEFAULT=anthropic` | ✅ | Default AI provider for chat. |
| `ANTHROPIC_API_KEY` | ✅ | Claude (chat + guardrail moderator). **Rotate the value shared earlier — see §7.** |
| `VOYAGEAI_API_KEY` | ⚠️ RAG | Embeddings for psychoeducation search (3.2). Without it, search degrades to "no match". |
| `ELEVENLABS_API_KEY` | ⚠️ Voice | Arabic speech-to-text (3.4). Without it, voice degrades to typing. |
| `BROADCAST_CONNECTION=reverb` | ✅ | Enables websocket broadcasting. |
| `REVERB_APP_ID` / `REVERB_APP_KEY` / `REVERB_APP_SECRET` | ✅ | Reverb server credentials (generate strong unique values). |
| `REVERB_HOST` (your domain), `REVERB_PORT=443`, `REVERB_SCHEME=https` | ✅ | Reverb connection (prod). |
| `VITE_REVERB_APP_KEY` / `VITE_REVERB_HOST` / `VITE_REVERB_PORT` / `VITE_REVERB_SCHEME` | ✅ | Client-side Echo (must be present **at `npm run build` time**). |
| `GUARDRAIL_AI_MODERATION=true` | default | Output guardrail AI layer. Set `false` for rules-only (no per-reply AI cost). |
| `CONSENT_VERSION=1.0` | default | Bump to force re-consent when terms change. |
| `RETENTION_SCREENING_DAYS=365`, `RETENTION_CHAT_DAYS=180` | default | Anonymous-data retention windows. **Review against your data-protection policy.** |
| `RETENTION_CRISIS_DAYS`, `RETENTION_CONSENT_DAYS` | unset | Leave unset = never auto-purge (safety/legal hold). |
| `ACTIVITYLOG_ENABLED=true` | default | Clinical audit trail. |
| `QUEUE_CONNECTION` | ✅ | `database` (or redis) — required for the queue worker. |

> The `VITE_REVERB_*` values are baked into the JS bundle at build time. If you change them,
> you must rebuild (`npm run build`) — a config cache clear is not enough.

---

## 2. Dependencies & build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build            # bundles laravel-echo + pusher-js, writes the Vite manifest
```

New since the last release: `laravel/reverb`, `spatie/laravel-activitylog`, plus
`laravel-echo` + `pusher-js` (dev deps used by the build).

---

## 3. Migrations & seeders

```bash
php artisan migrate --force
```

New tables this release: `coping_exercises`, `knowledge_articles`, `activity_log`,
`consent_records`.

One-time content seeds (safe to re-run — they upsert):

```bash
php artisan db:seed --class=CopingExerciseSeeder --force
php artisan db:seed --class=KnowledgeArticleSeeder --force
```

> After seeding knowledge articles, their embeddings are generated **asynchronously** by the
> queue worker (§4) once `VOYAGEAI_API_KEY` is set. Watch the **Indexed** column flip to ✓ in
> the Filament "Knowledge Articles" screen.

---

## 4. Long-running processes (Forge → Daemons / Scheduler)

Three processes must run continuously:

1. **Reverb websocket server** (Forge Daemon):
   ```bash
   php artisan reverb:start
   ```
2. **Queue worker** (Forge Daemon or Queue) — processes `StreamChatResponse` (chat streaming),
   `EmbedKnowledgeArticle` (RAG indexing), `CrisisNotificationJob` + `CrisisDetectedNotification`
   (counselor alerts):
   ```bash
   php artisan queue:work --tries=3
   ```
3. **Scheduler** (Forge → Scheduler, or cron) — runs the daily data-retention purge:
   ```
   * * * * * php artisan schedule:run
   ```
   Verify with `php artisan schedule:list` (expect `app:purge-expired-data` daily at 03:00).

> Restart the Reverb daemon and queue worker on every deploy (Forge does this for daemons;
> otherwise `php artisan queue:restart` + restart the Reverb daemon).

---

## 5. Caches (after env changes)

```bash
php artisan config:clear   # critical: Filament reads broadcasting.echo from config at runtime
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

A stale config cache is the most common cause of "the counselor real-time toast / chat stream
isn't connecting" — the Reverb/Filament echo settings won't be picked up until cleared.

---

## 6. Post-deploy verification

- [ ] **Chat streams**: open the chat, send a message — the reply reveals token-by-token.
- [ ] **Reverb up**: browser console shows a websocket connection (no repeated reconnect errors).
- [ ] **Guardrail**: replies appear normally; check the `single` log channel has no unexpected
      "Guardrail blocked" entries (a few are fine — they mean it's working).
- [ ] **Crisis alert**: type a known crisis keyword → a counselor (logged into `/admin`) gets a
      live red toast + a notification; a `crisis_events` row and an `activity_log` entry appear.
- [ ] **Voice**: the mic button records and fills the input with a transcript (HTTPS required).
- [ ] **RAG**: knowledge articles show **Indexed ✓** once the worker has run.
- [ ] **Consent**: starting a screening shows the consent gate first.
- [ ] **Audit log**: `/admin/activities` lists recent changes.
- [ ] **Retention** (dry run first): `php artisan app:purge-expired-data --dry-run` — review the
      counts before the scheduled job runs for real.

---

## 7. Security follow-ups

- [ ] **Rotate `ANTHROPIC_API_KEY`.** A key was shared in plaintext during development; treat it
      as compromised. Generate a fresh one in the Anthropic console, revoke the old one, and
      update Forge env.
- [ ] **Scrubbed document.** A backup `.docx` was removed from `public/` and purged from git
      history via force-push. Consider its contents exposed (it was briefly public and may
      remain in GitHub's cached commit views); contact GitHub Support if it must be fully purged.
- [ ] Confirm `REVERB_APP_*` secrets are strong and unique to production.

---

## Standard deploy (subsequent releases)

Typical Forge deploy script:

```bash
cd /home/forge/your-site
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
# Reverb daemon auto-restarts via Forge; otherwise restart it manually.
```

Add new env vars and one-time seeders from the sections above only when a release introduces them.
