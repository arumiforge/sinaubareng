# Progress

Single source of truth for what is happening. Keep it short: move finished phases to `archive/` when they stop being useful.

Status values: `todo` · `in-progress` · `blocked` · `review` · `done`.
Owner = branch name or session label of whoever claimed the task.

## Current phase

**Phase 1 — Product concept docs** · drafts in review (merged to `main`, still `review`).
**Phase 2 — data and technical docs** · all tasks merged to `main` (2026-10-31). Data tasks are `done`; the three technical drafts wait for owner review.

## Open decisions blocking work

- D-039…D-122: items marked **Proposed** need owner review (Accepted ones are owner decisions)

## Phases

### Phase 0 — Repository conventions · done

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P0-01 | No-attribution rule | done | pw/amazing-hawking-dn7dfo | — | `CLAUDE.md`, `.claude/settings.json` |
| P0-02 | Language, stack, docs structure, agent workflow | done | pw/amazing-hawking-dn7dfo | P0-01 | `CLAUDE.md`, `docs/` |

### Phase 1 — Product concept docs · review

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P1-01 | Archive owner sources + owner interview | done | pw/eloquent-shannon-9iwufg | — | `docs/sources/`, D-007…D-021, D-023…D-037 |
| P1-02 | Product concept | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/product-concept.md` |
| P1-03 | Content engine (templates, wizard, games, TKA, export) | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/content-engine.md` |
| P1-04 | AI Provider Engine | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/ai-provider-engine.md`, D-022 |
| P1-05 | Data model | review | pw/eloquent-shannon-9iwufg | P1-02…P1-04 | `docs/drafts/data-model.md` |
| P1-06 | Design system and UI writing | review | pw/eloquent-shannon-9iwufg | P1-01 | `docs/drafts/design-system.md` |
| P1-07 | Religious education curriculum (SD religions, MI Kemenag) | review | pw/eloquent-shannon-9iwufg | P1-05 | `docs/drafts/religious-education.md`, `docs/data/curriculum/` |
| P1-08 | Finish KMA 1503/2025 (MI allocation) + collect open questions | done | p1/p1-08-mi-allocation | P1-07 | `docs/data/curriculum/mi-allocation-kma-1503-2025.json`, `docs/drafts/open-questions.md`, D-038 |

After approval: move the drafts to `docs/final/` and generate the Indonesian PDFs (one task per doc, can run in parallel).

### Phase 2 — data and technical docs · review

Ran in parallel sessions and was integrated on `p2/p2-00-integration`. Decisions from the task outputs are D-039…D-085.

| ID | Task | Status | Owner | Depends on | Output |
|---|---|---|---|---|---|
| P2-01 | Verify SD religion CP transcription | done | p2/p2-01-verify-religion-cp | — | `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` |
| P2-02 | Structure MI CP data, retype Arabic | done | p2/p2-02-mi-cp-structure | — | `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` |
| P2-03 | TKA SD/MI framework seed data | done | p2/p2-03-tka-framework-data | — | `docs/data/tka/kerangka-tka-sd.json` |
| P2-04 | Architecture overview | review | p2/p2-04-architecture | — | `docs/drafts/architecture.md` |
| P2-05 | Server setup guide | review | p2/p2-05-server-setup | — | `docs/drafts/server-setup.md` |
| P2-06 | Coding standards | review | p2/p2-06-coding-standards | — | `docs/drafts/coding-standards.md` |
| P2-07 | Export and localization guide | review | p2/p2-07-export-localization | P2-06 | `docs/drafts/export-localization.md` |
| P2-08 | Server monitoring and alerts | todo | — | P2-05 | `docs/drafts/server-monitoring.md` |

Later candidates (not yet tasks): v1 release slices · curriculum data fixes (Matematika K4 S2, Tata Boga target, TP↔fokus links) · TP/ATP/fokus/JP for five religions and five MI subjects (reviewed by Tim Validasi SIGRAK, D-113) · move approved drafts to `docs/final/` + Indonesian PDFs · import support for `ALOKASI-MI-KMA-1503-2025`.

## Handoff notes

Short notes for the next agent (newest first, max ~10 lines; delete stale ones).

- 2026-10-31 — D-120/D-121: TKA proposals D-041, D-042, D-044…D-047 and exam login throttling D-054 accepted; D-111 (AN offered) stands; exam font standard size. IDCloudHost confirmed outbound/7844. P2-08 (server monitoring) created. Open: print font choice (D-109 vs "font cukup 2").
- 2026-10-31 — Owner answers D-116…D-119: D-022 accepted (own Gemini client, no Laravel AI SDK), default model Gemini 3.8 Flash (free tier 5 RPM / 250k TPM / 20 RPD per project), usage logs kept 1 month, backup keys allowed.
- 2026-10-31 — Owner answers D-113…D-115: Tim Validasi SIGRAK reviews religion data with no same-religion teacher required; files on local disk, R2 optional; MI CP source errors corrected in the data (`raw_text` unchanged); Khonghucu "(孔)" confirmed.
- 2026-10-31 — Owner answers D-109…D-112: F4 215 × 330 mm and one print font, Gmail SMTP, AN competencies in Paket Soal TKA, reviewer recorded as Tim Validasi SIGRAK. The owner confirmed the Cloudflare Tunnel runs on the server, so outbound NAT is OK.
- 2026-10-31 — P2-07 merged (#17): export and localization guide, decisions D-097…D-108 (Proposed).
- 2026-10-31 — D-095/D-096: staging is a second site on the production VPS, and the disk is 320 GB. The `server-setup.md` section 15 rewrite is a P2-05 follow-up PR.
- 2026-10-31 — Owner answers D-090…D-094: Mode Contoh with the salesperson's key, manual payment, 10–25 per exam session, installable app, exam client on Laravel + Inertia. P2-07 (export and localization guide) created.
- 2026-10-31 — Follow-up: owner chose Ubuntu 24.04, 8 vCPU / 16 GB, and a Cloudflare Tunnel with no public IP (D-086…D-088). `server-setup.md`, `architecture.md` and CONTEXT follow it.
- 2026-10-31 — Phase 2 integrated. P1-08 and P2-01…P2-06 are merged (PRs #2–#9). Decisions D-039…D-085 are recorded, and owner answers are Accepted: domain `sigrak.com`, IDCloudHost Jakarta, R2 backups, staging server, Pest, TypeScript, Svelte 5, GitHub Actions, no `down()`. SD religion CP is verified against the scan, and MI CP is structured with Arabic retyped. Both still need guru review (D-037). Open owner items are in `docs/drafts/open-questions.md`.
- 2026-10-30 — D-037: Kepercayaan is not in v1, MI Kemenag subjects are taught by guru mapel, and each religion's data is reviewed by a guru agama of that religion.
- 2026-10-30 — Religion curriculum added (D-034…D-036). SD has six religions from BKPDM 020/2026, transcribed from a scan (verified in P2-01). MI uses the Kemenag subjects from SK Dirjen Pendis 9941/2025. Data is in `docs/data/curriculum/`.
- 2026-10-30 — Owner review round 2 is applied (D-030…D-033): bundled TKA figures + three.js cube stacks, partial PGK scoring, letterhead as an uploaded image (no Word-to-PDF, no merge), guru kelas vs guru mapel defaults, rendering libraries, no slide feature.
- 2026-10-30 — Owner review round 1 is applied (D-023…D-029): Gemini free tier only, no generic generator (Buat bebas removed, one job per content type), payment outside BOS, Mode Ujian off, TKA practice CBT in v1 on a separate exam site, Gotenberg + Laravel Excel, daily attendance.
- 2026-10-30 — The owner supplied scope (EDUNEXA notes + SIGRAK concept) and decisions were made in an interview (D-007…D-021). Five drafts are in `docs/drafts/`, and each ends with open questions. The next step is the owner's review. Do not start Phase 2 before the drafts are approved.
