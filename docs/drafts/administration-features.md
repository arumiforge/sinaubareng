# Class and School Administration Features for SD/MI

- **Status:** draft; owner decided 2026-11-08 (D-210…D-221), see [§7](#7-owner-decisions-2026-11-08). Legacy app upgrade details added 2026-11-10 (D-301, D-303, D-306, D-308; P4-24); the accepted features are in `docs/final/product-concept.md`
- **Task:** P4-04
- **Last updated:** 2026-11-10
- **Scope:** SD (Kemendikdasmen) and MI (Kemenag), grades 1–6. SMP and above are out of scope.

## Purpose

SIGRAK already covers the teacher's daily administration (Di Kelas), assessment and report cards (Asesmen), class data (Kelas Saya), and the principal, operator, and supervisor portals ([`product-concept.md`](../final/product-concept.md)). This draft lists **every other administration feature an SD/MI could need**, checks each against current rules (2025–2026) and against comparable apps, and recommends which should become main features. The owner's choices are in [§7](#7-owner-decisions-2026-11-08).

The rule for picking stays the same as for content: build what a school must do anyway, from data SIGRAK already has, so nobody types anything twice (product principles 4 and 7).

## 1. What changed in the rules (2025–2026)

Every national standard was reissued in 2025–2026. The ones that change administration:

| Area | Current basis | What it means for SIGRAK |
|---|---|---|
| Curriculum | Permendikdasmen 13/2025 (amends Permendikbudristek 12/2024), with Kepmendikdasmen 126/P/2025 (Pembelajaran Mendalam) and 127/P/2025 (Koding dan KA) | 8 dimensi profil lulusan replace Profil Pelajar Pancasila. **Kokurikuler** replaces P5, per-year allocation, may run as **Gerakan 7 KAIH**. Koding dan KA is an elective from grade 5 (2 JP/week, 72 JP/year). **Pramuka or another kepanduan must be offered** again (Pasal 22 amended) |
| Process | Permendikdasmen 1/2026 (Standar Proses), in force 5 Jan 2026 | A planning document with at least objectives, steps, and assessment. Format is free (RPM fits). The minimum is Pasal 5 (read in P4-32). Vocabulary: *murid* ("disiplin positif" is not in 1/2026; it comes from the 6/2026 school-culture rules) |
| Assessment | Permendikbudristek 21/2022 (still in force) + Panduan Pembelajaran dan Asesmen, edisi revisi 2025 | Formative everywhere, summative from SD. Rapor has **12 minimum components** (identity, school, class, semester, subject, score, competency description, **kokurikuler description**, **ekstrakurikuler**, **absences**, **catatan wali kelas**, **tanggapan orang tua**). Fase A may report without numbers. Teachers reflect **at least once a semester**. Kenaikan kelas and kelulusan are decided by the school through a dewan guru meeting; SD graduation looks at grades 5 and 6 plus a school exam |
| Management | Permendikdasmen 26/2025 (Standar Pengelolaan) | KSP is required and set by the principal. RKJM (4 years) and RKT (1 year) made with the komite. Evaluasi diri based on Rapor Pendidikan. SD rombel at most 28 murid. Read in P4-23: one learning session per day (Pasal 17, 3-year transition in Pasal 26) and at most 24 rombel per SD (Pasal 9) |
| Teacher workload | Permendikdasmen 11/2025 + Kepmendikdasmen 221/P/2025; for madrasah KMA 736/2026 | 24–40 JTM. Wali kelas = **2 JTM in SD, 6 JTM in MI**. Other extra duties (pembina ekskul, koordinator kokurikuler) up to 6 JTM. Duties count only with an SK and in Dapodik |
| Teacher performance | PermenPAN-RB 21/2024, run in **Ruang GTK** (Rumah Pendidikan), synced to BKN e-Kinerja | Teachers plan, the principal observes and appraises. Evidence comes from planning, journals, and observation |
| School safety | Permendikdasmen 6/2026 (Budaya Sekolah Aman dan Nyaman, revokes 46/2023 and TPPK) | Tata tertib, SOP, a confidential complaint channel, class agreements written by the teacher, yearly monitoring. Class duties are in Pasal 18(3); in SD the *guru kelas* holds them (6/2026 names "wali kelas" only for SMP/SMA/SMK; the Kepmendikdasmen 17/2026 guide, Tabel 3.5, says "Guru Kelas atau Wali Kelas" for PAUD/SD; SIGRAK says "guru kelas" in SD, D-383, D-387). The 17/2026 guide sets minimum contents for tata tertib, kode etik, and POS with a yearly review, a first response to complaints within 24 hours, and no murid names in reports to the Pokja |
| Students | Permendikdasmen 3/2025 (SPMB), Permendikbudristek 58/2024 (ijazah) | Transfers allowed except in the last semester of grade 6; the receiving school updates Dapodik within 14 working days. Ijazah signed by the principal, e-ijazah since 2025 |
| Exams | Permendikdasmen 9/2025 (TKA, optional for grade 6), 9/2026 (AN, final grade) | Already covered by Simulasi TKA |
| Funding | Permendikdasmen 8/2026 (BOSP, ARKAS); madrasah: Kepdirjen Pendis 944/2026 (e-RKAM) | Government apps handle this. Out of scope (see D-025) |
| MI | KMA 1503/2025 (amends KMA 450/2024), KBC, EMIS 4.0 (SIMPATIKA moving into EMIS GTK), RDM v3.1 | MI exports should fit RDM; teacher data lives in EMIS |
| Accreditation | BAN-PDM IA 2024 (guide version 2025), Permendikdasmen 14/2026 | Performance-oriented: 14 items across teacher performance, principal leadership, and school climate. Evidence from documents, observation, interviews |

Status of each number: checked against regulation text (peraturan.go.id or the pasal.id mirror) except where marked *secondary* in [Sources](#sources). Product copy must re-check numbers on jdih.kemendikdasmen.go.id before use.

## 2. Benchmark (short)

| Group | Examples | What they do | Gap for SD/MI |
|---|---|---|---|
| Government | Rumah Pendidikan / Ruang GTK, e-Rapor SD, Dapodik, ARKAS, Rapor Pendidikan; Kemenag EMIS 4.0, RDM, e-RKAM | Data of record, rapor, finance, training, performance | Each stands alone. Teachers re-type grades into e-Rapor/RDM. No daily class workflow |
| School suites | Gredu, SmartSchool/MySCH, Sigma Guru | Attendance, grade book, rapor, parent app, SPP, CBT, sometimes library/inventory/letters | Built for SMP/SMA per-subject schedules; many modules immature; no curriculum link |
| Teacher apps | AIO Class | Attendance, journal with photos, grades, broadcasts, conduct points | No AI, no curriculum |
| AI generators | GuruInovatif Modul Generator, Canva, Genspark, BRIN Asisten Guru (prototype), Edura (claims Prota/Promes/ATP) | One-shot modul ajar, soal | Not tied to journal, scores, or rapor |

**Table stakes:** attendance with export, grade book to rapor, announcements, student data. **Nobody covers well:** one chain from CP → TP → Prota/Promes → RPM → jurnal → asesmen → remedial → rapor and kokurikuler descriptions; the SD guru kelas shape; MI subjects and RDM; offline phone use; evidence for supervision and Ruang GTK performance. That chain is SIGRAK's sales story, and the strongest candidates below extend it.

## 3. What SIGRAK already has

So the catalogue does not repeat it: Prota/Promes (Buat › Kurikulum), RPM, Kokurikuler template, LKPD, Bank Soal (incl. kisi-kisi), daily Absensi, Jurnal from RPM, Catatan Siswa (anecdotal, BK, achievement), Rencana Penilaian, Nilai, Analisis per TP, Rapor (add-on), Simulasi TKA (add-on), Siswa with transfers and promotion, Jadwal, Piket, Keuangan and Inventaris kelas (add-on), Kaldik, Kabar, principal portal (Pantau Guru, Validasi, Supervisi, Program Sekolah, Agenda & Rapat, Buku Sekolah), supervisor portal, letterhead and print settings. Buku Induk and Dapodik sync are on hold (D-161).

## 4. Catalogue of candidates

Columns: **Basis** (rule or guide; *customary* = expected in supervision but no current rule) · **Need** (M = mandatory or expected in every school, N = nice-to-have) · **Reuses** · **Effort** (S ≈ days, M ≈ 1–3 weeks, L ≈ a slice) · **Value** for selling to SD/MI (★ to ★★★).

### 4.1 Class administration (guru kelas, guru mapel)

| # | Feature | What it is | Basis | Need | Reuses | Effort | Value |
|---|---|---|---|---|---|---|---|
| A1 | **Remedial dan pengayaan** | From Analisis per TP, list students below KKTP and above it, and make a remedial/enrichment plan per TP (grouping, activity, re-assessment). AI drafts the activity through a template; re-test scores replace or sit beside the first score | Permendikbudristek 21/2022; Panduan Asesmen 2025; IA 2024 item 3 (follow-up on results) | M | Analisis per TP, Bank Soal, LKPD, RPM | M | ★★★ |
| A2 | **Asesmen diagnostik awal** | Start-of-year/unit diagnostic: non-cognitive (learning style, interest, wellbeing, short checklist) and cognitive (short prerequisite items per TP). Result: class groups for differentiation, shown in the RPM wizard as "kondisi murid" | Panduan Asesmen 2025 | M (expected) | Bank Soal, Asesmen, RPM wizard | M | ★★ |
| A3 | **KKTP per TP** | Set the criterion for each TP (rubric, interval, or description), defaulted from the curriculum, used by Analisis and A1 | Panduan Asesmen 2025 | M (expected) | Rencana Penilaian, TP data | S | ★★ |
| A4 | **Refleksi guru per semester** | The 7 reflection questions of the Panduan, pre-filled from journals, attendance, and per-TP results; ends in a follow-up plan. Downloadable | Panduan Asesmen 2025 ch. 6 ("at least once a semester") | M | Jurnal, Analisis | S | ★★ |
| A5 | **Asesmen kokurikuler** | Record each student's progress on the chosen dimensi profil lulusan per kokurikuler activity (simple rubric: berkembang/cakap/mahir or the school's own), then draft the rapor kokurikuler description per semester | Panduan Kokurikuler 2025 (C.2–C.3); rapor component 8 | M | Kokurikuler template, Rapor, Catatan Siswa | M | ★★★ |
| A6 | **Gerakan 7 KAIH** | Class habit tracker for the 7 habits: a weekly class checklist by the teacher plus a printable home sheet for parents (no parent login, D-010); summary feeds A5 | SEB 1/2025, SE Sesjen 14/2025 (*draft status*), Panduan Kokurikuler | N (popular) | Kokurikuler, Di Kelas | M | ★★ |
| A7 | **Analisis butir soal** | Difficulty and discrimination per item after a summative, with flags for items to revise in the Bank Soal | Customary (kisi-kisi, analisis butir) | N | Scores, Bank Soal | S | ★ |
| A8 | **Kesepakatan kelas dan disiplin positif** | Class agreement written with students at the start of the year (template + AI ideas), printable; positive-discipline notes as a Catatan Murid kind. Kepmendikdasmen 17/2026 requires at least: nilai kebajikan, interaksi yang saling menghargai, tindakan pembinaan yang mendidik; the whole class signs it, it is reminded weekly and shared with parents (the four drafting stages are optional) | Permendikdasmen 6/2026; Kepmendikdasmen 17/2026 | M (new) | Catatan Siswa, print | S | ★★ |
| A9 | **Presensi per jam pelajaran** | Attendance per lesson for guru mapel | Customary | N | Absensi | S | ★ (already *Later*, D-029) |
| A10 | **Portofolio murid** | Photos of student work linked to a TP, for rapor and parent meetings | Panduan Asesmen 2025 (other report forms) | N | Asesmen, storage | M–L (disk) | ★★ |
| A11 | **Rincian minggu/hari efektif** | Computed from Kaldik and Jadwal, printed with Prota/Promes | Customary, expected with Prota | M (expected) | Kaldik, Jadwal, Prota | S | ★★ (not in the final docs yet) |

### 4.2 Homeroom (wali kelas)

| # | Feature | What it is | Basis | Need | Reuses | Effort | Value |
|---|---|---|---|---|---|---|---|
| H1 | **Rapor lengkap sesuai Panduan 2025** | Make sure the Rapor add-on outputs all 12 components: kokurikuler (A5), ekstrakurikuler, absences from Absensi, **catatan wali kelas** (AI draft from notes and attendance, never with personal data in the prompt), space for tanggapan orang tua, Fase A "without numbers" option | Panduan Asesmen 2025 §5.B | M | Rapor, Absensi, Catatan Siswa | M | ★★★ |
| H2 | **Peringatan dini kehadiran** | Flag a student whose absences pass a school threshold, or 3 days in a row; shows on Hari Ini and to the principal, with a suggested action (home visit, call). Thresholds (D-303): unexplained absence (tanpa keterangan) brings monthly attendance below 90% (sakit and izin not counted), or 3 consecutive school days without explanation; both changeable or switchable off per school. Kehadiran also gets a recap per week, month, and semester | Panduan Asesmen 2025 ("wali kelas must detect absence early") | M | Absensi, Kabar | S | ★★★ |
| H3 | **Kenaikan kelas** | Before the dewan guru meeting: per student, unfinished TP, absences, notes; the meeting decision and a berita acara (PDF). Then the existing Kenaikan moves students | Panduan Asesmen 2025 §5.C; 21/2022 | M | Analisis, Absensi, Kenaikan | S–M | ★★ |
| H4 | **Kelulusan kelas 6** | Grade 5–6 recap, school exam (ujian satuan pendidikan) scores, graduation decision, berita acara, Surat Keterangan Lulus, data sheet for ijazah/transkrip | Panduan Asesmen 2025; Permendikbudristek 58/2024; MI: Kepdirjen 751/2026, 3576/2026 (*secondary*) | M | Nilai, Rapor, print | M | ★★ |
| H5 | **Ekstrakurikuler and Pramuka** | Ekskul list, members, attendance, pembina, rapor description. Pramuka must be offered again | Permendikdasmen 13/2025 Pasal 22 | M | Rapor; P4-01 tables | M | ★★ (was held with Buku Induk, D-161) |
| H6 | **Buku penghubung / pesan ke orang tua** | Ready-made messages (absence, progress, meeting invite, rapor day) sent from the teacher's own WhatsApp with one tap (`wa.me` text, no links into SIGRAK); a log of contacts per student | Panduan Asesmen 2025 (communicate all year); IA 2024 (parent interviews) | M (expected) | Siswa, Catatan Siswa | S | ★★ |
| H7 | **Kunjungan rumah** | Home-visit record as a Catatan Siswa kind, with a printable form | Customary (BK in SD is done by guru kelas) | N | Catatan Siswa | S | ★ |
| H8 | **Data kesehatan ringan** | Height, weight, simple UKS screening per semester, for rapor Fase A notes and UKS | Panduan Asesmen 2025 (Fase A notes); UKS (*rule status unverified*) | N | Siswa | S | ★ (sensitive data) |

### 4.3 School and principal

| # | Feature | What it is | Basis | Need | Reuses | Effort | Value |
|---|---|---|---|---|---|---|---|
| K1 | **Penyusun KSP** | Build the Kurikulum Satuan Pendidikan in the 4 components of the 2025 guide (karakteristik, visi-misi-tujuan, pengorganisasian pembelajaran, perencanaan) + evaluation plan, pre-filled from school data, structure, allocation, kokurikuler plan, and Kaldik; AI drafts narrative sections; principal sets it (penetapan page) | Permendikdasmen 26/2025 Pasal 7; Panduan KSP 2025; IA 2024 item 9 | M | Template engine, curriculum data, Kaldik, Program Sekolah | L | ★★★ |
| K2 | **Supervisi akademik Pembelajaran Mendalam** | Instruments for the existing Supervisi section: pre-observation, observation (mindful/meaningful/joyful, 4 frameworks), reflection, follow-up; schedule; results report. The teacher's RPM and journal open beside the form | 26/2025 Pasal 22–23; Kepmen 126/P/2025; Ruang GTK observation cycle | M | Supervisi, RPM, Jurnal | M | ★★★ |
| K3 | **Bukti dukung kinerja (Ruang GTK)** | One download per teacher per period: RPM list, journal recap, reflection (A4), observation result (K2). For uploading to Ruang GTK; no integration. Also covers the principal: a Kinerja Kepala Sekolah evidence download with the PKKS result (D-308). Built on the Berkas Administrasi builder as the K3 preset (D-278) | PermenPAN-RB 21/2024; Ruang GTK | N (high demand) | Dokumen Saya, Jurnal, K2 | S | ★★★ |
| K4 | **Pembagian tugas dan beban kerja (JTM)** | From Jadwal and roles: JTM per teacher, extra duties (wali kelas 2 JTM SD / 6 JTM MI, pembina, koordinator kokurikuler), warning below 24 JTM, and the **SK Pembagian Tugas** as PDF | Permendikdasmen 11/2025, Kepmen 221/P/2025; KMA 736/2026 | M | Jadwal, memberships, print | S–M | ★★★ |
| K5 | **Laporan bulanan sekolah** | Monthly recap for dinas/korwil: students by grade and gender, in/out transfers, attendance %, teacher attendance; PDF/Excel in a regional layout | Customary (regional) | M (in many regions) | Siswa, Absensi | S | ★★ |
| K6 | **Evaluasi KSP and refleksi sekolah** | Semester/year evaluation from SIGRAK data (completeness, per-TP results, attendance) plus Rapor Pendidikan notes typed by the principal; feeds next year's RKT | 26/2025 Pasal 4 (evaluasi diri); Panduan KSP ch. 5 | M | K1, Ringkasan, Program Sekolah | M | ★★ |
| K7 | **Kesiapan akreditasi** | Checklist of IA 2024 items with links to the evidence SIGRAK already holds and what is missing | BAN-PDM IA 2024 | N (high demand before visits) | All modules | M | ★★ |
| K8 | **Budaya sekolah aman** | Tata tertib and SOP documents, an internal incident log (who, what, follow-up), yearly monitoring report. A *public* complaint channel is **not** offered (D-010: login only) | Permendikdasmen 6/2026 | M (new) | Buku Sekolah, Catatan Siswa | M | ★★ |
| K9 | **Presensi guru dan tendik** | Daily teacher check-in on the phone (no GPS in v1), recap for the principal and K5 | Customary; ASN rules are regional | N | Hari Ini | S–M | ★★ |
| K10 | **PIC MBG log** | Daily meal check log for the school's MBG PIC | BGN SE 5/2025 | N | — | S | ★ |

### 4.4 Operator and TU

| # | Feature | What it is | Basis | Need | Reuses | Effort | Value |
|---|---|---|---|---|---|---|---|
| O1 | **Buku Induk and Dapodik sync** | Already designed, on hold | D-158, D-161 | M (customary) | P4-01 | L | ★★★ |
| O2 | **Mutasi siswa lengkap** | Surat keterangan pindah, surat penerimaan, reminder to update Dapodik within 14 working days, block in the last semester of grade 6 | Permendikdasmen 3/2025 Bab III | M | Pindah atau keluar, print | S | ★★ |
| O3 | **Persuratan** | Incoming/outgoing letter register, automatic numbering per school pattern, ready letters with letterhead (surat keterangan aktif, undangan rapat, surat tugas, SK) | Regional tata naskah | M (every TU) | Buku Sekolah, letterhead, print | M | ★★ |
| O4 | **Inventaris sekolah (sarpras)** | School-level assets by room, condition, KIB-style list for negeri schools, yearly stock-take. Class inventory is the room view of the same record: the wali kelas edits condition and count for their own room; Tim SIGRAK sets in the console whether the room view needs the class add-on (D-306) | Permendagri 7/2024; Permendikbudristek 22/2023 | M | Inventaris kelas | M | ★★ |
| O5 | **Perpustakaan** | Catalogue, loans by student record, visit log | Perpusnas 4/2024 | N | Siswa | L | ★ |
| O6 | **Ekspor e-Rapor SD / RDM** | Score and description export in the import layout of e-Rapor SD and RDM | Practice (both widely used) | M (in practice) | Rapor | M | ★★★ (already *later*, D-144) |
| O7 | **Kartu pelajar dan cetak massal** | Student ID cards, class lists, attendance sheets to print | Customary | N | Siswa, print | S | ★ |
| O8 | **SPMB online** | Public enrolment form | 3/2025 | — | — | L | Not a fit: needs public pages (D-010) |
| T1 | **Tabungan murid** | Savings ledger per student (deposit, withdrawal, balance), recap per class and school, printable passbook page. Detail (D-301): the book follows the student (`savings_entries`, balance per student per school) and moves with kenaikan; on graduation or mutasi keluar with a balance, a berita acara penyerahan tabungan and a closing withdrawal (`handover_records`); "Tabungan belum diserahkan" stays in the wali kelas's Kabar until closed | Customary | N (common) | Keuangan kelas, print | S–M | ★★ (added by the owner, D-219) |
| O9 | **BOS/RKAS, SPP** | Budget and fee collection | 8/2026; e-RKAM | — | — | L | Not a fit: ARKAS/e-RKAM own this; SPP is sensitive (D-025) |

### 4.5 Supervisor (pengawas, korwil)

| # | Feature | What it is | Basis | Need | Reuses | Effort | Value |
|---|---|---|---|---|---|---|---|
| P1 | **Pendampingan Pembelajaran Mendalam** | Per school: K2 results (aggregates), KSP status, completeness; visit notes and follow-up | Kepmen 126/P/2025 (pengawas mentoring cycle); Panduan KSP (pengawas role) | M | Supervisor portal, K1, K2 | M | ★★ |
| P2 | **Rekap lintas sekolah** | One table across supervised schools: KSP set, supervision done, rapor done, attendance %, no student-level data | Customary | N | Supervisor portal | S | ★★ |

### 4.6 Parents

Parents are not users (D-010), and no public share links exist. Parent-facing value can still come through the teacher: H6 (WhatsApp messages from the teacher's own phone), the printed 7 KAIH home sheet (A6), and tanggapan orang tua on the rapor (H1). A **parent portal** would reverse D-010 and add personal-data duties; it is listed only so the owner can confirm it stays out.

## 5. Strongest candidates (before the owner's choices)

Ranked by "must do anyway × data already in SIGRAK × sales value". Each extends the CP → RPM → jurnal → asesmen → rapor chain that no competitor covers.

| Rank | Feature | Why | Fits |
|---|---|---|---|
| 1 | H1 Rapor lengkap + A5 Asesmen kokurikuler | Rapor without the kokurikuler and wali-kelas parts is not complete under the 2025 guide; e-Rapor users already struggle here | Slice 6 (P5-08), inside the Rapor add-on |
| 2 | A1 Remedial dan pengayaan (+ A3 KKTP) | Turns Analisis per TP into action; accreditation asks for it; AI can draft the activities | Slice 6 (P5-08); template in a later templates slice |
| 3 | K2 Supervisi PM + K3 Bukti kinerja | Principal's yearly duty and every teacher's Ruang GTK evidence; one download sells both plans | Slice 7 (P5-09) |
| 4 | K4 Pembagian tugas dan JTM | Made from the timetable in seconds; every school writes this SK each year; SD/MI difference handled | Slice 7 (P5-09), operator/principal |
| 5 | H2 Peringatan dini kehadiran | Small, visible, explicitly asked by the guide | Slice 5 (P5-07) |
| 6 | K1 Penyusun KSP (+ K6 evaluasi) | Mandatory, heavy to write, accreditation item; big principal-side seller. Large effort, so after v1 | New Phase 6 slice (or add-on) |
| 7 | A4 Refleksi guru, A8 Kesepakatan kelas, H3 Kenaikan, O2 Mutasi | Small, mandatory, reuse existing data | Slices 5–7 as small tasks |
| 8 | O6 Ekspor e-Rapor/RDM | Removes double entry; strongest MI argument | After slice 6 (D-144) |

Lower priority: A2, A6, H4, H5 (with D-161), H6, K5, K7, K8, O3, O4, P1, P2. Out of scope unless the owner says otherwise: O8 SPMB, O9 BOS/SPP, parent portal, K10 MBG, O5 Perpustakaan.

## 6. Decision list (as presented)

The owner answers each with **Ya** (include, add to the build plan), **Nanti** (later candidate), or **Tidak** (exclude). Suggested answer in brackets.

1. H1 Rapor lengkap 12 komponen (kokurikuler, ekskul, absences, catatan wali kelas with AI draft, tanggapan ortu, Fase A without numbers) [Ya]
2. A5 Asesmen kokurikuler per dimensi profil lulusan [Ya]
3. A1 Remedial dan pengayaan from Analisis per TP [Ya]
4. A3 KKTP per TP [Ya]
5. A2 Asesmen diagnostik awal [Nanti]
6. A4 Refleksi guru per semester [Ya]
7. A6 Gerakan 7 KAIH tracker + home sheet [Nanti]
8. A7 Analisis butir soal [Nanti]
9. A8 Kesepakatan kelas and disiplin positif [Ya]
10. A10 Portofolio murid (photos) [Nanti]
11. A11 Rincian minggu efektif with Prota/Promes [Ya]
12. H2 Peringatan dini kehadiran [Ya]
13. H3 Kenaikan kelas (meeting sheet + berita acara) [Ya]
14. H4 Kelulusan kelas 6 (SKL, ijazah data) [Nanti]
15. H5 Ekstrakurikuler and Pramuka (restart part of D-161?) [Nanti]
16. H6 Pesan ke orang tua via the teacher's WhatsApp [Ya]
17. H7 Kunjungan rumah, H8 data kesehatan [Nanti]
18. K1 Penyusun KSP + K6 evaluasi KSP [Ya, after v1]
19. K2 Supervisi akademik Pembelajaran Mendalam [Ya]
20. K3 Bukti dukung kinerja (Ruang GTK) [Ya]
21. K4 Pembagian tugas, JTM, SK [Ya]
22. K5 Laporan bulanan sekolah [Nanti]
23. K7 Kesiapan akreditasi [Nanti]
24. K8 Budaya sekolah aman (tata tertib, SOP, incident log; no public channel) [Nanti]
25. K9 Presensi guru dan tendik [Nanti]
26. O2 Mutasi siswa lengkap [Ya]
27. O3 Persuratan [Nanti]
28. O4 Inventaris sekolah [Nanti]
29. O5 Perpustakaan, O7 kartu pelajar [Tidak / Nanti]
30. O6 Ekspor e-Rapor SD and RDM: move up from "later" (D-144) [Ya, after slice 6]
31. P1 Pendampingan PM, P2 Rekap lintas sekolah for pengawas [Ya, P2 in slice 7; P1 Nanti]
32. Out of scope confirmed: SPMB online, BOS/RKAS/SPP, parent portal, MBG log [Tidak]

Open questions:

- **Q1 Packaging.** Are new features core (Sigrak Guru / Sigrak Sekolah) or add-ons (D-158)? Suggested: teacher features (A1, A3, A4, A8, A11, H2, H3, H6) are core Sigrak Guru; H1/A5 sit in the Rapor add-on; principal features (K2, K3, K4, O2) are core Sigrak Sekolah; K1/K6 could be a new add-on "Kurikulum Sekolah".
- **Q2 Wali kelas JTM.** Use 2 JTM for SD and 6 JTM for MI as the default, editable by the school? (The two ministries differ.)
- **Q3 Catatan wali kelas by AI.** OK to draft it with AI from notes and attendance counts, sending no names or personal data (`PromptDataGuard`)?
- **Q4 Regional formats.** Can the owner send the Kudus formats for Laporan bulanan, SK pembagian tugas, and supervisi instruments used by the local dinas/Kemenag?

## 7. Owner decisions (2026-11-08)

All new rules in §1 are applied to the product (D-210). Every accepted feature starts as **core**; Tim SIGRAK decides later in the console which ones become add-ons (D-211).

| # | Feature | Choice | Decision | Build fit |
|---|---|---|---|---|
| 1 | H1 Rapor 12 komponen, Fase A without numbers | Ya | D-214, D-213 | P5-08 |
| 2 | A5 Asesmen kokurikuler | Ya | D-214 | P5-08 |
| 3 | A1 Remedial dan pengayaan | Ya | D-214 | P5-08 (+ template later) |
| 4 | A3 KKTP per TP | Ya | D-214 | P5-08 |
| 5 | A2 Asesmen diagnostik | Ya | D-214 | P5-08 |
| 6 | A4 Refleksi guru | Ya | D-214 | P5-07/P5-08 |
| 7 | A6 7 KAIH | Ya | D-214 | P5-07 |
| 8 | A7 Analisis butir soal | Ya | D-214 | P5-08 |
| 9 | A8 Kesepakatan kelas, disiplin positif | Ya | D-214 | P5-07 |
| 10 | A10 Portofolio foto | Ya | D-214 | P5-08 (disk limits to design) |
| 11 | A11 Minggu efektif | Ya | D-214 | P5-04 (Prota/Promes print) |
| 12 | H2 Peringatan dini kehadiran | Ya | D-215 | P5-07 |
| 13 | H3 Kenaikan kelas | Ya | D-215 | P5-08 |
| 14 | H4 Kelulusan kelas 6 | Ya | D-215 | P5-08 |
| 15 | H5 Ekstrakurikuler and Pramuka | Ya, early in v1 | D-216 | P5-07 |
| 16 | H6 Pesan ke orang tua | Ya, plain `wa.me` text only | D-215 | P5-07 |
| 17 | H7, H8 Kunjungan rumah, kesehatan | Ya | D-215 | P5-07 |
| 18 | K1, K6 Penyusun and evaluasi KSP | Ya, after v1 | D-217, D-209 | After v1 |
| 19 | K2 Supervisi PM | Ya | D-217, D-221 | P5-09 |
| 20 | K3 Bukti kinerja Ruang GTK | Ya | D-217 | P5-09 |
| 21 | K4 Pembagian tugas, JTM, SK | Ya | D-217, D-212, D-221 | P5-09 |
| 22 | K5 Laporan bulanan | Nanti; BOS/RKAS reports never | D-219, D-220 | — |
| 23 | K7 Kesiapan akreditasi | Nanti | D-220 | — |
| 24 | K8 Budaya sekolah aman | Ya | D-217 | P5-09 |
| 25 | K9 Presensi guru dan tendik | Tidak (staffing systems cover it) | D-217 | — |
| 26 | O2 Mutasi siswa lengkap | Ya | D-217 | P5-09 |
| 27 | O3 Persuratan | Nanti | D-220 | — |
| 28 | O4 Inventaris sekolah | Ya | D-217 | P5-09 |
| 29 | O5 Perpustakaan, O7 kartu pelajar | Upcoming / Nanti | D-220 | — |
| 30 | O6 Ekspor e-Rapor SD and RDM | Ya, right after slice 6 | D-218 | After P5-08 |
| 31 | P1, P2 Pendampingan PM, rekap lintas sekolah | Ya | D-217 | P5-09 |
| — | T1 Tabungan murid | Ya (added by the owner) | D-219 | P5-07 (with Keuangan kelas) |
| — | Parent portal, SPP, MBG log, perpustakaan | Upcoming | D-220 | — |

Answers to the open questions: Q1 → D-211; Q2 → D-212; Q3 → D-213; Q4 → D-219 and D-221.

**Still open**

- The owner's samples (D-221) arrived on 2026-11-08. The supervision instrument is in `docs/sources/` and is reworked in [`teacher-supervision-instruments.md`](teacher-supervision-instruments.md) (P4-12). The SK sample is kept in `docs/sources/` as received (`Contoh_SK_Pembagian_Tugas_SD_Semester_Genap_2025_2026.pdf`, owner's choice); its structure is below.

**SK pembagian tugas sample (structure only, from a Kudus SD, semester genap 2025/2026).** Two pages. Page 1, the decree: letterhead (Pemda, Dinas, school, address, email, NSS, NPSN); "Keputusan Kepala [sekolah] Nomor 400.3.5/…/2026 tentang Pembagian Tugas Guru dan Karyawan dalam Proses Belajar Mengajar/Praktik/Bimbingan dan Konseling/Tugas-tugas Lain, Semester …, Tahun Pelajaran …"; *Menimbang*; *Mengingat* (a list of laws and regulations, in the sample still the pre-2025 ones, so SIGRAK should default to the current list: UU 20/2003, PP 57/2021 as amended, Permendikdasmen 11/2025, 13/2025, 1/2026, 26/2025; MI: KMA 736/2026, KMA 1503/2025); *Memutuskan/Menetapkan* with five clauses (Pertama: the assignment in the lampiran; Kedua: each teacher reports in writing to the principal; Ketiga: costs charged to the matching budget; Keempat: corrections if errors are found; Kelima: in force from the date set); place and date; principal's name and NIP; tembusan (Kepala Dinas, the people concerned). Page 2, the lampiran table: No, Nama/NIP, Gol, Jabatan/status (PNS rank, PPPK, GTT, PTT), Jenis guru (guru kelas, PAI, PJOK, Bahasa Inggris, tenaga administrasi), Mengajar kelas, Jumlah jam, Tugas tambahan (e.g. Plt. kepala sekolah, kurikulum, bendahara sekolah/BOS, UKS, pembina ekstrakurikuler, koperasi, operator, aset, tabungan, perpustakaan, Pramuka, kebersihan); then a JP table per grade I–VI for guru kelas, PAI, PJOK, Bahasa Inggris, Bahasa Jawa, with totals; place, date, and signature. Everything except the clause text comes from SIGRAK data (school profile, memberships, timetable, extra duties), so K4 generates both pages; the clauses and *Mengingat* list are an editable template.
- Portfolio photo limits (size, count, retention) to fit the 320 GB disk (D-096, D-114).
- The "murid" term (D-210) changes the glossary and UI copy; done in the final-docs update task.

## Sources

All accessed 2026-11-07. *Secondary* = news or blog only; verify before product copy.

Official documents in the repo (received in `docs/dokumen-resmi/`, now in `docs/sources/` under descriptive names, D-203): Permendikdasmen 13/2025; Kepmendikdasmen 126/P/2025 and 127/P/2025; BSKAP Panduan Kokurikuler 2025, Panduan Pengembangan KSP edisi revisi 2025, Panduan Pembelajaran dan Asesmen edisi revisi 2025, Panduan Pembelajaran KKA (Feb 2025).

Regulation text:
- Permendikdasmen 1/2026: https://www.peraturan.go.id/filespengundangan/Permendikdasmen-no-1-tahun-2026.pdf
- Permendikdasmen 26/2025: https://peraturan.go.id/files/Permendikdasmen-no-26-tahun-2025.pdf
- Permendikdasmen 8/2026 (BOSP): https://peraturan.go.id/files/Permendikdasmen-no-8-tahun-2026.pdf
- Permendikdasmen 9/2026 (AN): https://www.peraturan.go.id/files/Permendikdasmen-no-9-tahun-2026.pdf
- Permendikbudristek 22/2023 (sarpras): https://peraturan.go.id/files/permendikbudristek-no-22-tahun-2023.pdf
- Permendikbudristek 58/2024 (ijazah): https://peraturan.go.id/filespengundangan/permendikbudristek-no-58-tahun-2024.pdf
- Perpusnas 4/2024: https://peraturan.go.id/files/perpusnas-no-4-tahun-2024.pdf
- PermenPAN-RB 21/2024: https://peraturan.bpk.go.id/Details/311201/permen-panrb-no-21-tahun-2024
- Permendagri 7/2024: https://peraturan.bpk.go.id/Details/300030/permendagri-no-7-tahun-2024
- Mirror (pasal.id): [13/2025](https://pasal.id/peraturan/permen/permendikdasmen-no-13-tahun-2025), [11/2025](https://pasal.id/peraturan/permen/permendikdasmen-no-11-tahun-2025), [9/2025](https://pasal.id/peraturan/permen/permendikdasmen-no-9-tahun-2025), [3/2025](https://pasal.id/peraturan/permen/permendikdasmen-no-3-tahun-2025), [6/2026](https://pasal.id/peraturan/permen/permendikdasmen-no-6-tahun-2026), [21/2022](https://pasal.id/peraturan/permen/permendikbudriset-no-21-tahun-2022)
- Pramuka required (13/2025 Pasal 22): https://www.kemendikdasmen.go.id/siaran-pers/13309-perkemahan-anak-indonesia-hebat-2025-pramuka-jadi-pilar-pembentukan-karakter-pelajar
- Komite (Permendikbud 75/2016): https://setkab.go.id/permendikbud-nomor-75-tahun-2016-komite-sekolah-boleh-galang-dana/

Secondary:
- Kepmendikdasmen 221/P/2025 (JTM): https://www.komunitasbelajar.id/2025/11/kepmendikdasmen-nomor-221p2025.html
- KMA 736/2026 (madrasah workload): https://www.komunitasbelajar.id/2026/07/kma-nomor-736-tahun-2026-tentang-pedoman-pemenuhan-beban-kerja-guru-madrasah.html
- Permendikdasmen 23/2026: https://www.komunitasbelajar.id/2026/09/permendikdasmen-nomor-23-tahun-2026.html
- SE Sesjen 14/2025 (7 KAIH): https://www.komunitasbelajar.id/2025/09/se-sesjen-kemendikdasmen-nomor-14-tahun.html
- IA 2024 guide version 2025: https://www.imrantululi.net/read/281/panduan-penjelasan-instrumen-akreditasi-2024-ia2024-versi-2025-transformasi-pendidikan-berbasis-refleksi-dan-inklusivitas-untuk-sdmi-smpmts-dan-smama
- Madrasah BOS, ijazah, kaldik: https://www.komunitasbelajar.id/2026/02/juknis-bos-madrasah-ra-mi-mts-ma-mak.html, https://www.komunitasbelajar.id/2026/06/juknis-penerbitan-ijazah-madrasah-tahun.html, https://www.komunitasbelajar.id/2026/06/kalender-pendidikan-madrasah-tahun.html
- KMA 1503/2025: https://yunandra.com/kma-1503-tahun-2025-pedoman-kurikulum-madrasah/
- RDM kokurikuler: https://tirto.id/deskripsi-penilaian-kokurikuler-untuk-pengisian-rdm-hnHH
- MBG PIC: https://news.republika.co.id/berita/t3dlev484/guru-penanggung-jawab-mbg-di-sekolah-bakal-dapat-insentif-rp-100-ribu-per-hari
- Kinerja 2026: https://www.metrotvnews.com/read/k8oCQ6D9-pengelolaan-kinerja-2026-libatkan-banyak-guru

Benchmark:
- Ruang GTK / Rumah Pendidikan: https://www.goodnewsfromindonesia.id/2025/01/25/ini-perbedaan-pmm-ruang-gtk-dan-rumah-pendidikan-bagi-guru
- e-Rapor SD 2025: https://www.imrantululi.net/read/365/aplikasi-rapor-elektronik-e-rapor-tahun-2025-jenjang-sd-smp-sma-smk-slb
- RDM study: https://new-conference.unisma.ac.id/index.php/KNPI/article/view/2083
- e-RKAM: https://nu.or.id/nasional/mengenal-fitur-fitur-di-aplikasi-e-rkam-akun-tingkat-madrasah-kK6Ru
- Gredu: https://gizmologi.id/news/gredu-solusi-digitalisasi-sekolah/
- MySCH/SmartSchool (2021): https://mysch.id/blog/detail/107/aplikasi-administrasi-sekolah
- AIO Class: https://apps.apple.com/us/app/aio-class-class-management/id6737825197
- Sigma Guru: https://apps.apple.com/us/app/id6478269542
- GuruInovatif Modul Generator: https://guruinovatif.id/modul-generator
- BRIN Asisten Guru AI: https://www.detik.com/edu/sekolah/d-8256523/brin-buat-aplikasi-ai-khusus-buat-guru-ada-fitur-pembelajaran-mendalam
- Kelas Pintar: https://selular.id/2026/06/kelas-pintar-jadi-best-edutech-app-with-ai-di-selular-award-2026/
- Edura School (claims, page blocked): https://rmol.id/nusantara/read/2026/06/16/710905/edura-school-jawab-tantangan-guru-di-era-digital
