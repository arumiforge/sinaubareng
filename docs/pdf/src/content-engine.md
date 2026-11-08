# Mesin Konten: Generator Terpandu dan Mesin Templat

<p class="source-note">Diterjemahkan dari <code>docs/final/content-engine.md</code> pada 8 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-03
- **Terakhir diperbarui:** 2026-11-01
- **Sumber:** `Konsep_Awal_Produk_SIGRAK.md`, `Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md`, `Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md` (di `docs/sources/`)

## Tujuan

Dokumen ini menjelaskan bagaimana jawaban guru menjadi konten pembelajaran yang siap pakai: templat, wizard, pipeline pembuatan, penyuntingan, game, paket dan ujian latihan TKA, pengelolaan versi, dan ekspor. Dokumen ini untuk pengembang yang membangun menu *Buat*. Lapisan yang berkomunikasi dengan Gemini dirinci terpisah di `ai-provider-engine.md`. Konteks produk ada di `product-concept.md`.

## Istilah

| Istilah | Arti |
|---|---|
| **Templat** | Definisi buatan pengembang untuk satu jenis konten (RPM, Bank Soal, LKPD, Quiz Battle, Paket Soal TKA) |
| **Wizard** | Pertanyaan yang diajukan templat kepada guru. Hanya pertanyaan pedagogis |
| **Spesifikasi** | Objek bertipe dan tervalidasi yang disusun dari jawaban wizard ditambah data yang sudah dimiliki sistem. Satu-satunya masukan untuk menyusun prompt |
| **Generation** | Satu kali templat dijalankan untuk satu guru. Terdiri dari satu atau lebih langkah dan menghasilkan sebuah dokumen |
| **Dokumen** | Hasil yang disimpan: konten JSON terstruktur yang disunting, diekspor, ditayangkan, atau diunduh guru |
| **Renderer** | Kode yang mengubah konten dokumen menjadi pratinjau/penyunting, PDF, DOCX, Excel, atau game yang bisa dimainkan |

## Satu templat per jenis konten, tanpa generator umum

Konsep pemilik menyebut *Guided Generator* (wizard bertahap) dan *Developer Templates*. Di SIGRAK keduanya satu hal: **setiap templat punya wizard terpandunya sendiri**. Tidak ada generator umum "buat materi apa saja" (D-024). Guru sudah bisa mendapat konten umum dengan mengobrol langsung dengan Gemini, jadi SIGRAK hanya menawarkan jenis konten yang nilainya bertambah lewat templat, yang tidak didapat dari Gemini saja:

- data kurikulum (CP, TP, ATP, JP) terisi otomatis;
- struktur dan format cetak yang tetap dan siap dipakai sekolah;
- aturan validasi (jumlah, kunci, cakupan TP, bentuk soal TKA);
- penyajian di kelas (mode tayang, unduh game, ujian CBT).

Setiap jenis konten punya kelas templat, wizard, job pembuatan, dan renderer sendiri. Yang dipakai bersama hanya infrastrukturnya: kelas pipeline, gateway AI, kerangka penyunting, dan mesin ekspor.

## Anatomi templat

Setiap templat punya sembilan bagian dari konsep pemilik. Templat tinggal di kode, dengan registri database di atasnya (D-013).

| Bagian | Isi | Letak | Diubah oleh |
|---|---|---|---|
| Metadata | Key, nama dan deskripsi (key terjemahan), kategori (`document` / `game`), ikon, kelas dan mata pelajaran yang didukung | Kode + baris registri `templates` (penanda aktif, urutan) | Pengembang; tim menyalakan/mematikan |
| Konfigurasi wizard | Langkah, pertanyaan, pilihan, nilai bawaan, sumber isian awal, syarat tampil/sembunyi | Kode | Pengembang |
| Skema masukan | JSON Schema dari spesifikasi | Kode | Pengembang |
| Instruksi prompt | Instruksi sistem dan tugas, aturan gaya, contoh | Kode (dasar) + override opsional per versi, disunting di Filament | Pengembang; tim menyetel kata-katanya tanpa deploy |
| Skema keluaran | JSON Schema dari keluaran AI, juga dikirim ke Gemini sebagai skema keluaran terstruktur | Kode | Pengembang |
| Desain | Tata letak cetak untuk dokumen; tema dan aset untuk game | Kode | Pengembang |
| Aturan validasi | Aturan bisnis di luar skema (lihat setiap templat di bawah) | Kode | Pengembang |
| Renderer | Pratinjau/penyunting Svelte, view PDF/DOCX/Excel di server, runtime game vanilla JS | Kode | Pengembang |
| Versi | Bilangan bulat per templat. Baris `template_versions` mengikat versi kode dengan override prompt | Tabel `template_versions` | Dibuat saat deploy atau saat override prompt |

Usulan susunan folder:

```
app/Content/
├── Templates/
│   ├── Contracts/Template.php          # interface every template implements
│   ├── TemplateResolver.php            # key + version → template instance
│   ├── TemplateVersionManager.php      # creates/pins versions, applies prompt overrides
│   ├── Rpm/
│   │   ├── RpmTemplate.php             # metadata, wizard, steps, rules
│   │   ├── schemas/specification.json
│   │   ├── schemas/output.json         # + output-kokurikuler.json
│   │   └── prompts/instructions.md
│   ├── BankSoal/  Lkpd/  QuizBattle/  TkaPackage/
├── Generation/
│   ├── SpecificationBuilder.php
│   ├── PromptBuilder.php
│   ├── OutputValidator.php
│   ├── OutputRepairer.php
│   └── GenerationRunner.php            # shared step runner used by every job below
└── Export/                             # PdfExporter, DocxExporter, ExcelExporter, GameExporter

app/Jobs/Generation/                     # one job per content type (D-024)
├── GenerateRpm.php
├── GenerateBankSoal.php
├── GenerateLkpd.php
├── GenerateQuizBattle.php
├── GenerateTkaPackage.php
└── ReviseSection.php                    # "Perbaiki bagian ini", any template

resources/js/Templates/<Key>/            # Svelte preview + section editors
resources/game-runtime/quiz-battle/      # vanilla JS + CSS, built by Vite as a standalone bundle
resources/exam-client/                   # TKA exam site front end (separate bundle, no AI)
resources/views/exports/<key>/           # Blade views for PDF/DOCX
```

Ini memisahkan dua urusan yang diminta konsep pemilik untuk dipisahkan. `app/AI/` (lihat `ai-provider-engine.md`) tidak tahu apa pun tentang pendidikan. `app/Content/` tidak tahu apa pun tentang Gemini.

## Aturan wizard

1. **Tanyakan hanya yang diketahui guru secara pedagogis:** kelas, mata pelajaran, topik dan tujuan pembelajaran (dipilih, bukan diketik), kondisi siswa, durasi, tingkat kesulitan, jumlah butir, model pembelajaran, dan jenis asesmen.
2. **Jangan pernah tanyakan yang sudah diketahui sistem:** JP, jumlah pertemuan, menit, teks CP, fase, kode TP, identitas sekolah, dan tanggal dari jadwal pelajaran.
3. **Isi awal** kelas dan mata pelajaran dari slot jadwal pada tanggal yang dipilih, dan dari penugasan guru.
4. **Rantai kurikulum hanya bisa dipilih:** Kelas → Mata pelajaran → Semester → Materi pokok (ATP) → Fokus → TP, semuanya dibaca dari data kurikulum. Kelas 7–12 disembunyikan.
5. **Paling banyak 4 langkah**, masing-masing satu layar di ponsel. Setiap pertanyaan punya nilai bawaan. Bila AI bisa memilih dengan wajar (misalnya dimensi profil, atau model pembelajaran per pertemuan), tawarkan "Biarkan otomatis".
6. **Satu catatan teks bebas yang opsional** ("Catatan untuk bahan ini", maks. 300 karakter) untuk konteks dengan kata-kata guru sendiri, misalnya "banyak siswa belum lancar membaca". Catatan ini masuk ke prompt sebagai *data* yang dibatasi, tidak pernah sebagai instruksi.
7. Jawaban tersimpan otomatis sebagai draf. Pembuatan yang selesai menawarkan "Buat ulang dengan jawaban yang sama".
8. Semua teks wizard berasal dari `lang/id` dan mengikuti `design-system.md` (bagian Aturan penulisan UI).

## Pipeline pembuatan

```
Wizard answers
   │
   ▼
SpecificationBuilder ── validates against the input schema,
   │                    enriches from DB (curriculum, JP, timetable, school)
   ▼
Specification (stored on the generation, immutable)
   │
   ▼  for each template step:
PromptBuilder ── template version's instructions + specification as JSON data
   │              + earlier step outputs + output schema
   ▼
AI Provider Engine ── sticky key, structured JSON output (app/AI)
   │
   ▼
OutputValidator ── JSON Schema + template rules
   │  invalid?
   ├──► OutputRepairer ── 1) deterministic fixes (trim, coerce numbers, drop unknown keys)
   │                      2) at most one AI repair call that receives the validation errors
   │    still invalid → step fails with a teacher-friendly message
   ▼
Step output accepted (stored once per step)
   │
   ▼
Document saved: content JSON + template key + template version
```

- **Langkah.** Templat besar membagi pembuatan menjadi beberapa langkah agar setiap respons tetap dalam batas ukuran dan kegagalan hanya mengulang satu bagian. Contoh untuk RPM: `design` (identifikasi + desain pembelajaran) → `meetings` (satu langkah per kelompok pertemuan) → `assessment` (asesmen + lampiran). Langkah berikutnya menerima keluaran sebelumnya sebagai konteks. Semua langkah dalam satu pembuatan memakai kunci AI yang sama kecuali terjadi failover (pemilihan lengket; lihat mesin AI).
- **Kolom data tidak pernah dibuat AI.** Identitas, CP, kode TP, jumlah pertemuan, menit, dan rujukan berasal dari data dan digabungkan ke dokumen setelah pembuatan. Keluaran AI untuk kolom-kolom itu diabaikan.
- **Pekerjaan di latar belakang.** Setiap jenis konten punya job antrean sendiri (`GenerateRpm`, `GenerateBankSoal`, …) dengan rencana langkah, timeout, dan label kemajuan sendiri. Halaman menampilkan kemajuan ("Langkah 2 dari 4"), dan guru boleh meninggalkan halaman lalu diberi tahu saat selesai.
- **Pesan kegagalan** menyebut apa yang terjadi dan apa yang harus dilakukan, dengan detail teknis di balik "Lihat detail". Contoh: "RPM belum berhasil dibuat. Semua kunci AI Anda sedang mencapai batas; coba lagi pukul 15.00."

## Penyuntingan dan revisi sebagian

- Pratinjau berupa penyunting terstruktur per bagian, tidak pernah JSON mentah. Teks AI ditampilkan sebagai teks biasa (escaping Svelte; tidak pernah `{@html}` untuk keluaran AI).
- **"Perbaiki bagian ini"**: guru memilih bagian dan aksi (*sederhanakan*, *buat diferensiasi*, *sesuaikan untuk siswa berkebutuhan khusus*, *perpanjang*, *perpendek*). Templat menetapkan skema keluaran tingkat bagian untuk ini. Hasilnya menampilkan versi lama dan baru berdampingan, lalu guru menerima atau membuangnya.
- Setiap revisi AI atau simpan manual menyimpan konten sebelumnya sebagai revisi (5 terakhir, D-153), sehingga guru bisa membatalkan.

## Templat v1

### RPM

| | |
|---|---|
| Wizard | **Kapan:** tanggal mulai, atau "RPM lepas jadwal" (tanpa jadwal pelajaran, tanpa kemajuan ATP). Kelas dan mata pelajaran dari jadwal. **Apa:** materi pokok dan fokus. JP dan pertemuan dihitung dari data dan dikunci (`jp_fokus_dapat_diubah_guru: false`). **Cara:** model pembelajaran per pertemuan (PjBL, Inkuiri & Discovery Learning, Problem Solving, Game Based Learning, Diskusi kelompok & eksplorasi) atau "Biarkan otomatis", ditambah catatan opsional. **Periksa** |
| Diisi dari data | Kelas, fase, mata pelajaran, elemen, CP, TP + kodenya, materi pokok, fokus, jumlah pertemuan, menit per pertemuan (JP × 35 untuk SD), rujukan buku, tautan ke RPM sebelumnya |
| Diisi AI | *Identifikasi:* kesiapan dan kebutuhan, karakteristik materi, dimensi profil lulusan (hanya dipilih dari delapan dimensi di bawah), asesmen awal (prasyarat, butir). *Desain:* *Lintas disiplin* opsional (mata pelajaran terkait), metode dan sintaks (*Praktik Pedagogis*), *Kemitraan Pembelajaran*, *Lingkungan Pembelajaran* (ruang fisik, ruang virtual, budaya belajar), *Pemanfaatan Digital*, media dan sumber. *Pengalaman belajar:* per pertemuan — materi, pembukaan (*Awal*), kegiatan inti mengikuti sintaks dan dibagi menjadi tiga pengalaman belajar *Memahami*, *Mengaplikasi*, *Merefleksi*, masing-masing ditandai prinsip yang dipakai (*berkesadaran*, *bermakna*, *menggembirakan*), penutup (*Penutup*), rangkuman. Satu pertemuan boleh hanya mencakup sebagian dari tiga pengalaman itu. *Asesmen:* asesmen formatif (di awal, selama, dan di akhir pembelajaran), prinsip, teknik, indikator, rubrik 4 tingkat, butir evaluasi, tindak lanjut. *Lampiran:* petunjuk dan tabel LKPD, kartu, langkah. *Kesinambungan:* ringkasan materi terakhir, rekomendasi untuk berikutnya. Untuk MI: saran Tema KBC + Materi Insersi (opsional, D-142) |
| Dimensi profil lulusan | Satu set tetap berisi delapan dimensi, dengan kode tetap sesuai urutan ini: DPL1 keimanan dan ketakwaan terhadap Tuhan Yang Maha Esa, DPL2 kewargaan, DPL3 penalaran kritis, DPL4 kreativitas, DPL5 kolaborasi, DPL6 kemandirian, DPL7 kesehatan, DPL8 komunikasi (Permendikdasmen 13/2025 Pasal 17). AI hanya memilih dari set ini. Deskripsinya: `docs/data/regulations/` |
| Kerangka resmi | Bagian-bagiannya mengikuti kerangka rencana pembelajaran mendalam: Identifikasi, Desain Pembelajaran, Pengalaman Belajar, Asesmen (Panduan Pembelajaran dan Asesmen 2025, hlm. 35–39). Label bagian memakai istilah resmi di `lang/id`. Formatnya bebas, jadi tata letak SIGRAK diperbolehkan |
| KBC (hanya MI) | Untuk sekolah MI, wizard menambahkan *Tema KBC* (satu atau lebih tema Panca Cinta) dan *Materi Insersi*, dan AI merangkainya ke TP, kegiatan, dan asesmen. Kedua kolom opsional: AI mengisi saran awal dari CP dan materi, dan guru boleh mengubah atau mengosongkannya, sehingga RPM tetap bisa dibuat tanpanya (D-142). Ini berlaku untuk setiap mata pelajaran MI dan tidak berlaku untuk SD (D-126) |
| Varian kokurikuler | Skema keluaran tersendiri, satu rencana per kegiatan kokurikuler, mengikuti Panduan Kokurikuler 2025: kepala (satuan pendidikan, kelas, tema, alokasi waktu dalam JP, lokasi), A *Dimensi Profil Lulusan*, B *Tujuan Pembelajaran* (kompetensi dan materi), C *Praktik Pedagogis*, D *Lingkungan Pembelajaran*, E *Kemitraan Pembelajaran*, F *Pemanfaatan Digital*, G *Kegiatan* (Memahami, Mengaplikasi, Merefleksi), H *Asesmen* (formatif, ditambah sumatif dengan rubrik 4 tingkat). Bentuknya, yang boleh digabung: *Pembelajaran Kolaboratif Lintas Disiplin* (mata pelajaran terkait dicantumkan), *Gerakan 7 Kebiasaan Anak Indonesia Hebat* (7 KAIH), dan *Cara Lainnya* (misalnya projek). Kebiasaan ketujuh diberi label "Tidur Cepat", dengan "tidur tepat waktu" sebagai penjelasannya (D-207). Koding dan Kecerdasan Artifisial berjalan di sini sebagai isi kokurikuler (D-208). Membaca rencana kokurikuler tahunan sekolah (dimensi, tema, bentuk, mata pelajaran terkait, JP, tahapan tahunan, pemantauan) yang disetujui kepala sekolah |
| Aturan | Jumlah pertemuan dan menit sama dengan nilai hasil hitungan. Setiap TP dari topik muncul di sedikitnya satu pertemuan atau indikator. Rubrik punya tepat 4 tingkat (sangat baik, baik, berkembang, perlu bimbingan). Tidak ada bagian kosong |
| Penjadwalan | Pertemuan ditempatkan di jadwal pelajaran kelas. Pertemuan yang ditandai "tidak terlaksana" di jurnal menggeser sisanya ke slot JP berikutnya. RPM semester 2 menunggu tanggal mulai semester genap (perilaku yang dibawa dari penjadwal EDUNEXA) |

### Bank Soal

| | |
|---|---|
| Wizard | **Cakupan:** jenis asesmen (Penilaian Harian = satu materi pokok; STS/SAS = beberapa materi dalam satu semester; Ujian Sekolah/Madrasah). **Komposisi:** jumlah per jenis — PG, PG Kompleks (MCMA), **PGK Kategori** (benar/salah per pernyataan, seperti TKA; baru), Benar-Salah, **Menjodohkan** (baru), Isian, Uraian. Campuran tingkat kesulitan (Campuran/Mudah/Sedang/Sulit). Campuran level kognitif C1–C6 atau "Biarkan otomatis". **Pelaksanaan:** tanggal dan durasi. **Periksa**. Untuk kelas 1–2 (Fase A) panduan menyarankan observasi dan unjuk kerja ketimbang tes tertulis, jadi wizard menyampaikannya lewat catatan singkat. Butir tertulis tetap tersedia sebagai pilihan dan tidak pernah diblokir (D-206) |
| Per butir | Jenis, pokok soal, pilihan, kunci, pembahasan, indikator, `kode_tp`, level kognitif, tingkat kesulitan |
| Aturan | Jumlah sesuai komposisi. PG punya tepat satu kunci, PG Kompleks dua atau lebih. PGK Kategori punya 3–5 pernyataan, masing-masing dengan kunci. Pasangan Menjodohkan satu-satu. Setiap `kode_tp` ada di dalam cakupan yang dipilih |
| Ekspor | Kisi-kisi, naskah soal, kunci, pembahasan, indikator, kartu soal (PDF, Word, Excel) |
| Dipakai di Asesmen | Bila sebuah asesmen memakai dokumen Bank Soal, butirnya disalin ke butir asesmen beserta `kode_tp`, sehingga analisis per TP tidak perlu mengetik ulang (lihat `data-model.md`, bagian Konten) |

### LKPD

| | |
|---|---|
| Wizard | Materi/TP (atau diisi awal dari lampiran RPM) → jenis kegiatan (pengamatan, percobaan, diskusi, latihan) dan individu atau kelompok → **Periksa** |
| Keluaran | Judul, tujuan, petunjuk, langkah, tabel (kolom, baris, ukuran), pertanyaan, refleksi |
| Desain | Tata letak cetak untuk A4 dan F4, kop sekolah opsional |

### Quiz Battle (game)

| | |
|---|---|
| Wizard | **Soal:** pakai set Bank Soal yang tersimpan (hanya jenis yang bisa dinilai otomatis: PG, Benar-Salah) atau buat soal baru dari materi/TP. **Permainan:** tim (2–4) atau individu, detik per soal (10–60), poin, tampilkan pembahasan setelah setiap soal, jumlah babak. **Periksa** |
| Diisi AI | Soal, jawaban, pembahasan (bila soal baru), petunjuk singkat per soal, nama tim sesuai tema topik, satu kalimat pembuka |
| Milik runtime | Tata letak, kontrol, timer, penilaian, urutan giliran, animasi, suara, kontrol keyboard dan remote, ukuran responsif, aksesibilitas |
| Aturan | Setiap nilai konfigurasi ada dalam rentang/enum yang diizinkan templat. Setiap soal punya kunci yang valid |

### Paket Soal TKA (`tka_package`)

Templat ini menghasilkan paket latihan yang mengikuti kerangka resmi TKA untuk SD/MI. Kerangkanya adalah Kerangka Asesmen TKA (Perka BSKAP 047/H/AN/2025), dipublikasikan di [situs TKA Pusmendik](https://pusmendik.kemendikdasmen.go.id/tka) dan dicek pada 2026-10-30.

| | |
|---|---|
| Wizard | **Mata uji:** Bahasa Indonesia atau Matematika. **Cakupan:** seluruh isi kerangka (bawaan) atau elemen/kompetensi tertentu untuk latihan terfokus. **Tingkat:** campuran (bawaan), lebih mudah, atau lebih sulit. **Jumlah:** 30 butir (bawaan TKA) atau set latihan yang lebih pendek. **Periksa** |
| Kerangka resmi (data, bukan AI) | *Bahasa Indonesia* hanya mengukur membaca. Jenis teksnya informasi dan fiksi. Kompetensi: pemahaman tekstual, pemahaman inferensial, evaluasi dan apresiasi, dengan subkompetensi dari matriks kerangka. *Matematika* mencakup elemen Bilangan, Geometri dan Pengukuran, serta Data. Kompetensinya memahami, mengaplikasikan, dan bernalar, dengan subelemen dan batasan dari matriks kerangka |
| Bentuk butir | Hanya tiga bentuk TKA: **PG** (satu jawaban), **PGK MCMA** (beberapa pilihan benar), **PGK Kategori** (setiap pernyataan dijawab benar/salah, sesuai/tidak sesuai, atau mendukung/tidak mendukung). Butir bisa tunggal (*soal tunggal*) atau berkelompok pada satu stimulus (*soal grup*) |
| Diisi AI | Teks stimulus, pokok soal, pilihan, kunci, pembahasan singkat, dan tag kompetensi/subkompetensi untuk setiap butir |
| Aturan | Teks stimulus Bahasa Indonesia sepanjang 150–200 kata (kecuali puisi), dengan kalimat pendek sederhana dan kosakata yang sebagian besar denotatif, seperti dijelaskan kerangka. Setiap butir diberi tag kompetensi dan bentuk dari kerangka. Campuran bentuk dan kompetensi dalam paket tetap dalam batas yang diatur. Setiap butir PGK Kategori punya 3 pernyataan (seperti contoh resmi). Tidak ada butir Isian atau Uraian |
| Visual stimulus | Tidak pernah gambar AI (D-030). Tabel, piktogram, dan diagram batang dibuat sebagai **data terstruktur** dan digambar oleh renderer kita. Bangun datar, jaring-jaring, dan gambar pengukuran berasal dari **set gambar yang dibundel** yang dipilih AI berdasarkan id. **Susunan kubus** (tampak depan/atas/samping) dibuat sebagai peta tinggi dan digambar dengan three.js, dan kunci untuk "seperti apa tampak depannya" **dihitung oleh kode**, bukan oleh AI |
| Keluaran | Dokumen paket: butir berurutan, kunci, pembahasan, kisi-kisi per kompetensi. Ekspor: naskah, kunci, pembahasan, kisi-kisi (PDF/Word/Excel). Dokumen ini juga menjadi sumber sesi **Simulasi TKA** (di bawah) |

## Ujian latihan TKA (CBT)

Ujian ini meniru cara TKA nasional berjalan: berbasis komputer, peserta masuk dengan kartu login, token dari proktor wajib, ada batas waktu, dan hasilnya dilaporkan sebagai nilai dengan kategori. Siswa bukan pengguna aplikasi (D-010), jadi ujian berjalan di **situs ujian terpisah** dengan kredensial per sesi (D-027).

| Bagian | Perilaku |
|---|---|
| Sesi | Dibuat guru atau operator dari *Paket Soal TKA*. Sesi punya tanggal, jendela waktu, durasi (bawaan 75 menit, durasi TKA untuk setiap mata uji SD), kelas 6 yang ikut, dan proktor (guru) |
| Kartu ujian | Satu *kartu login* per siswa: nama, kelas, sesi, nama pengguna, kata sandi. Dicetak sebagai PDF (beberapa kartu per halaman). Kredensial hanya berlaku untuk sesi itu dan kedaluwarsa saat sesi ditutup |
| Token | Token 6 karakter yang diturunkan dari rahasia sesi dan jendela 15 menit yang sedang berjalan (gaya TOTP), jadi tidak ada yang perlu disimpan. Layar proktor menampilkan token yang berlaku. Peserta membutuhkannya untuk mulai atau melanjutkan |
| Klien ujian | Layar polos mirip TKA di subdomain sendiri: area soal, kisi nomor, tanda *ragu-ragu*, timer, pengatur ukuran huruf. Berjalan di desktop dan tablet. **Tanpa panggilan AI, tanpa tautan ke aplikasi, tanpa data selain ujian milik peserta itu sendiri** |
| Integritas | Urutan butir dan pilihan diacak per peserta (kelompok stimulus tetap bersama). Setiap jawaban tersimpan otomatis dengan nomor urut (idempoten), sehingga sambung ulang melanjutkan dari titik terakhir. Waktu ditegakkan oleh server. Proktor bisa mereset login peserta atau menambah waktu untuk satu peserta, dan kedua aksi itu dicatat |
| Hasil | Nilai 0–100 per mata uji, dipetakan ke kategori TKA (Kurang < 50; Memadai 50 sampai < 76,67; Baik 76,67 sampai < 95; Istimewa ≥ 95, seperti dilaporkan untuk TKA 2026). Ditambah analisis per kompetensi dan per butir, ekspor ke Excel dan PDF, dan salinan opsional ke Asesmen |
| Penilaian (latihan) | PG: 1 poin untuk kunci. PGK MCMA dan PGK Kategori: **nilai sebagian** per pilihan atau pernyataan yang benar (D-030). Total dinormalkan ke rentang TKA SD **0–100**. Model penilaian butir TKA resmi tidak dipublikasikan, jadi nilai latihan adalah *perkiraan* dan UI menyatakannya |

"Mode Ujian" umum di Bank Soal tetap tidak aktif (D-026). Klien ujian dibangun hanya untuk paket TKA.

## Game: pendekatan hibrida, menayangkan, mengunduh

**Hibrida sejak rancangan.** Runtime vanilla JS memegang seluruh struktur, kira-kira 70–90% dari sebuah game (target rancangan dari konsep pemilik, bukan hasil ukur). AI mengisi JSON konten dan memilih nilai konfigurasi dari pilihan yang diizinkan. Karena itu setiap game dari satu versi templat berperilaku sama.

**Tanpa kode buatan AI di v1 (D-015).** Bila guru menginginkan sesuatu yang tidak ada di templat, "Usulkan fitur" mencatat permintaan (`template_feature_requests`) untuk tim. Pilihan kode AI dalam sandbox mungkin menyusul dengan rancangannya sendiri.

**Tayangkan (mode tayang).** Rute layar penuh di dalam sesi guru. Hurufnya besar untuk proyektor, bisa dikendalikan dengan keyboard atau clicker, dan nilai hanya disimpan di perangkat guru. Tidak menyimpan data siswa.

**Unduh game (HTML mandiri, D-011).** Berupa satu berkas `.html`:

- JS dan CSS runtime disisipkan langsung. Konten disematkan sebagai `<script type="application/json" id="content">` dengan `<` di-escape. Runtime menampilkan setiap string konten dengan `textContent`, tidak pernah `innerHTML`.
- Tag meta Content-Security-Policy memblokir semua akses jaringan: `default-src 'none'; script-src 'unsafe-inline'; style-src 'unsafe-inline'; img-src data:`. Berkas bisa dimainkan offline dan tidak bisa menghubungi server mana pun.
- Gambar dan suara disematkan sebagai URI `data:` (v1 hanya memakai aset yang dibundel).
- Isinya hanya konten pembelajaran, ditambah judul game. Tidak memuat data siswa dan tidak memuat identitas guru selain yang diketik guru di judul.
- Versi runtime disematkan. Mengunduh ulang memakai runtime dari versi templat dokumen itu.

Berkas yang diunduh memang berada di luar dinding login. Hal itu dapat diterima karena isinya hanya konten pembelajaran.

## Pengelolaan versi

- Baris `template_versions` **tidak bisa diubah begitu dipakai sebuah generation**. Perubahan prompt yang dibuat di Filament membuat versi baru, tanpa perlu deploy.
- Dokumen menyimpan `template_key` dan `template_version`. Renderer tetap kompatibel ke belakang dalam satu templat. Perubahan skema keluaran yang tidak kompatibel butuh versi baru ditambah langkah `upgrade(content, fromVersion)` atau renderer lama yang dipertahankan.
- Revisi bagian memakai versi templat dokumen itu sendiri, bukan versi terbaru.

## Ekspor

Ini mesin ekspor tunggal dari catatan pengembang. Mesinnya diputuskan di D-028:

- **Satu model dokumen** (JSON konten). Setiap format dibuat darinya, tidak pernah dari tangkapan layar.
- **PDF: Gotenberg 8** (Chromium headless di container Docker), dipanggil lewat `spatie/laravel-pdf` dengan driver Gotenberg-nya:
  - PDF berbasis teks dari view Blade/HTML, dengan CSS penuh: tabel dengan `page-break-inside: avoid`, F4 dan A4, header dan footer berulang dengan nomor halaman, dan gambar kop.
  - JavaScript berjalan sebelum ditangkap (`waitUntilReady`), sehingga rumus KaTeX, grafik, dan gambar three.js tampil di cetakan persis seperti di layar.
  - Membentuk huruf Arab dengan benar, yang dibutuhkan materi PAI.
  - **Satu PDF per unduhan.** Sebuah paket (RPM + LKPD + rubrik + kunci, atau semua kartu ujian) adalah satu view HTML dengan pemisah halaman, jadi tidak perlu menggabungkan PDF. Konversi Word ke PDF juga tidak dipakai (D-031).
  - Mengapa layanan terpisah:
    - Chromium terisolasi di container. Ubuntu 22.04 hanya menyediakan Chromium sebagai snap, yang merepotkan bila dijalankan dari worker PHP.
    - Pemakaian memorinya tidak membebani PHP-FPM.
    - Job ekspor di antrean `exports` memanggilnya lewat HTTP. Layanan ini terikat ke localhost dengan basic auth, dan versi image-nya dikunci.
  - Alternatif yang ditolak: Dompdf/mPDF (CSS dan pembagian halaman lemah, tanpa JavaScript), Browsershot dan driver chrome-php (Node dan/atau Chromium di host itu sendiri), dan WeasyPrint (tanpa JavaScript, dependensi Python tambahan).
- **DOCX:** PhpWord, dibangun dari tata letak Word milik SIGRAK per templat, dengan gambar kop di header dan penanda tangan sebagai teks. Sekolah tidak menyerahkan templat Word.
- **Excel: Laravel Excel** (`maatwebsite/excel`, versi utama terkini, di atas PhpSpreadsheet). Menangani ekspor bergaya dengan banyak sheet (leger, kisi-kisi, analisis, hasil TKA), rumus, ekspor antrean untuk berkas besar, dan impor bertahap dengan validasi per baris (impor siswa, nilai). Angka disimpan sebagai angka dan kolom bisa difilter. Pastikan dukungan Laravel 13 saat memasang.
- **Kop sebagai gambar (D-031).**
  - Sekolah mengunggah gambar kop bawaan, dan guru boleh mengunggah kop sendiri yang menggantikan kop sekolah pada dokumen guru itu.
  - Tata letak, logo, dan garis pemisah semuanya ada di dalam gambar, jadi guru yang mengaturnya. Sistem hanya menempatkan gambar selebar area isi di bagian atas halaman pertama.
  - Aturan gambar: PNG atau JPG, latar putih atau transparan, lebar minimal 2.000 px, rasio lebar-tinggi antara 4:1 dan 8:1, paling besar 2 MB. Tinggi cetaknya dibatasi 4 cm.
  - Layar unggah menampilkan pratinjau gambar di A4 dan F4 dan memberi peringatan bila resolusinya terlalu rendah untuk dicetak.
- **Pengaturan cetak lain (per sekolah):** nama dan NIP penanda tangan (teks), A4/F4, margin, font (Arial atau Times New Roman, D-125), dan bagian mana yang disertakan.
- **Label dalam bahasa Indonesia** lewat key terjemahan (konvensi repo).
- **Buka di Canva (tingkat 1):** mengunduh DOCX/PDF dan membuka canva.com agar guru mengimpornya. Impor langsung lewat Canva Connect API *Nanti*.

## Pustaka render

Pustaka ini hanya ditambahkan bila sebuah templat membutuhkannya (D-033). Setiap pustaka dikunci versinya, dibundel Vite, dan tidak pernah dimuat dari CDN. Runtime game dan klien ujian masing-masing hanya membundel yang dipakainya.

| Pustaka | Dipakai untuk | Di mana |
|---|---|---|
| **three.js** (dengan SVG renderer-nya untuk cetak) | Susunan kubus dan bangun ruang dari data (tampak depan/atas/samping); ruang untuk game 3D di masa depan | Paket TKA, Bank Soal, pratinjau LKPD, klien ujian, PDF |
| **KaTeX** | Pecahan, operasi, dan satuan di butir Matematika | Pratinjau penyunting, klien ujian, game, PDF |
| **ApexCharts** (pustaka grafik TailAdmin) | Diagram batang di stimulus butir; analisis nilai dan ringkasan portal | Aplikasi, klien ujian (hanya stimulus), PDF |
| Font **Noto Naskh Arabic** (atau Amiri) | Teks Arab di materi PAI | Aplikasi, PDF, DOCX |
| Set gambar dan piktogram yang dibundel (SVG) | Bangun datar, jaring-jaring, gambar pengukuran, ikon piktogram | Semua templat (D-030) |

Tidak ada pustaka presentasi: fitur slide tidak direncanakan (D-033). Gambar buatan AI tidak dipakai.

## Pertanyaan terbuka

- [ ] Pembagian langkah yang tepat untuk templat RPM, disetel berdasarkan ukuran keluaran sungguhan.
