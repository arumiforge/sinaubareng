# Export and Localization Guide

- **Status:** draft
- **Task:** P2-07
- **Last updated:** 2026-10-31

## Purpose

This guide describes how SIGRAK's Indonesian UI text and its Indonesian exports (PDF, DOCX, Excel, CSV) are built. The goal is that every screen and every export follows the same rules from the first line of code. It is for developers and agents.

It builds on [coding-standards.md](coding-standards.md) sections 7–8 (TR-*, EX-* rules) and does not change them. Rules here are numbered `LOC-*` (localization) and `XP-*` (export). Items marked *proposed* are listed under [Decisions to record](#decisions-to-record).

Sources: [`CLAUDE.md`](../../CLAUDE.md) (language policy, D-002), [`content-engine.md#export`](content-engine.md#export), [`design-system.md`](design-system.md#ui-writing-rules), [`Parafrase_Teks_UI_Sigrak.xlsx`](../sources/Parafrase_Teks_UI_Sigrak.xlsx), D-028, D-031, D-033, D-039, D-080, D-094.

## 1. `lang/id` layout

### Files

One PHP file per area. The locale and the fallback are both `id`, and there is no `lang/en` (TR-8).

| File | Holds | Loaded for |
|---|---|---|
| `common.php` | Shared actions, states, generic errors, pagination, confirm and toast patterns | Every page |
| `nav.php` | Menu names, page titles in the shell | Every school-app page |
| `terms.php` | The glossary: one key per standard term (see §2.3) | Referenced by other files; also every page |
| `enums.php` | Labels for every backed enum: `enums.<enum_snake>.<value>` | Every page |
| `auth.php`, `account.php` | Sign-in, invitations, password, profile, role switch | Their pages |
| `today.php`, `classes.php`, `attendance.php`, `journals.php`, `student_notes.php` | Hari Ini, Kelas Saya, Di Kelas | Their pages |
| `documents.php`, `templates.php`, `rpm.php`, `bank_soal.php`, `lkpd.php`, `quiz_battle.php`, `tka_package.php`, `generation.php` | Buat, Dokumen Saya, one file per template (D-024), generation status | Their pages |
| `assessments.php`, `reports.php` | Asesmen, rapor | Their pages |
| `ai_keys.php` | Kunci AI Saya, consent text (D-023) | Their pages |
| `school.php`, `supervisor.php` | School portal (principal, operator), supervisor portal | Their pages |
| `exam.php` | Exam site only (D-027, D-094): token, exam, finish, child-friendly copy | Exam client only |
| `help.php` | Bantuan | Help pages |
| `console.php` | Staff console labels beyond Filament's own `id` locale | Filament only |
| `export.php` | Export headings, sheet names, file-name words, print labels ("Halaman :page dari :total") | Export jobs; never sent to the browser |
| `mail.php`, `notifications.php` | Mail subjects and lines, database notification text | Server only |
| `validation.php` | Laravel validation messages + `attributes` | Server only |
| `errors.php` | HTTP error pages (403, 404, 419, 429, 500, 503) | Error views |

### Key naming

- **LOC-1** A key is `<file>.<group>.<name>`, all English snake_case, and it names the meaning, not the words (`journals.toasts.saved`, not `journals.jurnal_tersimpan`).
- **LOC-2** Use these groups inside a file, so keys are predictable:

| Group | For | Example key |
|---|---|---|
| `titles` | Page and panel titles | `journals.titles.index` |
| `actions` | Buttons and links | `common.actions.download_pdf` |
| `fields` | Form labels; `placeholders` and `hints` for their helpers | `bank_soal.fields.duration_minutes` |
| `columns` | Table and export column headings | `export.columns.created_at` |
| `options` | Choices that are not enums | `common.options.semester_even` |
| `toasts` | One-line success messages | `school.toasts.confirmation_sent` |
| `errors` | Error messages (what happened + what to do) | `rpm.errors.delete_failed` |
| `dialogs.<name>` | `title`, `body`, `confirm`, `cancel` of one dialog | `classes.dialogs.replace_homeroom.body` |
| `empty` | Empty states | `classes.empty.students` |
| `progress` | Status while working | `generation.progress.step` |

- **LOC-3** Depth is at most three segments after the file name. Do not nest per screen widget.
- **LOC-4** Reuse before you add. Generic actions (`common.actions.save`, `.download`, `.back`) live in `common.php`. A specific button names its object ("Simpan jurnal"), so it gets its own key in the area file.

### Parameters and plurals

- **LOC-5** Parameters use `:name` (TR-3). Name them for what they hold (`:class`, `:time`, `:count`), never `:0` or `:x`. A value that needs formatting is formatted **before** it is passed (`IndonesianFormat::time($until)`), never inside the string.
- **LOC-6** Indonesian nouns do not inflect, so `trans_choice` is used only when zero or one reads differently:

```php
// lang/id/classes.php
'students_count' => '{0} Belum ada siswa|[1,*] :count siswa',

trans_choice('classes.students_count', $n, ['count' => IndonesianFormat::number($n)]);
```

- **LOC-7** Never build a sentence from pieces (`__('a') . ' ' . $name . __('b')`). Word order belongs to the translation.
- **LOC-8** Strings with markup (a bold word, a link) use parameters for the markup-free parts and a Svelte component for the markup. Translations never contain HTML.

### Shared Inertia prop and `t()` (D-080)

The server sends only the groups a page needs. The browser never loads all of `lang/id`.

- **LOC-9** `HandleInertiaRequests::share()` always shares `common`, `nav`, `terms`, and `enums` under the prop `translations`. A page adds its own files through route middleware *proposed*:

```php
Route::get('/journals', [JournalController::class, 'index'])
    ->middleware('translations:journals,attendance')
    ->name('journals.index');
```

- **LOC-10** The middleware merges the named files into the `translations` prop, flattened to dot keys (`journals.toasts.saved`). Server-only files (`export`, `mail`, `notifications`, `validation`, `errors`, `console`) are refused, so they cannot leak to the browser.
- **LOC-11** On partial reloads (polling, `only: [...]`) the `translations` prop is not requested again. It is a lazy prop that is included on full visits only.
- **LOC-12** The exam client (D-094) has its own root view and its own share list: `common` and `exam` only, never `nav` or school text.
- **LOC-13** In Svelte, one helper reads the prop. It has the same semantics as Laravel's `__()`, plus `tc()` for `trans_choice`:

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

## 2. From the paraphrase spreadsheet to keys

`Parafrase_Teks_UI_Sigrak.xlsx` has 1,499 rows in *Parafrase* (columns: No, Halaman, Jenis teks, Teks lama, Teks baru, Catatan, Status, Sumber) and 37 terms in *Istilah*. *Ringkasan* only counts rows per page. The spreadsheet stays in `docs/sources/` unchanged. Moving its strings into `lang/id` happens screen by screen, when each screen is built ([design system](design-system.md#copy-source-and-localization)).

### 2.1 Mapping rule

- **LOC-14** *Teks baru* is the starting copy. *Teks lama* is never used. The *Catatan* note and the [terms](design-system.md#terms) win over *Teks baru* where they differ (for example, "Templat" vs "template" once decided).
- **LOC-15** **File** comes from the new screen that shows the text, not from the EDUNEXA page. The old page is only a hint:

| *Halaman* (EDUNEXA) | Usual file(s) |
|---|---|
| Semua halaman | `common`, `terms` |
| Beranda & akun | `auth`, `account`, `today`, `classes`, `ai_keys` |
| Generator RPM | `rpm`, `documents`, `generation` |
| Bank Soal | `bank_soal` |
| Worksheet/LKPD | `lkpd` |
| Administrasi Kelas | `classes`, `attendance`, `journals`, `student_notes`, `assessments`, `reports`, and the class finance and inventory files when those screens exist |
| Portal Kepala Sekolah | `school` |
| Portal Pengawas | `supervisor` |
| Super Admin | `console`, only where the staff console still needs the string |
| Tutorial | `help` |

- **LOC-16** **Group** comes from *Jenis teks*:

| *Jenis teks* | Group |
|---|---|
| Tombol, Tautan | `actions` |
| Label formulir, Label, Placeholder, Petunjuk, Tooltip | `fields`, `placeholders`, `hints` |
| Judul kolom | `columns` |
| Pilihan | `options`, or `enums.*` if it is a fixed set |
| Galat, Galat formulir, Peringatan | `errors` (form errors that match a validation rule go to `validation.php`) |
| Notifikasi, Pesan informasi | `toasts` |
| Judul dialog, Isi dialog, Konfirmasi | `dialogs.<name>.title/body/confirm` |
| Pesan kosong | `empty` |
| Status proses, Status | `progress` |
| Judul halaman, Judul panel, Subjudul, Judul menu, Kartu menu, Tab | `titles`, or `nav.*` for menus |

- **LOC-17** **Placeholders.** Each `{…}` becomes a named `:param`. A `({…})` that held a raw technical error message is dropped from the UI text. That detail goes behind "Lihat detail" (design-system rule 4).
- **LOC-18** **One key per meaning.** Rows with the same *Teks baru* and the same meaning share one key ("Simpan" appears on many pages → `common.actions.save`).
- **LOC-19** **Dropped rows.** Strings for features that no longer exist (Cloud sync, the Super Admin dashboard, Bank Modul) get no key.
- **LOC-20** **Tracking.** A mapping file records each row's outcome, so nobody needs to diff the spreadsheet by hand (*proposed*). The file is `docs/data/ui-copy/key-map.csv`, with columns `no`, `key`, `status` ∈ {`mapped`, `merged`, `dropped`, `pending`}, and `note`. The task that builds a screen fills in that screen's rows.

### 2.2 Sample mapping (23 rows)

| No | *Jenis teks* | Teks lama | Teks baru | Key |
|---|---|---|---|---|
| 2 | Tombol | Download | Unduh | `common.actions.download` |
| 5 | Tombol | Edit | Ubah | `common.actions.edit` |
| 27 | Tombol | PDF | Unduh PDF | `common.actions.download_pdf` |
| 32 | Pilihan | Genap (2) | Semester 2 (Genap) | `enums.semester.2` |
| 60 | Galat | Impor gagal: {…} | File belum berhasil diimpor. Pastikan file berasal dari Sigrak dan belum diubah. ({…}) | `common.errors.import_failed` (detail dropped, LOC-17) |
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

### 2.3 The 37 *Istilah* terms

- **LOC-21** The terms have one home: `lang/id/terms.php`, one key per standard term (*proposed*). Examples: `terms.rpm` → "RPM", `terms.my_documents` → "Dokumen Saya", `terms.ai_key` → "Kunci AI", `terms.attendance` → "Absensi". Other strings use the term through a parameter only where the term really varies. In normal copy the word is written out, and the check below keeps it consistent.
- **LOC-22** **Banned-terms check.** A test reads the *Istilah lama* column, kept as a list in `tests/Fixtures/banned-terms.php`, and fails when a value in any `lang/id` file contains an old term. Examples: "Download", "Generate", "Modul Ajar", "API Key", "Presensi", "Instansi", "Cloud", "Edit", "Ijin", "...", "&", an emoji, or ALL-CAPS words of 3+ letters (except acronyms on an allow-list: RPM, LKPD, TKA, PDF, NISN, NIP, BOS, KKTP). The test reports the file, the key, and the suggested term.
- **LOC-23** A new standard term is added to the design system's [Terms](design-system.md#terms) table, `terms.php`, and the banned list in the same PR.

### 2.4 Finding missing and unused strings

- **LOC-24** `php artisan lang:audit` (*proposed*) scans `app/`, `resources/views/`, and `resources/js/` for `__('…')`, `trans_choice('…')`, `@lang('…')`, `t('…')`, `tc('…')`, and enum `label()` lookups. It then reports:
  - **missing:** a key used in code but not in `lang/id`. This fails CI (TR-7).
  - **unused:** a key in `lang/id` that no code uses. This is a warning, listed in the PR, and the key is removed in the same task.
  - **dynamic:** a key built at runtime (`"enums.{$enum}.{$value}"`). It is allowed only for the `enums.*` and `export.columns.*` patterns, which the audit expands from the enum cases and the export column lists.
- **LOC-25** **Hard-coded text check.** The audit also flags literal text in Svelte templates and Blade views that is not wrapped in `t()`/`__()`. The allowed exceptions are punctuation, numbers, `aria-hidden` icons, and lines marked `<!-- i18n-ignore: reason -->`.

## 3. Validation and system messages

- **LOC-26** `lang/id/validation.php` is Indonesian. It starts from the MIT-licensed `laravel-lang` Indonesian file, copied once and not installed as a dependency (*proposed*), and is then edited to the [voice](design-system.md#voice-and-tone):
  - Messages say what to fix, in calm words: "Nama siswa wajib diisi." and "NISN harus 10 angka.", never "Input tidak valid".
  - Every field in a Form Request has an `attributes` entry in lowercase ("nama siswa", "tanggal lahir"), so messages never show `student_name`.
  - Form-specific messages go in `custom` (`validation.custom.academic_year.invalid`) or in the Form Request's `messages()` with keys from the area file.
- **LOC-27** **Error pages** (`errors.php` + `resources/views/errors/*.blade.php`) use plain copy and offer a next step:
  - 403: "Anda tidak punya akses ke halaman ini."
  - 404: "Halaman tidak ditemukan."
  - 419: "Sesi Anda sudah berakhir. Muat ulang halaman, lalu coba lagi."
  - 429: "Terlalu banyak percobaan. Coba lagi dalam :minutes menit."
  - 500: "Ada masalah di sistem kami. Coba lagi sebentar lagi."
  - 503: "Sigrak sedang dirawat. Coba lagi pukul :time."
- **LOC-28** **Generation and AI errors** use the copy from the [AI engine error table](ai-provider-engine.md#error-handling), keyed by error class: `generation.errors.<error_class>` (for example `generation.errors.rate_limited_daily` with `:project`, `:time`). The raw provider message never reaches the UI.
- **LOC-29** **Mail.** Every `MailMessage` sets `subject`, `greeting`, the lines, the action text, and `salutation` from `mail.php`. Laravel's English defaults ("Hello!", "Regards") never appear. The mail views are published and translated. The sender name is "Sigrak".
  - Mail never contains student personal data beyond what the recipient may already see in the app.
  - Mail never contains a password or an exam card. Exam cards are printed only, never mailed.
- **LOC-30** **Database notifications** (Kabar, generation finished) store a translation key and its params (`['key' => 'notifications.generation_done', 'params' => ['title' => …]]`), not the rendered text (*proposed*). The text is rendered when it is read, so a copy fix also reaches old notifications.
- **LOC-31** **Filament** (staff console) runs with Filament's own `id` locale, plus `console.php` for SIGRAK-specific labels. Staff-facing text follows the same voice, but technical terms are allowed there (it is not a teacher screen).

## 4. Export rules per format

Common rules for all formats:

- **XP-1** An export is rendered from the document's content JSON or from query data, never from a screenshot. It runs on the `exports` queue when it can take more than ~2 s.
- **XP-2** Every label, heading, sheet name, and file name comes from `lang/id/export.php` or the template's file (EX-1). Enum values use their labels (EX-2).
- **XP-3** Dates, numbers, and money go through `IndonesianFormat` (§5) or real cell types (Excel), never raw values.
- **XP-4** Exports never include data the requesting user cannot see in the app. The export query goes through the same scope and policy as the screen (TEN rules).
- **XP-5** Student religion (D-036) appears only in exports meant for people allowed to see it (class teachers, operator, principal), and never in supervisor exports.

### 4.1 PDF (Gotenberg, D-028, D-031)

- **XP-6** One Blade view per PDF: `resources/views/exports/<key>/pdf.blade.php`. It extends `exports/layouts/print.blade.php`, with `<html lang="id">`. A package (RPM + LKPD + rubric + key, or all exam cards) is one view with page breaks, never a merge.
- **XP-7** **Paper:** set by `@page` from print settings. A4 is `210mm 297mm`. F4 is `215mm 330mm` (*proposed*; see open questions). Margins come from print settings in mm.
- **XP-8** **Letterhead:** the school's or the teacher's image (D-031) goes at the top of the **first page only**, at full content width, `max-height: 4cm`. It is passed to Gotenberg as an asset in the request, never as a URL (architecture decision 5).
- **XP-9** **Fonts** are bundled as files and sent with the request (D-033: no CDN). The school's choice maps as follows:

| Print setting | Font used |
|---|---|
| Arial (default) | Liberation Sans (metric-compatible, *proposed*) |
| Times New Roman | Liberation Serif (metric-compatible, *proposed*) |
| Arabic text (any setting) | Noto Naskh Arabic |
| Math | KaTeX fonts |

- **XP-10** **Page numbers** use Gotenberg's footer HTML, with `<span class="pageNumber">` and `<span class="totalPages">` placed in `export.print.page_of` ("Halaman :page dari :total"). Exam cards and single-page documents have no page numbers.
- **XP-11** **Signers** (names, NIP, titles) come from print settings as text, with a place and a date in the long form ("Kudus, 31 Oktober 2026").
- **XP-12** JavaScript content (KaTeX, ApexCharts, three.js) must finish before capture. The view sets a ready flag that the PDF call waits for, and a view with no JS needs no wait.
- **XP-13** **Watermark** (Sigrak Dasar plan) is CSS in the layout, with its text from `export.print.watermark`.
- **XP-14** Tables use `page-break-inside: avoid` on rows and `thead { display: table-header-group }`, so headings repeat on each page.

### 4.2 DOCX (PhpWord)

- **XP-15** Each template has a DOCX builder in `app/Content/Export/Docx/<Key>DocxBuilder.php` that builds SIGRAK's own layout in code (D-031). There are no uploaded Word templates and no Word-to-PDF conversion.
- **XP-16** The document language is set to `id-ID` (proofing and hyphenation). The page size and margins follow the print settings (same values as XP-7).
- **XP-17** The letterhead image goes in the **first-page header**, and the other pages get an empty header. Signers are plain text.
- **XP-18** Fonts are set by name ("Arial", "Times New Roman", "Noto Naskh Arabic"), because Word substitutes fonts on the teacher's machine. Arabic runs use an RTL paragraph style (see §7).
- **XP-19** Headings use Word heading styles, so teachers get a navigation pane and a table of contents. Body text does not use direct formatting.

### 4.3 Excel and CSV (Laravel Excel)

- **XP-20** Excel exports are classes in `app/Exports/` (admin data) or `app/Content/Export/` (template documents). They implement `WithHeadings`, `WithMapping`, `WithColumnFormatting`, `WithStyles`, and `WithTitle`. Headings come from `__('export.columns.*')`:

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

- **XP-21** **Real types:**
  - Numbers are numeric cells, never formatted strings.
  - Dates are Excel date serials (`Date::dateTimeToExcel()`), converted to `Asia/Jakarta` first.
  - Booleans are labels ("Ya"/"Tidak").
  - Excel shows `.` for thousands and `,` for decimals on Indonesian systems by itself, so the format codes stay in Excel's neutral form.
  - Long numeric ids (NISN, NIP, NPSN) are **text** cells, so leading zeros survive.
- **XP-22** **Sheet layout:**
  - Sheet names are Indonesian and at most 31 characters ("Leger", "Kisi-kisi", "Analisis per TP").
  - Row 1 holds the headings, with a frozen pane and an auto filter.
  - A title block (school, class, period) goes on a separate "Info" sheet, so the data sheet stays filterable.
- **XP-23** **Formula injection.** A text cell that starts with `=`, `+`, `-`, `@`, tab, or CR, and comes from user or AI text, is written as a plain string (explicit string type in XLSX, a leading `'` in CSV).
- **XP-24** **CSV** (*proposed*):
  - The separator is `;`, the encoding is UTF-8 **with BOM**, and line ends are CRLF. Excel on Indonesian Windows, where the list separator is `;`, then opens it directly with the right characters.
  - Numbers have no thousands separator and use `,` for decimals. Dates are `dd/mm/yyyy`.
  - XLSX is the default download. CSV is offered only where a user asks for it.
  - A file meant for another system (for example an e-Rapor import) follows that system's spec instead, and the spec is noted in the export class.
- **XP-25** **Imports** (students, scores) accept the same headings as the matching export, and both XLSX and CSV (`;` or `,`, with or without BOM). They validate each row and report errors in Indonesian by row number ("Baris 12: NISN harus 10 angka."). Template files for imports are generated from the same column list as the export.

## 5. Formatting helpers

- **XP-26** `App\Support\IndonesianFormat` (PHP) and `resources/js/lib/format.ts` (TS) are the only formatters (EX-6). They share an API and the same outputs, and a test feeds both the same fixtures.
- **XP-27** Storage is UTC (DB-10). Every method converts to `Asia/Jakarta` before formatting. Input is a Carbon or ISO-8601 string.

| PHP (`IndonesianFormat::`) | TS (`format.`) | Output |
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
| `score($n)` | `score(n)` | `76,67` (two decimals, matches `decimal(5,2)`) |

- **XP-28** Rupiah has no space after `Rp` and no decimals, following the EYD writing rules: `Rp1.234.567`. The helper builds the string itself and does not rely on ICU's currency pattern, which differs between ICU versions (*proposed*).
- **XP-29** Month and day names come from Carbon's `id` locale (`translatedFormat`) and from `Intl` with `id-ID`. A test pins them: Januari … Desember, Senin … Minggu, and "Jumat" without an apostrophe.
- **XP-30** Times use `.` (14.00), per Indonesian convention. "WIB" is added only where the user might be in another time zone, or where the time refers to an outside event (the AI quota reset time, exam windows). Elsewhere a plain time is enough.

## 6. File names of exports

- **XP-31** The pattern is `<kind>-<context>-<date>.<ext>`:
  - All parts come from `export.files.*` words and are slugged with `Str::slug($part, '-', 'id')`.
  - The date is ISO (`2026-10-31`) so files sort.
  - The full name is at most 80 characters.
  - Examples: `rpm-ipas-kelas-4a-2026-10-31.pdf`, `leger-kelas-4a-semester-1-2026-2027.xlsx`, `kartu-login-simulasi-tka-6a-2026-11-03.pdf`.
- **XP-32** **No student personal data in file names.** No names, NISN, or birth dates. A per-student file uses the class and the roster number (`rapor-kelas-6a-no-07-semester-1.pdf`), and a bundle of all students is one file or one ZIP.
- **XP-33** No teacher names in file names either. The document title can be used (the teacher typed it) after it is slugged.
- **XP-34** The download response sends both an ASCII `filename` and a UTF-8 `filename*` in `Content-Disposition`.

## 7. RTL and Arabic in UI and exports

MI CP text (D-039) and PAI material mix Indonesian and Arabic. The rules:

- **LOC-32** Text that may contain Arabic inline (CP `text`, AI output for PAI subjects, teacher input) is shown in an element with `dir="auto"`, and inline values are wrapped in `<bdi>`, so punctuation and numbers stay in place. Strings are never reversed or reordered by hand.
- **LOC-33** `arabic_passages` (D-039) are separate blocks with `lang="ar" dir="rtl"` and the Arabic font class (`font-arabic`: Noto Naskh Arabic, bundled), at about 1.25× the body size for legibility.
- **LOC-34** In plain-text contexts (CSV cells, mail text, notifications), an Arabic segment inside Indonesian text is wrapped in the Unicode isolates U+2067 (RLI) … U+2069 (PDI).
- **XP-35** **PDF:** the same HTML rules as the UI (Chromium shapes Arabic correctly). The Arabic font is embedded through `@font-face` (XP-9).
- **XP-36** **DOCX:** Arabic paragraphs use an RTL paragraph style with the bidi flag and `lang="ar-SA"`. Inline Arabic gets its own run with the RTL flag.
- **XP-37** **Excel:** a cell with only Arabic is right-aligned with reading order RTL. Mixed cells use the context reading order.
- **XP-38** A test renders one CP row with an Arabic passage to HTML and DOCX and checks that `dir`/`lang` (HTML) and the RTL flags (DOCX XML) are present.

## 8. Checks that keep this true

| Check | Runs in | Fails when |
|---|---|---|
| `lang:audit` missing keys (LOC-24) | CI (Pest) | A key used in PHP, Blade, or Svelte is not in `lang/id` |
| `lang:audit` hard-coded text (LOC-25) | CI | Literal UI text outside `t()`/`__()` in Svelte or Blade |
| Unused keys (LOC-24) | CI (warning) | A key nobody uses (listed, fixed in the same task) |
| Banned terms (LOC-22) | CI (Pest) | A `lang/id` value contains an old *Istilah* term, "...", "&", an emoji, or ALL-CAPS |
| Server-only groups (LOC-10) | Feature test | `export`, `mail`, `validation`, … appear in the `translations` prop |
| Parameter parity | Pest | A key's `:params` differ from those passed at a call site (checked for literal calls) |
| Enum labels | Pest arch | An enum case has no `enums.*` label |
| Export headings snapshot | Pest | An export's headings differ from the stored snapshot (`tests/Snapshots/exports/<name>.json`); the snapshot shows the Indonesian headings for review |
| Excel cell types (EX-4) | Pest | A number, date, or Rupiah column holds strings, or NISN/NIP is numeric |
| CSV format (XP-24) | Pest | The file lacks the BOM, uses `,` as the separator, or a cell starts with `=` unescaped |
| Formatter parity (XP-26) | Pest + Vitest | PHP and TS formatters give different output for the shared fixtures |
| File names (XP-31, XP-32) | Pest | A generated name is over 80 characters, not slugged, or contains a student's name or NISN from the fixture |
| RTL rendering (XP-38) | Pest | The Arabic fixture lacks `dir`/`lang` (HTML) or RTL flags (DOCX) |

All are part of the GIT-4 CI run (GitHub Actions).

## Decisions to record

New choices in this guide. They get numbered at integration.

1. **Page-level translation groups.** `common`, `nav`, `terms`, and `enums` are always shared. A page adds files through a `translations:<files>` route middleware. Server-only files are refused, and the prop is a lazy prop sent on full visits only (refines D-080).
2. **Glossary file + banned-terms test.** The 37 *Istilah* terms live in `lang/id/terms.php`, and a test rejects the old terms in any `lang/id` value.
3. **Spreadsheet tracking file.** `docs/data/ui-copy/key-map.csv` records, per spreadsheet row, the key or why the row was dropped. It is filled in screen by screen.
4. **`lang:audit` command** for missing, unused, and hard-coded strings. Missing keys fail CI.
5. **Validation messages** start from a one-time copy of the MIT `laravel-lang` Indonesian file, edited to Sigrak's voice. It is not installed as a package.
6. **Database notifications store key + params**, rendered when read.
7. **CSV format:** `;` separator, UTF-8 with BOM, CRLF, comma decimals. XLSX is the default, and external-system files follow their own spec.
8. **Rupiah format** `Rp1.234.567` (no space, no decimals), built by our helper and not by ICU. The Money row in coding-standards §8 now points to `IndonesianFormat::rupiah()` to match.
9. **Print fonts:** Liberation Sans/Serif as metric-compatible stand-ins for Arial/Times New Roman in Gotenberg, Noto Naskh Arabic for Arabic, all bundled.
10. **F4 paper** is 215 × 330 mm.
11. **Per-student export files** are named by class and roster number, never by student name or NISN.
12. **"WIB"** is shown only for times tied to outside events or other time zones (quota reset, exam windows).

## Open questions

- [ ] F4 size: 215 × 330 mm (the common Indonesian "folio") or 210 × 330 mm? Schools may differ; a setting may be needed.
- [ ] e-Rapor import format (columns, file type) is still unknown (data-model open question). The XP-24 exception covers it once known.
- [ ] Should a school be able to choose another print font beyond Arial and Times New Roman? If yes, every option needs a bundled, licensable font file.
- [ ] Does the exam client need a larger default font size for grades 5–6 than the school app? (Design question; the A−/A/A+ control exists.)

