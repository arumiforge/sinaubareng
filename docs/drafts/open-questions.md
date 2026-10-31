# Open Questions (Phase 1)

- **Status:** draft (owner review)
- **Task:** P1-08
- **Last updated:** 2026-10-30

## Purpose

One list of every unresolved open question from the Phase 1 drafts, so the owner can answer them in one pass. Each draft keeps its own *Open questions* list; this file only collects them. When a question is answered, record the decision in `DECISIONS.md`, tick it in the source draft, and remove it here.

**Who answers:** *owner* = a product or business choice only the owner can make · *team* = the development team finds out (testing, research, checking a service) · *owner + team* = the team prepares, the owner decides.

## Business

| Question | Source | Who answers |
|---|---|---|
| Prices for the four tiers (paid from non-BOS funds, D-025) | [product-concept.md](product-concept.md#open-questions) | owner |
| Present the short version to principals and supervisors, and record their objections | [product-concept.md](product-concept.md#open-questions) | owner |
| Legal reading of Gemini's under-18 clause for a service that also has a student exam site (the exam site never calls the AI, D-027) | [product-concept.md](product-concept.md#open-questions) | owner (legal advice) |

## Brand

| Question | Source | Who answers |
|---|---|---|
| Final name: check "Sigrak" (and backup "Kapur") in PDKI classes 9, 41, 42, then register the .id and .com domains together | [product-concept.md](product-concept.md#open-questions) | owner + team |
| Logo: brief a designer (colour, single-colour, negative, minimum 16 px) | [product-concept.md](product-concept.md#open-questions) | owner |

## Product and navigation

| Question | Source | Who answers |
|---|---|---|
| Does the kokurikuler module ship in v1 as an RPM template variant? | [product-concept.md](product-concept.md#open-questions) | owner |
| Test the five-tab menu with 5 class teachers and 2 subject teachers on a click prototype (no real data) | [product-concept.md](product-concept.md#open-questions) | team (owner recruits the teachers) |
| v1 release slices: confirm the proposed order when Phase 2 is planned | [product-concept.md](product-concept.md#v1-scope) | owner + team |

## Content engine

| Question | Source | Who answers |
|---|---|---|
| Exact step split for the RPM template, tuned against real output sizes | [content-engine.md](content-engine.md#open-questions) | team |
| Laravel Excel: confirm Laravel 13 support when installing | [content-engine.md](content-engine.md#export) | team |

## AI

| Question | Source | Who answers |
|---|---|---|
| D-022 AI engine technical choices (own `AIProvider` client, polling, `app/Content/` vs `app/AI/`) are still *proposed* | [ai-provider-engine.md](ai-provider-engine.md#architecture), D-022 | owner |
| Which 429 detail fields separate per-minute from per-day limits (check real responses) | [ai-provider-engine.md](ai-provider-engine.md#open-questions) | team |
| Usage-log retention period (proposed: detailed rows 180 days, monthly aggregates kept) | [ai-provider-engine.md](ai-provider-engine.md#open-questions), [data-model.md](data-model.md#open-questions) | owner |
| Should a teacher be able to mark one key as "cadangan" (failover only)? Not in v1 unless the owner asks | [ai-provider-engine.md](ai-provider-engine.md#open-questions) | owner |
| Which stable Gemini models currently have free-tier quota (check AI Studio at build time) | [ai-provider-engine.md](ai-provider-engine.md#open-questions) | team |
| Completion notice in *Kabar* when a generation finishes is *proposed* | [ai-provider-engine.md](ai-provider-engine.md#queues-and-progress) | owner |

## Data model

| Question | Source | Who answers |
|---|---|---|
| Report card export format required by e-Rapor (columns and file type) | [data-model.md](data-model.md#open-questions) | owner + team |
| Retention for `document_revisions` (proposed: last 10) | [data-model.md](data-model.md#open-questions) | owner |
| Does a supervisor need a `school_id` context for anything they write (supervision reports), or is `supervisor_assignments` enough? | [data-model.md](data-model.md#open-questions) | team |

## Design

| Question | Source | Who answers |
|---|---|---|
| Use "Templat" (KBBI) or keep the familiar "template" in UI copy? | [design-system.md](design-system.md#open-questions) | owner |
| Game runtime palette: designer task, or derived from the brand colour? | [design-system.md](design-system.md#open-questions) | owner |

## Religion and curriculum

| Question | Source | Who answers |
|---|---|---|
| An official-channel copy of SK Dirjen Pendis 9941/2025 (the archived file comes from a public mirror) | [religious-education.md](religious-education.md#open-questions) | owner + team |
| Names of the reviewing guru agama for each religion, and of the MI subject reviewers (who, from which school; D-037) | [religious-education.md](religious-education.md#open-questions) | owner |
| Inclusive schools: does any target school need the special-education CP (Lampiran II of BKPDM 020/2026)? | [religious-education.md](religious-education.md#open-questions) | owner |
| SD religion JP: confirm that the national structure has one "Pendidikan Agama dan Budi Pekerti" row (3 JP per week) for every religion | [religious-education.md](religious-education.md#sd-six-religion-subjects-d-034) | team |
| Known curriculum data issues: Matematika grade 4 semester 2 (80 of 90 JP), Keterampilan Tata Boga target, TP↔fokus links | [data-model.md](data-model.md#curriculum) | team, with the curriculum owner |

The MI allocation question (official copy of KMA 1503/2025) is closed by D-038.

## TKA

| Question | Source | Who answers |
|---|---|---|
| TKA 2027 schedule for SD/MI, once published, to time the TKA release | [product-concept.md](product-concept.md#open-questions) | team (watch for the announcement) |

## Phase 2 additions (2026-10-31)

Collected at integration from the Phase 2 outputs. Owner answers already given are recorded as Accepted decisions (D-069…D-073, D-074…D-076, D-084, D-085).

| Question | Source | Who answers |
|---|---|---|
| Review the **Proposed** decisions D-039…D-083 (architecture, server, coding, TKA, MI CP shape) | [DECISIONS.md](../agent/DECISIONS.md) | owner |
| Ubuntu 22.04 (+ Ubuntu Pro) or 24.04 LTS for the VPS (D-067; recommendation: 24.04) | [server-setup.md](server-setup.md#open-questions) | owner |
| IDCloudHost plan sizes for production and staging | [server-setup.md](server-setup.md#open-questions) | owner + team |
| Mail provider for invitations, password reset, notifications | [architecture.md](architecture.md#open-questions) | owner |
| File storage: local disk only, or object storage later for letterheads and exports | [architecture.md](architecture.md#open-questions) | owner + team |
| Exam capacity target (participants at once across schools), to size the VPS | [architecture.md](architecture.md#open-questions) | owner |
| Exam client technology: Svelte without Inertia, JSON autosave endpoints | [architecture.md](architecture.md#open-questions) | team |
| Mode Contoh (demo school): allow AI calls with the salesperson's key, or prepared content only | [architecture.md](architecture.md#open-questions) | owner |
| Payment provider for subscriptions | [architecture.md](architecture.md#open-questions) | owner |
| PWA install for the school app, or service worker for Di Kelas only | [architecture.md](architecture.md#open-questions) | owner |
| Paket Soal TKA: offer the AN literasi/numerasi competencies merged into TKA from 2026 (D-043) | [kerangka-tka-sd.json](../data/tka/kerangka-tka-sd.json) | owner |
| Official source for the TKA item count (30) and category limits (50; 76,67), now `confidence: secondary` | P2-03 task Result | team |
| MI CP source typos (e.g. خَالِيْ for a father's brother): show as printed or corrected (D-040) | [cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json](../data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json) | guru Bahasa Arab |
| Khonghucu Fase A prints "(孔)" instead of "(孔子)": confirm with the guru agama | [cp-pendidikan-agama-sd-bkpdm-020-2026.json](../data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json) | guru agama Khonghucu |
