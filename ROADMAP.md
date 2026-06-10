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
9. **RAG over vetted psychoeducation** — counselor-curated knowledge base + embeddings
   (Voyage/Jina/OpenAI already configured in `config/ai.php`) so answers are sourced, not invented.
10. **Streaming + voice** — token streaming in the Livewire chat; Arabic speech-to-text
    (ElevenLabs already configured) for lower-literacy access.
11. **Self-help content library** — Filament-managed CBT/grounding/breathing modules; mood
    journal & daily check-ins with `DistressAnalyzer` auto-tagging.
12. **Multi-university scale** — `spatie/laravel-multitenancy` per-institution isolation +
    anonymized wellness-office reporting.

---

## Cross-cutting (alongside all phases)

- **Realtime infra**: Laravel Reverb (websockets) — prerequisite for crisis takeover,
  streaming, and live counselor alerts.
- **Abuse / rate limiting** on the chat endpoint (cost + safety).
- **Test coverage** for every crisis path — safety logic must be fully covered.
