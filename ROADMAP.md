# Sanad — Product Roadmap

Sanad is a bilingual (Arabic / English) mental-health **screening + AI support** tool for
Egyptian university students. This roadmap is ordered by **clinical responsibility first**:
in a mental-health product, safety and accuracy outrank engagement.

## Current state (baseline)

- **Screening**: questionnaire wizard → severity scoring → recommendations → PDF export.
- **AI**: `SanadChat` (Anthropic, conversational support agent) + `DistressAnalyzer`
  (structured-output severity classifier).
- **Crisis safety**: `CrisisKeyword` detection, `CrisisEvent` logging, `CrisisHelpResource` hotlines.
- **Admin**: Filament panel with stats/trends/severity/crisis widgets + session exporter.
- **Auth**: Fortify + 2FA; `spatie/laravel-permission` installed (roles not yet seeded).
- **Design choice**: `screening_sessions.user_id` is **nullable** — anonymous screening is
  intentional and must be preserved by any tracking feature.

---

## Phase 1 — NOW · Safety hardening & trust

1. **Real-time crisis escalation pipeline** — ✅ **Built.** On a keyword hit (chat or screening)
   the `CrisisDetectionService` records the `CrisisEvent` and fires a decoupled `CrisisDetected`
   domain event; the synchronous `EscalateCrisis` listener dispatches the queued
   `CrisisNotificationJob`, which sends `CrisisDetectedNotification` over mail + database + a
   **real-time `broadcast`** channel — surfacing a live red toast in the counselor's Filament
   panel over Reverb. The student-side chat/screening crisis overlay remains the front-of-house
   takeover. _Future listeners (SMS/WhatsApp, counselor assignment, audit) hang off the same event._
2. **Clinical audit trail** — ✅ **Built.** `spatie/laravel-activitylog` via a reusable
   `Auditable` trait on the safety/clinical models (crisis keywords & resources, knowledge
   articles, coping exercises, recommendations, AI provider settings, crisis events, and User
   role changes). Whitelists protect secrets/bloat (no `api_key`, no embedding vectors); guardrail
   blocks are logged to the trail too. A **read-only** Filament "Audit Log" resource lets admins
   review who changed what, when. (Spatie-first, not hand-rolled.)
3. **Consent & data retention** — ✅ **Built.** A **versioned** consent gate (`config/consent.php`)
   blocks the screening wizard until the anonymous student agrees; agreement is recorded in
   `consent_records` (bumping the version forces re-consent). An `app:purge-expired-data` command
   (scheduled daily) deletes anonymous screening + chat PII past its `config/retention.php` window —
   DB cascades clean child rows — while crisis events and consent records are kept by default
   (null = safety/legal hold). Purges are written to the audit trail.
4. **AI output guardrail** — ✅ **Built.** Every `SanadChat` reply is moderated **before** it
   reaches the student (moderate-first): `StreamChatResponse` now generates the full reply, runs
   `ResponseGuardrailService` (a deterministic dosage/medication **rule** layer + a Claude
   `ResponseModerator` structured-output classifier), then streams only the approved text — or a
   supportive safe-fallback if blocked. Fails open with a logged warning when moderation is
   unavailable (a blocked reply withholding support is itself harmful); `GUARDRAIL_AI_MODERATION`
   is the ops/cost kill-switch for the AI layer.

## Phase 2 — NEXT · Counselor side & continuity of care

5. **Roles + Counselor dashboard** — 🟡 **Started (crisis triage slice).** Added a `counselor`
   role with panel access (super-admin-only resources stay locked via existing policies). Crisis
   events now carry a triage state — `status` (open/acknowledged/resolved) + `handled_by` +
   `handled_at` — worked from the Filament crisis queue via Acknowledge/Resolve actions, plus
   `CounselorNote`s. Triage changes flow into the audit trail automatically. The **dashboard**
   now shows a `CrisisTriageStats` overview (open / in-progress / resolved-this-week) and a
   `CrisisQueue` table widget — the unresolved queue with the same triage actions inline, polling
   every 30s (the Phase 1 broadcast already gives instant toasts). _Next slices:_ a notes-thread
   view, true broadcast-driven queue refresh, and longitudinal per-student history (item 6).
6. **Longitudinal tracking** — optional student accounts linking sessions over time + a
   student-facing progress chart. Keep the anonymous path intact.
7. **Referral & appointment workflow** — route high-severity students to the counseling center.

## Phase 3 — LATER · AI depth & scale  *(next focus)*

8. **Tool-using chat agent** — give `SanadChat` tools: fetch the student's own screening
   result, pull a relevant CBT exercise, surface crisis resources on demand.
9. **RAG over vetted psychoeducation** — ✅ **Built.** Counselor-curated knowledge base so answers
   are sourced, not invented. **Architecture:** a Filament-managed `KnowledgeArticle`
   model storing its embedding as a JSON column, generated via `Str::of(...)->toEmbeddings()`
   using **Voyage AI** (`VOYAGEAI_API_KEY`); retrieval ranks by **cosine similarity computed
   in PHP** over the small curated set, surfaced to `SanadChat` as a `searchPsychoeducation`
   tool. _Native Laravel `whereVectorSimilarTo` is Postgres-only (pgvector `<=>`) and the app
   runs MySQL 8.0.33, so DB-side vector search is intentionally avoided; brute-force cosine is
   ample for a vetted KB and keeps content local._
10. **Streaming + voice** — ✅ **Built.** _Streaming:_ AI replies stream token-by-token over
    **Laravel Reverb** websockets (`StreamChatResponse` → `chat.{uuid}` channel; Echo appends
    `.text_delta`, finalizes on `.stream_end`). _Voice:_ a mic button records audio in the chat,
    uploads it via Livewire, and `TranscriptionService` transcribes it through **ElevenLabs**
    speech-to-text (`scribe_v2`, language-hinted) into the input for the student to review before
    sending. Degrades gracefully to typing when the key is unset or transcription fails.
11. **Self-help content library** — Filament-managed CBT/grounding/breathing modules; mood
    journal & daily check-ins with `DistressAnalyzer` auto-tagging.
12. **Multi-university scale** — `spatie/laravel-multitenancy` per-institution isolation +
    anonymized wellness-office reporting.

---

## Cross-cutting (alongside all phases)

- **Realtime infra**: ✅ Laravel Reverb (websockets) installed for response streaming — also the
  foundation for the Phase 1 crisis takeover and live counselor alerts.
- **Abuse / rate limiting** on the chat endpoint (cost + safety).
- **Test coverage** for every crisis path — safety logic must be fully covered.
