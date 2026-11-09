# Handoff notes archive (2026-10-30 … 2026-11-01)

Moved from `PROGRESS.md` on 2026-11-01 at the end of the Phase 1–3 coordination run. Historical only; the decisions themselves are in `DECISIONS.md`.


- 2026-11-01 — P3-01 done (#35, #42): ten final docs + Indonesian PDFs. The backup heartbeat (D-136) is added to final server-setup §12, and both TP/ATP files are linked from final religious-education and data-model. `server-setup.pdf` predates the heartbeat; rebuild it in the next PDF pass. Follow-up candidate: a committed PDF build script.
- 2026-11-01 — P2-10 merged (#40): MI Kemenag TP/ATP draft data, decisions D-145/D-146 (Proposed). After P3-01: link both TP/ATP files from final religious-education and data-model.
- 2026-11-01 — D-143/D-144: D-130…D-141 accepted; Healthchecks.io free plan, owner as the only recipient; e-Rapor export deferred (own Excel layout for now). After P3-01: tick the e-Rapor question in docs/final/data-model.md.
- 2026-11-01 — D-142: the KBC fields are optional and AI pre-filled. The print font question is closed by D-125. The final-doc text rides along in P3-01 part 2.
- 2026-11-01 — P2-08 (#32) and P2-09 (#34) merged; decisions D-128…D-141. D-128 is the daily AI counter (from #30). After P3-01: add the backup heartbeat to server-setup §12 (D-136), and link the TP/ATP file from religious-education and data-model.
- 2026-10-31 — D-127 supersedes D-115: the MI CP shows the printed text again, and the 9 rows with possible source errors carry `review_notes[]`.
- 2026-10-31 — D-125/D-126: the school chooses Arial or Times New Roman for printing, and KBC (*Tema KBC*, *Materi Insersi*) is in the v1 RPM for MI. The doc text changes ride along in P3-01.
- 2026-10-31 — D-124: students only receive finished AI content; this closes the Gemini under-18 question.
- 2026-10-31 — D-123: the owner approved all ten drafts, which also accepts every remaining Proposed decision. P3-01 (final docs + Indonesian PDFs) is created; `open-questions.md` stays in drafts.
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
- 2026-10-30 — The owner supplied scope (legacy app notes + SIGRAK concept) and decisions were made in an interview (D-007…D-021). Five drafts are in `docs/drafts/`, and each ends with open questions. The next step is the owner's review. Do not start Phase 2 before the drafts are approved.
