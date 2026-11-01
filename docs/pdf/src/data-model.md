# Model Data

<p class="source-note">Diterjemahkan dari <code>docs/final/data-model.md</code> pada 1 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-05
- **Terakhir diperbarui:** 2026-11-01
- **Sumber:** `Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md`, `Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md`, `EDUNEXA-Dokumentasi-Fungsionalitas.pdf` (semuanya di `docs/sources/`)

## Tujuan

Ini adalah model relasional SIGRAK di MySQL 8 (D-007), direvisi dari catatan pengembang EDUNEXA agar sesuai dengan konsep baru: siswa berupa catatan data, konten berasal dari templat, dan guru memiliki beberapa kunci AI. Dokumen ini menetapkan batas entitas, key, invarian, dan kepemilikan. Dokumen ini **bukan** spesifikasi migrasi: setiap tabel mendapat tinjauan skema sendiri saat migrasinya ditulis.

## Konvensi

| Topik | Aturan |
|---|---|
| Nama | Bahasa Inggris, tabel snake_case jamak, model StudlyCase tunggal (konvensi repo) |
| Key | Primary key `bigint` auto-increment untuk join internal. Baris yang tampil di URL (dokumen, generation, undangan, siswa) juga mendapat `public_id` UUIDv7 (Laravel `HasUuids`), agar jumlah data tidak bocor dan id tidak bisa ditebak berurutan |
| Foreign key | Selalu dideklarasikan, selalu diindeks, dengan pilihan `ON DELETE` yang eksplisit |
| Nullability | `NOT NULL` secara bawaan. `NULL` hanya bila ketiadaan punya arti tersendiri, yang disebutkan di dokumen ini |
| Himpunan tetap | `varchar` + `CHECK (col IN (…))` (MySQL ≥ 8.0.16 menegakkan CHECK), dicerminkan oleh PHP backed enum |
| Waktu | `timestamp` disimpan dalam UTC. Ditampilkan dalam `Asia/Jakarta` (konvensi repo). Tanggal tanpa jam memakai `date` |
| Uang | Rupiah bilangan bulat (`bigint`), tanpa float |
| Penghapusan | Tanpa soft delete. Hapus permanen setelah konfirmasi yang menyebut nama itemnya. Siklus hidup dimodelkan sebagai `status` eksplisit bila baris tetap terlihat tetapi tidak aktif |
| JSON | Hanya untuk konten yang bentuknya ditetapkan JSON Schema templat (`documents.content`, `generations.specification`, keluaran langkah) dan untuk data rujukan yang diimpor. Tidak pernah menjadi tempat kolom yang perlu di-query |

## Tenancy dan akses

- Setiap tabel milik sekolah punya `school_id NOT NULL`, diindeks, dan indeks gabungan diawali kolom itu.
- MySQL tidak punya row-level security, jadi batasnya ditegakkan di aplikasi dengan cara **fail-closed**:
  - Trait `BelongsToSchool` secara bawaan menambahkan global scope dari keanggotaan yang sedang aktif. Melewatinya harus eksplisit (`withoutSchoolScope()`) dan hanya boleh di konsol internal serta di job yang menyebut sekolahnya.
  - Setiap aksi controller diotorisasi lewat policy.
  - Feature test memastikan pengguna sekolah A tidak pernah bisa membaca baris sekolah B (satu tes IDOR/BOLA per resource).
- Pengawas membaca data lintas sekolah binaannya lewat `supervisor_assignments` dengan policy hanya-baca. Mereka **tidak melihat baris tingkat siswa**.
- Data siswa (catatan, absensi, catatan guru, nilai) hanya bisa dibaca guru kelas itu, operator, dan kepala sekolah (aturan dari catatan pengembang). Catatan konseling (BK) hanya terlihat oleh penulisnya, wali kelas, dan kepala sekolah.

## Akun dan sekolah

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `regions` | `province`, `regency` | Unik (`province`, `regency`). Menyimpan bawaan untuk muatan lokal dan kalender (D-018) |
| `schools` | `name`, `npsn` (unik, nullable sampai diverifikasi), `level` ∈ {`sd`,`mi`}, `region_id` FK RESTRICT, alamat | Satu sekolah, satu tenant. `level` menentukan daftar mata pelajaran dan tabel alokasi (SD: enam mata pelajaran agama; MI: mata pelajaran Kemenag; lihat `religious-education.md`) |
| `users` | `name`, `email` (unik), `password`, `nip` nullable (guru non-ASN tidak punya) | Hanya pengguna sekolah: guru, kepala sekolah, operator, pengawas. **Tanpa kolom peran.** Peran berasal dari keanggotaan. **Tanpa pengguna siswa** (D-010) |
| `school_memberships` | `school_id` FK CASCADE, `user_id` FK CASCADE, `role` ∈ {`principal`,`operator`,`teacher`}, `status` ∈ {`active`,`ended`}, `started_on`, `ended_on` nullable | Unik (`school_id`, `user_id`, `role`). Satu pengguna bisa memegang beberapa peran. Satu kepala sekolah aktif per sekolah (diperiksa di service dan oleh unique index pada kolom generated `active_principal_school_id`) |
| `supervisor_assignments` | `user_id` FK, `school_id` FK, `started_on`, `ended_on` nullable | Cakupan hanya-baca untuk pengawas/korwil |
| `invitations` | `public_id`, `school_id`, `email`, `role`, `token_hash` (unik), `expires_at`, `accepted_at` nullable, `invited_by` FK users | Satu-satunya jalan masuk ke sekolah. Tidak ada kata sandi bawaan bersama |
| `staff_users` | `name`, `email`, `password`, `role` ∈ {`admin`,`support`,`content`} | Akun internal Tim SIGRAK untuk konsol Filament, dengan **guard dan tabel terpisah**, agar akun sekolah tidak pernah bisa menjadi staf |
| `school_transfer_requests` | `user_id`, `from_school_id`, `to_school_id`, `status`, `decided_by` nullable | "Pindah Sekolah", disetujui sekolah tujuan |

## Tahun ajaran, kelas, siswa

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `academic_years` | `school_id`, `name` (mis. `2026/2027`), `starts_on`, `ends_on`, `even_semester_starts_on` | CHECK `starts_on < even_semester_starts_on < ends_on`. Bawaan mulai 1 Juli |
| `classes` | `school_id`, `academic_year_id` FK, `grade` 1–6 (CHECK), `name` (mis. `4A`), `homeroom_teacher_id` FK users nullable (belum ditetapkan) | Unik (`academic_year_id`, `name`) |
| `teaching_assignments` | `school_id`, `class_id` FK CASCADE, `subject_id` FK, `teacher_id` FK users | Unik (`class_id`, `subject_id`). Saat kelas dibuat, mata pelajaran dengan `default_teacher = class_teacher` diberikan ke wali kelas, dan sisanya menunggu operator memilih guru mapel. Operator bisa menugaskan ulang baris mana pun (D-032) |
| `students` | `public_id`, `school_id`, `name`, `nisn` nullable (tidak semua siswa punya), `gender` ∈ {`l`,`p`}, `birth_date` nullable, `religion` ∈ {`islam`,`kristen`,`katolik`,`hindu`,`buddha`,`khonghucu`} nullable (belum dicatat) | **Catatan data, tidak pernah akun.** `religion` bersifat sensitif (D-036): hanya dipakai untuk mengelompokkan kelas agama, tidak pernah dikirim ke AI, disembunyikan dari pengawas |
| `enrollments` | `student_id` FK CASCADE, `class_id` FK RESTRICT, `status` ∈ {`active`,`moved`,`left`,`graduated`}, `ended_on` nullable | Satu pendaftaran aktif per siswa per tahun ajaran. Mencakup perpindahan ("Pindah atau keluar") dan kenaikan kelas ("Kenaikan") |

## Kurikulum

Kurikulum dipisah menjadi **bawaan global yang berversi** dan **perubahan per sekolah**, agar sekolah bisa memakai bawaan, menyesuaikannya, atau menambah miliknya sendiri tanpa merusak data bersama.

| Tabel | Kolom utama | Kolom sumber (KM-SD-2026-R2) |
|---|---|---|
| `curriculum_versions` | `code` (unik, mis. `KM-SD-2026-R2`), `status` ∈ {`draft`,`active`,`retired`} | `Versi_Kurikulum`, `Status_Versi` |
| `subjects` | `code` (mis. `PP`), `name`, `kind` ∈ {`compulsory`,`local_content`,`cocurricular`}, `default_teacher` ∈ {`class_teacher`,`subject_teacher`}, `religion` nullable (NULL = bukan mata pelajaran agama), `school_level` ∈ {`sd`,`mi`,`both`} | `Mata Pelajaran`, `Status_Mapel`. `subject_teacher` untuk PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, dan Pendidikan Agama (D-032). SD punya satu mata pelajaran agama per agama; MI punya Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, dan Bahasa Arab (D-034, D-035) |
| `subject_allocations` | `curriculum_version_id`, `subject_id`, `grade`, `phase` ∈ {A,B,C}, `learning_type` ∈ {`intracurricular`,`cocurricular`}, `jp_per_week`, `weeks_per_year`, `s1_jp`, `s1_effective_weeks`, `s2_jp`, `s2_effective_weeks` | `struktur_alokasi_sd.json` → `alokasi[]`, `kokurikuler_default[]` |
| `topics` (materi pokok / ATP) | `curriculum_version_id`, `code` (unik, `ID_Kurikulum`), `legacy_codes` JSON, `subject_id`, `grade`, `phase`, `semester` ∈ {1,2}, `elements`, `title`, `cp_text`, `atp_code`, `order_in_semester`, `jp_total`, `jp_per_week`, `week_start`, `week_end`, `weekly_distribution` JSON, `references` JSON, `audit` JSON, `extras` JSON (cabang seni, keterampilan proses sains) | Satu baris `data_kurikulum_dengan_cp.json`. Kolom berisi string JSON diurai saat impor |
| `focus_items` (fokus materi) | `topic_id` FK CASCADE, `code` (unik, `FM-…`), `order`, `title`, `estimated_jp`, `allocation_status` | `Data_Fokus`. `Fokus_Materi` yang ganda dibuang |
| `learning_objectives` (TP) | `topic_id` FK CASCADE, `code` (unik, `TP-…`), `order`, `text` | `Data_Tujuan` |
| `focus_item_objectives` | `focus_item_id`, `learning_objective_id` | **Tautan baru** (temuan 3 di dokumen pemetaan). Kosong saat impor, diisi lewat tugas data. Dibutuhkan untuk analisis per TP di tingkat fokus |
| `region_subjects` | `region_id`, `subject_id`, `allocation` | Muatan lokal bawaan per daerah, mis. Bahasa Jawa untuk Kudus |
| `school_subjects` | `school_id`, `subject_id`, `enabled` | Sekolah menyalakan atau mematikan muatan lokal |
| `school_topic_changes` | `school_id`, `base_topic_id` FK nullable (**NULL = topik baru yang ditambahkan sekolah**), `payload` JSON (bentuknya sama dengan topik), `status` ∈ {`draft`,`submitted`,`returned`,`principal_approved`,`approved`}, `submitted_by` | Alur validasi guru → kepala sekolah → pengawas (D-019) |
| `curriculum_validations` | `school_topic_change_id`, `reviewer_id`, `level` ∈ {`principal`,`supervisor`}, `decision` ∈ {`approved`,`returned`}, `note` | Hanya ditambah. Setiap pengembalian membawa catatan untuk guru |

**Versi kurikulum di v1:**

| Kode | Isi | Data sumber |
|---|---|---|
| `KM-SD-2026-R2` | Mata pelajaran SD nasional dengan topik, TP, ATP, fokus, JP (termasuk PAI) | Berkas JSON EDUNEXA |
| `CP-PABP-BKPDM-020-2026` | CP enam mata pelajaran agama, SD Fase A–C | `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` (ditranskripsi, dicocokkan dengan pindaian di P2-01) |
| `CP-PAI-BA-MADRASAH-9941-2025` | CP mata pelajaran Kemenag di MI, Fase A–C | `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` (disusun menjadi baris elemen di P2-02, D-039) |
| `ALOKASI-MI-KMA-1503-2025` | Alokasi MI: JP per mata pelajaran dan kelas (intrakurikuler, kokurikuler), wajib vs pilihan, rentang muatan lokal | `docs/data/curriculum/mi-allocation-kma-1503-2025.json` (dicocokkan dengan KMA 1503/2025 Lampiran II, Tabel 1–5, hlm. 28–31) |

Topik, TP, ATP, fokus, dan JP untuk lima agama selain Islam dan lima mata pelajaran MI sudah dibuat drafnya di `tp-atp-pendidikan-agama-sd-2026.json` (`TPATP-PABP-SD-2026-R1`) dan `tp-atp-pai-bahasa-arab-mi-2026.json` (`TPATP-PAI-BA-MI-2026-R1`), dengan bentuk tabel model data ini (D-140), menunggu tinjauan Tim Validasi SIGRAK. Versi yang hanya berisi CP sudah cukup bagi wizard RPM untuk menampilkan CP dan elemen.

**Impor.** `php artisan curriculum:import <file>` memuat `data_kurikulum_dengan_cp.json` dan `struktur_alokasi_sd.json`. Perintah ini idempoten berdasarkan `code`, menyimpan alias `Legacy_ID` di `legacy_codes`, dan melaporkan data yang tidak cocok. Hanya data kurikulum yang diimpor dari EDUNEXA (D-020).

**Masalah data yang terbawa** (dari dokumen pemetaan, masih terbuka):

| Masalah | Tindakan |
|---|---|
| Matematika kelas 4 semester 2 = 80 JP, target 90 | Tugas perbaikan data bersama pemilik kurikulum |
| Keterampilan Tata Boga (kelas 3–6) tidak punya target alokasi | Tambah target atau tandai sebagai pilihan sekolah |
| Bahasa Inggris kelas 1–2 harus tetap kokurikuler | `learning_type = cocurricular` |
| TP hanya tertaut ke topik, tidak ke fokus | `focus_item_objectives` + tugas data |
| Kurikulum ada di JSON statis sekaligus di Firestore | Tabel SQL menjadi satu-satunya sumber; versi hanya berubah lewat `curriculum_versions` |

## Kalender, jadwal pelajaran, pertemuan

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `calendars` | `region_id` nullable / `school_id` nullable, `academic_year` | Tepat satu pemilik: CHECK (`region_id` IS NULL) <> (`school_id` IS NULL). Kalender daerah adalah bawaan yang ditimpa kalender sekolah |
| `calendar_events` | `calendar_id` FK CASCADE, `starts_on`, `ends_on`, `kind` ∈ {`holiday`,`exam`,`school_event`,`no_lessons`}, `title` | CHECK `starts_on <= ends_on` |
| `timetable_slots` | `school_id`, `class_id`, `weekday` 1–6, `period_no`, `starts_at`, `ends_at`, `subject_id`, `teacher_id`, `jp` | Unik (`class_id`, `weekday`, `period_no`). 1 JP = 35 menit untuk SD |
| `meetings` | `school_id`, `document_id` FK CASCADE (RPM-nya), `number`, `focus_item_id`, `jp`, `minutes`, `planned_on` nullable (NULL untuk "RPM lepas jadwal"), `status` ∈ {`planned`,`done`,`not_done`} | Unik (`document_id`, `number`). Pertemuan `not_done` menggeser pertemuan berikutnya ke slot selanjutnya (perilaku penjadwal EDUNEXA). Tiga versi jadwal terakhir disimpan di `meeting_schedule_history` |

## Konten

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `templates` | `key` (unik), `category` ∈ {`document`,`game`}, `is_active`, `sort_order`, `current_version` | Hanya registri. Definisinya ada di kode (D-013). Satu baris per jenis konten: `rpm`, `bank_soal`, `lkpd`, `quiz_battle`, `tka_package` (tanpa templat umum, D-024) |
| `template_versions` | `template_key` FK, `version`, `code_ref` (tag/sha git), `prompt_override` text nullable (NULL = dasar dari kode), `created_by_staff_id` nullable, `first_used_at` nullable | Unik (`template_key`, `version`). **Tidak bisa diubah begitu `first_used_at` terisi** (ditegakkan di `TemplateVersionManager` dan formulir Filament) |
| `generations` | `public_id`, `school_id`, `user_id`, `template_key`, `template_version`, `kind` ∈ {`create`,`section_revision`}, `document_id` nullable (diisi untuk revisi, atau setelah disimpan), `client_token`, `specification` JSON, `status` ∈ {`queued`,`running`,`succeeded`,`failed`,`cancelled`}, `pinned_credential_id` FK SET NULL, `error_class` nullable, `started_at`, `finished_at` | Unik (`user_id`, `client_token`) mencegah kiriman ganda. FK (`template_key`, `template_version`) → `template_versions` |
| `generation_steps` | `generation_id` FK CASCADE, `step_key`, `position`, `status` ∈ {`pending`,`running`,`succeeded`,`failed`}, `attempts`, `output` JSON nullable (NULL sampai diterima) | Unik (`generation_id`, `step_key`). Keluaran ditulis sekali, dengan compare-and-set pada `status` |
| `documents` | `public_id`, `school_id`, `owner_id` FK users, `template_key`, `template_version`, `title`, `grade`, `subject_id` nullable, `topic_id` nullable (paket TKA mencakup banyak topik), `content` JSON, `status` ∈ {`draft`,`final`}, `source_generation_id` FK SET NULL | "Dokumen Saya". `content` divalidasi terhadap skema keluaran templat ditambah kolom data yang digabungkan |
| `document_revisions` | `document_id` FK CASCADE, `content` JSON, `reason` ∈ {`manual_save`,`ai_section_revision`}, `created_by` | 5 terakhir disimpan per dokumen, yang lebih lama dipangkas (D-153) |
| `document_comments` | `document_id` FK CASCADE, `author_id`, `body`, `created_at` | Komentar kepala sekolah dan pengawas (D-019) |
| `document_exports` | `document_id` FK CASCADE, `format` ∈ {`pdf`,`docx`,`xlsx`,`html`}, `template_version`, `content_hash`, `path`, `status` | Cache. Dibangun ulang bila `content_hash` atau pengaturan cetak berubah |
| `template_feature_requests` | `user_id`, `template_key`, `body`, `status` ∈ {`new`,`planned`,`declined`,`done`} | "Usulkan fitur" (D-015) |

Butir Bank Soal tetap berada di `content` dokumen (satu sumber untuk tampilan). Bila sebuah asesmen memakai dokumen Bank Soal, butirnya **disalin** ke `assessment_items`, berupa snapshot beku dengan `kode_tp` untuk penilaian dan analisis per TP.

## AI

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `ai_credentials` | `user_id` FK CASCADE, `label`, `project_label`, `account_label` nullable, `secret` (cast terenkripsi), `secret_last4`, `secret_fingerprint`, `status` ∈ {`active`,`invalid`,`disabled`}, `is_backup` bool bawaan false (D-119), `invalid_reason` nullable, `consecutive_failures`, `circuit_open_until` nullable, `last_success_at` nullable, `last_tested_at`, `multi_key_consent_at` nullable (NULL untuk kunci pertama) | Unik (`user_id`, `secret_fingerprint`). Hanya dipakai pemiliknya. Batas per pengguna diatur di konfigurasi. Kunci kedua dan seterusnya butuh persetujuan (D-023). **Tanpa `school_id`**: kunci milik guru (D-012) |
| `ai_credential_cooldowns` | `ai_credential_id` FK CASCADE, `model`, `until`, `reason` ∈ {`rate_minute`,`rate_daily`} | Unik (`ai_credential_id`, `model`). Cermin cooldown Redis untuk UI |
| `ai_usage_logs` | `user_id`, `school_id` nullable (NULL untuk pengawas tanpa sekolah), `ai_credential_id` FK SET NULL, `generation_id` FK SET NULL, `step_key`, `attempt`, `purpose`, `model`, `outcome` (berhasil atau kelas galat), `http_status` nullable, `input_tokens`, `output_tokens`, `latency_ms`, `created_at` | Hanya ditambah. Indeks (`user_id`, `created_at`), (`school_id`, `created_at`). Tanpa kunci, tanpa teks prompt |

## Administrasi (dipertahankan dari EDUNEXA, dimodelkan secara relasional)

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `journals` | `school_id`, `class_id`, `subject_id`, `teacher_id`, `meeting_id` nullable (jurnal tanpa RPM), `date`, `status` ∈ {`done`,`not_done`}, `notes`, `client_uuid` | Unik `client_uuid` (sinkronisasi offline yang idempoten) |
| `attendance_records` | `school_id`, `class_id`, `student_id`, `date`, `status` ∈ {`present`,`sick`,`permitted`,`absent`}, `recorded_by`, `recorded_at_device`, `client_uuid` | Absensi **harian** (D-029): unik (`student_id`, `date`) dan unik `client_uuid`. Dicatat wali kelas. Absensi per jam pelajaran *Nanti* |
| `student_notes` | `school_id`, `student_id`, `class_id`, `author_id`, `kind` ∈ {`anecdotal`,`counselling`,`achievement`}, `body`, `client_uuid` | Visibilitas konseling dibatasi (lihat Tenancy) |
| `assessment_plans` | `school_id`, `class_id`, `subject_id`, `semester`, komponen | "Rencana Penilaian" |
| `assessments` | `school_id`, `class_id`, `subject_id`, `kind` ∈ {`formative`,`summative`,`sts`,`sas`}, `held_on`, `kktp`, `source_document_id` FK SET NULL | |
| `assessment_items` | `assessment_id` FK CASCADE, `number`, `type`, `kode_tp`, `cognitive_level`, `max_score`, `answer_key` nullable (uraian tidak punya) | Snapshot beku dari butir Bank Soal |
| `scores` | `assessment_item_id` FK CASCADE, `student_id`, `score` decimal(5,2) | Unik (`assessment_item_id`, `student_id`). CHECK 0 ≤ nilai ≤ maksimum (di service; nilai maksimum ada di butir) |
| `report_descriptions` | `school_id`, `student_id`, `subject_id`, `semester`, `draft`, `final` nullable (belum disetujui) | Draf AI dari nilai per TP; guru menyuntingnya sebelum dipakai |
| `class_finance_entries` | `school_id`, `class_id`, `kind` ∈ {`income`,`expense`}, `amount` bigint rupiah CHECK > 0, `occurred_on`, `note`, `recorded_by` | Fitur bisa dimatikan per sekolah |
| `class_inventory_items`, `duty_rosters` | Lingkup kelas | |
| `school_announcements` (Kabar), `reminders` (Pengingat), `supervisions`, `school_books` (buku tamu, humas, notulen), `school_programs`, `agendas` | `school_id` | Fitur portal |
| `school_print_settings` | `school_id` (unik), penanda tangan (nama, NIP, jabatan), `paper` ∈ {`a4`,`f4`}, margin, `font` ∈ {`arial`,`times_new_roman`} (D-125) | Pengaturan cetak per sekolah. Kop ada di `letterheads` |
| `letterheads` | `school_id` nullable / `user_id` nullable, `image_path`, `width_px`, `height_px`, `uploaded_by` | Tepat satu pemilik: CHECK (`school_id` IS NULL) <> (`user_id` IS NULL). Unik per pemilik. Kop milik guru menggantikan kop sekolah pada dokumen guru itu. Aturan unggah: PNG/JPG, lebar ≥ 2.000 px, rasio 4:1 sampai 8:1, ≤ 2 MB (D-031) |
| `subscriptions` | `school_id` nullable / `user_id` nullable / `supervisor_user_id` nullable, `plan` ∈ {`trial`,`game_trial`,`teacher`,`school`,`cluster`}, `academic_year`, `starts_on`, `ends_on`, `status` | Tepat satu kolom pemilik terisi (CHECK). `trial` berlaku 7 hari (D-160); setelah itu akun tanpa paket berbayar hanya bisa melihat dan mengunduh (D-157). `game_trial` (7 hari, sekali per guru) membuka Game kelas tanpa add-on sekolah (D-159). Tabel penagihan dibuat di tugas penagihan |
| `school_addons` | `school_id`, `addon` ∈ {`tka`,`buku_induk`,`class_finance`,`school_books`,`games`,`report_cards`}, `academic_year`, `status` | Unik (`school_id`, `addon`, `academic_year`). Membutuhkan langganan `school` yang aktif (D-158) |

## Latihan ujian TKA (CBT)

Tabel-tabel ini mendukung situs ujian yang terpisah (D-026, D-027). Peserta ujian adalah siswa yang masuk dengan **kredensial per sesi**. Mereka bukan baris di `users`.

| Tabel | Kolom utama | Catatan dan invarian |
|---|---|---|
| `exam_sessions` | `public_id`, `school_id`, `package_document_id` FK RESTRICT (dokumen `tka_package`), `subject` ∈ {`bahasa_indonesia`,`matematika`}, `title`, `opens_at`, `closes_at`, `duration_minutes` (bawaan 75), `token_secret` (terenkripsi), `token_period_minutes` (bawaan 15), `proctor_id` FK users, `status` ∈ {`draft`,`scheduled`,`open`,`closed`} | CHECK `opens_at < closes_at`, CHECK `duration_minutes` antara 10 dan 180. Token dihitung dari `token_secret` dan jendela waktu, jadi token tidak disimpan |
| `exam_items` | `exam_session_id` FK CASCADE, `number`, `group_key` nullable (NULL = butir tunggal, terisi = berbagi stimulus), `form` ∈ {`pg`,`pgk_mcma`,`pgk_category`}, `stimulus` JSON nullable, `content` JSON, `answer_key` JSON, `competency`, `max_points` | Snapshot beku dari paket saat sesi dijadwalkan. Mengubah paket kemudian tidak mengubah ujian yang sedang berjalan |
| `exam_participants` | `public_id`, `exam_session_id` FK CASCADE, `student_id` FK RESTRICT, `username` (unik per sesi), `password` (terenkripsi, agar kartu bisa dicetak ulang), `item_order` JSON (diacak per peserta), `status` ∈ {`not_started`,`in_progress`,`submitted`,`expired`}, `started_at` nullable, `submitted_at` nullable, `extra_minutes` (bawaan 0), `login_resets` | Unik (`exam_session_id`, `student_id`). Kredensial tidak berlaku lagi saat sesi `closed`. Kata sandi dihapus 30 hari setelah sesi ditutup |
| `exam_answers` | `exam_participant_id` FK CASCADE, `exam_item_id` FK CASCADE, `response` JSON, `is_flagged` (ragu-ragu), `client_seq`, `saved_at` | Unik (`exam_participant_id`, `exam_item_id`). Upsert hanya bila `client_seq` lebih besar dari yang tersimpan, sehingga pengiriman ulang dan sambung ulang bersifat idempoten |
| `exam_results` | `exam_participant_id` FK CASCADE (unik), `score` decimal(5,2) CHECK 0–100, `category` ∈ {`kurang`,`memadai`,`baik`,`istimewa`}, `per_competency` JSON, `scored_at` | Perkiraan latihan, bukan hasil TKA resmi |
| `exam_events` | `exam_session_id`, `exam_participant_id` nullable (NULL = kejadian tingkat sesi), `actor_id` nullable (NULL = peserta itu sendiri), `kind` ∈ {`login`,`token_fail`,`reset_login`,`extend_time`,`submit`,`force_submit`}, `created_at` | Log audit proktor yang hanya ditambah |

Akses: guru dan operator sekolah mengelola sesi; proktor menjalankannya. Kepala sekolah melihat hasil. Pengawas hanya melihat ringkasan tingkat sekolah. Guard situs ujian hanya membaca `exam_participants` serta butir dan jawaban milik peserta itu sendiri.

**Sinkronisasi offline (Di Kelas, D-016).**

- Perangkat membuat baris dengan `client_uuid` dan mengantrekannya di IndexedDB. Endpoint sinkronisasi melakukan upsert berdasarkan `client_uuid`, sehingga pengiriman berulang tidak pernah menggandakan data.
- Bila terjadi konflik, penulisan terakhir yang menang per baris, dan waktu perangkat disimpan di `recorded_at_device` di samping timestamp server.

## Pertanyaan terbuka

- [x] Ekspor e-Rapor: ditunda; data rapor memakai tata letak Excel SIGRAK sendiri untuk sementara, dan pemilik merancang ekspor e-Rapor khusus nanti (D-144).
- [x] Masa simpan: `document_revisions` menyimpan 5 terakhir per dokumen (D-153); `ai_usage_logs` 1 bulan (D-118).
- [x] Konteks `school_id` pengawas: `supervisor_assignments` sudah cukup (D-122, usulan).
