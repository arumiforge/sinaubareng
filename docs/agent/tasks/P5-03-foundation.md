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
| P5-03g | KM-SD-2026-R2 source files: download `data_kurikulum_dengan_cp.json` and `struktur_alokasi_sd.json` from the EDUNEXA portal into `docs/data/curriculum/`, record provenance, check them against the data model | — (unblocks the KM-SD-2026-R2 part of c) | `docs/data/curriculum/`, findings in Result | review |

Not in this slice: students and enrollments, school calendar (Kaldik), school transfer requests, the 3-step onboarding, and the supervisor portal. They come with the slices that use them.

## Done when

- [ ] Every table above has its migration with the keys, FKs, and CHECKs from the data model.
- [ ] Each school-owned resource has an IDOR test (school A cannot read school B).
- [ ] Only sign-in, invitation acceptance, and password reset work without a session.
- [x] `curriculum:import` is idempotent by `code` and reports mismatches.
- [ ] All UI text in `lang/id`; CI green; each sub-task merged and its branch deleted.

## Blockers

- ~~KM-SD-2026-R2 source files are not in the repository.~~ Resolved by P5-03g (2026-11-08): `data_kurikulum_dengan_cp.json` and `struktur_alokasi_sd.json` are in `docs/data/curriculum/`. Open owner question for the import: Seni Suara Daerah (new subject) and the muatan lokal cap, see the P5-03g Result.

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

**P5-03g** (review; needs the owner's call on C5/C6): the KM-SD-2026-R2 files from the EDUNEXA portal, downloaded 2026-11-08 and kept byte-for-byte, are in `docs/data/curriculum/`. Provenance (URL, size, SHA-256) in `docs/data/curriculum/README.md`; full findings in `docs/drafts/km-sd-2026-r2-source-check.md`. For P5-03c:
- 432 topics, 2,021 fokus, 1,372 TP, 12 subjects, 58 `alokasi[]` rows (the mapping doc had 401 / 1,900 / 1,272 / 11 / 52). Every source field in the data model exists; no duplicate topic, ATP, fokus, or TP codes; fokus JP and allocations all add up. Matematika grade 4 semester 2 is now 90 JP (the known issue is fixed in the source).
- JSON columns are real objects/arrays, not strings. No subject code field: the code is inside `ID_Kurikulum`, and Seni dan Budaya uses five codes (`MUS`, `SM`, `SMU`, `SR`, `SRP`) with `urutan_semester` restarting per `Cabang_Seni`. `Status_Mapel` is free text (10 values). `focus_items.allocation_status` source values are sentences.
- Legacy IDs: 73 rows have no `Legacy_ID`; 5 `Legacy_ID` values sit on 2–3 rows; 16 aliases map to several rows and 20 aliases equal another row's current `ID_Kurikulum` (grade 5 renumbering). Look up `topics.code` first, aliases second, and report ambiguous aliases.
- D-204: Bahasa Inggris grades 1–2 come as kokurikuler (`kokurikuler_default[]`), no IPAS grades 1–2: as expected. D-208: Koding grades 5–6 has two intrakurikuler `alokasi[]` rows the import must skip. Tata Boga: no allocation row, and grade 6 semester 2 has 34 JP in a 14-week semester (data fix). PAI is named `PAI dan Budi Pekerti` and maps to the Islam religion subject.
- **New: Seni Suara Daerah** (grades 1–6, 31 topics, 2 JP/week, muatan lokal for Jawa Tengah, CP written by the system and marked "PERLU VERIFIKASI"). It is in no decision. With Bahasa Jawa it doubles the 72 JP muatan lokal cap. Owner decision needed (C5, C6 in the draft); proposed data-model changes are in its section 6.

**P5-03c** (review, branch `p5/p5-03c-curriculum`):
- Tables: `curriculum_versions`, `subjects`, `subject_allocations`, `topics`, `focus_items`, `learning_objectives`, `focus_item_objectives`, `region_subjects`, `school_subjects` (school-owned, `BelongsToSchool`; every-grade rows made unique through a generated `grade_key`), plus `learning_outcomes` for the CP-only versions (D-226). Fixed sets have `CHECK`s from the new enums in `app/Enums`; grade, semester, week, and allocation ranges are `CHECK`s too. `focus_items.allocation_status` is a 255-character string (KM uses sentences); `curriculum_versions.meta` keeps one entry per source file.
- `php artisan curriculum:import [files…] [--strict]` (`app/Curriculum/Import`). With no file it loads `config/curriculum.php` in order: `subjects.json` (catalogue with KM-SD-2026-R2 codes and Kudus' Bahasa Jawa default, D-227, D-228), both CP files, the SD allocation from Permendikdasmen 13/2025 (Tabel 1–5 only; the six religions from its one Pendidikan Agama row, no Koding per D-208, muatan lokal cap in `meta`), the MI allocation (KMA 1503/2025), KM-SD-2026-R2 (`struktur_alokasi_sd.json`, then `data_kurikulum_dengan_cp.json`), and the two religion TP/ATP drafts with their fokus–TP links. Rules: D-229.
- Result on the repository data: 7 versions (all `draft`), 22 subjects, 199 CP rows, 284 allocations, 956 topics, 2,695 TP, 4,069 fokus. A second run changes nothing. One mismatch, as P5-03g found: `KM-SD-2026-R2-KTB-K6-04` (Tata Boga grade 6 semester 2) runs to week 17 of a 14-week semester (data fix). Notes list Koding (D-208), Bahasa Inggris grades 1–2 and `kokurikuler_default` (D-204), topics with no allocation to compare (Bahasa Inggris 1–2, Koding, Tata Boga), and ambiguous legacy codes (15 shared, 20 equal to another topic's code).
- `App\Curriculum\SubjectsForGrade` gives the subjects a class of a grade takes at a school: structure, then region local content, then the school's own switches (D-230). It runs only for the school in the context. P5-03d uses it for default teaching assignments.
- Waiting for the owner (from P5-03g): Seni Suara Daerah (C5, imported as school-optional muatan lokal for now) and the muatan lokal cap check (C6).
- Left for later slices: the other sections of the regulation files (rules, SDLB/MILB tables, ekstrakurikuler, kokurikuler guide, KSP, assessment), the SD muatan lokal cap check per school, comparing PAI's KM CP with BKPDM 020/2026, console pages for curriculum data and for `school_subjects`/`region_subjects`, `school_topic_changes` and `curriculum_validations` (P5-09). `docs/final/data-model.md` and its PDF do not list `learning_outcomes` yet, nor the P5-03g proposals (add with the next data-model update, e.g. P4-05).
