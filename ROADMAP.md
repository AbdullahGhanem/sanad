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

1. **Real-time crisis escalation pipeline** — on `severe` / keyword hit: write `CrisisEvent`,
   fire a queued counselor notification (email/WhatsApp/SMS), switch chat UI to a crisis
   takeover state. Decouple via a `CrisisDetected` event + listeners.
2. **Clinical audit trail** — install `spatie/laravel-activitylog`; log screenings, crisis
   events, and AI messages. (Spatie-first: do not hand-roll.)
3. **Consent & data retention** — explicit versioned consent before screening + automated purge job.
4. **AI output guardrail** — moderation pass on every `SanadChat` reply before it reaches the student.

## Phase 2 — NEXT · Counselor side & continuity of care

5. **Roles + Counselor dashboard** — seed `admin`/`counselor`/`student`; counselor Filament
   panel with flagged-student queue, crisis events, session notes.
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
10. **Streaming + voice** — ✅ **Streaming built.** AI replies stream token-by-token over
    **Laravel Reverb** websockets: `StreamChatResponse` queued job runs the agent via
    `broadcastNow()` to a public `chat.{uuid}` channel and persists the final message in
    `then()`; the Livewire chat subscribes with Echo and appends `.text_delta` chunks live,
    finalizing on `.stream_end`. ⬜ _Remaining:_ Arabic speech-to-text (ElevenLabs already
    configured) for lower-literacy access.
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
