# Buku Induk and Dapodik Sync

- **Status:** draft, on hold: upcoming, not in v1 (D-161)
- **Task:** P4-01
- **Last updated:** 2026-11-01
- **Add-on:** Buku Induk and Dapodik sync (D-158), for schools with Sigrak Sekolah

## Purpose

Schools already keep their official data in Dapodik. This add-on lets the operator bring that data into SIGRAK without retyping it: they export Excel files from Dapodik and upload them, and SIGRAK recognizes the files, checks every row, shows what will change, and applies it after one confirmation. The same data fills the **Buku Induk** (the student master book) and the staff records (pangkat and gaji berkala).

The operator never maps columns or edits spreadsheets. If a file is not recognized or a row is wrong, SIGRAK says so in plain Indonesian.

Items marked *proposed* need owner approval and are listed under Decisions to record.

## What comes from where

| Data | Source | Notes |
|---|---|---|
| Students, parents and guardian, current rombel | Dapodik: Peserta Didik → Unduh → Daftar PD (Excel) | One file covers students and their class ("Rombel Saat Ini"), so no separate rombel file is needed |
| Staff (guru and tenaga kependidikan), pangkat/golongan | Dapodik: the PTK list export (Excel) | Includes staff who have no SIGRAK account, such as the school guard |
| Riwayat kepangkatan and gaji berkala (KGB) | Not yet known to be exportable by the operator | In current Dapodik versions, KGB history is entered through each PTK's own login. Until a Dapodik export is confirmed, SIGRAK accepts its own Excel template for this, with Indonesian headings (D-150) |
| Ekstrakurikuler and members | Not yet known | Same fallback: a SIGRAK template, or entry in SIGRAK |
| Buku Induk extras that Dapodik lacks (date of leaving, reason, notes) | Entered in SIGRAK | Never overwritten by an import |

The Dapodik 2026 column layout is not published. The column map is fixed from anonymized sample exports supplied by the owner (open question 1), and each recognized layout is stored with the Dapodik version it came from, so a later Dapodik change only adds a new map.

## Import flow

The operator sees four steps in *Data Sekolah → Sinkron Dapodik*:

1. **Unggah.** Upload one or more `.xlsx` files exported from Dapodik. Short help shows where each file is downloaded in Dapodik.
2. **Periksa.** SIGRAK processes the files in the background and shows a summary, for example "312 siswa: 5 baru, 18 berubah, 2 perlu dicek, 1 galat, 3 tidak ada di file Dapodik". Each group opens a list. Changed rows show old and new values side by side.
3. **Terapkan.** One button applies everything that has no error. Rows marked *perlu dicek* are applied only after the operator picks an answer for each.
4. **Selesai.** A short report, downloadable as Excel, and an entry in the import history.

Behind those steps:

| Stage | What SIGRAK does |
|---|---|
| Recognize | Reads the first rows of each sheet and finds the heading row by known heading text (Dapodik files start with title rows, and some headings span two rows, such as "Data Ayah" over "Nama"). Two-row headings are joined ("Data Ayah › Nama"). The file type and layout version come from the set of headings found. An unknown file stops here: "File ini belum dikenali. Pastikan file diunduh dari menu Unduh Daftar PD di Dapodik." |
| Normalize | Trims spaces; keeps NISN, NIK, NIPD, NIP, NUPTK as text so leading zeros survive; reads dates as Excel dates or `dd/mm/yyyy`; maps gender (L/P) and religion to SIGRAK's values (D-036) |
| Check | Applies the checks below to every row. Errors block that row only, never the whole file |
| Match | Finds the existing SIGRAK record for each row (keys below) |
| Preview | Stores the result per row: new, changed (with the changed fields), unchanged, needs review, or error. Nothing in the school's data has changed yet |
| Apply | Writes in one database transaction per chunk, on the queue. Applying the same file twice changes nothing the second time |
| Clean up | Deletes the uploaded file as soon as it is parsed. The preview rows are deleted 30 days after the import (*proposed*) |

**Matching keys.**

- **Students**, in order: NISN, then NIK, then NIPD within the school, then name + birth date. Two candidates, or a match on name + birth date only, go to *perlu dicek*.
- **Staff**, in order: NIK, NUPTK, NIP, then name + birth date, with the same review rule. A staff row that matches a SIGRAK user by email or NIP is linked to that account; otherwise it stays a staff record without an account.
- **Rombel:** "Rombel Saat Ini" is matched to a class of the active academic year by name, after normalizing ("Kelas 4 A", "4A", "4-A" are the same). An unmatched rombel is listed once, and the operator either picks an existing class or lets SIGRAK create it.

**Who wins when data differs.** Dapodik is the official source, so for the fields it supplies, the import replaces SIGRAK's value, and the preview shows every replacement first. Fields that exist only in SIGRAK (attendance, notes, scores, leaving details, accounts) are never touched. A student who is in SIGRAK but missing from the file is **never deleted**: they are listed under "tidak ada di file Dapodik", and the operator decides whether they moved, left, or stay.

## Checks

| Check | Result when it fails |
|---|---|
| NISN is 10 digits, NIK is 16 digits, NPSN matches the school | Error on that row ("Baris 12: NISN harus 10 angka.") |
| The file's NPSN or school name is another school | The whole file stops: "File ini milik sekolah lain." |
| Required fields present: name, gender, birth date | Error on that row |
| Dates are real dates, birth date is in the past | Error on that row |
| The same NISN or NIK appears twice in the file | Both rows go to *perlu dicek* |
| A student is in two rombel | *Perlu dicek* |
| Religion or gender value not in SIGRAK's list | Error, naming the value found |
| Rombel grade does not fit the school level (SD/MI grades 1–6) | Error |

Every message names the row number and the field, in plain Indonesian, like the other imports (XP-25).

## Fields kept, and fields not stored

SIGRAK keeps only what the Buku Induk and school administration need (*proposed*, data minimization under UU 27/2022 on personal data protection).

| Kept | Not stored, even if the file has it |
|---|---|
| **Students:** name, NISN, NIPD, NIK, gender, birth place and date, religion, address, siblings and child order, residence status, previous school, date entered, rombel, special needs, height and weight | Bank account details, KIP/KKS/KPS numbers, PIP eligibility, house coordinates, family card (KK) number |
| **Parents and guardian:** name, birth year, education, occupation | NIK of parents, income |
| **Staff:** name, NIK, NUPTK, NIP, gender, birth place and date, employment status, PTK type, additional duty, pangkat/golongan with TMT, TMT CPNS/PNS | Bank account details, NPWP, spouse data, mother's name, Karpeg/Karis numbers, coordinates |

NIK is stored with the `encrypted` cast plus a keyed hash for matching, the same pattern as AI keys (D-012). It is shown masked ("3319••••••••0012") except to the operator.

## Buku Induk

- **Per student:** one Buku Induk page with identity, parents and guardian, education history (date entered, previous school, class each year), progress (one row per semester), and leaving details (date, reason, destination school).
- **Progress per semester** comes from the Rapor add-on when the school has it; otherwise those rows stay empty for manual entry.
- **Outputs:** a PDF page per student or for a whole class through Gotenberg (D-028), with the school letterhead (D-031), and an Excel list with Indonesian headings (D-150).
- **Staff list:** each PTK with pangkat/golongan, TMT, and the computed date of the next KGB (2 years after the last TMT KGB) and the next regular promotion (4 years after the last TMT pangkat). These dates are estimates, labeled as such, for planning only.

## Access

| Who | Sees |
|---|---|
| Operator | Everything, runs imports |
| Principal | Everything, read-only, plus the import history |
| Homeroom teacher | Buku Induk pages of their own class's students, read-only, without NIK |
| Other teachers | Nothing beyond the student list they already have |
| A teacher | Their own staff record |
| Supervisor | Nothing (no student-level data, as today) |

None of this data is ever sent to the AI (`PromptDataGuard`, D-036), and it never appears on the exam site.

## Data model additions

| Table | Key columns | Notes |
|---|---|---|
| `student_profiles` | `student_id` FK CASCADE (unique), `nipd`, `nik` (encrypted), `nik_hash`, birth place, address, siblings, child order, residence status, previous school, `entered_on`, special needs, height, weight, `left_on` nullable, `leave_reason` nullable, `destination_school` nullable | Buku Induk fields beyond `students`. `nik_hash` unique within a school |
| `student_guardians` | `student_id` FK CASCADE, `relation` ∈ {`father`,`mother`,`guardian`}, name, birth year, education, occupation | Unique (`student_id`, `relation`) |
| `school_staff` | `school_id`, `user_id` FK nullable (NULL = no SIGRAK account), name, `nik` (encrypted), `nik_hash`, `nuptk` nullable, `nip` nullable, gender, birth place and date, employment status, PTK type, additional duty, `status` ∈ {`active`,`left`} | Unique (`school_id`, `nik_hash`) |
| `staff_rank_history` | `school_staff_id` FK CASCADE, pangkat/golongan, SK number, SK date, `tmt` | Kepangkatan |
| `staff_salary_steps` | `school_staff_id` FK CASCADE, golongan, SK number, SK date, `tmt`, masa kerja, gaji pokok | Gaji berkala (KGB) |
| `extracurriculars`, `extracurricular_members` | `school_id`, name, coach; `extracurricular_id`, `student_id`, `academic_year_id` | Used by Buku Induk and Rapor |
| `dapodik_imports` | `school_id`, `uploaded_by`, `file_type` ∈ {`students`,`staff`,`rank_history`,`salary_steps`,`extracurriculars`}, `layout_version`, `status` ∈ {`processing`,`ready`,`applied`,`failed`}, counts per result, `applied_at` | Import history |
| `dapodik_import_rows` | `dapodik_import_id` FK CASCADE, `row_number`, `result` ∈ {`new`,`changed`,`unchanged`,`review`,`error`}, `matched_id` nullable, `changes` JSON, `messages` JSON | Preview only, deleted 30 days after the import. JSON is allowed here because the rows are temporary and never queried by field |

All school-owned tables carry `school_id` and the `BelongsToSchool` scope. The import code lives in a new module, `app/SchoolData/` (*proposed*), which may use `Tenancy` and `Classroom` and never uses `AI`.

## Decisions to record

1. Dapodik sync is file-based: the operator uploads Dapodik Excel exports; SIGRAK recognizes them by headings and stores a column map per Dapodik layout version. No connection to Dapodik servers.
2. Every import goes through a preview; nothing changes before the operator presses *Terapkan*. Re-applying the same file changes nothing.
3. Dapodik wins for the fields it supplies; SIGRAK-only fields are never touched; students missing from the file are never deleted.
4. The field lists above: what is kept, and what is never stored.
5. NIK stored encrypted with a keyed hash for matching, masked in the UI.
6. Uploaded files are deleted after parsing; preview rows are deleted after 30 days.
7. Access rules above, including read-only Buku Induk for the homeroom teacher.
8. New module `app/SchoolData/`.

## Open questions

- [ ] **Sample files (owner).** Send one Daftar PD export and one PTK export from Dapodik 2026, with names and numbers replaced by dummy values, so the column map can be fixed.
- [ ] **KGB and kepangkatan history (owner).** Can the operator export riwayat gaji berkala and kepangkatan from Dapodik? If not, the SIGRAK template is used.
- [ ] **Ekstrakurikuler (owner).** Does Dapodik export ekstrakurikuler members? If not, entry stays in SIGRAK.
- [ ] **Buku Induk format (owner).** Does the local dinas or Kemenag office require a specific Buku Induk layout? If yes, send a blank copy.
- [ ] **Homeroom access (owner).** Confirm that homeroom teachers may view their students' Buku Induk pages, without NIK.

## Sources

Checked on 2026-11-01:

- Download path "Peserta Didik → Unduh Daftar PD": [Forum Dapodik](https://www.forumdapodik.com/2021/02/cara-mengunduh-daftar-peserta-didik-di.html), [Kemenag Pasangkayu](https://kemenagpasangkayu.id/login-verval-pd-new2-dapodikdasmen-2026-cek-data-siswa-residu-dan-rombel/)
- Riwayat gaji berkala entered through each PTK's login: [tasadmin.id](https://www.tasadmin.id/2019/08/cara-mengisi-riwayat-gaji-berkala-di.html)
- Official guide (not readable online because of its size): [Panduan Aplikasi Dapodik versi 2026](https://obj.pauddasmen.id/dapodik/Panduan_Lengkap_Aplikasi_Dapodik_versi_2026.pdf)
