# Legacy App Upgrade 01: Decided Changes

- **Status:** draft · decisions accepted 2026-11-09 (D-252…D-328); `docs/final/` not yet updated
- **Task:** P4-13
- **Last updated:** 2026-11-09
- **Read with:** [`usulan-01`](../sources/usulan-01/usulan-01-peta-fitur-aplikasi-lama-ke-sigrak.md) (owner source, Indonesian), [`legacy-overlap-adjustments.md`](legacy-overlap-adjustments.md) (Part B), [`legacy-coordination.md`](legacy-coordination.md) (conflicts, gaps, sessions), [`DECISIONS.md`](../agent/DECISIONS.md#d-252--calendar-work-is-p5-03i-draft-timetable-is-p5-03j)

## Purpose

SIGRAK is the next generation of the legacy app, not a copy. Two proposals covered the same ground on 2026-11-09:

- **usulan-01** maps legacy app features to SIGRAK as U-01…U-15, with the open questions T-1…T-11.
- **Part B** adjusts the shared features as B-01…B-16, with the owner's answers Q1…Q10.

The walk-through of the legacy app portals also raised ideas that neither proposal carried (G-1…G-24). Six parallel sessions interviewed the owner on 2026-11-09, and this session merged their results. This draft is the single English record of the outcome. It is the input for P4-14 (update `docs/final/` and the PDFs) and for the build slices.

Each decision text lives in `DECISIONS.md`. This file only maps each item to its D-number and lists what each doc and task still needs.

## 1. Guiding differences from the legacy app

These hold for every item. They come from usulan-01 §1.1.

| # | SIGRAK rule | Where it shows |
|---|---|---|
| P1 | One server-side service per number; every role reads the same result | D-259, D-263, D-269 |
| P2 | Terms follow the 2025–2026 rules (murid, kokurikuler, daya serap, Kehadiran) | D-302, D-322, glossary in usulan-01 §2 |
| P3 | Layered privacy: no student work to AI, no public pages, supervisors see school totals | D-281, D-287, D-308 |
| P4 | Automatic, but people confirm: drafts before anything is saved | D-262, D-292, D-297, D-304 |
| P5 | Data goes straight into the supervision instruments | D-278, D-312 |
| P6 | Less admin load: reflection per semester, untargeted rows only shown | D-263 |
| P7 | Text PDFs from the server, print rules set once | D-280, D-283 |

The UI terms in usulan-01 §2 apply, with these changes:
- "Download Paket" becomes **Berkas Administrasi** (D-278).
- The menu is **Kehadiran** (D-302).
- "teguran" is never used (D-311).

## 2. Item → decision map

### 2.1 Calendar and timetable (P5-03i, P5-03j)

| Item | Decision |
|---|---|
| Task IDs (usulan-01 P5-03h → P5-03i, P5-03e2 → P5-03j) | D-252 |
| U-05 national holidays, data check, joint leave | D-253, D-254, D-255 |
| U-05 region reference calendars, school copy with updates | D-256, D-257 |
| U-02 class-level events (`calendar_events.class_id`) | D-258 |
| U-05 effective days and weeks | D-259 |
| G-6 conflict checker | D-260 |
| B-11 calendar change impact preview | D-261 |
| U-06 draft timetable | D-262 |
| Jawa Tengah kaldik, 6-day version, for Kudus, Pati, Jepara | D-323 |

### 2.2 Completeness and RPM flow (P5-04, P5-07)

| Item | Decision |
|---|---|
| U-01 + B-01 completeness service (C-2) | D-263 |
| T-1 windows | D-264 |
| U-01 weekly snapshots | D-265 |
| B-01 "Siapkan besok" | D-266 |
| B-03 + U-02 journal outcome, `partial`, carry-over | D-267 |
| U-02 not-done reasons, lost-JP recap | D-268 |
| U-12 + B-02 + U-02 JP balance before SAS (C-3) | D-269 |
| U-12 RPM readiness (T-11) | D-270 |
| U-12 Kabar | D-271 |
| U-13 early semester-2 drafts | D-272 |
| B-04 "Pasang ke jadwal" | D-273 |
| B-02 shift preview and undo, Promes Realisasi (Q1) | D-274 |
| B-04 parallel-class copy (Q3) | D-275 |
| G-19 restore curriculum defaults | D-276 |
| G-19 kokurikuler JP target | D-277 |

### 2.3 Berkas, print, approval (P5-04, P5-07, P5-09)

| Item | Decision |
|---|---|
| U-03 + B-10 Berkas Administrasi (C-4, Q6) | D-278 |
| B-10 school-wide download | D-279 |
| T-2 PDF merge for Berkas (amends D-031, XP-6) | D-280 |
| U-04 pengesahan dokumen | D-281 |
| T-3 approved revisions kept (amends D-153) | D-282 |
| U-15 cover, margin profiles, landscape, NIHIL (T-9) | D-283 |
| G-18 kop otomatis | D-284 |
| U-14 completion sound (T-8) | D-285 |

### 2.4 Assessment and rapor (P5-05, P5-08)

| Item | Decision |
|---|---|
| U-08 vs B-06 scanning (C-1; W-3 kept, Q5 replaced) | D-286 |
| U-08 sheet content and forms | D-287 |
| U-08 TKA paper backup | D-288 |
| T-6 + Q9 offline scope (C-6, amends D-016) | D-289 |
| U-09 weights (T-7) | D-290 |
| U-09 remedial cap (T-7) | D-291 |
| U-09 + B-08 leger lock (C-5) | D-292 |
| G-20 Bank Soal output | D-293 |
| B-05 item library (Q4) | D-294 |
| B-07 daya serap, Kelompok belajar | D-295 |
| B-16 LKPD blocks (Q10) | D-296 |
| LJK layout from the schools' own sheet (crossed letter boxes, partial scores for Isian/Uraian) | D-325 |

### 2.5 Kelas Saya and homeroom (P5-07, P5-08, P5-09)

| Item | Decision |
|---|---|
| U-07 + B-13.1 piket (C-8) | D-297, D-298 |
| U-11 + B-13.3 class cash (C-9) | D-299 |
| U-11 sumbangan sukarela (W-4) | D-300 |
| U-10 savings and handover | D-301 |
| T-10 / B-5 Kehadiran | D-302 |
| G-21 attendance recap and early warning | D-303 |
| G-22 drafts for kenaikan and mutasi | D-304 |
| G-24 catalog import | D-305 |
| B-13.2 inventory room view (Q7) | D-306 |
| B-14 teacher transfer (Q8) | D-307 |

### 2.6 Portals and console (P4-08, P5-09, console)

| Item | Decision |
|---|---|
| G-1 PKKS and principal performance evidence | D-308, D-324 (rules now, regional variants later) |
| Kudus supervision instruments (teacher S-01, S-04/S-07, S-08, S-09; principal administration) as a regional variant | D-327 |
| Principal administration items without SIGRAK data: manual score + optional evidence | D-328 |
| G-2 sekolah binaan requests | D-309 |
| G-7 Pusat Tindak Lanjut | D-310 |
| G-15 Pengingat | D-311 |
| G-3 suggested S-01/S-15 scores | D-312 |
| G-5 findings bank | D-313 |
| G-8 console access | D-314 |
| G-10 AI model per purpose (amends D-117) | D-315 |
| Console AI adapters: OpenAI-compatible (OpenAI, OpenRouter, DeepSeek) and Anthropic; teachers stay on Gemini | D-326 |
| G-11 Kesehatan Sistem | D-316 |
| G-9 curriculum console | D-317 |
| G-12 Arsip Tahun Ajaran, retention | D-318 |
| G-13 + G-17 targeted Kabar, kecamatan | D-319 |
| G-14 help-request status | D-320 |
| G-16 "Versi baru tersedia" | D-321 |

### 2.7 Part B items that need no new decision

These stay as written in Part B and are built in their slice:
- **B-09:** one-tap note tags; BK notes never go into Berkas, K3, supervisor views, or AI prompts, and they get their own retention rule. Achievements can be marked "masuk rapor".
- **B-12:** every Kabar item has a kind and one action button; Ingatkan is limited to one per item per teacher per week (also in D-311); old items fold into a weekly digest.
- **B-06 grid cepat** (in D-286).

## 3. Not adopted, deferred, rejected

| Item | Outcome | Decision |
|---|---|---|
| Legacy app features listed in G-23 | Not carried over | D-322 |
| G-4 AI draft score for S-04 Telaah RPM | Deferred to usulan-02 | D-322 |
| U-04 "Cocokkan berkas" | Deferred to usulan-02 | D-322 |
| G-9 AI draft from Buku Guru | Rejected (SIBI licence) | D-317 |
| G-8 editable permission matrix | Rejected | D-314 |
| U-06 numeric preference weights | Replaced by switches | D-262 |
| U-02 `meetings.not_done_reason` and the separate "tidak muat sebelum SAS" Kabar | Replaced by D-267 and D-269 | — |
| U-03 "merge only above ~300 pages" | Replaced by always-merge for Berkas | D-280 |
| B-05 "skip items used in the last N weeks" | Replaced by the "pernah dipakai" flag | D-294 |
| B-13.3 automatic monthly cash message | Replaced by manual sharing | D-299 |
| Part B Q5 (scanning with Kartu Jawab) | Replaced | D-286 |

## 4. Doc changes for P4-14

| Doc | Changes |
|---|---|
| `docs/final/data-model.md` | **New tables:** `national_holidays`, `progress_snapshots`, `document_approvals`, a Berkas job record, `answer_sheets`, `sheet_scans`, `question_items`, `savings_entries`, `handover_records`, `class_contribution_kinds`, `class_contributions`, leger state with confirmations and unlock log, retention settings with log, AI model settings with log, staff announcements with targets, help-request status.<br>**Columns:**<ul><li>`calendar_events.class_id`; an SAS/STS subtype on `exam` events (needed by D-269); a joint-leave school-day override</li><li>`journals.status` gains `partial`, plus `not_done_reason`, `not_done_note`, `unfinished_experiences`; `meetings.status` gains `partial` and is derived from the journal</li><li>`school_print_settings.margin_profiles`, `duplex`, `cover_logo_path`; a letterhead source flag</li><li>`school_topic_changes` reverted status</li><li>`schools.district`</li><li>`supervisor_assignments` request status</li><li>`academic_years` closed flag</li><li>remedial history on scores</li><li>`duty_rosters` groups and members</li><li>school settings: readiness thresholds, timetable preference switches, attendance thresholds, piket-on-idle switch, assessment weights and remedial cap</li><li>per-user sound and notification settings</li></ul>**Calendar note:** "school calendar copied from a region calendar, with offered updates" |
| `docs/final/content-engine.md` | Scheduling: partial with Lanjutan, reasons, shift preview and undo, JP balance, readiness, early semester 2, Pasang ke jadwal, parallel copy, Promes Realisasi. Bank Soal: per-item output and item library. LKPD blocks |
| `docs/final/product-concept.md` | Menus: Kehadiran, Berkas Administrasi, Pusat Tindak Lanjut, Arsip Tahun Ajaran, pengawas portal (Sekolah Binaan, PKKS). Feature-status rows (Add or Keep → upgrade) for every item in §2. Console list (D-314…D-320). Offline scope (D-289) |
| `docs/final/export-localization.md` | XP-6 amended (D-280). XP-39 cover, XP-40 margin profiles and duplex, XP-41 auto landscape, XP-42 NIHIL, XP-43 Berkas, XP-44 answer sheet. Prints: Promes Realisasi, Daya Serap, locked leger, kehadiran recap, piket, berita acara tabungan, cash report. File names (XP-32) |
| `docs/final/architecture.md` | `app/Progress/`, `app/Calendar/` (EffectiveDays, ConflictChecker), `RpmReadiness`. Jobs: Monday 00.30 snapshot, Monday 06.00 Kabar, Berkas and school ZIP, prune skipping approved revisions. On-device OMR with an offline queue, Di Kelas read-only RPM cache, version banner and service-worker update |
| `docs/final/ai-provider-engine.md` | Model per purpose from the database, config as the default (D-315); test key owned by Tim SIGRAK |
| `docs/final/design-system.md` | Terms (Berkas Administrasi, pengesahan dokumen, Kehadiran, Kelompok piket, Sumbangan sukarela, Sisa dari kesepakatan, Pengingat). Banned words: tunggakan, kurang bayar, belum lunas, teguran, tanda tangan elektronik. Status labels with text and colour. Sound rules |
| `docs/final/ifp-classroom.md` | Lanjutan block in Game kelas; piket on the idle screen (D-298); Kartu Jawab reuses the LJK code (D-286) |
| `docs/drafts/teacher-supervision-instruments.md` | Principal supervision now in scope (D-308); S-01/S-15 suggested scores from completeness (D-312) and the Berkas summary sheet; findings bank (D-313) |
| `docs/drafts/administration-features.md` | K3 covers the principal; H2 thresholds (D-303); T1 detail (D-301); inventory room view (D-306) |
| `docs/pdf/` | Indonesian PDFs of every changed final doc (D-156) |

## 5. Tasks

| Task | Content | Depends on |
|---|---|---|
| P4-14 | Apply §4 to `docs/final/` and the drafts, and rebuild the PDFs | P4-13 |
| P4-15 | Catalog import to `docs/data/catalogs/` and Tim Validasi review (D-305) | — |
| P5-03i | Calendar: D-253…D-261, D-323. First step: the online SKB check (D-254) and the Jawa Tengah 6-day calendar from `docs/sources/Pedoman_Kalender_Pendidikan_Prov_Jawa_Tengah_2026_2027.pdf` | P5-03e |
| P5-03j | Draft timetable (D-262) | P5-03i |
| P5-04 (added) | D-267 (enums), D-269 (calculation), D-270, D-272…D-277, D-283…D-285; console parts D-315, D-316 | P5-03i |
| P5-05 (added) | D-293, D-294 (index and reuse), D-296 | P5-04 |
| P5-07 (added) | D-263…D-271 (teacher side), D-278, D-280, D-289 (RPM cache), D-297…D-303, D-321 | P5-04 |
| P5-08 (added) | D-286…D-292, D-294 (stats write-back), D-295, D-301 (kenaikan and graduation), D-304 (kenaikan) | P5-05 |
| P5-09 (added) | D-263/D-264 portal views, D-268 recap, D-279, D-281, D-282, D-304 (mutasi), D-306…D-313 | P5-03 |
| Console (with P5-04/P5-10) | D-314, D-317…D-320 | P5-03 |
| P4-08 (design input) | D-308…D-313 | — |
| Later | D-288 (TKA paper backup) | P5-08 |

## Open questions

- **Tim Validasi, before importing the Jawa Tengah calendar (D-323):**
  - The guide gives the end of the 6-day semester-1 break as 1 January 2027 in Pasal 23 and as 2 January in Lampiran I. The Lampiran XIII count (24 effective days in January) matches 1 January, so 2 January is a school day.
  - For 2027 the guide lists estimated holidays only. The legacy app file has cuti bersama dates that the guide lacks (5 Feb, 12 and 15 Mar, 25 Mar, 18–19 May). The guide is the initial data; an admin updates cuti bersama in the console once the 2027 SKB is out (D-253, D-254).
- **Pilot schools:** the binder order the Kudus region expects (D-278, Format Kudus; its component list now comes from D-327).
- **Settle in the build:**
  - the OMR library (a one-day spike on a low-end Android phone, D-287);
  - the maximum number of Menjodohkan pairs per item on the sheet (P4-06);
  - where Tim SIGRAK's test keys are stored (env or console, D-315, D-326);
  - how held kokurikuler JP is recorded (kokurikuler module design, D-277).
- **Owner, before a teacher-facing purpose uses a Tim SIGRAK key from another provider:** who pays for it (D-326).
- **Tim SIGRAK, console plan mapping (D-158, D-211):** whether the leger lock and weights sit in core Asesmen or the Rapor add-on, and whether sumbangan and savings sit in the Keuangan add-on.
