# Data Model

- **Status:** final (approved 2026-10-31, D-123)
- **Task:** P1-05
- **Last updated:** 2026-11-01
- **Sources:** [`Catatan_Pengembang_Aplikasi_Lama_Design_System_Data_dan_AI.md`](../sources/Catatan_Pengembang_Aplikasi_Lama_Design_System_Data_dan_AI.md), [`Pemetaan_Data_Materi_Aplikasi_Lama_untuk_Refactor_RPM_Generator.md`](../sources/Pemetaan_Data_Materi_Aplikasi_Lama_untuk_Refactor_RPM_Generator.md), [`Dokumentasi-Fungsionalitas-Aplikasi-Lama.pdf`](../sources/Dokumentasi-Fungsionalitas-Aplikasi-Lama.pdf)

## Purpose

This is the relational model for SIGRAK on MySQL 8 (D-007), revised from the legacy app developer notes to fit the new concept: students are records, content comes from templates, and teachers own several AI keys. It sets entity boundaries, keys, invariants, and ownership. It is **not** a migration spec: each table gets its own schema review when its migration is written.

## Conventions

| Topic | Rule |
|---|---|
| Names | English, plural snake_case tables, singular StudlyCase models (repo convention) |
| Keys | `bigint` auto-increment primary keys for internal joins. Rows exposed in URLs (documents, generations, invitations, students) also get a `public_id` UUIDv7 (Laravel `HasUuids`), so counts are not leaked and ids cannot be enumerated |
| Foreign keys | Always declared, always indexed, with an explicit `ON DELETE` choice |
| Nullability | `NOT NULL` by default. `NULL` only where absence has a distinct meaning, stated in this doc |
| Fixed sets | `varchar` + `CHECK (col IN (…))` (MySQL ≥ 8.0.16 enforces CHECK), mirrored by a PHP backed enum |
| Time | `timestamp` in WIB (`Asia/Jakarta`, D-165): the app and its MySQL session both use `+07:00`. Dates without time use `date` |
| Money | Integer rupiah (`bigint`), no floats |
| Deletion | No soft deletes. Hard delete after a confirmation that names the item. Lifecycle is modelled as an explicit `status` where a row stays visible but inactive |
| JSON | Only for content whose shape a template's JSON Schema defines (`documents.content`, `generations.specification`, step outputs) and for imported reference blobs. Never as a home for queryable fields |

## Tenancy and access

- Every school-owned table has `school_id NOT NULL`, indexed, and composite indexes start with it.
- MySQL has no row-level security, so the scope is enforced in the app in a **fail-closed** way:
  - A `BelongsToSchool` trait adds a global scope from the current membership by default. Bypassing it is explicit (`withoutSchoolScope()`) and allowed only in the internal console and in jobs that name the school.
  - Every controller action is authorized through policies.
  - Feature tests assert that school A's user can never read school B's rows (an IDOR/BOLA test per resource).
- Supervisors read across their assigned schools through `supervisor_assignments` with read-only policies. They see **no student-level rows**.
- Student data (records, attendance, notes, scores) is readable only by the class's teachers, the operator, and the principal (rule from the developer notes). Counselling (BK) notes are visible only to the author, the homeroom teacher, and the principal.

## Accounts and schools

| Table | Key columns | Notes and invariants |
|---|---|---|
| `regions` | `province`, `regency` | Unique (`province`, `regency`). Holds the defaults for local content and the calendar (D-018) |
| `schools` | `name`, `npsn` (unique, nullable until verified), `level` ∈ {`sd`,`mi`}, `region_id` FK RESTRICT, address | One school, one tenant. `level` decides the subject set and allocation table (SD: six religion subjects; MI: Kemenag subjects; see [`religious-education.md`](religious-education.md)) |
| `users` | `name`, `email` (unique), `password`, `nip` nullable (non-civil-servant teachers have none) | School users only: teachers, principals, operators, supervisors. **No role column.** Roles come from memberships. **No student users** (D-010) |
| `school_memberships` | `school_id` FK CASCADE, `user_id` FK CASCADE, `role` ∈ {`principal`,`operator`,`teacher`}, `status` ∈ {`active`,`ended`}, `started_on`, `ended_on` nullable | Unique (`school_id`, `user_id`, `role`). One user can hold several roles. One active principal per school (checked in the service and by a unique index on a generated column `active_principal_school_id`) |
| `supervisor_assignments` | `user_id` FK, `school_id` FK, `started_on`, `ended_on` nullable | Read-only scope for pengawas/korwil |
| `invitations` | `public_id`, `school_id`, `email`, `role`, `token_hash` (unique), `expires_at`, `accepted_at` nullable, `invited_by` FK users | The only way into a school. No shared default passwords |
| `staff_users` | `name`, `email`, `password`, `role` ∈ {`admin`,`support`,`content`} | Internal Tim SIGRAK accounts for the Filament console, on a **separate guard and table**, so a school account can never become staff |
| `school_transfer_requests` | `user_id`, `from_school_id`, `to_school_id`, `status`, `decided_by` nullable | "Pindah Sekolah", approved by the target school |

## School year, classes, students

| Table | Key columns | Notes and invariants |
|---|---|---|
| `academic_years` | `school_id`, `name` (e.g. `2026/2027`), `starts_on`, `ends_on`, `even_semester_starts_on` | CHECK `starts_on < even_semester_starts_on < ends_on`. Default start 1 July |
| `classes` | `school_id`, `academic_year_id` FK, `grade` 1–6 (CHECK), `name` (e.g. `4A`), `homeroom_teacher_id` FK users nullable (not assigned yet) | Unique (`academic_year_id`, `name`) |
| `teaching_assignments` | `school_id`, `class_id` FK CASCADE, `subject_id` FK, `teacher_id` FK users | Unique (`class_id`, `subject_id`). When a class is created, subjects with `default_teacher = class_teacher` go to the homeroom teacher, and the rest wait for the operator to pick a guru mapel. The operator can reassign any row (D-032) |
| `students` | `public_id`, `school_id`, `name`, `nisn` nullable (not every student has one), `gender` ∈ {`l`,`p`}, `birth_date` nullable, `religion` ∈ {`islam`,`kristen`,`katolik`,`hindu`,`buddha`,`khonghucu`} nullable (not recorded yet) | **A record, never an account.** `religion` is sensitive (D-036): used only to group religion classes, never sent to AI, hidden from supervisors |
| `enrollments` | `student_id` FK CASCADE, `class_id` FK RESTRICT, `status` ∈ {`active`,`moved`,`left`,`graduated`}, `ended_on` nullable | One active enrollment per student per academic year. Covers transfers ("Pindah atau keluar") and promotion ("Kenaikan") |

## Curriculum

Curriculum is split into **global, versioned defaults** and **per-school changes**, so a school can use the defaults, adjust them, or add its own without breaking shared data.

| Table | Key columns | Source field (SIGRAK curriculum files, D-363) |
|---|---|---|
| `curriculum_versions` | `code` (unique, e.g. `SIGRAK-SD-2026-R1`), `status` ∈ {`draft`,`active`,`retired`}, `meta` JSON (per source file: sha256, source, and each subject's `curation`, D-364) | `version_code`, `status` (a new version always starts as `draft`, D-113) |
| `subjects` | `code` (e.g. `PP`), `name`, `kind` ∈ {`compulsory`,`local_content`,`cocurricular`}, `default_teacher` ∈ {`class_teacher`,`subject_teacher`}, `religion` nullable (NULL = not a religion subject), `school_level` ∈ {`sd`,`mi`,`both`} | `subjects.json`. `subject_teacher` for PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, and Pendidikan Agama (D-032). SD has one religion subject per religion; MI has Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, and Bahasa Arab (D-034, D-035) |
| `subject_allocations` | `curriculum_version_id`, `subject_id`, `grade`, `phase` ∈ {A,B,C}, `learning_type` ∈ {`intracurricular`,`cocurricular`}, `jp_per_week`, `weeks_per_year`, `s1_jp`, `s1_effective_weeks`, `s2_jp`, `s2_effective_weeks` | The structure versions: Permendikdasmen 13/2025 for SD, KMA 1503/2025 for MI (D-366) |
| `topics` (materi pokok / ATP) | `curriculum_version_id`, `code` (unique, e.g. `SIGRAK-SD-2026-R1-PP-K1-01`), `subject_id`, `grade`, `phase`, `semester` ∈ {1,2}, `elements`, `title`, `cp_text`, `atp_code`, `order_in_semester`, `jp_total`, `jp_per_week`, `week_start`, `week_end`, `weekly_distribution` JSON, `references` JSON, `curation_notes` JSON nullable, `extras` JSON nullable (sub-element, art branch, science process skills, subject status, learning type, ATP details) | `subjects[].topics[]` of a SIGRAK file. Old codes are only in the team crosswalk, never in this table (D-365) |
| `focus_items` (fokus materi) | `topic_id` FK CASCADE, `code` (unique, `<topic>-FM01`), `order`, `title`, `estimated_jp`, `allocation_status` | `topics[].focus_items[]` |
| `learning_objectives` (TP) | `topic_id` FK CASCADE, `code` (unique, `<topic>-TP01`), `order`, `text` | `topics[].learning_objectives[]` |
| `focus_item_objectives` | `focus_item_id`, `learning_objective_id` | `focus_items[].objective_codes` where the file has them; otherwise filled by a data task (finding 3 in the mapping doc). Needed for per-TP analysis at fokus level |
| `region_subjects` | `region_id`, `subject_id`, `allocation` | Default local content per region, e.g. Bahasa Jawa for Kudus |
| `school_subjects` | `school_id`, `subject_id`, `grade` nullable (NULL = every grade), `enabled`, `in_report` bool | A school turns local content and optional content on or off. Optional content (Bahasa Inggris and IPAS in grades 1–2, D-204) is off by default, and `in_report` says whether the school also grades it in the rapor |
| `school_topic_changes` | `school_id`, `base_topic_id` FK nullable (**NULL = new topic added by the school**), `payload` JSON (same shape as a topic), `status` ∈ {`draft`,`submitted`,`returned`,`principal_approved`,`approved`}, `submitted_by` | Validation flow guru → kepala sekolah → pengawas (D-019) |
| `curriculum_validations` | `school_topic_change_id`, `reviewer_id`, `level` ∈ {`principal`,`supervisor`}, `decision` ∈ {`approved`,`returned`}, `note` | Append-only. Every return carries a note for the teacher |

**Curriculum versions in v1:**

| Code | Content | Source data |
|---|---|---|
| `SIGRAK-SD-2026-R1` | SIGRAK curriculum for SD: topics, TP, ATP, fokus, JP for the general subjects, PAI, muatan lokal, and KKA (kokurikuler content), and for the five other religions | [`sigrak-sd-2026-r1-umum.json`](../data/curriculum/sigrak-sd-2026-r1-umum.json), [`sigrak-sd-2026-r1-agama.json`](../data/curriculum/sigrak-sd-2026-r1-agama.json) (D-363; curated per subject, D-364) |
| `SIGRAK-MI-2026-R1` | SIGRAK curriculum for MI: topics, TP, ATP, fokus, JP for Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, and Bahasa Arab | [`sigrak-mi-2026-r1.json`](../data/curriculum/sigrak-mi-2026-r1.json) (D-363, D-364) |
| `CP-PABP-BKPDM-020-2026` | CP for the six religion subjects, SD Fase A–C | [`docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json`](../data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json) (transcribed, verified against the scan in P2-01) |
| `CP-PAI-BA-MADRASAH-9941-2025` | CP for MI's Kemenag subjects, Fase A–C | [`docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json`](../data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json) (structured into element rows in P2-02, D-039) |
| `STRUKTUR-SD-PERMENDIKDASMEN-13-2025` | SD allocation: intrakurikuler and kokurikuler JP per subject and grade (36 kokurikuler JP per compulsory subject per year, 32 in grade 6, none for Bahasa Inggris), muatan lokal cap | [`docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json`](../data/regulations/curriculum-structure-permendikdasmen-13-2025.json) (Lampiran II B, Tabel 1–5; draft until reviewed, D-113). Fills the SD rows of `subject_allocations`, intrakurikuler and kokurikuler; the only SD allocation source (D-366) |
| `ALOKASI-MI-KMA-1503-2025` | MI allocation: JP per subject and grade (intrakurikuler, kokurikuler), required vs elective, muatan lokal range | [`docs/data/curriculum/mi-allocation-kma-1503-2025.json`](../data/curriculum/mi-allocation-kma-1503-2025.json) (verified against KMA 1503/2025 Lampiran II, Tabel 1–5, pp. 28–31) |

Official CP versions and structure versions stay separate from the SIGRAK topic versions; topics use one version per level and year (D-363, superseding "one version per CP set" of D-139/D-145 for topics). Each subject carries `curation.status` ∈ {`converted`, `in_review`, `curated`}; only `curated` subjects are active for teachers (D-364, enforced from P5-04). CP-only versions are already enough for the RPM wizard to show CP and elements.

**Import.** `php artisan curriculum:import [file…]` loads the files of `config/curriculum.php` in order: subjects, CP files, the SD structure (13/2025), the MI allocation, then the three SIGRAK topic files. It reads only the SIGRAK format for topics and refuses the legacy app format with a clear error. It is idempotent by `code`, checks topic JP against each subject's semester targets (`subjects[].allocations`), and reports data mismatches. Files that share a version (SD general subjects and SD religions) each answer only for their own subjects. Old codes stay in [`crosswalk-legacy-2026.json`](../data/curriculum/crosswalk-legacy-2026.json), which is never imported (D-365). Only curriculum data was taken from the legacy app (D-020).

**Known data issues to carry over** (from the mapping doc, still open):

| Issue | Action |
|---|---|
| Matematika grade 4 semester 2 = 80 JP, target 90 | Closed: the SIGRAK R1 data adds up to 90 |
| Keterampilan Tata Boga (grades 3–6) has no allocation target | School-optional muatan lokal (D-204). The SD muatan lokal cap of 72 JP per year (64 in grade 6, Permendikdasmen 13/2025) is checked per school across Bahasa Jawa, Tata Boga, and any other local subject |
| Bahasa Inggris grades 1–2 is not in the national structure (it starts in grade 3) | Optional content a school switches on, off by default (`school_subjects`). It is not a required rapor subject (D-204) |
| IPAS grades 1–2 is not in the national structure (it starts in grade 3) | Same as Bahasa Inggris grades 1–2 (D-204). SIGRAK-SD-2026-R1 has no topics for it yet, so they have to be written |
| Koding dan KA (grades 5–6) was an intrakurikuler subject in the legacy data | Not imported as an intrakurikuler allocation. Its topics become kokurikuler content (D-208) |
| TP linked only to topic, not to fokus | `focus_item_objectives` + data task |
| Curriculum existed both in static JSON and in Firestore | The SQL tables are the single source; versions change only through `curriculum_versions` |

## Calendar, timetable, meetings

| Table | Key columns | Notes and invariants |
|---|---|---|
| `calendars` | `region_id` nullable / `school_id` nullable, `academic_year` | Exactly one owner: CHECK (`region_id` IS NULL) <> (`school_id` IS NULL). Region calendars are defaults that a school calendar overrides |
| `calendar_events` | `calendar_id` FK CASCADE, `starts_on`, `ends_on`, `kind` ∈ {`holiday`,`exam`,`school_event`,`no_lessons`}, `title` | CHECK `starts_on <= ends_on` |
| `timetable_slots` | `school_id`, `class_id`, `weekday` 1–6, `period_no`, `starts_at`, `ends_at`, `subject_id`, `teacher_id`, `jp` | Unique (`class_id`, `weekday`, `period_no`). 1 JP = 35 minutes for SD |
| `meetings` | `school_id`, `document_id` FK CASCADE (the RPM), `number`, `focus_item_id`, `jp`, `minutes`, `planned_on` nullable (NULL for "RPM lepas jadwal"), `status` ∈ {`planned`,`done`,`not_done`} | Unique (`document_id`, `number`). A `not_done` meeting shifts later meetings to the next slot (legacy app scheduler behaviour). The last 3 schedule versions are kept in `meeting_schedule_history` |

## Content

| Table | Key columns | Notes and invariants |
|---|---|---|
| `templates` | `key` (unique), `category` ∈ {`document`,`game`}, `is_active`, `sort_order`, `current_version` | Registry only. The definition lives in code (D-013). One row per content type: `rpm`, `bank_soal`, `lkpd`, `quiz_battle`, `tka_package` (no generic template, D-024) |
| `template_versions` | `template_key` FK, `version`, `code_ref` (git tag/sha), `prompt_override` text nullable (NULL = code baseline), `created_by_staff_id` nullable, `first_used_at` nullable | Unique (`template_key`, `version`). **Immutable once `first_used_at` is set** (enforced in `TemplateVersionManager` and the Filament form) |
| `generations` | `public_id`, `school_id`, `user_id`, `template_key`, `template_version`, `kind` ∈ {`create`,`section_revision`}, `document_id` nullable (set for revisions, or once saved), `client_token`, `specification` JSON, `status` ∈ {`queued`,`running`,`succeeded`,`failed`,`cancelled`}, `pinned_credential_id` FK SET NULL, `error_class` nullable, `started_at`, `finished_at` | Unique (`user_id`, `client_token`) blocks double submits. FK (`template_key`, `template_version`) → `template_versions` |
| `generation_steps` | `generation_id` FK CASCADE, `step_key`, `position`, `status` ∈ {`pending`,`running`,`succeeded`,`failed`}, `attempts`, `output` JSON nullable (NULL until accepted) | Unique (`generation_id`, `step_key`). The output is written once, by compare-and-set on `status` |
| `documents` | `public_id`, `school_id`, `owner_id` FK users, `template_key`, `template_version`, `title`, `grade`, `subject_id` nullable, `topic_id` nullable (TKA packages span many topics), `content` JSON, `status` ∈ {`draft`,`final`}, `source_generation_id` FK SET NULL | "Dokumen Saya". `content` is validated against the template's output schema plus merged data fields |
| `document_revisions` | `document_id` FK CASCADE, `content` JSON, `reason` ∈ {`manual_save`,`ai_section_revision`}, `created_by` | Last 5 kept per document, older ones pruned (D-153) |
| `document_comments` | `document_id` FK CASCADE, `author_id`, `body`, `created_at` | Principal and supervisor comments (D-019) |
| `document_exports` | `document_id` FK CASCADE, `format` ∈ {`pdf`,`docx`,`xlsx`,`html`}, `template_version`, `content_hash`, `path`, `status` | Cache. Rebuilt when `content_hash` or print settings change |
| `template_feature_requests` | `user_id`, `template_key`, `body`, `status` ∈ {`new`,`planned`,`declined`,`done`} | "Usulkan fitur" (D-015) |

Bank Soal items stay inside the document's `content` (one source for rendering). When an assessment uses a Bank Soal document, its items are **copied** into `assessment_items`, a frozen snapshot with `kode_tp` for scoring and per-TP analysis.

## AI

| Table | Key columns | Notes and invariants |
|---|---|---|
| `ai_credentials` | `user_id` FK CASCADE, `label`, `project_label`, `account_label` nullable, `secret` (encrypted cast), `secret_last4`, `secret_fingerprint`, `status` ∈ {`active`,`invalid`,`disabled`}, `is_backup` bool default false (D-119), `invalid_reason` nullable, `consecutive_failures`, `circuit_open_until` nullable, `last_success_at` nullable, `last_tested_at`, `multi_key_consent_at` nullable (NULL for the first key) | Unique (`user_id`, `secret_fingerprint`). Owner-only use. Max per user in config. The second and later keys require consent (D-023). **No `school_id`**: keys belong to the teacher (D-012) |
| `ai_credential_cooldowns` | `ai_credential_id` FK CASCADE, `model`, `until`, `reason` ∈ {`rate_minute`,`rate_daily`} | Unique (`ai_credential_id`, `model`). Mirror of the Redis cooldowns for the UI |
| `ai_usage_logs` | `user_id`, `school_id` nullable (NULL for a supervisor with no school), `ai_credential_id` FK SET NULL, `generation_id` FK SET NULL, `step_key`, `attempt`, `purpose`, `model`, `outcome` (success or error class), `http_status` nullable, `input_tokens`, `output_tokens`, `latency_ms`, `created_at` | Append-only. Index (`user_id`, `created_at`), (`school_id`, `created_at`). No key, no prompt text |

## Administration (kept from the legacy app, modelled relationally)

| Table | Key columns | Notes and invariants |
|---|---|---|
| `journals` | `school_id`, `class_id`, `subject_id`, `teacher_id`, `meeting_id` nullable (journal without an RPM), `date`, `status` ∈ {`done`,`not_done`}, `notes`, `client_uuid` | Unique `client_uuid` (idempotent offline sync) |
| `attendance_records` | `school_id`, `class_id`, `student_id`, `date`, `status` ∈ {`present`,`sick`,`permitted`,`absent`}, `recorded_by`, `recorded_at_device`, `client_uuid` | **Daily** attendance (D-029): unique (`student_id`, `date`) and unique `client_uuid`. Recorded by the homeroom teacher. Per-lesson attendance is *Later* |
| `student_notes` | `school_id`, `student_id`, `class_id`, `author_id`, `kind` ∈ {`anecdotal`,`counselling`,`achievement`}, `body`, `client_uuid` | Counselling visibility is restricted (see Tenancy) |
| `assessment_plans` | `school_id`, `class_id`, `subject_id`, `semester`, components | "Rencana Penilaian" |
| `assessments` | `school_id`, `class_id`, `subject_id`, `kind` ∈ {`formative`,`summative`,`sts`,`sas`}, `held_on`, `kktp_approach` ∈ {`description`,`rubric`,`interval`,`percentage`}, `kktp` (criteria text; a threshold, when used, is set per school), `source_document_id` FK SET NULL | Formative scores never become rapor grades. They feed the per-TP analysis and the description drafts (D-205) |
| `assessment_items` | `assessment_id` FK CASCADE, `number`, `type`, `kode_tp`, `cognitive_level`, `max_score`, `answer_key` nullable (essays have none) | Frozen snapshot of Bank Soal items |
| `scores` | `assessment_item_id` FK CASCADE, `student_id`, `score` decimal(5,2) | Unique (`assessment_item_id`, `student_id`). CHECK 0 ≤ score ≤ max (in the service; max lives on the item) |
| `report_descriptions` | `school_id`, `student_id`, `subject_id`, `semester`, `draft`, `final` nullable (not approved yet) | AI draft from the highest and lowest TP achievement in the formative results (D-205); the teacher edits before use |
| `report_cards` | `school_id`, `student_id`, `class_id`, `semester`, `cocurricular_draft`, `cocurricular_final` nullable, `homeroom_note`, `numeric_grades` bool | One per student per semester. Kokurikuler description: the dimensions strengthened and those still developing. `numeric_grades = false` gives a description-only rapor, a school choice for grades 1–2. The rapor also prints attendance totals from `attendance_records` and a blank parent-response box (Panduan Pembelajaran dan Asesmen 2025, pp. 70–73) |
| `report_extracurriculars` | `report_card_id` FK CASCADE, `activity`, `description` | Ekstrakurikuler rows on the v1 rapor (D-205). Free text per student; fuller ekstrakurikuler records stay out of v1 (D-161) |
| `class_finance_entries` | `school_id`, `class_id`, `kind` ∈ {`income`,`expense`}, `amount` bigint rupiah CHECK > 0, `occurred_on`, `note`, `recorded_by` | Feature can be switched off per school |
| `class_inventory_items`, `duty_rosters` | Class-scoped | |
| `school_announcements` (Kabar), `reminders` (Pengingat), `supervisions`, `school_books` (guest, public relations, minutes), `school_programs`, `agendas` | `school_id` | Portal features |
| `school_print_settings` | `school_id` (unique), signers (names, NIP, titles), `paper` ∈ {`a4`,`f4`}, margins, `font` ∈ {`arial`,`times_new_roman`} (D-125) | Per-school print settings. The letterhead is in `letterheads` |
| `letterheads` | `school_id` nullable / `user_id` nullable, `image_path`, `width_px`, `height_px`, `uploaded_by` | Exactly one owner: CHECK (`school_id` IS NULL) <> (`user_id` IS NULL). Unique per owner. A teacher's own letterhead replaces the school's on that teacher's documents. Upload rules: PNG/JPG, ≥ 2,000 px wide, ratio 4:1 to 8:1, ≤ 2 MB (D-031) |
| `subscriptions` | `school_id` nullable / `user_id` nullable / `supervisor_user_id` nullable, `plan` ∈ {`trial`,`game_trial`,`teacher`,`school`,`cluster`}, `academic_year`, `starts_on`, `ends_on`, `status` | Exactly one owner column set (CHECK). A `trial` lasts 7 days (D-160); afterwards an account without a paid plan can only view and download (D-157). A `game_trial` (7 days, once per teacher) opens Game kelas without the school add-on (D-159). Billing tables come in the billing task |
| `school_addons` | `school_id`, `addon` ∈ {`tka`,`buku_induk`,`class_finance`,`school_books`,`games`,`report_cards`}, `academic_year`, `status` | Unique (`school_id`, `addon`, `academic_year`). Needs an active `school` subscription (D-158) |

## TKA practice exam (CBT)

These tables back the separate exam site (D-026, D-027). Exam participants are students acting through **session-scoped credentials**. They are not rows in `users`.

| Table | Key columns | Notes and invariants |
|---|---|---|
| `exam_sessions` | `public_id`, `school_id`, `package_document_id` FK RESTRICT (a `tka_package` document), `subject` ∈ {`bahasa_indonesia`,`matematika`}, `title`, `opens_at`, `closes_at`, `duration_minutes` (default 75), `token_secret` (encrypted), `token_period_minutes` (default 15), `proctor_id` FK users, `status` ∈ {`draft`,`scheduled`,`open`,`closed`} | CHECK `opens_at < closes_at`, CHECK `duration_minutes` between 10 and 180. The token is computed from `token_secret` and the time window, so tokens are not stored |
| `exam_items` | `exam_session_id` FK CASCADE, `number`, `group_key` nullable (NULL = single item, set = shares a stimulus), `form` ∈ {`pg`,`pgk_mcma`,`pgk_category`}, `stimulus` JSON nullable, `content` JSON, `answer_key` JSON, `competency`, `max_points` | Frozen snapshot of the package when the session is scheduled. Editing the package later does not change a running exam |
| `exam_participants` | `public_id`, `exam_session_id` FK CASCADE, `student_id` FK RESTRICT, `username` (unique per session), `password` (encrypted, so cards can be reprinted), `item_order` JSON (shuffled per participant), `status` ∈ {`not_started`,`in_progress`,`submitted`,`expired`}, `started_at` nullable, `submitted_at` nullable, `extra_minutes` (default 0), `login_resets` | Unique (`exam_session_id`, `student_id`). Credentials stop working when the session is `closed`. Passwords are purged 30 days after closing |
| `exam_answers` | `exam_participant_id` FK CASCADE, `exam_item_id` FK CASCADE, `response` JSON, `is_flagged` (ragu-ragu), `client_seq`, `saved_at` | Unique (`exam_participant_id`, `exam_item_id`). Upsert only when `client_seq` is higher than the stored one, so retries and reconnects are idempotent |
| `exam_results` | `exam_participant_id` FK CASCADE (unique), `score` decimal(5,2) CHECK 0–100, `category` ∈ {`kurang`,`memadai`,`baik`,`istimewa`}, `per_competency` JSON, `scored_at` | Practice estimate, not an official TKA result |
| `exam_events` | `exam_session_id`, `exam_participant_id` nullable (NULL = session-level event), `actor_id` nullable (NULL = the participant), `kind` ∈ {`login`,`token_fail`,`reset_login`,`extend_time`,`submit`,`force_submit`}, `created_at` | Append-only proctor audit log |

Access: teachers and the operator of the school manage sessions; the proctor runs them. Principals see results. Supervisors see school-level aggregates only. The exam site's guard reads only `exam_participants` and the participant's own items and answers.

**Offline sync (Di Kelas, D-016).**

- The device creates rows with `client_uuid` and queues them in IndexedDB. The sync endpoint upserts by `client_uuid`, so a repeated send never duplicates.
- On conflict the last write wins per row, and the device time is kept in `recorded_at_device` next to the server timestamps.

## Open questions

- [x] e-Rapor export: deferred; report-card data uses SIGRAK's own Excel layout for now, and the owner designs a dedicated e-Rapor export later (D-144).
- [x] Retention: `document_revisions` keeps the last 5 per document (D-153); `ai_usage_logs` 1 month (D-118).
- [x] Supervisor `school_id` context: `supervisor_assignments` is enough (D-122, proposed).
