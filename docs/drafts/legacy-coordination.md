# Legacy App Upgrade: Decision Coordination (usulan-01 + Part B)

- **Status:** done 2026-11-09 · all six sessions answered; outcome in [`legacy-upgrade-01.md`](legacy-upgrade-01.md), decisions D-252…D-328
- **Task:** P4-13 (proposal → decisions; no app code)
- **Last updated:** 2026-11-09
- **Read with:** [`usulan-01`](../sources/usulan-01/usulan-01-peta-fitur-aplikasi-lama-ke-sigrak.md) (Indonesian, owner source, items U-01…U-15, open questions T-1…T-11), [`usulan-01/data/`](../sources/usulan-01/data/README.md), [`legacy-overlap-adjustments.md`](legacy-overlap-adjustments.md) (Part B, items B-01…B-16, owner answers Q1…Q10)

## Purpose

Two proposals reached the repo on the same day and overlap. The session that produced usulan-01 (a walk-through of the legacy portals) also raised ideas that neither file carries. This file records what was covered, what conflicted, and how the decisions were split across parallel sessions. It is kept as the record of the process; the outcome is in [`legacy-upgrade-01.md`](legacy-upgrade-01.md).

## 1. Conflicts between the two proposals

| # | Topic | usulan-01 | Part B | To settle in |
|---|---|---|---|---|
| C-1 | Answer-sheet scanning (OMR) | Core in P5-08 (W-3); PG, PG kompleks, B-S; QR = random sheet `public_id`; offline queue | Later, built with Kartu Jawab (Q5); QR = assessment + student number | S4 |
| C-2 | Completeness service | `ProgressCalculator` (`app/Progress/`), free range, `progress_snapshots`, guru mapel exclusions, reflection per semester, annual docs | `CompletenessLedger`, fixed windows (hari/minggu/semester), states incl. `excused`, "Siapkan besok" | S1 |
| C-3 | JP capacity warning | U-12 Neraca JP before SAS + U-02 "tidak muat sebelum SAS" Kabar | B-02 capacity check with merge / move / accept options | S1 |
| C-4 | Semester package | "Unduh Paket Administrasi", in Hari Ini + Pantau Guru, PDF only, profiles Standar/Kudus, summary sheet, NIHIL; merge only if too heavy (T-2) | "Berkas Administrasi", in Dokumen Saya, merged PDF + ZIP option (Q6), K3 preset | S3 |
| C-5 | Leger lock | Locked after wali kelas + guru mapel confirm; principal unlocks | Locked after principal sign-off; reopening needs a reason | S4 |
| C-6 | D-016 amendments | Offline scan queue (T-6) | Read-only RPM meetings today + tomorrow (Q9, answered) | S4 (one combined decision) |
| C-7 | Task IDs | Proposes `P5-03h` for holidays (taken: copy classes, done) and `P4-13` | — | S2 / coordinator |
| C-8 | Piket | U-07 gender balance, rotation, tasks, mutasi | B-13.1 skip absent students, Game kelas idle screen | S5 (merge) |
| C-9 | Class cash | U-11 no money messages to parents (H6) | B-13.3 monthly class-cash `wa.me` summary, totals only | S5 |

## 2. Discussed in the usulan-01 session but in neither file

From the transcript of the walk-through session (2026-11-08). Assign each to v1, a later usulan-02, or reject.

| # | Idea | Session |
|---|---|---|
| G-1 | Supervisi manajerial / PKKS of the principal by the pengawas; Kinerja Kepala Sekolah (extends K3) — owner never answered whether it is in scope | S6 |
| G-2 | Pengawas requests sekolah binaan, approved by principal or staff (today: console assignment, D-168) | S6 |
| G-3 | Auto-scoring of S-01/S-15 from data, principal confirms | S6 |
| G-4 | AI draft score for S-04 Telaah RPM, principal must check | S6 |
| G-5 | Findings bank per supervision instrument | S6 |
| G-6 | One conflict checker across lessons, supervision, agenda, calendar | S2 |
| G-7 | Pusat Tindak Lanjut (P5-09) | S6 |
| G-8 | Access matrix for the Filament console | S6 |
| G-9 | Curriculum console: diff, publish, rollback, impact report; Buku Guru AI draft (references only, SIBI licence); JP audit of RPMs against the latest ATP | S6 |
| G-10 | AI model registry in the console (active + fallback per template, test button) | S6 |
| G-11 | Kesehatan Sistem page (Horizon, failed jobs, disk, generations per day) | S6 |
| G-12 | Retention policy per data type; read-only Arsip Tahun Ajaran | S6 |
| G-13 | Kabar dari pusat targeted by region / role, with a display period | S6 |
| G-14 | Help-ticket statuses (baru / diproses / selesai) | S6 |
| G-15 | Teguran → "pengingat" with read / acted-on status | S6 |
| G-16 | "Versi baru tersedia" banner (asset version check, offline Di Kelas) | S6 |
| G-17 | Console users grouped by kecamatan / kabupaten | S6 |
| G-18 | "Buat kop otomatis" from logo + profile (touches D-031) | S3 |
| G-19 | Kurikulum: keep "Pulihkan bawaan" / "Reset penyesuaian"; auto annual JP target for kokurikuler | S1 |
| G-20 | Bank Soal output components (kisi-kisi, naskah, kunci, pembahasan, indikator, kartu soal) as the template output schema | S4 |
| G-21 | Attendance recap per period with early warning (H2) | S5 |
| G-22 | Draft status before mutasi / kenaikan is set | S5 |
| G-23 | "Do not re-adopt" list (generator bebas, Bahan Tayang, SMP–SMA, mandatory key at activation, default password, html2canvas PDF, 15-min idle logout, Mode Ujian) | coordinator |
| G-24 | Data files without a task: kokurikuler, ekstrakurikuler, inventaris kelas, pilihan-isian (diagnostic, prestasi, notulen, mutasi, …) | S5 (import plan, D-113 review) |

Already covered by Part B: transfer rules (B-14), Kabar actions (B-12, partly G-13), Bank Soal reuse (B-05).

## 3. Parallel sessions

| Session | Scope | Owner questions to settle |
|---|---|---|
| S1 Completeness and RPM flow | U-01/B-01, U-02, B-02, B-03, U-12, U-13, B-04, B-11, G-19 | C-2, C-3, T-1, T-11, B-6 |
| S2 Calendar and timetable | U-05, U-06, G-6, B-11 (calendar side) | T-4, T-5, C-7, holiday data check |
| S3 Package, print, approval | U-03/B-10, U-15, U-04, U-14, G-18 | C-4, T-2, T-3, T-8, T-9, B-3, B-4 |
| S4 Assessment and rapor | U-08/B-06, U-09/B-08, B-05, B-07, B-15, G-20 | C-1, C-5, C-6, T-6, T-7 |
| S5 Kelas Saya and homeroom | U-07, U-10, U-11, B-09, B-13, B-14, B-16, G-21, G-22, G-24 | C-8, C-9, T-10 (Absensi vs Kehadiran, B-5) |
| S6 Portals and console | G-1…G-5, G-7…G-17 | v1 / usulan-02 / reject per item |

Rules for every session: interview the owner in small rounds (one topic at a time, a recommendation first); change nothing in the repo; send the result to the coordinator, which assigns D-numbers (from D-252) and asks the owner before applying anything.

## 4. Outcome

| Session | Answered | Decisions |
|---|---|---|
| S1 Completeness and RPM flow | 4 rounds | D-263…D-273, D-276, D-277 (class events merged into D-258) |
| S2 Calendar and timetable | 4 rounds | D-252…D-262 |
| S3 Package, print, approval | 4 rounds | D-278…D-285 |
| S4 Assessment and rapor | 5 rounds | D-286…D-295 |
| S5 Kelas Saya and homeroom | 5 rounds | D-297…D-305 |
| S6 Portals and console | 4 rounds | D-308…D-321 |
| Coordinator | Part B answers without a session, G-23, final round, follow-up (kaldik Jateng, LJK photo, AI providers, Kudus supervision set: D-323…D-328) | D-274, D-275, D-296, D-306, D-307, D-322; Berkas range, retention and revoke rules in D-278 and D-281; attendance rule in D-303; retention floors in D-318 |

Conflicts C-1…C-9 and gaps G-1…G-24 are all closed (see the map in `legacy-upgrade-01.md` §2–3).

## Open questions

- None for this process. Remaining data and build questions are in `legacy-upgrade-01.md`.
