# P5-03 — Slice 1: Foundation

- **Phase:** P5
- **Status:** in-progress
- **Owner:** pw/lucid-fermi-pxp4d5
- **Depends on:** P5-01
- **Can run in parallel with:** P5-02

## Goal

School users can sign in, schools exist as tenants with roles, the operator can set up the academic year, classes, teaching assignments, and timetable, and the curriculum is in the database. This is the base every later slice builds on.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/final/data-model.md` (Conventions, Tenancy and access, Accounts and schools, School year, Curriculum, Calendar/timetable rows for `timetable_slots`)
- `docs/final/architecture.md` (Module boundaries, Security boundaries)
- `docs/final/coding-standards.md` (sections 1–7, 9)
- `docs/final/product-concept.md` (Roles, Navigation, Operator flow)
- `docs/final/religious-education.md` (subject sets for SD and MI)

## Split

Each sub-task is one PR. Arrows are dependencies.

| ID | Sub-task | Depends on | Output | Status |
|---|---|---|---|---|
| P5-03a | Tenancy core: `regions`, `schools`, `users.nip`, `school_memberships`, `supervisor_assignments`, `staff_users`; `BelongsToSchool` scope (fail-closed), current-school context; staff console resources for regions, schools, and staff users | — | `app/Tenancy`, migrations, Filament resources, tenancy tests | done |
| P5-03b | Auth and invitations: sign-in (`/masuk`), sign-out, password reset (`/lupa-kata-sandi`), `invitations` and acceptance (`/undangan/{invitation}`), throttling, Akun (profile, password), role switch. Staff invite a school's first operator or principal from the console; the operator invites teachers | a | Controllers, Svelte pages, mail, tests | done |
| P5-03c | Curriculum tables and `curriculum:import`: `curriculum_versions`, `subjects`, `subject_allocations`, `topics`, `focus_items`, `learning_objectives`, `focus_item_objectives`, `region_subjects`, `school_subjects`. Imports the files in `docs/data/curriculum/` and, once supplied, KM-SD-2026-R2 | — (parallel with a) | `app/Curriculum`, migrations, import command, tests | review |
| P5-03d | Academic year, classes, teaching assignments: operator pages Tahun Ajaran and Kelas; default teaching assignments from `subjects.default_teacher` (D-032), reassignment by the operator | a, b, c | Operator portal pages, policies, IDOR tests | todo |
| P5-03e | Timetable: `timetable_slots`, operator page Jadwal per class, teacher view Kelas Saya > Jadwal | d | Pages, policies, tests | todo |
| P5-03f | School directory: `school_directory_entries` from the Kemendikdasmen reference site (Kudus, Jepara, Pati); NPSN fills the console's school form; Sebaran sekolah list, map, and Excel for marketing (D-169, D-170) | a | `scripts/school-directory/`, `docs/data/schools/`, import command, tests | review |

Not in this slice: students and enrollments, school calendar (Kaldik), school transfer requests, the 3-step onboarding, and the supervisor portal. They come with the slices that use them.

## Done when

- [ ] Every table above has its migration with the keys, FKs, and CHECKs from the data model.
- [ ] Each school-owned resource has an IDOR test (school A cannot read school B).
- [ ] Only sign-in, invitation acceptance, and password reset work without a session.
- [x] `curriculum:import` is idempotent by `code` and reports mismatches.
- [ ] All UI text in `lang/id`; CI green; each sub-task merged and its branch deleted.

## Blockers

- KM-SD-2026-R2 source files (`data_kurikulum_dengan_cp.json`, `struktur_alokasi_sd.json`) are not in the repository. P5-03c imports everything else, and its readers for these two files are ready (built from the EDUNEXA mapping doc, tested on fixtures). When the owner supplies them: put them in `docs/data/curriculum/`, run `php artisan curriculum:import`, and fix the reader or the subject codes in `subjects.json` where the report says so.

## Result

**P5-03a** (done, PR #52):
- Tables `regions`, `schools`, `school_memberships` (one active principal per school through a virtual generated column with a unique index), `supervisor_assignments`, `staff_users`, and `users.nip`. Fixed sets have `CHECK` constraints built from the enums in `app/Enums`.
- `app/Tenancy`: `SchoolContext` (a scoped singleton, so it is empty for every request and job, with `runFor()` for jobs), `SchoolScope` (no school in the context means no rows), `BelongsToSchool` (scope, fills `school_id` on create, refuses another school, `withoutSchoolScope()`), and `Actions/ResolveSchoolContext`. The `SetSchoolContext` middleware on `app.` picks the membership kept in the session, or the user's oldest active one.
- Staff console: Wilayah (`/wilayah`), Sekolah (`/sekolah`), Akun staf (`/staf`), gated by staff role (D-167). `php artisan staff:create <email>` makes the first admin.
- Left for P5-03b: the role-switch control (it writes the session key that `SetSchoolContext` reads), managing memberships and supervisor assignments in the console, and invitations.

**P5-03b** (done, PR #55):
- Routes in `routes/app.php`: `/masuk`, `POST /keluar`, `/lupa-kata-sandi`, `/kata-sandi-baru/{token}`, `/undangan/{invitation}?token=…`, `/akun` (profile, `/akun/kata-sandi`), `PUT /ganti-peran` (role switch), and `/guru` (operator: teachers, invite, `kirim-ulang`, cancel). Only sign-in, reset, and invitation acceptance are open; `tests/Feature/Auth/SessionRequiredTest.php` fails if another route is left open.
- `invitations` table plus `school_memberships.public_id` (D-182). `app/Tenancy/Actions`: `InviteMember` (also renews and resends), `FindInvitation` (token lookup), and `AcceptInvitation`. `UserMemberships` lists a user's own active roles. The mail is `App\Mail\InvitationMail` (queued). The reset mail uses our own Indonesian view (`resources/views/mail/`).
- `SetSchoolContext` now runs before route model binding (middleware priority in `bootstrap/app.php`), so a school-scoped `{model}` from another school is a 404.
- New `translations:<files>` route middleware (LOC-9/10, `ShareTranslations`). Shared props now include `auth` (user and memberships) and `flash.toast`. `lang/id/validation.php` is filled in.
- Staff console: Anggota sekolah (`/anggota-sekolah`), Undangan (`/undangan`), Penugasan pengawas (`/penugasan-pengawas`), for admin and support.
- Password and throttle rules: D-183.
- Supervisor (pengawas/korwil) accounts are created with the supervisor portal in P5-09 (D-184). Until then the console assigns existing users only.
- Server: invitations and resets send mail through the queue and SMTP (D-110); the owner set `MAIL_*` on the server on 2026-11-07.

**P5-03f** (review): `school_directory_entries` from referensi.data.kemendikdasmen.go.id, fetched 2026-11-03: Kudus 570 (code `031900`), Jepara 772 (`032000`), Pati 825 (`031800`) SD/MI, in `docs/data/schools/`. Refresh one regency with `node scripts/school-directory/fetch.cjs <code> docs/data/schools/<name>.json`, then run `php artisan school-directory:import docs/data/schools/<name>.json` on each server; another regency uses its own code from the site's URL. Typing a known NPSN in Sekolah > Buat fills the empty fields. Sebaran sekolah (`/sebaran-sekolah`) shows customers and prospects per district, with a summary-per-group switch and an Excel download of the filtered rows; Peta sekolah (`/peta-sekolah`) shows them on a map; the Sekolah list shows which schools match the directory (D-170). Coordinates come from each school's detail page: `node scripts/school-directory/fetch.cjs --coordinates <file>` (one request per school, resumable).

**P5-03c** (review, branch `p5/p5-03c-curriculum`):
- Tables: `curriculum_versions`, `subjects`, `subject_allocations`, `topics`, `focus_items`, `learning_objectives`, `focus_item_objectives`, `region_subjects`, `school_subjects` (school-owned, `BelongsToSchool`; every-grade rows made unique through a generated `grade_key`), plus `learning_outcomes` for the CP-only versions (D-226). Fixed sets have `CHECK`s from the new enums in `app/Enums`; grade, semester, week, and allocation ranges are `CHECK`s too.
- `php artisan curriculum:import [files…] [--strict]` (`app/Curriculum/Import`). With no file it loads `config/curriculum.php` in order: `subjects.json` (subject catalogue and Kudus' Bahasa Jawa default, D-227, D-228), both CP files, the SD allocation from Permendikdasmen 13/2025 (Tabel 1–5 only; intrakurikuler and kokurikuler rows, the six religions from its one Pendidikan Agama row, no Koding per D-208, muatan lokal cap in `meta`), the MI allocation (KMA 1503/2025), and the two religion TP/ATP drafts with their fokus–TP links. Result on the repository data: 6 versions (all `draft`), 21 subjects, 199 CP rows, 228 allocations, 524 topics, 1,323 TP, 2,048 fokus, and no mismatches. A second run changes nothing. Rules: D-229.
- KM-SD-2026-R2: the readers for `data_kurikulum_dengan_cp.json` and `struktur_alokasi_sd.json` exist (`EdunexaTopics`, `EdunexaAllocation`) and are tested on fixtures, including the Matematika K4 S2 80/90 mismatch; the real files are still missing (see Blockers). `kokurikuler_default` (Bahasa Inggris grades 1–2) is not imported (D-204, D-229).
- `App\Curriculum\SubjectsForGrade` gives the subjects a class of a grade takes at a school: structure, then region local content, then the school's own switches (D-230). It runs only for the school in the context. P5-03d uses it for default teaching assignments.
- Left for later slices: the other sections of the regulation files (rules, SDLB/MILB tables, ekstrakurikuler, kokurikuler guide, KSP, assessment), the SD muatan lokal cap check per school, console pages for curriculum data and for `school_subjects`/`region_subjects`, `school_topic_changes` and `curriculum_validations` (P5-09). `docs/final/data-model.md` and its PDF do not list `learning_outcomes` yet (add with the next data-model update, e.g. P4-05).
