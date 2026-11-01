# Standar Koding

<p class="source-note">Diterjemahkan dari <code>docs/final/coding-standards.md</code> pada 1 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P2-06
- **Terakhir diperbarui:** 2026-11-01

## Tujuan

Ini adalah aturan yang diikuti setiap pengembang dan agen begitu pembangunan SIGRAK dimulai. Setiap aturan singkat dan bisa diperiksa saat review atau oleh alat. Bila sebuah aturan berasal dari keputusan atau draf sebelumnya, aturan itu merujuk ke sana tanpa mengulanginya. Butir bertanda *usulan* tercantum di Keputusan untuk dicatat dan perlu persetujuan pemilik. Butir bertanda *disetujui pemilik* sudah disetujui pemilik pada 2026-10-31 dan hanya perlu nomor keputusan.

Sumber: `CLAUDE.md`, `CONTEXT.md`, `architecture.md`, `data-model.md`, `ai-provider-engine.md`, `content-engine.md`, `design-system.md`.

## 1. PHP dan Laravel

| # | Aturan | Diperiksa oleh |
|---|---|---|
| PHP-1 | Gaya kode PSR-12 lewat **Laravel Pint** dengan `pint.json` repo (preset Laravel + `declare_strict_types`). Tidak ada perdebatan format manual. | `vendor/bin/pint --test` di CI |
| PHP-2 | Setiap berkas PHP di `app/`, `database/`, `routes/`, `tests/`, dan `config/` diawali `declare(strict_types=1);`. | Aturan Pint |
| PHP-3 | Analisis statis dengan **Larastan**, level 6 di awal, dinaikkan bertahap (*usulan*). Kode baru tidak menambah entri baseline. | `vendor/bin/phpstan` di CI |
| PHP-4 | Parameter, tipe kembalian, dan properti diberi tipe. `mixed` dan array tanpa tipe hanya boleh dengan bentuk PHPDoc (`array<int, Topic>`, `array{id: int, name: string}`). | Larastan |
| PHP-5 | Himpunan tetap berupa **PHP backed string enum** di `app/Enums/`, di-cast di model, mencerminkan `CHECK` kolomnya (lihat `data-model.md`, bagian Konvensi). Nilai enum adalah nilai DB snake_case bahasa Inggris; labelnya dari `lang/id/enums.php`. Tidak ada string ajaib untuk status. | Review, arch test |
| PHP-6 | Masukan divalidasi di **Form Request** (`app/Http/Requests/<Area>/<Action>Request.php`). Jangan pernah `$request->all()` ke model; pakai `$request->validated()`. | Review, arch test |
| PHP-7 | Setiap aksi controller diotorisasi lewat **Policy** (`authorize()` di Form Request atau middleware `Gate`/`can`). Aksi tanpa otorisasi gagal review. | Review + tes IDOR |
| PHP-8 | **Tidak ada logika bisnis di controller.** Controller memvalidasi (Form Request), mengotorisasi (Policy), memanggil satu Action atau service, dan mengembalikan respons Inertia atau redirect. Sekitar 15 baris per metode adalah tanda bahaya, bukan batas. | Review |
| PHP-9 | Operasi bisnis ada di kelas **Action** bertujuan tunggal dengan satu metode publik `handle()` (*usulan*): di folder modul bila ada (`app/Classroom/Actions/RecordAttendance.php`), selain itu di `app/Actions/<Domain>/`. `app/AI` dan `app/Content` mempertahankan lapisannya sendiri. | Review |
| PHP-10 | Model mendeklarasikan `$fillable` secara eksplisit. `$guarded = []` dan `Model::unguard()` tidak diperbolehkan. Atribut sensitif (`secret`, `password`, `token_secret`) masuk `$hidden`. | Arch test |
| PHP-11 | Baca konfigurasi lewat `config()`. `env()` hanya dipanggil di dalam `config/*.php`. | Aturan Larastan |
| PHP-12 | Tidak ada SQL mentah dengan nilai yang disisipkan. `DB::raw`/`whereRaw` hanya dengan binding. | Review |
| PHP-13 | Cegah N+1: `Model::preventLazyLoading()` di luar produksi; eager load pada query yang mengisi daftar. | Exception saat dev/tes |
| PHP-14 | Job membawa id, bukan model dengan relasi termuat, dan memeriksa ulang keadaan saat berjalan. Job pembuatan konten mengikuti aturan antrean di `ai-provider-engine.md` (bagian Timeout dan permintaan ganda) (`tries = 1`), dengan `ShouldBeUniqueUntilProcessing` + `WithoutOverlapping` berdasarkan id generation agar job bisa mengirim ulang dirinya (`architecture.md`, Keputusan untuk dicatat, butir 3). | Review |
| PHP-15 | Tanggal berasal dari `now()`/`CarbonImmutable` (diatur sebagai kelas tanggal bawaan), tidak pernah `date()`/`time()`, agar tes bisa membekukan waktu. | Review |

Contoh controller yang tipis:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Classroom\Actions\CreateJournal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreJournalRequest;
use Illuminate\Http\RedirectResponse;

final class JournalController extends Controller
{
    public function store(StoreJournalRequest $request, CreateJournal $createJournal): RedirectResponse
    {
        $journal = $createJournal->handle($request->user(), $request->validated());

        return to_route('journals.show', $journal)->with('toast', __('journals.saved'));
    }
}
```

## 2. Susunan folder

Folder modul dan dependensi yang diizinkan ditetapkan di `architecture.md` (bagian Batas modul): `app/AI`, `app/Content`, `app/Tenancy`, `app/Curriculum`, `app/Classroom`, `app/Exam`, `app/Filament`, `app/Jobs/Generation`. Bagian ini tidak mengulang atau mengubahnya. Bagian ini hanya menambahkan folder Laravel di sekitarnya dan aturan tempat kode diletakkan.

| Path | Isi |
|---|---|
| Folder modul | Seperti di `architecture.md` (Batas modul). `BelongsToSchool` dan konteks sekolah ada di `app/Tenancy/` |
| `app/Actions/<Domain>/` | Operasi bisnis bertujuan tunggal untuk fitur **tanpa** folder modul (asesmen, rapor, portal, keuangan, inventaris). Di dalam modul, action-nya ada di modul itu (`app/Classroom/Actions/RecordAttendance.php`) |
| `app/Enums/` | Backed enum untuk setiap himpunan tetap |
| `app/Models/` | Model Eloquent |
| `app/Policies/` | Satu policy per model yang terbuka bagi pengguna |
| `app/Http/Controllers/{Teacher,School,Supervisor,Exam}/` | Controller tipis yang dikelompokkan per area. Controller situs ujian hanya ada di `Exam/` dan memanggil `app/Exam` |
| `app/Http/Requests/<Area>/` | Form Request |
| `app/Http/Resources/` | Bentuk data untuk prop Inertia (lihat SV-4) |
| `app/Jobs/` | Job antrean (`Generation/` sesuai D-024) |
| `app/Exports/`, `app/Imports/` | Kelas Laravel Excel untuk data administrasi (leger, absensi, impor siswa). Ekspor dokumen templat tetap di `app/Content/Export/` |
| `app/Filament/` | Konsol internal, hanya guard `staff` |
| `app/Support/` | Helper kecil yang lepas dari framework, mis. `IndonesianFormat` |
| `routes/app.php`, `routes/exam.php`, `routes/admin.php` | Satu berkas per kelompok hostname (`app.`, `ujian.`, `admin.<domain>`, didaftarkan dengan `Route::domain(...)`), masing-masing dengan guard sendiri (`architecture.md`, bagian Hostname) |
| `resources/js/Pages/<Area>/` | Halaman Inertia |
| `resources/js/Components/` | Komponen Svelte bersama (bagian TailAdmin yang diporting), `resources/js/Templates/<Key>/` per templat |
| `resources/game-runtime/`, `resources/exam-client/` | Bundel terpisah (D-011, D-027) |
| `resources/views/exports/<key>/` | View Blade untuk PDF/DOCX |
| `lang/id/` | Semua teks UI dan ekspor |
| `python/<tool>/` | Alat Python, hanya bila Laravel/Node tidak bisa mengerjakannya dengan baik, masing-masing dengan `requirements.txt` berversi terkunci dan README |

Aturan:

- **LAY-1** Setiap dependensi mengikuti tabel "Boleh memakai / Tidak boleh memakai" di `architecture.md` (Batas modul). Setiap baris tabel itu punya satu Pest architecture test.
- **LAY-2** Di luar `app/AI`, kode menjangkau AI hanya lewat `App\AI\AIGateway`, tidak pernah lewat `App\AI\Providers\*` atau bagian dalam `KeyManager`.
- **LAY-3** `app/AI` tidak pernah meng-import `App\Content`, `App\Exam`, atau model siswa.
- **LAY-4** `App\Exam`, `App\Classroom`, `App\Http\Controllers\Exam`, dan `resources/exam-client` tidak pernah meng-import `App\AI\*` (D-027; data offline tidak pernah menyentuh AI).
- **LAY-5** Controller dan `app/Actions` boleh memanggil modul; modul tidak pernah meng-import `App\Http\*`.

Contoh: `arch()->expect('App\Exam')->not->toUse('App\AI');`

## 3. Penamaan

| Hal | Konvensi | Contoh |
|---|---|---|
| Kelas, enum, komponen Svelte | StudlyCase / PascalCase | `KeySelector`, `GenerationStatus`, `KeyRow.svelte` |
| Metode, variabel, prop | camelCase | `selectKey()`, `$pinnedCredential`, `secretLast4` |
| Case enum | StudlyCase, nilai snake_case | `case RateLimitedDaily = 'rate_limited_daily';` |
| Tabel, kolom | tabel snake_case jamak, kolom snake_case | `teaching_assignments.teacher_id` |
| Model | StudlyCase tunggal | `TeachingAssignment` |
| Tabel pivot | sesuai nama di model data; yang baru memakai bentuk tunggal alfabetis Laravel | `focus_item_objectives`, `role_user` |
| Boolean | awalan `is_`/`has_` | `is_active`, `is_flagged` |
| Timestamp / tanggal | `_at` (timestamp), `_on` (tanggal) | `submitted_at`, `starts_on` |
| Action, job | diawali kata kerja | `CreateJournal`, `GenerateRpm` |
| Event | bentuk lampau | `GenerationSucceeded` |
| URL (path yang dilihat orang) | kebab-case, kata bahasa Indonesia yang sederhana (D-149) | `/masuk`, `/lupa-kata-sandi`, `/kunci-ai/{credential}`, `/dokumen/{document}/ubah` |
| Nama rute | dot.snake, bahasa Inggris | `login`, `ai_keys.store`, `exam.sessions.show` |
| Key config, key terjemahan | snake_case, dipisah titik | `ai.purposes.document_long`, `journals.saved` |
| Branch | `<phase>/<task-id>-<slug>`, dihapus setelah merge (D-148) | `p3/p3-04-journals` |

Istilah sekolah Indonesia yang tidak punya padanan bahasa Inggris yang tepat tetap memakai identifier bahasa Inggris yang pendek ditambah komentar atau tautan dokumen, tidak pernah identifier bahasa Indonesia (`homeroom_teacher_id`, bukan `wali_kelas_id`). Pengecualian: kode kurikulum dan nilai enum yang sudah ditetapkan di model data (`kurang`, `memadai`, `pgk_mcma`, …).

Path URL adalah satu-satunya tempat routing memakai kata bahasa Indonesia (D-149). Tulis path dalam bahasa Indonesia yang sederhana, dan biarkan nama rute serta parameternya tetap bahasa Inggris, agar kode selalu membuat tautan lewat `route()` dan tidak pernah lewat path yang ditulis manual:

```php
// AppServiceProvider::boot()
Route::resourceVerbs(['create' => 'buat', 'edit' => 'ubah']);

// routes/app.php
Route::get('/masuk', [LoginController::class, 'create'])->name('login');
Route::resource('dokumen', DocumentController::class)
    ->parameters(['dokumen' => 'document'])
    ->names('documents'); // /dokumen/{document}/ubah -> documents.edit
```

Aturan ini berlaku untuk setiap halaman yang dibuka orang: aplikasi sekolah, situs ujian, dan konsol staf (`$slug` pada resource Filament, sejauh Filament mengizinkannya). Path teknis yang tidak dibaca siapa pun, seperti `/up`, webhook, dan aset `/build/*`, boleh memakai nama bahasa Inggris bawaan framework.

## 4. Migrasi dan database

| # | Aturan |
|---|---|
| DB-1 | Satu tabel per berkas migrasi (atau satu perubahan per berkas untuk alter). |
| DB-2 | Jangan pernah mengubah migrasi yang sudah ada di `main`. Tambahkan yang baru. |
| DB-3 | Setiap foreign key dideklarasikan dengan `constrained()` dan `cascadeOnDelete()` / `restrictOnDelete()` / `nullOnDelete()` yang eksplisit, sesuai model data. |
| DB-4 | Setiap tabel milik sekolah punya `school_id` `NOT NULL`, diindeks, dan indeks gabungan diawali kolom itu. |
| DB-5 | Kolom `NOT NULL` kecuali model data menyebut arti `NULL`-nya. Tulis arti itu sebagai komentar kolom. |
| DB-6 | Himpunan tetap: kolom `string` + `CHECK (col IN (…))` yang ditambahkan dengan `DB::statement` di migrasi yang sama, dibuat dari case enum agar tidak bisa menyimpang. |
| DB-7 | Invarian keunikan dan rentang dari model data menjadi constraint DB (unique index, `CHECK`), bukan hanya validasi. |
| DB-8 | **Tanpa soft delete.** Trait `SoftDeletes` dan kolom `softDeletes()` dilarang. Siklus hidup memakai `status` eksplisit. |
| DB-9 | Uang berupa rupiah `bigInteger`. Nilai memakai `decimal(5,2)`. Tidak ada `float`/`double` untuk keduanya. |
| DB-10 | Timestamp disimpan dalam UTC (`APP_TIMEZONE=UTC`). Konversi ke `Asia/Jakarta` hanya saat ditampilkan atau diekspor. |
| DB-11 | Baris yang tampil di URL memakai `public_id` (UUIDv7) untuk route model binding (`getRouteKeyName()`); id bigint tidak pernah muncul di URL atau prop. |
| DB-12 | Kolom JSON hanya di tempat yang diizinkan model data. Jangan pernah memfilter atau mengurutkan berdasarkan path JSON di query yang sering dipakai; jadikan kolom tersendiri. |
| DB-13 | `down()` opsional (*disetujui pemilik*). Produksi hanya maju; kesalahan diperbaiki dengan migrasi baru, tidak pernah dengan rollback. Secara lokal, bangun ulang dengan `migrate:fresh --seed`. |
| DB-14 | Seeder data rujukan (kurikulum, daerah) berupa upsert idempoten berdasarkan natural key. Data demo hanya ada di factory dan `DemoSeeder`, tidak pernah dijalankan di produksi. |

```php
Schema::create('journals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('school_id')->constrained()->restrictOnDelete();
    $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
    $table->string('status');
    $table->uuid('client_uuid')->unique();
    $table->timestamps();
    $table->index(['school_id', 'class_id']);
});

$statuses = implode("','", array_column(JournalStatus::cases(), 'value'));
DB::statement("ALTER TABLE journals ADD CONSTRAINT journals_status_check CHECK (status IN ('{$statuses}'))");
```

## 5. Tenancy dan tes akses

Model tenancy ada di `data-model.md` (Tenancy dan akses). Aturan kode:

- **TEN-1** Setiap model milik sekolah memakai trait `App\Tenancy\BelongsToSchool`. Model dengan `school_id` tanpa trait itu gagal arch test.
- **TEN-2** `withoutSchoolScope()` hanya boleh di `app/Filament/` dan di job atau command yang menerima dan menyebut sekolahnya secara eksplisit. Setiap pemanggilan diberi komentar satu baris tentang alasannya.
- **TEN-3** Job yang menyentuh data sekolah menerima `schoolId` di konstruktor dan menyetel konteks tenancy lebih dulu. Job tidak pernah bergantung pada sesi request.
- **TEN-4** Route model binding diselesaikan lewat query yang ber-scope, sehingga `public_id` milik sekolah lain menghasilkan 404, bukan 403 (keberadaan data tidak bocor).
- **TEN-5** **Satu tes IDOR per resource dan aksi** yang membaca atau mengubah data: pengguna sekolah A meminta baris sekolah B dan mendapat 404; pengguna tanpa peran mendapat 403. Polanya:

```php
it('hides another school\'s journal', function () {
    $journal = Journal::factory()->for(School::factory())->create();
    $teacher = User::factory()->teacherAt(School::factory()->create())->create();

    actingAs($teacher)->get(route('journals.show', $journal))->assertNotFound();
});
```

- **TEN-6** Data tingkat siswa, catatan konseling, dan agama mengikuti aturan visibilitas di model data (D-036). Setiap aturan punya tes policy, termasuk "pengawas tidak melihat baris siswa".
- **TEN-7** Prop Inertia untuk halaman satu sekolah tidak pernah memuat data sekolah lain, bahkan jumlahnya.

## 6. Svelte dan Inertia

| # | Aturan |
|---|---|
| SV-1 | Svelte biasa dengan `@inertiajs/svelte`, tanpa SvelteKit (D-008). Sintaks runes Svelte 5 (*disetujui pemilik*). |
| SV-2 | TypeScript di `<script lang="ts">` untuk aplikasi sekolah dan klien ujian (*disetujui pemilik*). Runtime game tetap vanilla JS (D-011), dengan tipe JSDoc dan `// @ts-check`. |
| SV-3 | Halaman di `resources/js/Pages/<Area>/<Name>.svelte`, satu per rute Inertia. Bagian bersama di `resources/js/Components/`. Komponen tetap presentasional; data berasal dari prop halaman. |
| SV-4 | Prop disusun di server dengan API Resource atau array eksplisit. **Jangan pernah mengirim model utuh** (`toArray()` membocorkan kolom). Rahasia tidak pernah sampai ke prop (kunci AI hanya menampilkan label, 4 karakter terakhir, dan status). |
| SV-5 | Id di prop dan URL adalah `public_id`. URL rute berasal dari helper rute yang dihasilkan, tidak pernah string susunan manual. |
| SV-6 | Formulir memakai `useForm` Inertia; galat validasi server tampil di bawah kolomnya. Tombol *Buat* mengirim client token (UUID) untuk mencegah kiriman ganda. |
| SV-7 | Tidak ada `{@html}` untuk teks pengguna atau AI. Hanya boleh untuk keluaran renderer tepercaya milik kita (KaTeX, gambar SVG yang dibundel), masing-masing ditandai komentar. |
| SV-8 | Gaya hanya lewat kelas Tailwind yang dipetakan ke token di `resources/css/tokens.css` (lihat sistem desain). Tidak ada warna hex atau nilai sembarang (`text-[9px]`) di komponen. |
| SV-9 | Setiap layar dicek pada lebar 360 px dan 1280 px. Tabel berubah menjadi daftar kartu di bawah 640 px; area sentuh ≥ 44 px. |
| SV-10 | Tombol ikon saja punya `aria-label`; kolom formulir punya `<label>`; status tidak pernah hanya lewat warna. |
| SV-11 | Pustaka (Lucide, KaTeX, three.js, ApexCharts, font) di-import dari `node_modules` dan dibundel Vite. Tidak ada `<script>` atau `<link>` CDN (D-033). Pustaka berat (three.js, ApexCharts) dimuat lazy dengan `import()` di halaman yang membutuhkannya. |
| SV-12 | Keadaan yang berjalan lama (kemajuan pembuatan) memakai reload parsial Inertia (`only: [...]`) dengan polling, seperti di mesin AI. |

## 7. Terjemahan dan teks UI

| # | Aturan |
|---|---|
| TR-1 | Semua teks yang terlihat pengguna ada di `lang/id/*.php`. Tidak ada string UI literal berbahasa Indonesia (atau Inggris) di PHP, Blade, atau Svelte. |
| TR-2 | Key berupa snake_case bahasa Inggris, dikelompokkan per berkas area: `common.php`, `nav.php`, `journals.php`, `ai_keys.php`, `enums.php`, `export.php`, `validation.php`. Key = makna, bukan teksnya (`journals.saved`, bukan `journals.jurnal_tersimpan`). |
| TR-3 | Placeholder memakai sintaks `:name` Laravel. Bentuk jamak memakai `trans_choice`. Jangan pernah menyusun kalimat dengan menyambung potongan terjemahan. |
| TR-4 | Teks mengikuti nada, aturan penulisan, dan istilah di sistem desain (mis. "Unduh", "Kunci AI", tidak pernah "generate"/"prompt"). |
| TR-5 | Pesan validasi dan nama atribut berbahasa Indonesia, di `lang/id/validation.php` (array `attributes`). |
| TR-6 | Svelte mendapat teks lewat satu helper `t('key', params)` yang diisi dari server (prop Inertia bersama per kelompok halaman, *usulan*). Key-nya sama dengan PHP. |
| TR-7 | Sebuah tes gagal bila key yang dipakai di PHP, Blade, atau Svelte tidak ada di `lang/id`. |
| TR-8 | Locale aplikasi dan locale fallback sama-sama `id`. Tidak ada `lang/en`. |

## 8. Ekspor, tanggal, dan angka

| # | Aturan |
|---|---|
| EX-1 | Setiap ekspor (Excel, CSV, Word, PDF) memakai judul kolom, label, nama sheet, dan nama berkas berbahasa Indonesia, lewat key terjemahan: `__('export.columns.created_at')` → "Tanggal Dibuat". |
| EX-2 | Nilai enum di ekspor memakai labelnya (`present` → "Hadir"), tidak pernah nilai mentah. |
| EX-3 | Nama berkas ekspor berbahasa Indonesia, huruf kecil, dipisah tanda hubung, dengan tanggal: `jurnal-kelas-4a-2026-10-30.xlsx`. |
| EX-4 | Sel Excel mempertahankan tipe sungguhan: angka sebagai angka, tanggal sebagai tanggal dengan format tampilan Indonesia (`dd/mm/yyyy`), agar guru bisa mengurutkan dan menjumlahkan. |
| EX-5 | PDF berupa satu view Blade per dokumen lewat Gotenberg (D-028, D-031); font cetak berasal dari pengaturan cetak sekolah, Arial atau Times New Roman (D-125). |
| EX-6 | Semua format melewati `App\Support\IndonesianFormat` (PHP) dan `resources/js/lib/format.ts` (JS). Tidak ada `number_format()` atau `toLocaleString()` dadakan di view. |

| Nilai | Format | PHP | JS |
|---|---|---|---|
| Tanggal | `30/10/2026` | `$date->timezone('Asia/Jakarta')->format('d/m/Y')` | `Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta' })` |
| Tanggal dalam kalimat | `30 Oktober 2026` | `->locale('id')->translatedFormat('j F Y')` | `{ dateStyle: 'long' }` |
| Jam | `14.00` (WIB) | `->format('H.i')` | `{ hour: '2-digit', minute: '2-digit' }` |
| Angka | `1.234.567,89` | `Number::format($n, 2, locale: 'id')` | `Intl.NumberFormat('id-ID')` |
| Uang | `Rp1.234.567` | `IndonesianFormat::rupiah($n)` (tanpa pola mata uang ICU; lihat `export-localization.md`, bagian 5) | `format.rupiah(n)` |

Zona waktu tampilan adalah `Asia/Jakarta`, kecuali kelak ada pengaturan sekolah yang menyatakan lain.

## 9. Tes

| # | Aturan |
|---|---|
| TS-1 | Framework tes: **Pest** (*disetujui pemilik*), termasuk architecture test-nya untuk aturan lapisan dan model di atas. |
| TS-2 | Tes berjalan di **MySQL 8** di CI dan lokal (*usulan*), bukan SQLite, karena skema bergantung pada constraint `CHECK`, kolom JSON, dan kolom generated. |
| TS-3 | Wajib per fitur: feature test untuk setiap rute (jalur berhasil + gagal validasi), tes IDOR/peran dari TEN-5, dan tes policy. Unit test untuk Action yang punya aturan nyata, untuk pemilihan kunci dan klasifikasi galat di `app/AI`, dan untuk pemformat. |
| TS-4 | **Tidak ada panggilan AI sungguhan di tes.** Pasang `AIProvider` palsu dengan respons dan urutan galat yang sudah ditulis. `Http::preventStrayRequests()` aktif di `TestCase` dasar. Satu-satunya smoke test Gemini sungguhan bersifat pilihan dan tidak pernah berjalan di CI (lihat bagian Pengujian di mesin AI). |
| TS-5 | Contract test Gemini memakai respons rekaman di `tests/Fixtures/gemini/`, sudah dibersihkan dari kunci. |
| TS-6 | Ekspor punya tes yang memeriksa judul kolom berbahasa Indonesia dan sel angka/tanggal bertipe benar. PDF dites di tingkat view HTML; Gotenberg dipalsukan di CI. |
| TS-7 | Factory tersedia untuk setiap model dan secara bawaan menghasilkan baris yang valid dalam satu sekolah. Nama siswa palsu berasal dari `fake('id_ID')`; jangan pernah memakai data orang sungguhan di fixture. |
| TS-8 | Perbaikan bug disertai tes yang gagal tanpa perbaikan itu. |
| TS-9 | Frontend: `svelte-check` dan ESLint di CI. Component test (Vitest) untuk editor konten dan penyimpanan jawaban di klien ujian (*usulan*). |

## 10. Aturan privasi AI untuk kode

Aturan ini mengubah aturan privasi di `ai-provider-engine.md` menjadi pemeriksaan kode:

- **AI-1** Hanya `App\AI\Providers\Gemini\GeminiClient` yang memanggil API Google. Tidak ada pemanggilan `Http::` lain ke `*.googleapis.com`.
- **AI-2** Setiap request yang disusun dari data sekolah melewati `PromptBuilder` dan `PromptDataGuard`. Kode tidak pernah memasukkan model `Student`, nama, NISN, tanggal lahir, absensi, catatan, agama, atau nilai yang bisa dikenali ke dalam `AIRequest`.
- **AI-3** Draf deskripsi rapor memakai label anonim ("Murid A"); nama sebenarnya digabungkan setelah pembuatan, di server.
- **AI-4** Instruksi hanya berasal dari templat. Catatan guru dan teks kurikulum masuk ke blok data yang dibatasi; keluaran AI hanya diterima setelah validasi skema.
- **AI-5** Templat menyebut tujuan (purpose), tidak pernah id model; id model ada di `config/ai.php`. Jangan pernah mengirim `temperature`, `top_p`, atau `top_k`.
- **AI-6** Kunci hanya dikirim di header `x-goog-api-key`, hanya didekripsi di `ApiKeyManager`/`GeminiClient`, dan tidak pernah dimasukkan ke URL, prop, log, pesan exception, atau payload antrean (job membawa id kredensial).
- **AI-7** Teks AI yang tampil di UI di-escape (SV-7).
- **AI-8** Kode di bawah situs ujian tidak pernah memakai `App\AI` (LAY-4).

Daftar periksa review untuk setiap PR yang menyentuh `app/Content` atau `app/AI`: "Data apa yang sampai ke prompt, dan apakah ada yang bisa mengenali seorang siswa?"

## 11. Logging dan rahasia

| # | Aturan |
|---|---|
| LOG-1 | Rahasia hanya ada di `.env` di server (tidak pernah di-commit). `.env.example` mencantumkan setiap key dengan nilai kosong atau contoh. |
| LOG-2 | Log dan konteks exception membawa id (`user_id`, `school_id`, `generation_id`, `public_id`), tidak pernah rahasia, kata sandi, token, teks prompt atau respons, atau data pribadi siswa. |
| LOG-3 | Parameter yang memuat rahasia ditandai `#[\SensitiveParameter]` agar tersembunyi di stack trace. |
| LOG-4 | Telescope hanya berjalan di lokal/staging, di balik gate `staff`, dan menyembunyikan kolom `x-goog-api-key`, `authorization`, `cookie`, `password`, `secret`, dan `token`. |
| LOG-5 | Kolom terenkripsi memakai cast `encrypted`; jangan pernah didekripsi untuk dicatat atau dibandingkan. Duplikat dicari lewat fingerprint. |
| LOG-6 | Galat untuk pengguna memakai pesan terjemahan (apa yang terjadi + apa yang harus dilakukan). Detail teknis masuk ke log dan di balik "Lihat detail", tidak pernah teks exception mentah. |
| LOG-7 | Unggahan (kop, impor) divalidasi jenis dan ukurannya, disimpan di disk privat, dan disajikan lewat rute yang terotorisasi atau bertanda tangan. |
| LOG-8 | Kata sandi dan token peserta ujian tidak pernah muncul di log; tindakan proktor masuk ke `exam_events`, bukan log aplikasi. |

## 12. Commit dan pull request

| # | Aturan |
|---|---|
| GIT-1 | **Tidak ada atribusi AI di mana pun**: tidak ada baris `Co-Authored-By` atau sesi di commit, tidak ada apa pun di judul PR, deskripsi, komentar, kode, atau dokumen (lihat `CLAUDE.md`). Author dan committer setiap commit adalah pemilik, `Arumi Studios <arumiforge@gmail.com>` (D-148). |
| GIT-2 | Pesan commit dalam bahasa Inggris, bentuk perintah, subjek ≤ 72 karakter, tanpa titik di akhir: `Add daily attendance sync endpoint`. Badan pesan menjelaskan *alasannya* bila tidak jelas. |
| GIT-3 | Satu tugas per branch (`<phase>/<task-id>-<slug>`) dan per PR, mengikuti `WORKFLOW.md`. Perbaikan yang tidak terkait masuk PR sendiri. Setelah di-merge, branch dihapus; hanya `main` yang dipertahankan (D-148). |
| GIT-4 | Sebelum membuka PR: `pint --test`, `phpstan`, `pest`, `npm run lint`, `npm run check`, `npm run build` semuanya lolos. CI memakai **GitHub Actions** (*disetujui pemilik*): workflow di `.github/workflows/` menjalankan perintah yang sama di setiap PR ke `main`, dengan service container MySQL 8. PR hanya di-merge bila hijau. |
| GIT-5 | Deskripsi PR: apa yang berubah, alasannya (id tugas, id keputusan), cara mengujinya, dan tangkapan layar di lebar ponsel dan laptop untuk perubahan UI. |
| GIT-6 | Jangan pernah meng-commit `.env`, kunci, data siswa sungguhan, dump, atau hasil build (`public/build`, `docs/.build/`). |
| GIT-7 | PR yang menambah migrasi, dependensi baru, atau key env baru menyebutkannya di deskripsi. |

## 13. Dependensi

| # | Aturan |
|---|---|
| DEP-1 | `composer.lock` dan `package-lock.json` di-commit. CI memasang dengan `composer install` dan `npm ci`, tidak pernah `update`. |
| DEP-2 | Pustaka runtime front-end yang memengaruhi hasil (KaTeX, three.js, ApexCharts, Lucide, font) memakai versi pasti di `package.json` (tanpa `^`/`~`), agar dokumen dan gambar tampil sama setelah dipasang ulang. |
| DEP-3 | Tidak ada CDN saat berjalan untuk skrip, gaya, atau font (D-033). Semuanya dibundel atau di-host sendiri. |
| DEP-4 | Image Docker (Gotenberg) dikunci ke tag versi, tidak pernah `latest` (D-028). Paket Python dikunci dengan `==` di `requirements.txt`. |
| DEP-5 | Menambah dependensi perlu alasan di PR: fungsinya, mengapa Laravel atau paket yang ada tidak bisa, lisensinya, dan keadaan pemeliharaannya. Utamakan paket first-party Laravel. |
| DEP-6 | Pembaruan dependensi masuk PR sendiri, dengan changelog dibaca dan seluruh rangkaian tes lolos. `composer audit` dan `npm audit --omit=dev` berjalan di CI. |

## Keputusan untuk dicatat

Pilihan baru di dokumen ini. Nomornya diberikan di `DECISIONS.md` saat integrasi.

1. **Pest** sebagai framework tes (*disetujui pemilik 2026-10-31*), dengan Pest architecture test yang menegakkan lapisan (LAY-1…5, TEN-1, PHP-10). Alasan: tes yang mudah dibaca dan arch test bawaan membuat beberapa aturan di sini bisa diperiksa mesin. PHPUnit tetap ada di bawahnya.
2. **TypeScript** di Svelte (*disetujui pemilik 2026-10-31*) (`lang="ts"`) untuk aplikasi sekolah dan klien ujian; runtime game tetap vanilla JS dengan JSDoc + `@ts-check` (mempertahankan D-011). Alasan: prop Inertia bertipe menangkap kesalahan prop/serialisasi; `svelte-check` berjalan di CI.
3. **Svelte 5 (runes)** sebagai sintaks komponen (*disetujui pemilik 2026-10-31*). Alasan: versi utama Svelte terkini, didukung `@inertiajs/svelte`.
4. Analisis statis **Larastan**, mulai dari level 6.
5. **Kelas Action** menampung operasi bisnis; controller tetap tipis. Kelas ini ada di dalam folder modul yang diusulkan di `architecture.md` (Keputusan untuk dicatat, butir 2), dan di `app/Actions/<Domain>/` hanya untuk fitur tanpa modul. Ini menambah keputusan itu tanpa mengubahnya.
6. **Tes berjalan di MySQL 8**, bukan SQLite, karena skema bergantung pada fitur MySQL 8 (D-007).
7. **Terjemahan frontend** berasal dari `lang/id` lewat prop Inertia bersama dan satu helper `t()`; tanpa berkas terjemahan JS terpisah.
8. **Helper rute bertipe** untuk frontend (Laravel Wayfinder, atau Ziggy bila Wayfinder tidak cocok untuk Svelte biasa), dipilih saat kerangka proyek dibuat.
9. **Zona waktu aplikasi UTC**, tampilan dan ekspor dalam `Asia/Jakarta` (membuat "disimpan dalam UTC" dari model data menjadi eksplisit di konfigurasi).
10. **Versi terkunci pasti** untuk pustaka front-end yang memengaruhi hasil (DEP-2).
11. **GitHub Actions** sebagai layanan CI (*disetujui pemilik 2026-10-31*), menjalankan pemeriksaan GIT-4 di setiap PR ke `main` dengan service container MySQL 8.
12. **Migrasi tidak perlu `down()`** (*disetujui pemilik 2026-10-31*). Produksi hanya maju.

## Pertanyaan terbuka

- [x] Pastikan keputusan 1–3 (Pest, TypeScript, Svelte 5) sebelum kerangka proyek dibuat. Disetujui pemilik pada 2026-10-31.
- [x] Layanan CI: GitHub Actions. Disetujui pemilik pada 2026-10-31.
- [x] `down()` di migrasi tidak wajib (DB-13). Disetujui pemilik pada 2026-10-31.
