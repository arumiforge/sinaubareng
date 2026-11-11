# Panduan Ekspor dan Lokalisasi

<p class="source-note">Diterjemahkan dari <code>docs/final/export-localization.md</code> pada 10 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P2-07
- **Terakhir diperbarui:** 2026-11-10 (aturan cetak, Berkas Administrasi, cetakan baru, dan kata terlarang dari D-210…D-371)

## Tujuan

Panduan ini menjelaskan cara teks UI berbahasa Indonesia dan ekspor berbahasa Indonesia (PDF, DOCX, Excel, CSV) di SIGRAK dibangun. Tujuannya agar setiap layar dan setiap ekspor mengikuti aturan yang sama sejak baris kode pertama. Panduan ini untuk pengembang dan agen.

Panduan ini melanjutkan `coding-standards.md` bagian 7–8 (aturan TR-\* dan EX-\*) tanpa mengubahnya. Aturan di sini diberi nomor `LOC-*` (lokalisasi) dan `XP-*` (ekspor). Butir bertanda *usulan* tercantum di Keputusan untuk dicatat.

Sumber: `CLAUDE.md` (kebijakan bahasa, D-002), `content-engine.md` (bagian Ekspor), `design-system.md`, `Parafrase_Teks_UI_Sigrak.xlsx`, D-028, D-031, D-033, D-039, D-080, D-094; keputusan berikutnya D-210, D-218, D-274, D-278…D-284, D-287, D-292, D-295, D-297…D-303, D-311, D-325, D-370, D-371 (`legacy-upgrade-01.md` §4).

## 1. Susunan `lang/id`

### Berkas

Satu berkas PHP per area. Locale dan fallback sama-sama `id`, dan tidak ada `lang/en` (TR-8).

| Berkas | Isi | Dimuat untuk |
|---|---|---|
| `common.php` | Aksi bersama, status, galat umum, paginasi, pola konfirmasi dan toast | Semua halaman |
| `nav.php` | Nama menu, judul halaman di kerangka aplikasi | Semua halaman aplikasi sekolah |
| `terms.php` | Glosarium: satu key per istilah baku (lihat §2.3) | Dirujuk berkas lain; juga semua halaman |
| `enums.php` | Label untuk setiap backed enum: `enums.<enum_snake>.<value>` | Semua halaman |
| `auth.php`, `account.php` | Masuk, undangan, kata sandi, profil, ganti peran | Halamannya |
| `today.php`, `classes.php`, `attendance.php`, `journals.php`, `student_notes.php` | Hari Ini, Kelas Saya, Di Kelas | Halamannya |
| `documents.php`, `templates.php`, `rpm.php`, `bank_soal.php`, `lkpd.php`, `quiz_battle.php`, `tka_package.php`, `generation.php` | Buat, Dokumen Saya, satu berkas per templat (D-024), status pembuatan | Halamannya |
| `assessments.php`, `reports.php` | Asesmen, rapor | Halamannya |
| `ai_keys.php` | Kunci AI Saya, teks persetujuan (D-023) | Halamannya |
| `school.php`, `supervisor.php` | Portal sekolah (kepala sekolah, operator), portal pengawas | Halamannya |
| `exam.php` | Khusus situs ujian (D-027, D-094): token, ujian, selesai, teks ramah anak | Hanya klien ujian |
| `help.php` | Bantuan | Halaman bantuan |
| `console.php` | Label konsol staf di luar locale `id` bawaan Filament | Hanya Filament |
| `export.php` | Judul kolom ekspor, nama sheet, kata untuk nama berkas, label cetak ("Halaman :page dari :total") | Job ekspor; tidak pernah dikirim ke browser |
| `mail.php`, `notifications.php` | Subjek dan isi email, teks notifikasi database | Hanya server |
| `validation.php` | Pesan validasi Laravel + `attributes` | Hanya server |
| `errors.php` | Halaman galat HTTP (403, 404, 419, 429, 500, 503) | View galat |

### Penamaan key

- **LOC-1** Key berbentuk `<file>.<group>.<name>`, semuanya snake_case bahasa Inggris, dan menyebut maknanya, bukan kata-katanya (`journals.toasts.saved`, bukan `journals.jurnal_tersimpan`).
- **LOC-2** Pakai kelompok berikut di dalam berkas, agar key mudah ditebak:

| Kelompok | Untuk | Contoh key |
|---|---|---|
| `titles` | Judul halaman dan panel | `journals.titles.index` |
| `actions` | Tombol dan tautan | `common.actions.download_pdf` |
| `fields` | Label formulir; `placeholders` dan `hints` untuk pendampingnya | `bank_soal.fields.duration_minutes` |
| `columns` | Judul kolom tabel dan ekspor | `export.columns.created_at` |
| `options` | Pilihan yang bukan enum | `common.options.semester_even` |
| `toasts` | Pesan berhasil satu baris | `school.toasts.confirmation_sent` |
| `errors` | Pesan galat (apa yang terjadi + apa yang harus dilakukan) | `rpm.errors.delete_failed` |
| `dialogs.<name>` | `title`, `body`, `confirm`, `cancel` dari satu dialog | `classes.dialogs.replace_homeroom.body` |
| `empty` | Keadaan kosong | `classes.empty.students` |
| `progress` | Status saat sedang bekerja | `generation.progress.step` |

- **LOC-3** Kedalaman paling banyak tiga segmen setelah nama berkas. Jangan bersarang per widget layar.
- **LOC-4** Pakai ulang sebelum menambah. Aksi umum (`common.actions.save`, `.download`, `.back`) ada di `common.php`. Tombol yang spesifik menyebut bendanya ("Simpan jurnal"), jadi mendapat key sendiri di berkas areanya.

### Parameter dan bentuk jamak

- **LOC-5** Parameter memakai `:name` (TR-3). Beri nama sesuai isinya (`:class`, `:time`, `:count`), jangan `:0` atau `:x`. Nilai yang perlu diformat diformat **sebelum** dikirim (`IndonesianFormat::time($until)`), bukan di dalam string.
- **LOC-6** Kata benda bahasa Indonesia tidak berubah bentuk, jadi `trans_choice` hanya dipakai bila nol atau satu dibaca berbeda:

```php
// lang/id/classes.php
'students_count' => '{0} Belum ada murid|[1,*] :count murid',

trans_choice('classes.students_count', $n, ['count' => IndonesianFormat::number($n)]);
```

- **LOC-7** Jangan pernah menyusun kalimat dari potongan (`__('a') . ' ' . $name . __('b')`). Urutan kata adalah urusan terjemahan.
- **LOC-8** Teks dengan markup (satu kata tebal, sebuah tautan) memakai parameter untuk bagian tanpa markup dan komponen Svelte untuk markupnya. Terjemahan tidak pernah berisi HTML.

### Prop Inertia bersama dan `t()` (D-080)

Server hanya mengirim kelompok yang dibutuhkan halaman. Browser tidak pernah memuat seluruh `lang/id`.

- **LOC-9** `HandleInertiaRequests::share()` selalu membagikan `common`, `nav`, `terms`, dan `enums` dalam prop `translations`. Halaman menambahkan berkasnya sendiri lewat middleware rute *usulan*. Path-nya berbahasa Indonesia, nama rutenya tetap bahasa Inggris (D-149):

```php
Route::get('/jurnal', [JournalController::class, 'index'])
    ->middleware('translations:journals,attendance')
    ->name('journals.index');
```

- **LOC-10** Middleware menggabungkan berkas yang disebut ke prop `translations`, diratakan menjadi key bertitik (`journals.toasts.saved`). Berkas khusus server (`export`, `mail`, `notifications`, `validation`, `errors`, `console`) ditolak, sehingga tidak bisa bocor ke browser.
- **LOC-11** Pada reload parsial (polling, `only: [...]`), prop `translations` tidak diminta lagi. Prop ini lazy dan hanya disertakan pada kunjungan penuh.
- **LOC-12** Klien ujian (D-094) punya root view dan daftar share sendiri: hanya `common` dan `exam`, tidak pernah `nav` atau teks sekolah.
- **LOC-13** Di Svelte, satu helper membaca prop itu. Maknanya sama dengan `__()` di Laravel, ditambah `tc()` untuk `trans_choice`:

```ts
// resources/js/lib/i18n.ts
import { page } from '@inertiajs/svelte';

export function t(key: string, params: Record<string, string | number> = {}): string {
    const text = page.props.translations[key] ?? key; // missing key shows the key; the test in §7 fails first
    return text.replace(/:(\w+)/g, (match, name) => (name in params ? String(params[name]) : match));
}
```

```svelte
<button type="submit">{t('journals.actions.save')}</button>
<p>{t('ai_keys.errors.all_limited', { time: format.time(earliest) })}</p>
```

## 2. Dari spreadsheet parafrase ke key

`Parafrase_Teks_UI_Sigrak.xlsx` punya 1.499 baris di lembar *Parafrase* (kolom: No, Halaman, Jenis teks, Teks lama, Teks baru, Catatan, Status, Sumber) dan 37 istilah di lembar *Istilah*. Lembar *Ringkasan* hanya menghitung baris per halaman. Spreadsheet tetap di `docs/sources/` tanpa diubah. Pemindahan teksnya ke `lang/id` dilakukan per layar, saat layar itu dibangun (lihat `design-system.md`).

### 2.1 Aturan pemetaan

- **LOC-14** *Teks baru* adalah teks awal. *Teks lama* tidak pernah dipakai. Catatan di kolom *Catatan* dan daftar istilah di `design-system.md` lebih diutamakan daripada *Teks baru* bila berbeda (misalnya "Templat" vs "template" setelah diputuskan).
- **LOC-15** **Berkas** ditentukan oleh layar baru yang menampilkan teks itu, bukan oleh halaman aplikasi lama. Halaman lama hanya petunjuk:

| *Halaman* (aplikasi lama) | Berkas yang biasa |
|---|---|
| Semua halaman | `common`, `terms` |
| Beranda & akun | `auth`, `account`, `today`, `classes`, `ai_keys` |
| Generator RPM | `rpm`, `documents`, `generation` |
| Bank Soal | `bank_soal` |
| Worksheet/LKPD | `lkpd` |
| Administrasi Kelas | `classes`, `attendance`, `journals`, `student_notes`, `assessments`, `reports`, serta berkas keuangan dan inventaris kelas bila layarnya sudah ada |
| Portal Kepala Sekolah | `school` |
| Portal Pengawas | `supervisor` |
| Super Admin | `console`, hanya bila konsol staf masih membutuhkan teksnya |
| Tutorial | `help` |

- **LOC-16** **Kelompok** ditentukan oleh *Jenis teks*:

| *Jenis teks* | Kelompok |
|---|---|
| Tombol, Tautan | `actions` |
| Label formulir, Label, Placeholder, Petunjuk, Tooltip | `fields`, `placeholders`, `hints` |
| Judul kolom | `columns` |
| Pilihan | `options`, atau `enums.*` bila himpunannya tetap |
| Galat, Galat formulir, Peringatan | `errors` (galat formulir yang cocok dengan aturan validasi masuk ke `validation.php`) |
| Notifikasi, Pesan informasi | `toasts` |
| Judul dialog, Isi dialog, Konfirmasi | `dialogs.<name>.title/body/confirm` |
| Pesan kosong | `empty` |
| Status proses, Status | `progress` |
| Judul halaman, Judul panel, Subjudul, Judul menu, Kartu menu, Tab | `titles`, atau `nav.*` untuk menu |

- **LOC-17** **Placeholder.** Setiap `{…}` menjadi `:param` bernama. `({…})` yang berisi pesan galat teknis mentah dibuang dari teks UI. Detail itu disimpan di balik "Lihat detail" (aturan 4 di sistem desain).
- **LOC-18** **Satu key per makna.** Baris dengan *Teks baru* dan makna yang sama memakai satu key ("Simpan" muncul di banyak halaman → `common.actions.save`).
- **LOC-19** **Baris yang dibuang.** Teks untuk fitur yang sudah tidak ada (sinkronisasi Cloud, dasbor Super Admin, Bank Modul) tidak diberi key.
- **LOC-20** **Pelacakan.** Satu berkas pemetaan mencatat hasil setiap baris, agar tidak ada yang perlu membandingkan spreadsheet secara manual (*usulan*). Berkasnya `docs/data/ui-copy/key-map.csv`, dengan kolom `no`, `key`, `status` ∈ {`mapped`, `merged`, `dropped`, `pending`}, dan `note`. Tugas yang membangun sebuah layar mengisi baris-baris layar itu.

### 2.2 Contoh pemetaan (23 baris)

| No | *Jenis teks* | Teks lama | Teks baru | Key |
|---|---|---|---|---|
| 2 | Tombol | Download | Unduh | `common.actions.download` |
| 5 | Tombol | Edit | Ubah | `common.actions.edit` |
| 27 | Tombol | PDF | Unduh PDF | `common.actions.download_pdf` |
| 32 | Pilihan | Genap (2) | Semester 2 (Genap) | `enums.semester.2` |
| 60 | Galat | Impor gagal: {…} | File belum berhasil diimpor. Pastikan file berasal dari Sigrak dan belum diubah. ({…}) | `common.errors.import_failed` (detail dibuang, LOC-17) |
| 88 | Tautan | Dapatkan API KEY | Cara membuat kunci AI | `ai_keys.actions.how_to_create` |
| 132 | Kartu menu | Bank Soal | Buat Soal | `nav.create_questions` |
| 219 | Konfirmasi | Kelas ini sudah memiliki wali kelas terdaftar ({…}). Apakah Anda pengganti guru tersebut? | Kelas ini sudah punya wali kelas ({…}). Apakah Anda menggantikan beliau? | `classes.dialogs.replace_homeroom.body` (`:teacher`) |
| 221 | Galat | Gagal menambah kelas: {…} | Kelas belum berhasil ditambahkan. ({…}) | `classes.errors.create_failed` |
| 268 | Notifikasi | Konfirmasi telah dikirim kepada Kepala Sekolah. | Konfirmasi terkirim ke kepala sekolah. | `school.toasts.confirmation_sent` |
| 309 | Galat | Instansi tidak ditemukan. | Sekolah tidak ditemukan. | `school.errors.not_found` |
| 393 | Galat | Dokumen belum dapat dihapus dari Cloud. … | RPM belum berhasil dihapus. Periksa koneksi, lalu coba lagi. | `rpm.errors.delete_failed` |
| 480 | Label formulir | Durasi Waktu | Waktu mengerjakan (menit) | `bank_soal.fields.duration_minutes` |
| 532 | Galat | Gagal membuat PDF ({…}): {…} | PDF ukuran {…} belum berhasil dibuat. Coba lagi. | `lkpd.errors.pdf_failed` (`:paper`) |
| 552 | Judul menu | Buku Catatan | Catatan siswa | `nav.student_notes` |
| 758 | Petunjuk | Refleksi tidak disusun otomatis … | Ketuk Susun untuk membuat refleksi dari jurnal yang terlaksana. Periksa, lalu ketuk Konfirmasi. | `journals.hints.compose_reflection` |
| 961 | Judul dialog | Pindah Kelas / Rombel | Pindah rombel | `classes.dialogs.move_student.title` |
| 976 | Galat | Data Siswa Tidak Ditemukan | Siswa tidak ditemukan | `classes.errors.student_not_found` |
| 1126 | Galat | Tahun Ajaran Belum Valid | Tahun ajaran belum benar | `validation.custom.academic_year.invalid` |
| 1219 | Judul halaman | Monitoring Administrasi Guru | Pantau guru | `school.titles.teacher_monitor` |
| 1241 | Petunjuk | Pilih ukuran kertas yang digunakan saat ekspor atau cetak PDF. | Dipakai saat mengunduh atau mencetak PDF. | `school.hints.paper_size` |
| 1282 | Galat | Pemberitahuan gagal dibatalkan: {…} | Pengingat belum berhasil ditarik. ({…}) | `school.errors.reminder_withdraw_failed` |
| 1463 | Tombol | Lihat Daftar Isi | Daftar isi | `help.actions.contents` |

Spreadsheet ini dibuat sebelum D-210 dan D-302: bila *Teks baru* menulis "siswa" atau "Absensi", nilai key-nya menulis "murid" atau "Kehadiran" (LOC-14).

### 2.3 Ke-37 istilah *Istilah*

- **LOC-21** Istilah punya satu tempat: `lang/id/terms.php`, satu key per istilah baku (*usulan*). Contoh: `terms.rpm` → "RPM", `terms.my_documents` → "Dokumen Saya", `terms.ai_key` → "Kunci AI", `terms.attendance` → "Kehadiran" (D-302). Teks lain memakai istilah lewat parameter hanya bila istilahnya memang berubah-ubah. Dalam teks biasa kata itu ditulis langsung, dan pemeriksaan di bawah menjaga konsistensinya.
- **LOC-22** **Pemeriksaan istilah terlarang.** Sebuah tes membaca kolom *Istilah lama*, yang disimpan sebagai daftar di `tests/Fixtures/banned-terms.php`, dan gagal bila sebuah nilai di berkas `lang/id` mana pun memuat istilah lama. Contohnya: "Download", "Generate", "Modul Ajar", "API Key", "Presensi", "Instansi", "Cloud", "Edit", "Ijin", "...", "&", emoji, atau kata berhuruf kapital semua dengan 3+ huruf (kecuali akronim dalam daftar izin: RPM, LKPD, TKA, PDF, NISN, NIP, BOS, KKTP). Tes melaporkan berkas, key, dan istilah yang disarankan.
- **LOC-23** Istilah baku baru ditambahkan ke tabel Istilah di `design-system.md`, ke `terms.php`, dan ke daftar terlarang dalam PR yang sama.
- **LOC-35** **Kata terlarang (diuji).** Tes yang sama juga menolak kata berikut di nilai `lang/id` mana pun (dan karena itu di setiap cetakan, sebab cetakan membaca `lang/id/export.php`):
  - "tunggakan", "kurang bayar", "belum lunas" (D-300); teksnya "Sumbangan sukarela" dan "Sisa dari kesepakatan"
  - "teguran" (D-311); teksnya "Pengingat"
  - "tanda tangan elektronik" (D-281); teksnya "pengesahan dokumen"
  - "siswa", "P5", "taraf serap" (D-210, D-322); teksnya "murid", "kokurikuler", "daya serap"
  - "Absensi" sebagai label (D-302); teksnya "Kehadiran". Alias pencarian bantuan yang masih menemukan "absensi" dicatat di daftar pengecualian.

### 2.4 Mencari teks yang hilang dan tidak terpakai

- **LOC-24** `php artisan lang:audit` (*usulan*) memindai `app/`, `resources/views/`, dan `resources/js/` untuk `__('…')`, `trans_choice('…')`, `@lang('…')`, `t('…')`, `tc('…')`, dan pencarian `label()` enum. Lalu melaporkan:
  - **hilang:** key yang dipakai di kode tetapi tidak ada di `lang/id`. Ini menggagalkan CI (TR-7).
  - **tidak terpakai:** key di `lang/id` yang tidak dipakai kode mana pun. Ini peringatan, dicantumkan di PR, dan key dihapus dalam tugas yang sama.
  - **dinamis:** key yang disusun saat berjalan (`"enums.{$enum}.{$value}"`). Hanya boleh untuk pola `enums.*`, `export.columns.*`, dan `export.column_aliases.*`, yang dijabarkan audit dari case enum dan daftar kolom ekspor.
- **LOC-25** **Pemeriksaan teks tertulis langsung.** Audit juga menandai teks literal di template Svelte dan view Blade yang tidak dibungkus `t()`/`__()`. Pengecualian yang diizinkan: tanda baca, angka, ikon `aria-hidden`, dan baris bertanda `<!-- i18n-ignore: reason -->`.

## 3. Validasi dan pesan sistem

- **LOC-26** `lang/id/validation.php` berbahasa Indonesia. Berkas ini berawal dari berkas bahasa Indonesia `laravel-lang` berlisensi MIT, disalin sekali dan tidak dipasang sebagai dependensi (*usulan*), lalu disunting sesuai nada di sistem desain:
  - Pesan menyebut apa yang perlu diperbaiki, dengan kata-kata tenang: "Nama murid wajib diisi." dan "NISN harus 10 angka.", tidak pernah "Input tidak valid".
  - Setiap kolom di Form Request punya entri `attributes` dalam huruf kecil ("nama murid", "tanggal lahir"), sehingga pesan tidak pernah menampilkan `student_name`.
  - Pesan khusus formulir masuk ke `custom` (`validation.custom.academic_year.invalid`) atau ke `messages()` Form Request dengan key dari berkas areanya.
- **LOC-27** **Halaman galat** (`errors.php` + `resources/views/errors/*.blade.php`) memakai teks sederhana dan menawarkan langkah berikutnya:
  - 403: "Anda tidak punya akses ke halaman ini."
  - 404: "Halaman tidak ditemukan."
  - 419: "Sesi Anda sudah berakhir. Muat ulang halaman, lalu coba lagi."
  - 429: "Terlalu banyak percobaan. Coba lagi dalam :minutes menit."
  - 500: "Ada masalah di sistem kami. Coba lagi sebentar lagi."
  - 503: "Sigrak sedang dirawat. Coba lagi pukul :time."
- **LOC-28** **Galat pembuatan konten dan AI** memakai teks dari tabel galat di `ai-provider-engine.md`, dengan key per kelas galat: `generation.errors.<error_class>` (misalnya `generation.errors.rate_limited_daily` dengan `:project`, `:time`). Pesan mentah dari penyedia tidak pernah sampai ke UI.
- **LOC-29** **Email.** Setiap `MailMessage` mengisi `subject`, `greeting`, isi, teks tombol, dan `salutation` dari `mail.php`. Bawaan bahasa Inggris Laravel ("Hello!", "Regards") tidak pernah muncul. View email dipublikasikan dan diterjemahkan. Nama pengirimnya "Sigrak".
  - Email tidak pernah memuat data pribadi siswa di luar yang sudah bisa dilihat penerimanya di aplikasi.
  - Email tidak pernah memuat kata sandi atau kartu ujian. Kartu ujian hanya dicetak, tidak pernah dikirim lewat email.
- **LOC-30** **Notifikasi database** (Kabar, pembuatan selesai) menyimpan key terjemahan dan parameternya (`['key' => 'notifications.generation_done', 'params' => ['title' => …]]`), bukan teks jadi (*usulan*). Teks disusun saat dibaca, sehingga perbaikan teks juga sampai ke notifikasi lama.
- **LOC-31** **Filament** (konsol staf) berjalan dengan locale `id` bawaan Filament, ditambah `console.php` untuk label khusus SIGRAK. Teks untuk staf mengikuti nada yang sama, tetapi istilah teknis boleh dipakai (bukan layar guru).

## 4. Aturan ekspor per format

Aturan umum untuk semua format:

- **XP-1** Ekspor dibuat dari JSON konten dokumen atau dari data query, tidak pernah dari tangkapan layar. Ekspor berjalan di antrean `exports` bila bisa memakan waktu lebih dari ~2 detik.
- **XP-2** Setiap label, judul, nama sheet, dan nama berkas berasal dari `lang/id/export.php` atau berkas templatnya (EX-1). Nilai enum memakai labelnya (EX-2).
- **XP-3** Tanggal, angka, dan uang melewati `IndonesianFormat` (§5) atau tipe sel sungguhan (Excel), tidak pernah nilai mentah.
- **XP-4** Ekspor tidak pernah memuat data yang tidak bisa dilihat pemintanya di aplikasi. Query ekspor melewati scope dan policy yang sama dengan layarnya (aturan TEN).
- **XP-5** Agama siswa (D-036) hanya muncul di ekspor untuk orang yang boleh melihatnya (guru kelas, operator, kepala sekolah), dan tidak pernah di ekspor pengawas.

### 4.1 PDF (Gotenberg, D-028, D-031)

- **XP-6** Satu view Blade per PDF: `resources/views/exports/<key>/pdf.blade.php`. View ini meng-extend `exports/layouts/print.blade.php`, dengan `<html lang="id">`. Sebuah paket (RPM + LKPD + rubrik + kunci, atau semua kartu ujian) adalah satu view dengan pemisah halaman, tidak pernah digabung. **Diubah (D-280):** Berkas Administrasi adalah satu-satunya PDF yang digabung (XP-43); dokumen lain tetap satu view → satu PDF.
- **XP-7** **Kertas:** diatur lewat `@page` dari pengaturan cetak. A4 adalah `210mm 297mm`. F4 adalah `215mm 330mm` (D-109). Margin berasal dari profil margin di pengaturan cetak (XP-40).
- **XP-8** **Kop:** gambar milik sekolah atau guru (D-031) diletakkan di bagian atas **halaman pertama saja** (halaman isi pertama setelah sampul, XP-39), selebar area isi, `max-height: 4cm`. Gambar dikirim ke Gotenberg sebagai aset dalam request, tidak pernah sebagai URL (keputusan arsitektur 5).
  - **Kop otomatis (D-284).** Sekolah yang belum punya gambar kop dapat menekan "Buat kop otomatis": SIGRAK menyusun PNG dari logo, nama, alamat, dan kontak di profil sekolah, lalu menyimpannya sebagai kop biasa yang bisa diganti. Cetakan tetap hanya melihat satu gambar.
- **XP-9** **Font** dibundel sebagai berkas dan dikirim bersama request (D-033: tanpa CDN). Sekolah memilih font cetaknya (D-125):

| Pengaturan cetak | Font yang dipakai |
|---|---|
| Arial (bawaan) | Liberation Sans (metriknya setara) |
| Times New Roman | Liberation Serif (metriknya setara) |
| Teks Arab (pengaturan apa pun) | Noto Naskh Arabic |
| Matematika | Font KaTeX |

- **XP-10** **Nomor halaman** memakai HTML footer Gotenberg, dengan `<span class="pageNumber">` dan `<span class="totalPages">` yang diletakkan di `export.print.page_of` ("Halaman :page dari :total"). Kartu ujian, dokumen satu halaman, dan sampul (XP-39) tidak diberi nomor halaman.
- **XP-11** **Penanda tangan** (nama, NIP, jabatan) berasal dari pengaturan cetak sebagai teks, dengan tempat dan tanggal dalam bentuk panjang ("Kudus, 31 Oktober 2026").
- **XP-12** Konten JavaScript (KaTeX, ApexCharts, three.js) harus selesai sebelum ditangkap. View menyetel penanda siap yang ditunggu oleh pemanggil PDF, dan view tanpa JS tidak perlu menunggu.
- **XP-13** **Tanpa watermark.** Tidak ada lagi paket gratis, hanya uji coba 1 minggu dengan semua fitur (D-157, D-160), jadi ekspor tidak pernah memakai watermark.
- **XP-14** Tabel memakai `page-break-inside: avoid` pada baris dan `thead { display: table-header-group }`, sehingga judul kolom berulang di setiap halaman.
- **XP-39** **Sampul (D-283).** Dokumen berbentuk buku dan Berkas Administrasi memakai satu sampul hitam-putih yang sama:
  - Tanpa nomor halaman.
  - Tahun dokumen mengikuti semester: semester 1 → tahun pertama tahun ajaran, semester 2 → tahun kedua, dokumen tahunan → tahun berjalan.
  - Logo sekolah opsional yang diunggah sekolah (`school_print_settings.cover_logo_path`). Tidak ada logo pemerintah yang dibundel.
- **XP-40** **Profil margin dan cetak bolak-balik (D-283).** `school_print_settings.margin_profiles` menyimpan tiga profil dalam mm (atas/kanan/bawah/kiri) yang dapat diubah sekolah. Setiap templat menyebut profilnya:

| Profil | Bawaan (mm) | Dipakai untuk |
|---|---|---|
| Buku administrasi | 15/12/15/25 | Prota, Promes, jurnal, leger, rekap, bagian Berkas |
| Dokumen ajar | 20/20/20/20 | RPM, LKPD, cetakan Bank Soal |
| Lembar tunggal | 15/15/15/15 | Lembar tunggal: lembar jawaban, kartu ujian, berita acara |

Opsi `school_print_settings.duplex` mencerminkan margin kiri dan kanan di halaman genap untuk cetak bolak-balik.
- **XP-41** **Lanskap otomatis (D-283).** Tabel dengan 9 kolom atau lebih dicetak lanskap, kecuali templat menentukan lain.
- **XP-42** **NIHIL (D-283).** Tabel kosong dicetak dengan satu baris selebar tabel bertuliskan "NIHIL", bukan dihilangkan, agar pengawas melihat bagian itu sudah diperiksa.
- **XP-43** **Berkas Administrasi (D-278, D-279, D-280).**
  - Isi: sampul (XP-39), lembar ringkasan kelengkapan (D-263), halaman daftar isi, halaman judul per bagian, dan halaman NIHIL untuk bagian yang kosong. Catatan BK tidak pernah dimasukkan.
  - Setiap bagian dirender sebagai PDF sendiri (berkas yang sama dipakai untuk opsi ZIP), lalu digabung lewat route merge Gotenberg. Daftar isi mengambil nomor halaman dari jumlah halaman tiap bagian, jadi tidak ada render kedua.
  - Keluaran: satu PDF gabungan secara bawaan, ZIP berisi PDF terpisah sebagai opsi, tanpa DOCX. Berkas disimpan 7 hari, lalu dihapus, dan dipakai ulang selama datanya tidak berubah.
  - Unduhan seluruh sekolah oleh kepala sekolah (D-279) berupa satu ZIP dengan satu PDF per guru, dibuat sebagai job di antrean `exports`.
- **XP-44** **Lembar jawaban (LJK, D-287, D-325, D-370).** Satu lembar per murid, dengan profil Lembar tunggal:
  - Kepala lembar: Mata Pelajaran, Kelas, Nama, Nomor. Nomor absen murid dicetak, sehingga jawaban tulisan tangan dapat diberi kode `nomor absen-nomor soal` (mis. `12-3`, D-370).
  - Butir PG dalam dua kolom kotak huruf (A–D, atau A–E bila dipakai) yang disilang murid; PG Kompleks, Benar-Salah, PGK Kategori, dan Menjodohkan memakai gaya kotak huruf yang sama; baris bernomor untuk Isian Singkat dan Uraian.
  - Empat penanda sudut dan satu QR yang hanya berisi `public_id` acak milik lembar itu (tanpa data murid di kertas). Versi tata letak disimpan di server.
- **XP-45** **Tanda pengesahan (D-281).** RPM, Prota, Promes, rencana asesmen, dan Berkas Administrasi mencetak QR, kode 10 karakter, dan "Disahkan melalui SIGRAK, [tanggal]" hanya selama revisi aktif sama dengan revisi yang disahkan. Pada Berkas, satu QR itu ada di kolom tanda tangan kepala sekolah pada lembar ringkasan. QR mengarah ke `/verifikasi/{code}`, yang hanya berfungsi setelah masuk. Cetakan tidak pernah menyebut "tanda tangan elektronik" (LOC-35).

### 4.2 DOCX (PhpWord)

- **XP-15** Setiap templat punya pembangun DOCX di `app/Content/Export/Docx/<Key>DocxBuilder.php` yang menyusun tata letak SIGRAK sendiri lewat kode (D-031). Tidak ada templat Word yang diunggah dan tidak ada konversi Word ke PDF.
- **XP-16** Bahasa dokumen diatur ke `id-ID` (pemeriksa ejaan dan pemenggalan kata). Ukuran halaman dan margin mengikuti pengaturan cetak (nilai yang sama dengan XP-7, profil margin yang sama dengan XP-40).
- **XP-17** Gambar kop masuk ke **header halaman pertama**, dan halaman lain mendapat header kosong. Penanda tangan berupa teks biasa.
- **XP-18** Font diatur berdasarkan nama ("Arial" atau "Times New Roman", "Noto Naskh Arabic"; D-125), karena Word mengganti font di komputer guru. Teks Arab memakai gaya paragraf RTL (lihat §7).
- **XP-19** Judul memakai gaya heading Word, sehingga guru mendapat panel navigasi dan daftar isi. Teks isi tidak memakai format langsung.

### 4.3 Excel dan CSV (Laravel Excel)

- **XP-20** Ekspor Excel adalah kelas di `app/Exports/` (data administrasi) atau `app/Content/Export/` (dokumen templat). Kelas ini mengimplementasikan `WithHeadings`, `WithMapping`, `WithColumnFormatting`, `WithStyles`, dan `WithTitle`. Judul kolom berasal dari `__('export.columns.*')`:

```php
public function headings(): array
{
    return array_map(fn (string $column) => __("export.columns.{$column}"), self::COLUMNS);
}

public function columnFormats(): array
{
    return [
        'C' => 'dd/mm/yyyy',          // date cell (Excel date serial)
        'E' => '"Rp"#,##0',           // Rupiah, no decimals
        'F' => '#,##0.00',            // score
    ];
}
```

- **XP-21** **Tipe sungguhan:**
  - Angka berupa sel numerik, tidak pernah string yang sudah diformat.
  - Tanggal berupa serial tanggal Excel (`Date::dateTimeToExcel()`), dikonversi ke `Asia/Jakarta` lebih dulu.
  - Boolean berupa label ("Ya"/"Tidak").
  - Excel di sistem berbahasa Indonesia otomatis menampilkan `.` untuk ribuan dan `,` untuk desimal, jadi kode format tetap dalam bentuk netral Excel.
  - Id numerik panjang (NISN, NIP, NPSN) berupa sel **teks**, agar nol di depan tidak hilang.
- **XP-22** **Tata letak sheet:**
  - Nama sheet berbahasa Indonesia dan paling banyak 31 karakter ("Leger", "Kisi-kisi", "Analisis per TP").
  - Baris 1 berisi judul kolom, dengan panel dibekukan dan filter otomatis.
  - Blok judul (sekolah, kelas, periode) diletakkan di sheet "Info" tersendiri, agar sheet data tetap bisa difilter.
- **XP-23** **Injeksi rumus.** Sel teks yang diawali `=`, `+`, `-`, `@`, tab, atau CR, dan berasal dari teks pengguna atau AI, ditulis sebagai string biasa (tipe string eksplisit di XLSX, awalan `'` di CSV).
- **XP-24** **CSV** (*usulan*):
  - Pemisahnya `;`, encoding UTF-8 **dengan BOM**, dan akhir baris CRLF. Excel di Windows berbahasa Indonesia, yang pemisah daftarnya `;`, lalu bisa langsung membukanya dengan karakter yang benar.
  - Angka tanpa pemisah ribuan dan memakai `,` untuk desimal. Tanggal `dd/mm/yyyy`.
  - XLSX adalah unduhan bawaan. CSV hanya ditawarkan bila pengguna memintanya.
  - Berkas untuk sistem lain (misalnya impor e-Rapor) mengikuti spesifikasi sistem itu, dan spesifikasinya dicatat di kelas ekspornya.
- **XP-25** **Impor** (siswa, nilai) menerima judul kolom yang sama dengan ekspor padanannya, dan menerima XLSX maupun CSV (`;` atau `,`, dengan atau tanpa BOM). Setiap baris divalidasi dan galatnya dilaporkan dalam bahasa Indonesia dengan nomor baris ("Baris 12: NISN harus 10 angka."). Berkas templat impor dibuat dari daftar kolom yang sama dengan ekspornya, sehingga guru yang mengunduh templat tidak pernah mengetik judul kolom. Impor membaca kembali apa yang ditulis ekspornya (D-150):
  - **Pencocokan judul kolom.** Judul kolom dibandingkan setelah spasi di tepi dibuang, spasi ganda diringkas, dan huruf besar/kecil serta tanda `*` atau `:` di akhir diabaikan, sehingga "nama  siswa" cocok dengan "Nama Siswa". Urutan kolom tidak berpengaruh. Judul yang tidak dikenal dilaporkan dengan namanya beserta judul yang diharapkan ("Kolom 'Nama Murid' tidak dikenal. Kolom yang dipakai: Nama Siswa, NISN, …"). Kolom wajib yang tidak ada menghentikan impor sebelum satu baris pun disimpan.
  - **Judul lama tetap berlaku.** Bila label judul kolom diubah, label lamanya dipindah ke `export.column_aliases.<column>` di `lang/id/export.php`, sehingga templat yang sudah diisi sebelumnya tetap bisa diimpor. Alias tidak pernah dihapus.
  - **Isi sel berbahasa Indonesia.** Sel himpunan tetap menerima label yang ditulis ekspor ("Hadir" → `present`, "Ya"/"Tidak" → `true`/`false`), dicocokkan dengan cara yang sama seperti judul kolom. Tanggal menerima teks `dd/mm/yyyy` dan sel tanggal Excel; angka menerima desimal koma (`76,5`) dan sel numerik.

### 4.4 Cetakan dan ekspor dari keputusan berikutnya

Setiap cetakan di bawah mengikuti XP-6…XP-14 dan XP-39…XP-45; versi Excel mengikuti XP-20…XP-23 dan dapat diimpor kembali bila ada impornya (D-150).

| Cetakan | Isi | Format | Keputusan |
|---|---|---|---|
| Promes Realisasi | Tampilan kedua Promes: minggu rencana dibanding minggu terlaksana per topik, disusun dari jurnal | PDF | D-274 |
| Analisis Daya Serap | Tata letak cetak (dan alias pencarian) Analisis per TP, dengan Kelompok belajar per TP | PDF, XLSX | D-295 |
| Leger | Dicetak dan diekspor hanya dari versi terkunci (XP-46) | PDF, XLSX | D-292 |
| Rekap kehadiran | Per minggu, bulan, dan semester, per kelas | PDF, XLSX | D-303 |
| Daftar piket | Kelompok piket per hari sekolah, dengan tugas dan rotasi | PDF | D-297 |
| Berita acara penyerahan tabungan | Saldo yang diserahkan saat lulus atau mutasi keluar; penanda tangan wali kelas, orang tua atau wali, kepala sekolah | PDF | D-301 |
| Laporan kas kelas | Pemasukan (termasuk sumbangan sukarela), pengeluaran, saldo; total secara bawaan, rincian per murid hanya bila wali kelas memilihnya | PDF, teks `wa.me` | D-299 |

- **XP-46** **Leger terkunci adalah satu-satunya sumber (D-292).** Cetakan rapor, cetakan leger, dan ekspor e-Rapor/RDM membaca versi leger yang terkunci, tidak pernah nilai yang sedang berjalan. Sebelum leger terkunci, ketiganya tidak ditawarkan.
- **XP-47** **Hasil TKA (D-371).** Ekspor hasil Simulasi TKA menulis kategori resmi (Baik, Memadai, Kurang) dan predikat Istimewa (nilai ≥ 95,00) di dua kolom terpisah, dibaca dari `exam_results`, agar cocok dengan SHTKA. Nilai memakai `score()` pada skala 0–100. Empat tingkat yang tampil di layar tidak disimpan sebagai satu nilai. Lembar Info (XP-22) memuat catatan hasil latihan yang sama dengan UI.
- **XP-48** **Ekspor e-Rapor SD dan RDM (D-218).** Dibuat tepat setelah slice Asesmen, dalam tata letak impor masing-masing sistem, dari leger terkunci (XP-46). Profil Jenjang menentukan tujuannya (e-Rapor SD untuk SD, RDM untuk MI). Ekspor ini mengikuti pengecualian XP-24 untuk berkas sistem lain.

## 5. Helper format

- **XP-26** `App\Support\IndonesianFormat` (PHP) dan `resources/js/lib/format.ts` (TS) adalah satu-satunya pemformat (EX-6). Keduanya punya API yang sama dan keluaran yang sama, dan sebuah tes memberi keduanya fixture yang sama.
- **XP-27** Waktu yang tersimpan sudah dalam WIB (DB-10, D-165). Setiap metode tetap mengonversi ke `Asia/Jakarta` sebelum memformat, sehingga masukan ISO-8601 dengan offset lain tetap terformat benar. Masukannya Carbon atau string ISO-8601.

| PHP (`IndonesianFormat::`) | TS (`format.`) | Keluaran |
|---|---|---|
| `date($d)` | `date(d)` | `31/10/2026` |
| `dateLong($d)` | `dateLong(d)` | `31 Oktober 2026` |
| `dateShort($d)` | `dateShort(d)` | `31 Okt 2026` |
| `dayDate($d)` | `dayDate(d)` | `Sabtu, 31 Oktober 2026` |
| `time($d)` | `time(d)` | `14.00` |
| `timeZoned($d)` | `timeZoned(d)` | `14.00 WIB` |
| `dateTime($d)` | `dateTime(d)` | `31/10/2026 14.00` |
| `number($n, $decimals = 0)` | `number(n, decimals = 0)` | `1.234.567` / `1.234.567,89` |
| `rupiah($amount)` | `rupiah(amount)` | `Rp1.234.567` |
| `percent($ratio, $decimals = 0)` | `percent(ratio, decimals = 0)` | `76,7%` (`$ratio` 0–1) |
| `score($n)` | `score(n)` | `76,67` (dua desimal, sesuai `decimal(5,2)`) |

- **XP-28** Rupiah ditulis tanpa spasi setelah `Rp` dan tanpa desimal, mengikuti kaidah EYD: `Rp1.234.567`. Helper menyusun string itu sendiri dan tidak bergantung pada pola mata uang ICU, yang berbeda antarversi ICU (*usulan*).
- **XP-29** Nama bulan dan hari berasal dari locale `id` Carbon (`translatedFormat`) dan dari `Intl` dengan `id-ID`. Sebuah tes mengunci keduanya: Januari … Desember, Senin … Minggu, dan "Jumat" tanpa apostrof.
- **XP-30** Jam memakai titik (14.00), sesuai kebiasaan Indonesia. "WIB" ditambahkan hanya bila pengguna mungkin berada di zona waktu lain, atau bila jamnya mengacu pada kejadian di luar (waktu reset kuota AI, jendela ujian). Di tempat lain, jam biasa sudah cukup.

## 6. Nama berkas ekspor

- **XP-31** Polanya `<kind>-<context>-<date>.<ext>`:
  - Semua bagian berasal dari kata di `export.files.*` dan dijadikan slug dengan `Str::slug($part, '-', 'id')`.
  - Tanggal dalam format ISO (`2026-10-31`) agar berkas urut.
  - Panjang nama paling banyak 80 karakter.
  - Contoh: `rpm-ipas-kelas-4a-2026-10-31.pdf`, `leger-kelas-4a-semester-1-2026-2027.xlsx`, `kartu-login-simulasi-tka-6a-2026-11-03.pdf`.
  - Cetakan dari §4.4: `berkas-administrasi-semester-1-2026-2027.pdf` (atau `.zip`), `promes-realisasi-ipas-kelas-4a-semester-1-2026-2027.pdf`, `analisis-daya-serap-ipas-kelas-4a-2026-11-10.pdf`, `rekap-kehadiran-kelas-4a-2026-11-10.xlsx`, `daftar-piket-kelas-4a-2026-11-10.pdf`, `laporan-kas-kelas-4a-2026-11-10.pdf`, `ljk-matematika-kelas-4a-2026-11-10.pdf`.
- **XP-32** **Tanpa data pribadi siswa di nama berkas.** Tanpa nama, NISN, atau tanggal lahir. Berkas per siswa memakai kelas dan nomor absen (`rapor-kelas-6a-no-07-semester-1.pdf`, `berita-acara-penyerahan-tabungan-kelas-6a-no-07-2027-06-20.pdf`), dan kumpulan semua siswa berupa satu berkas atau satu ZIP.
- **XP-33** Nama guru juga tidak dimasukkan ke nama berkas. Judul dokumen boleh dipakai (guru sendiri yang mengetiknya) setelah dijadikan slug.
- **XP-34** Respons unduhan mengirim `filename` ASCII sekaligus `filename*` UTF-8 di `Content-Disposition`.

## 7. RTL dan Arab di UI dan ekspor

Teks CP MI (D-039) dan materi PAI mencampur bahasa Indonesia dan Arab. Aturannya:

- **LOC-32** Teks yang mungkin memuat Arab di tengah kalimat (`text` CP, keluaran AI untuk mata pelajaran PAI, masukan guru) ditampilkan di elemen dengan `dir="auto"`, dan nilai di tengah kalimat dibungkus `<bdi>`, agar tanda baca dan angka tetap di tempatnya. String tidak pernah dibalik atau diurutkan ulang secara manual.
- **LOC-33** `arabic_passages` (D-039) berupa blok terpisah dengan `lang="ar" dir="rtl"` dan kelas font Arab (`font-arabic`: Noto Naskh Arabic, dibundel), sekitar 1,25× ukuran teks isi agar mudah dibaca.
- **LOC-34** Dalam konteks teks biasa (sel CSV, teks email, notifikasi), potongan Arab di dalam teks Indonesia dibungkus isolat Unicode U+2067 (RLI) … U+2069 (PDI).
- **XP-35** **PDF:** aturan HTML sama dengan UI (Chromium membentuk huruf Arab dengan benar). Font Arab disematkan lewat `@font-face` (XP-9).
- **XP-36** **DOCX:** paragraf Arab memakai gaya paragraf RTL dengan flag bidi dan `lang="ar-SA"`. Arab di tengah kalimat mendapat run sendiri dengan flag RTL.
- **XP-37** **Excel:** sel yang hanya berisi Arab rata kanan dengan urutan baca RTL. Sel campuran memakai urutan baca konteks.
- **XP-38** Sebuah tes merender satu baris CP yang punya bacaan Arab ke HTML dan DOCX, lalu memeriksa bahwa `dir`/`lang` (HTML) dan flag RTL (XML DOCX) ada.

## 8. Pemeriksaan yang menjaga aturan ini

| Pemeriksaan | Berjalan di | Gagal bila |
|---|---|---|
| `lang:audit` key hilang (LOC-24) | CI (Pest) | Key yang dipakai di PHP, Blade, atau Svelte tidak ada di `lang/id` |
| `lang:audit` teks tertulis langsung (LOC-25) | CI | Ada teks UI literal di luar `t()`/`__()` di Svelte atau Blade |
| Key tidak terpakai (LOC-24) | CI (peringatan) | Ada key yang tidak dipakai siapa pun (dicantumkan, diperbaiki di tugas yang sama) |
| Istilah terlarang (LOC-22) | CI (Pest) | Nilai di `lang/id` memuat istilah *Istilah* lama, "...", "&", emoji, atau huruf kapital semua |
| Kata terlarang (LOC-35) | CI (Pest) | Nilai di `lang/id` memuat tunggakan, kurang bayar, belum lunas, teguran, tanda tangan elektronik, siswa, P5, taraf serap, atau Absensi di luar daftar pengecualian pencarian bantuan |
| Aturan cetak (XP-41, XP-42) | Pest | Tabel 9+ kolom dirender tegak tanpa pengaturan templat, atau tabel kosong tanpa baris NIHIL |
| Ekspor TKA (XP-47) | Pest | Kategori dan predikat tidak berada di kolom terpisah, atau nilai 95,00 tidak mendapat predikat Istimewa |
| Kelompok khusus server (LOC-10) | Feature test | `export`, `mail`, `validation`, … muncul di prop `translations` |
| Kesamaan parameter | Pest | `:params` sebuah key berbeda dengan yang dikirim di pemanggilnya (diperiksa untuk pemanggilan literal) |
| Label enum | Pest arch | Ada case enum tanpa label `enums.*` |
| Snapshot judul kolom ekspor | Pest | Judul kolom ekspor berbeda dengan snapshot tersimpan (`tests/Snapshots/exports/<name>.json`); snapshot memperlihatkan judul berbahasa Indonesia untuk ditinjau |
| Impor bolak-balik (XP-25) | Pest | Mengimpor berkas hasil ekspornya sendiri, atau berkas yang sama dengan judul alias lama, huruf besar/kecil berbeda, dan spasi berlebih, tidak menghasilkan baris yang sama |
| Tipe sel Excel (EX-4) | Pest | Kolom angka, tanggal, atau Rupiah berisi string, atau NISN/NIP berupa angka |
| Format CSV (XP-24) | Pest | Berkas tidak punya BOM, memakai `,` sebagai pemisah, atau ada sel diawali `=` tanpa pengamanan |
| Kesamaan pemformat (XP-26) | Pest + Vitest | Pemformat PHP dan TS memberi keluaran berbeda untuk fixture bersama |
| Nama berkas (XP-31, XP-32) | Pest | Nama yang dibuat lebih dari 80 karakter, bukan slug, atau memuat nama atau NISN siswa dari fixture |
| Render RTL (XP-38) | Pest | Fixture Arab tidak punya `dir`/`lang` (HTML) atau flag RTL (DOCX) |

Semuanya bagian dari jalannya CI GIT-4 (GitHub Actions).

## Keputusan untuk dicatat

Pilihan baru di panduan ini. Nomornya diberikan saat integrasi.

1. **Kelompok terjemahan per halaman.** `common`, `nav`, `terms`, dan `enums` selalu dibagikan. Halaman menambah berkas lewat middleware rute `translations:<files>`. Berkas khusus server ditolak, dan prop-nya lazy, hanya dikirim pada kunjungan penuh (memperjelas D-080).
2. **Berkas glosarium + tes istilah terlarang.** Ke-37 istilah *Istilah* ada di `lang/id/terms.php`, dan sebuah tes menolak istilah lama di nilai `lang/id` mana pun.
3. **Berkas pelacakan spreadsheet.** `docs/data/ui-copy/key-map.csv` mencatat, per baris spreadsheet, key-nya atau alasan baris itu dibuang. Diisi per layar.
4. **Perintah `lang:audit`** untuk teks yang hilang, tidak terpakai, dan tertulis langsung. Key yang hilang menggagalkan CI.
5. **Pesan validasi** berawal dari salinan sekali berkas bahasa Indonesia `laravel-lang` (MIT), disunting sesuai nada Sigrak. Tidak dipasang sebagai paket.
6. **Notifikasi database menyimpan key + parameter**, disusun saat dibaca.
7. **Format CSV:** pemisah `;`, UTF-8 dengan BOM, CRLF, desimal koma. XLSX adalah bawaan, dan berkas untuk sistem luar mengikuti spesifikasinya sendiri.
8. **Format Rupiah** `Rp1.234.567` (tanpa spasi, tanpa desimal), disusun helper kita dan bukan oleh ICU. Baris Uang di `coding-standards.md` §8 kini merujuk `IndonesianFormat::rupiah()` agar sesuai.
9. **Font cetak:** Liberation Sans/Serif sebagai pengganti Arial/Times New Roman yang metriknya setara di Gotenberg, Noto Naskh Arabic untuk Arab, semuanya dibundel.
10. **Kertas F4** berukuran 215 × 330 mm.
11. **Berkas ekspor per siswa** diberi nama berdasarkan kelas dan nomor absen, tidak pernah nama atau NISN siswa.
12. **"WIB"** hanya ditampilkan untuk jam yang terkait kejadian di luar atau zona waktu lain (reset kuota, jendela ujian).

## Pertanyaan terbuka

- [x] Ukuran F4: 215 × 330 mm (D-109).
- [ ] Format impor e-Rapor SD dan RDM (kolom, jenis berkas) masih belum diketahui (pertanyaan terbuka model data). Ekspornya sendiri sudah diputuskan (D-218, XP-48); pengecualian XP-24 berlaku untuk tata letaknya setelah diketahui.
- [ ] Nama berkas di dalam ZIP Berkas seluruh sekolah untuk kepala sekolah (D-279): XP-33 melarang nama guru di nama berkas, padahal kepala sekolah perlu membedakan berkasnya. Menunggu keputusan pemilik (misalnya nomor urut plus lembar daftar di dalam ZIP).
- [x] Font cetak: Arial atau Times New Roman, dipilih sekolah; tidak ada yang lain (D-125).
- [x] Font klien ujian: ukuran bawaan standar, pengatur A−/A/A+ tetap ada (D-121).
