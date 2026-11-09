# EDUNEXA Overlap: Adjustments to Same or Similar Features (Part B)

- **Status:** draft; owner answered Q1–Q10 on 2026-11-09 (§4); ready to record as D-numbers and apply
- **Task:** proposal, not yet a task (Part B of the EDUNEXA comparison)
- **Last updated:** 2026-11-09
- **Read with:** [`product-concept.md`](../final/product-concept.md#feature-status) (fate of the 43 EDUNEXA features), [`content-engine.md`](../final/content-engine.md), [`data-model.md`](../final/data-model.md), [`administration-features.md`](administration-features.md) (P4-04, D-210…D-221), [`ifp-classroom.md`](../final/ifp-classroom.md), [`EDUNEXA-Dokumentasi-Fungsionalitas.pdf`](../sources/EDUNEXA-Dokumentasi-Fungsionalitas.pdf)

## Purpose

SIGRAK must be an upgrade of EDUNEXA, not a copy. Many SIGRAK features exist in EDUNEXA in the same or a similar form (status *Keep*, *Merge*, or *Replace* in the feature status table). The concept already moved them to a new stack and a cleaner menu, but in several places the **behaviour** is still EDUNEXA's. This draft lists those places and proposes adjustments that give each shared feature a clear step up, using data SIGRAK already has.

Out of scope here: features only SIGRAK has (templates, games, TKA, AI keys), and the new administration features already decided in P4-04 (A1…A11, H1…H8, K1…K10, O1…O9). Where a proposal touches one of them, it links instead of repeating it.

## 1. How each item is written

Each item has: **EDUNEXA** (how it worked, from the functional map) · **SIGRAK now** (what the final docs say) · **Gap** (why it is still a copy or a regression) · **Proposal** · **Reuses** · **Effort** (S ≈ days, M ≈ 1–3 weeks, L ≈ a slice) · **Fits** (build slice) · **Decision** (an owner question in §4, if any).

## 2. Summary

| ID | Area | Shared feature | Adjustment in one line | Effort | Decision |
|---|---|---|---|---|---|
| B-01 | Hari Ini | Progres Administrasi | One completeness ledger for every screen, plus "Siapkan besok" | M | — |
| B-02 | Buat | RPM scheduler (reflow) | Show and undo every shift; warn early when the semester runs out; Promes shows plan vs actual | M | Q1 |
| B-03 | Di Kelas | Jurnal | Third status "terlaksana sebagian" that carries the unfinished part, not the whole meeting | S | Q2 |
| B-04 | Buat | RPM / Modul mandiri | Reuse an RPM for a parallel class with zero AI calls; attach a "lepas jadwal" RPM to the timetable later | S | Q3 |
| B-05 | Asesmen | Bank Soal | Item library across documents: search by TP, reuse, avoid repeats, keep real item stats | M | Q4 |
| B-06 | Asesmen | Penilaian / SANDI | Fast score grid, and scan printed answer sheets with the phone camera | S + L | Q5 |
| B-07 | Asesmen | Analisis nilai, taraf serap | Keep the "daya serap" print teachers know; turn results into class groups for the next RPM | S | — |
| B-08 | Asesmen | Leger, inject e-Rapor | Readiness check per student, then a locked leger after sign-off | S | — |
| B-09 | Di Kelas | Catatan siswa, BK, prestasi | One-tap capture, BK kept out of every bundle and export, achievements flow to rapor | S | — |
| B-10 | Ekspor | Download Paket, Cover Dokumen | "Berkas Administrasi": one semester bundle per teacher with a generated cover and contents page | M | Q6 |
| B-11 | Kelas Saya | Kaldik, kaldik calon | Show what a calendar change does to RPM schedules before it applies | S | — |
| B-12 | Hari Ini | Broadcast, pemberitahuan | Kabar items carry their action; reminders are rate-limited | S | — |
| B-13 | Kelas Saya | Piket, Keuangan, Inventaris | Piket rotation generated from the roster; class inventory becomes a room view of school inventory; monthly class-cash message | S–M | Q7 |
| B-14 | Akun | Pindah Sekolah | Define what leaves with the teacher and what stays with the school | S | Q8 |
| B-15 | Offline | Service Worker + IndexedDB cache | Today's and tomorrow's RPM meetings readable offline (EDUNEXA had this; SIGRAK lost it) | S–M | Q9 |
| B-16 | Buat | Worksheet generator | LKPD blocks for grades 1–3 (trace, cut-and-stick, colour, match, pictogram) drawn by the renderer | M | Q10 |

## 3. Proposals

### B-01 Hari Ini: one completeness ledger, plus "Siapkan besok"

- **EDUNEXA.** The administration dashboard showed Target, Tersedia, Belum, Status, and Aksi per document type over a date range the teacher picked. Targets came from scheduled meetings; a guru mapel's target skipped subjects taught by others.
- **SIGRAK now.** *Hari Ini* shows what to fill today and progress this week. The principal's *Ringkasan* shows completeness per teacher. P4-04 adds K3 (bukti kinerja) and P2 (cross-school recap). Each would compute "complete" on its own.
- **Gap.** Four screens, four formulas: the teacher, principal, supervisor, and the Ruang GTK download could show different numbers for the same week. The date-range picker is gone, but nothing replaced its "what is missing in this period" view.
- **Proposal.**
  1. One service, `CompletenessLedger`, that turns timetable slots × meetings × journals × attendance into rows `(teacher, class, subject, date, item, state)`, with `item` ∈ {rpm_meeting, attendance, journal} and `state` ∈ {done, missing, not_due, excused (holiday or `no_lessons` event)}. Every screen reads it; none counts on its own.
  2. Fixed windows instead of a picker: *Hari ini*, *Minggu ini*, *Semester ini*. The semester view replaces EDUNEXA's range table.
  3. **Siapkan besok:** tomorrow's slots without an RPM meeting show one button that opens the RPM wizard with class, subject, date, and the next ATP topic already chosen.
  4. Each missing row links to the exact screen that fixes it (EDUNEXA's *Aksi* column, kept).
- **Reuses.** `timetable_slots`, `meetings`, `journals`, attendance, `calendar_events`.
- **Effort / fits.** M · P5-07 (teacher side), reused by P5-09 (portals, K3, P2).

### B-02 RPM scheduling: visible shifts, early capacity warning, Promes plan vs actual

- **EDUNEXA.** `rpm-scheduler.js` placed modules on JP slots and silently reflowed them when a meeting was marked not done. Semester 2 waited for the even-semester start.
- **SIGRAK now.** Same behaviour, carried over on purpose (`meetings.status = not_done` shifts later meetings; the last 3 schedule versions are kept in `meeting_schedule_history`). Calendar changes adjust automatically and post a Kabar notice.
- **Gap.** The teacher still cannot see *what* moved, cannot undo it, and learns too late that the semester has no room left for the remaining ATP topics. That is the main complaint a reflow engine causes.
- **Proposal.**
  1. **Pratinjau geser:** every shift (journal, calendar, timetable change) shows "2 pertemuan bergeser: Pertemuan 3 → Kamis 13/11, Pertemuan 4 → Senin 17/11" with *Batalkan*. Undo restores the previous `meeting_schedule_history` version.
  2. **Capacity check:** after each shift, compare remaining JP slots in the semester with remaining planned meetings per subject. When short, warn on *Hari Ini* weeks ahead, not on the last day, with three options: merge two meetings (an AI section revision on the RPM, "Perbaiki bagian ini" with a *padatkan* action), move the topic to the next semester, or accept the shortfall with a note (it then shows in the teacher's reflection, A4).
  3. **Promes hidup:** the Promes print gets a second view, *Realisasi*, built from journals: planned week vs actual week per topic. Supervisors ask for exactly this at the end of a semester.
- **Reuses.** `meeting_schedule_history`, section revision pipeline, Prota/Promes print (A11), Kabar.
- **Effort / fits.** M · P5-04 (scheduler, preview, undo), P5-07 (capacity warning on Hari Ini).
- **Decision.** Q1, answered: automatic, with a preview and undo.

### B-03 Jurnal: "terlaksana sebagian"

- **EDUNEXA.** A meeting is either done or not done. Not done shifts the whole module.
- **SIGRAK now.** `journals.status` and `meetings.status` keep the two states.
- **Gap.** In SD the usual case is a lesson that ran out of time: *Memahami* and *Mengaplikasi* were done, *Merefleksi* was not. Marking it "not done" repeats the whole meeting; marking it "done" hides the gap.
- **Proposal.** Add `partial`. The teacher ticks which learning experiences were not finished (the RPM meeting already splits them into *Memahami*, *Mengaplikasi*, *Merefleksi*). The unfinished part is attached to the next meeting as a carry-over block shown at the top of that meeting and in Game kelas; no reflow happens. Three `partial` in a row for one subject triggers the B-02 capacity check.
- **Reuses.** RPM meeting structure, `journals`, offline sync (`client_uuid`).
- **Effort / fits.** S · P5-04 (enum in `meetings`), P5-07 (Jurnal screen).
- **Decision.** Q2, answered: yes, add `partial`.

### B-04 RPM for parallel classes, and attaching a "lepas jadwal" RPM later

- **EDUNEXA.** Generated RPMs were cached per school, year, class, and user. *Modul mandiri* made an RPM outside the scheduler.
- **SIGRAK now.** One RPM document has its own `meetings` tied to one class's timetable. "RPM lepas jadwal" has `planned_on = NULL` and no ATP progress.
- **Gap.** A guru mapel (PJOK, PAI, Bahasa Inggris) teaches the same topic to 4A, 4B, and 4C. Today they would run the wizard three times and spend three times the AI quota. A "lepas jadwal" RPM can never join the schedule later.
- **Proposal.**
  1. **Pakai untuk kelas lain:** copy an RPM to a parallel class of the same grade and subject. Content is copied, no AI call; meetings are placed on the target class's timetable. Each copy then lives on its own (edits, journals, shifts).
  2. **Pasang ke jadwal:** a "lepas jadwal" RPM can be placed on a class timetable later, from a start date. Its meetings then enter ATP progress and the B-01 ledger.
- **Reuses.** Scheduler, `documents`, `meetings`.
- **Effort / fits.** S · P5-04.
- **Decision.** Q3, answered: copy per class with no AI call; each copy lives on its own.

### B-05 Bank Soal: item library across documents

- **EDUNEXA.** Questions lived in the browser (IndexedDB), per device. An "evaluation mode" ran tests.
- **SIGRAK now.** Items sit inside each Bank Soal document's `content`. When an assessment uses the document, items are copied into `assessment_items` with `kode_tp`. Exam mode is off (D-026).
- **Gap.** Items are trapped in the document that made them. To build an STS a teacher regenerates questions that already exist in earlier PH documents, which wastes quota and repeats items students have seen.
- **Proposal.**
  1. An **item index** (`question_items`: document, position, type, `kode_tp`, cognitive level, difficulty, `last_used_at`, stats) kept in sync with the document content, which stays the source for rendering.
  2. The Bank Soal wizard gets a *Komposisi* option **"Ambil dari soal saya dulu"**: it fills the composition from existing items that match the scope, skips items used in the last N weeks, and asks AI only for the gap.
  3. After an assessment, A7 (analisis butir) writes the real difficulty and discrimination back to the item, so later picks prefer items that worked.
- **Reuses.** Bank Soal schema, `assessment_items`, A7.
- **Effort / fits.** M · P5-05 (index and reuse), P5-08 (stats write-back).
- **Decision.** Q4, answered: yes, in P5-05.

### B-06 Nilai: fast entry and scanned answer sheets

- **EDUNEXA.** SANDI: typed score entry and recap per student, Excel and PDF exports.
- **SIGRAK now.** Asesmen > Nilai; Kartu Jawab (D-174) gives per-student formative data from printed cards, as the last Game kelas step.
- **Gap.** Typing 28 students × 25 items on a phone is the slowest job in the app, and per-TP analysis needs item-level scores, not one total.
- **Proposal.**
  1. **Grid cepat** (S): one column per assessment, numeric keypad on phones, arrow and Enter navigation on laptops, paste from Excel, "semua 100" then correct the few.
  2. **Pindai Lembar Jawab** (L): a Bank Soal assessment prints one answer sheet per student with a QR code (assessment + student number, never the name) and bubbles for PG and Benar-Salah items. The teacher photographs each sheet; the phone reads the bubbles, the teacher confirms, and item-level scores land in Nilai with `kode_tp`, so *Analisis per TP* is ready at once. Isian and Uraian stay manual. It shares the camera and roster-mapping code with Kartu Jawab.
- **Reuses.** `assessment_items`, Kartu Jawab camera work, print engine.
- **Effort / fits.** Grid S in P5-08; scanning L, after P5-08 or with Kartu Jawab.
- **Decision.** Q5, answered: grid cepat in P5-08; scanning is built with Kartu Jawab (D-174), sharing its camera and roster-mapping code.

### B-07 Analisis per TP: keep the familiar print, add groups for the next lesson

- **EDUNEXA.** *Analisis Nilai* and *Taraf Serap* as separate pages.
- **SIGRAK now.** Asesmen > Analisis, automatic per TP; A1 adds remedial and enrichment.
- **Gap.** Teachers and supervisors still ask for a page titled "Analisis Daya Serap"; a modern screen with no matching print looks like a missing feature.
- **Proposal.** Keep "Daya serap" as a print layout and a search alias of Analisis (no separate page). Add **Kelompok belajar**: from per-TP results, three groups per TP (perlu bimbingan, cukup, siap pengayaan) the teacher can adjust; the next RPM wizard for that subject offers the group sizes as *kondisi murid*. Only counts go to the prompt, never names (`PromptDataGuard`).
- **Effort / fits.** S · P5-08.

### B-08 Rapor: readiness check and a locked leger

- **EDUNEXA.** Leger plus a technical "inject e-Rapor" step.
- **SIGRAK now.** Asesmen > Rapor with the 12 components (D-214) and e-Rapor/RDM export (D-218).
- **Gap.** Neither app tells the homeroom teacher, before printing, which student is still missing a score, a description, kokurikuler, or ekstrakurikuler data.
- **Proposal.** **Cek kesiapan rapor:** a per-student checklist of the 12 components with links to fix each gap. After the principal signs off, the leger is locked; reopening needs a reason, which is logged. Exports always come from the locked version.
- **Effort / fits.** S · P5-08.

### B-09 Catatan Siswa: faster capture, BK kept out of bundles

- **EDUNEXA.** Separate books for anecdotes, BK, and achievements.
- **SIGRAK now.** One form with a kind; BK visible only to author, homeroom teacher, and principal.
- **Proposal.**
  1. **One-tap capture:** a short list of school-set tags ("membantu teman", "terlambat", "juara lomba") so most notes take one tap plus an optional sentence; works offline.
  2. BK notes are **never** included in B-10 bundles, K3 downloads, supervisor views, or AI prompts, and they get their own retention rule.
  3. Achievement notes can be marked "masuk rapor" (catatan wali kelas draft, H1) or attached to the portfolio (A10).
- **Effort / fits.** S · P5-07.

### B-10 Berkas Administrasi: semester bundle with a generated cover

- **EDUNEXA.** "Download Paket" exported administration documents; `edunexa-cover.js` built cover pages (book title, semester, year, paper, dinas name).
- **SIGRAK now.** Export per document only. K3 bundles evidence for Ruang GTK. No cover generator.
- **Gap.** At the end of a semester every teacher hands in a binder: cover, contents, Prota, Promes, RPMs, journal recap, attendance recap, scores, analysis. SIGRAK makes every part but not the binder.
- **Proposal.** **Berkas Administrasi** in Dokumen Saya: pick a semester, tick parts (all on by default, in the order supervisors expect), get a cover page and a contents page generated from school data and the print settings (letterhead image, signers, A4/F4), then all parts. The same builder serves K3 with a different preset. The principal can download a whole school's set.
- **Reuses.** Export engine (Gotenberg), print settings, B-01 ledger for "what is missing" warnings.
- **Effort / fits.** M · P5-07 or P5-09.
- **Decision.** Q6, answered: both formats, one merged PDF by default and a ZIP of separate files as an option.

### B-11 Kaldik: preview the effect of a calendar change

- **EDUNEXA.** `institution_kaldik` plus `kaldik_calon` (a draft calendar).
- **SIGRAK now.** Region defaults that a school calendar overrides; schedule changes adjust RPMs automatically with a Kabar notice.
- **Proposal.** When a region default or a school event changes, the operator first sees the impact ("12 RPM di 6 kelas akan bergeser"), applies it, and teachers get the B-02 preview with undo. This keeps the idea of EDUNEXA's draft calendar without a second calendar table.
- **Effort / fits.** S · P5-04 (with B-02).

### B-12 Kabar: items carry their action

- **EDUNEXA.** A broadcast board and a separate notification centre.
- **SIGRAK now.** Both merged into Kabar; a notice when a generation finishes (D-152).
- **Proposal.** Every Kabar item has a kind and one action button (*Buka RPM*, *Isi jurnal*, *Lihat komentar*). The principal's *Ingatkan* is limited to one reminder per item per teacher per week, so it nudges without nagging. Old items fold into a weekly digest.
- **Effort / fits.** S · P5-07/P5-09.

### B-13 Kelas Saya: Piket, Keuangan, Inventaris

- **EDUNEXA.** Three manual tables kept per class.
- **SIGRAK now.** *Keep* for all three; Keuangan and Inventaris kelas are an add-on; tabungan joins Keuangan (D-219); school inventory O4 is in scope (D-217).
- **Gap.** These are the plainest copies of EDUNEXA. Class inventory and school inventory (O4) would be typed twice.
- **Proposal.**
  1. **Piket:** generated rotation from the roster (balanced by count and gender, skips absent students for the day), printable, and shown in Game kelas idle screen if the school wants.
  2. **Inventaris:** class inventory becomes the room view of school inventory: an item belongs to the school and sits in a room; the wali kelas edits condition and count for their room. One record, two views.
  3. **Keuangan:** a monthly class-cash summary sent from the teacher's own WhatsApp (`wa.me` text, H6 rule), with totals only, never per-student debts.
- **Effort / fits.** S–M · P5-07 (piket, keuangan), P5-09 (inventaris with O4).
- **Decision.** Q7, answered: yes, one record and two views. Packaging note: Inventaris kelas is in an add-on (D-158) while school inventory O4 is core (D-211, D-217); Tim SIGRAK sets in the console whether the room view needs the add-on.

### B-14 Pindah Sekolah: what moves with the teacher

- **EDUNEXA.** Transfer requests approved by a super admin; data keyed by username, so it moved loosely with the person.
- **SIGRAK now.** *Akun > Pindah Sekolah*, approved by the target school. Principle 6: school data belongs to the school. Not yet written: what happens to the teacher's documents.
- **Proposal.** Split by owner. Stays with the old school: attendance, journals, scores, notes, rapor, and a read-only copy of the teacher's documents for supervision history. Moves with the teacher: their own documents (RPM, Bank Soal, LKPD, games) as copies, their AI keys, their plan. School-specific data inside a document (letterhead, school name) is replaced on first export at the new school.
- **Effort / fits.** S · P5-09.
- **Decision.** Q8, answered: the teacher takes copies of their own documents; the old school keeps read-only copies plus all school data.

### B-15 Offline: read today's lesson without signal

- **EDUNEXA.** The service worker and IndexedDB cached the RPM schedule and RPMs, so a teacher could read the lesson plan offline.
- **SIGRAK now.** Offline is limited to Absensi, Jurnal, and Catatan Siswa (D-016, D-093).
- **Gap.** This is a **regression**: the teacher fills the journal offline but cannot open the lesson the journal is about.
- **Proposal.** The Di Kelas cache also stores, **read-only**, the RPM meetings for today and tomorrow (text only, no exports, no editing), refreshed whenever the app is online. Editing and AI stay online-only, so D-016's intent (small PWA scope) holds.
- **Effort / fits.** S–M · P5-07.
- **Decision.** Q9, answered: yes, today and tomorrow, read-only. Amends D-016.

### B-16 LKPD: blocks for the lower grades

- **EDUNEXA.** A worksheet generator with a component engine, theme registry, and layout engine.
- **SIGRAK now.** The LKPD template outputs title, goal, instructions, steps, table, questions, and reflection, with an A4/F4 print layout.
- **Gap.** That fits grades 4–6. Grades 1–3 worksheets are mostly visual (tracing letters and numbers, cut and stick, colouring, matching with lines, counting pictures). EDUNEXA's component idea served this; SIGRAK's current schema does not.
- **Proposal.** A fixed set of **LKPD blocks** owned by the renderer, as with TKA visuals (D-030, no AI images): `trace` (letters, numbers, Arabic letters for MI), `match_lines`, `cut_and_stick`, `colour_by_rule`, `count_pictures` (bundled pictograms), `fill_table`, `short_answer`. The AI picks blocks and fills text and data; the renderer draws them. The wizard suggests blocks by phase (Fase A first).
- **Reuses.** TKA figure set and renderer approach, LKPD print layout.
- **Effort / fits.** M · P5-05.
- **Decision.** Q10, answered: yes, in v1 (P5-05).

## 4. Decision list for the owner

Suggested answer in brackets.

| # | Question | Options | Suggested | Owner answer |
|---|---|---|---|---|
| Q1 | B-02: how should RPM shifts behave? | (a) automatic with a preview and *Batalkan*; (b) always ask first; (c) silent, as in EDUNEXA | (a), and ask first only when a shift pushes a topic past the semester end | Automatic with preview and *Batalkan* (2026-11-09) |
| Q2 | B-03: add "terlaksana sebagian" to Jurnal? | Ya / Tidak | Ya | Ya (2026-11-09) |
| Q3 | B-04: RPM for parallel classes | (a) copy per class, no AI call; (b) one shared RPM for several classes; (c) not in v1 | (a) | Copy per class, no AI call (2026-11-09) |
| Q4 | B-05: item library across Bank Soal documents | Ya in P5-05 / Nanti / Tidak | Ya in P5-05 | Ya in P5-05 (2026-11-09) |
| Q5 | B-06: scanned answer sheets | Ya with P5-08 / Ya with Kartu Jawab / Nanti | Grid cepat in P5-08; scanning with Kartu Jawab (shared camera code) | As suggested (2026-11-09) |
| Q6 | B-10: Berkas Administrasi format | (a) one merged PDF; (b) ZIP of separate files; (c) both | Both, merged PDF by default (2026-11-09) |
| Q7 | B-13: class inventory as a room view of school inventory (O4) | Ya / Tidak, keep two lists | Ya | Ya, one record, two views (2026-11-09) |
| Q8 | B-14: documents on teacher transfer | (a) teacher takes copies, school keeps read-only copies; (b) everything stays with the school; (c) everything moves with the teacher | Teacher takes copies, school keeps read-only copies (2026-11-09) |
| Q9 | B-15: read-only RPM meetings offline (amends D-016) | Ya, today and tomorrow / Ya, this week / Tidak | Ya, today and tomorrow | Ya, today and tomorrow; amends D-016 (2026-11-09) |
| Q10 | B-16: LKPD blocks for grades 1–3 | Ya in v1 (P5-05) / Nanti | Ya in v1 | Ya in v1, P5-05 (2026-11-09) |

No decision needed for B-01, B-07, B-08, B-09, B-11, B-12; they follow existing decisions and only change behaviour inside a slice. The owner may still reject any of them.

## 5. Effect on the build plan and docs

| Slice | Items |
|---|---|
| P5-04 Content core | B-02 (preview, undo), B-03 (enum), B-04, B-11 |
| P5-05 More templates | B-05 (index, reuse), B-16 |
| P5-07 Di Kelas | B-01 (teacher side), B-02 (capacity warning), B-03 (screen), B-09, B-10 or P5-09, B-12, B-13 (piket, keuangan), B-15 |
| P5-08 Asesmen | B-05 (stats), B-06 (grid), B-07, B-08 |
| P5-09 Portals | B-01 (portal side), B-12, B-13 (inventaris), B-14 |
| Later | B-06 scanning (with Kartu Jawab, D-174; Q5) |

Docs to update once decided: `data-model.md` (`meetings.status` and `journals.status` gain `partial`; `question_items`; inventory ownership; transfer rules), `content-engine.md` (scheduler preview and undo, Bank Soal reuse, LKPD blocks), `product-concept.md` (feature status rows for the shared features), and D-016 (Q9 accepted: Di Kelas also caches today's and tomorrow's RPM meetings, read-only). Each accepted answer gets a D-number in `DECISIONS.md`.

## Open questions

- All of Q1…Q10 are answered. Next: give each answer a D-number in `DECISIONS.md` and update the docs listed in §5.
- B-06: which answer-sheet layout do Kudus schools already use (if any), so the printed sheet looks familiar?
- B-10: the order of parts in a teacher's semester binder differs by region; ask the pilot schools for the Kudus order before fixing the default.
