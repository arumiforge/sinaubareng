# Usulan 01 — Peta Fitur Aplikasi Lama ke SIGRAK: Administrasi Pembelajaran, Kalender, Jadwal, Asesmen, dan Cetak

- **Status:** usulan pemilik, siap diolah menjadi draft dan tugas
- **Tanggal:** 9 November 2026
- **Penyusun:** pemilik produk (disusun bersama Claude)
- **Basis repo:** `arumiforge/sinaubareng` commit `64aff02` (8 Nov 2026). Keputusan terakhir yang dirujuk: D-251
- **Basis pembanding:** portal aplikasi lama versi 5.20.7xx–8xx (portal guru, portal kepala sekolah, portal pengawas, super admin), dibaca 8–9 November 2026
- **Data pendukung:** folder [`data/`](data/README.md) di samping dokumen ini

## 0. Cara memakai dokumen ini (untuk Claude Code)

1. Simpan dokumen ini dan folder `data/` apa adanya di `docs/sources/usulan-01/`. Dokumen ini berbahasa Indonesia karena bahan dari pemilik. `docs/sources/` memang tempat bahan pemilik yang disimpan apa adanya, dan pengecualian bahasanya sudah diatur di `CLAUDE.md`.
2. Jangan langsung membangun. Ubah usulan ini menjadi:
   - satu draft berbahasa Inggris, `docs/drafts/legacy-upgrade-01.md`, mengikuti `docs/templates/DOC.md`;
   - keputusan baru di `docs/agent/DECISIONS.md`, bernomor lanjutan dari D-251, untuk setiap butir di §3 yang disetujui pemilik;
   - tugas di `docs/agent/PROGRESS.md` dan `docs/agent/tasks/` sesuai peta slice di §6.
3. Bila usulan bertentangan dengan keputusan yang sudah ada, keputusan yang ada menang sampai pemilik memutuskan lain. Daftar benturan yang sudah diketahui ada di §7.
4. Pertanyaan yang masih terbuka ada di §8. Tanyakan ke pemilik sebelum mengunci desain butir terkait.
5. Istilah di UI mengikuti glosarium §2, lalu masuk `lang/id` dengan kunci berbahasa Inggris.

## 1. Posisi: peningkatan dari aplikasi lama, bukan salinan

SIGRAK adalah generasi berikutnya dari aplikasi lama. Aplikasi lama membuktikan apa yang dibutuhkan guru SD/MI di lapangan. SIGRAK membawa hal-hal yang terbukti itu, lalu memperbaiki fondasinya. Setiap usulan di dokumen ini wajib menjawab dua pertanyaan: *apa yang dipertahankan dari aplikasi lama* dan *apa yang membuatnya lebih baik di SIGRAK*.

### 1.1 Tujuh pembeda yang dipegang di semua usulan

| # | Pembeda | Di aplikasi lama | Di SIGRAK |
|---|---|---|---|
| P1 | **Satu sumber data, angka sama di semua peran** | Rumus progres disalin ke beberapa aplikasi, dan datanya diunduh ulang dari Firebase di setiap portal | Satu service di server di atas data relasional MySQL. Guru, kepala sekolah, dan pengawas membaca hasil hitungan yang sama, dan konsistensinya dibuktikan oleh test |
| P2 | **Taat aturan 2025–2026** | Masih ada istilah lama: siswa, P5, taraf serap, ulangan, modul ajar sebagai satu-satunya nama, refleksi per pertemuan | Istilah dan alur mengikuti Permendikdasmen 1/2026 (murid), 13/2025 (struktur dan kokurikuler), Panduan Pembelajaran dan Asesmen 2025 (D-205, KKTP), Panduan Kokurikuler 2025 |
| P3 | **Privasi berlapis** | Pengawas melihat progres per guru. Tulisan tangan murid dikirim ke Gemini untuk dinilai. Ada halaman verifikasi publik | Pengawas hanya melihat rekap sekolah (D-224). Tidak ada data atau karya murid yang dikirim ke AI (D-036). Verifikasi dokumen hanya untuk pengguna yang login (D-010) |
| P4 | **Otomatis, tapi guru dan operator yang memutuskan** | Beberapa hitungan langsung menimpa data | Jadwal, kelompok piket, skor pindaian, dan nilai rapor selalu berupa **draf** yang dikonfirmasi dulu sebelum disimpan |
| P5 | **Dari data langsung ke instrumen supervisi** | Paket unduhan dan instrumen supervisi berjalan terpisah | Paket Administrasi dan progres mengikuti urutan butir S-01 dan S-15, sehingga kepala sekolah bisa langsung menilai |
| P6 | **Beban administrasi lebih ringan** | Refleksi diminta per pertemuan, dan ada banyak baris "aktivitas" tanpa makna | Refleksi per semester (D-214 A4). Progres hanya menuntut yang memang diwajibkan aturan. Sisanya ditampilkan sebagai "ada atau belum ada" |
| P7 | **Cetak rapi dari server** | PDF dibuat dari tangkapan layar di peramban (html2pdf), dengan banyak tambalan versi | PDF teks dari Gotenberg (XP-6 s.d. XP-14). Margin, sampul, dan "NIHIL" diatur sekali di Pengaturan Cetak |

## 2. Glosarium: istilah aplikasi lama ke istilah standar SIGRAK

Istilah di UI SIGRAK memakai kolom kanan. Istilah kiri tidak muncul di UI baru.

| Istilah aplikasi lama | Istilah SIGRAK | Dasar |
|---|---|---|
| Siswa | **Murid** | Permendikdasmen 1/2026, D-210 |
| Guru Umum | Guru kelas | `product-concept.md` Roles |
| Admin (sekolah) | Operator sekolah | Roles |
| Instansi | Sekolah / satuan pendidikan | UU 20/2003 |
| Modul Ajar | **RPM** (Rencana Pembelajaran Mendalam). Di instrumen supervisi ditulis "perencanaan pembelajaran (RPM)" | Kepmendikdasmen 126/P/2025, `content-engine.md` |
| Modul Mandiri | RPM lepas jadwal | `product-concept.md` |
| P5 / Projek P5 | Kokurikuler. Bentuknya: lintas disiplin ilmu, Gerakan 7 KAIH, cara lainnya | Panduan Kokurikuler 2025 |
| Kaldik | Kalender pendidikan | Permendikdasmen 13/2025 |
| Absensi | Kehadiran murid (menu: **Kehadiran**). Daftar hadir untuk cetakan | Rapor memuat "ketidakhadiran" (Panduan Pembelajaran dan Asesmen 2025) |
| Agenda Harian / Jurnal Harian | Jurnal mengajar | S-01.6 |
| Refleksi Pembelajaran (per pertemuan) | Catatan per pertemuan di jurnal, plus **Refleksi guru** per semester | D-214 A4, D-210 |
| Penilaian Harian (PH) | Asesmen sumatif lingkup materi, atau asesmen formatif sesuai tujuannya | Panduan Pembelajaran dan Asesmen 2025 |
| Ulangan / Analisis Ulangan | Asesmen / Analisis hasil asesmen | idem |
| Taraf Serap | Daya serap / ketercapaian TP | idem |
| Remidial | Remedial | KBBI |
| Inject e-Rapor | Ekspor ke e-Rapor | D-218 |
| Kartu Ujian / Lembar Jawab | Lembar jawaban murid (LJK) | — |
| Scan Jawaban | Pindai LJK | — |
| Asesmen Awal | Asesmen diagnostik | D-214 A2 |
| Gaya Belajar | Preferensi belajar (informasi pendukung, bukan label) | `data/pilihan-isian.json` (asesmen diagnostik) |
| Anekdot | Catatan anekdot | — |
| Buku Humas | Buku komunikasi dengan orang tua dan masyarakat | S-15.7 |
| Regu Piket | Kelompok piket | — |
| Tabungan Kelas / Nasabah | Tabungan murid / murid penabung | D-219 |
| Keuangan Lain / Kurang Bayar | Sumbangan sukarela kelas / sisa dari kesepakatan | §U-11 |
| Mutasi Masuk/Keluar, Pindah Rombel | Mutasi masuk, mutasi keluar, pindah rombongan belajar | Permendikdasmen 3/2025 |
| Pengesahan Elektronik / TTE Kepala Sekolah | **Pengesahan dokumen** (dengan kode verifikasi). Tidak disebut "tanda tangan elektronik" | §U-04 |
| Download Paket | Unduh Paket Administrasi Pembelajaran | §U-03 |
| Status "Kurang 182" | "Belum lengkap: 182" | `design-system.md` (bahasa ramah) |
| "aktivitas" (baris tanpa target) | "entri" atau "catatan" | — |
| Pemberitahuan / Papan Siaran | Kabar | `product-concept.md` |

## 3. Ringkasan usulan

Status: **Baru** = belum ada di dokumen SIGRAK. **Perluas** = sudah disebut, perlu diperinci. **Ada** = sudah ada, hanya perlu dikonfirmasi.

| ID | Usulan | Status | Slice | Prioritas | Keputusan terkait |
|---|---|---|---|---|---|
| U-01 | Mesin Kelengkapan Administrasi (`app/Progress/`) untuk Hari Ini, Kepala Sekolah, Pengawas | Baru | P5-07, dipakai P5-09 | Tinggi | D-029, D-214, D-224, D-240 |
| U-02 | Pertemuan tidak terlaksana dengan alasan, dan tanda "Kegiatan sekolah" | Perluas | P5-07 | Tinggi | data model `meetings`, `content-engine.md` |
| U-03 | Unduh Paket Administrasi Pembelajaran (sampul, daftar isi, ringkasan kelengkapan) | Baru | P5-07, lalu P5-09 | Tinggi | XP-6, D-031, S-01, S-15 |
| U-04 | Pengesahan dokumen oleh kepala sekolah dengan kode verifikasi (hanya untuk yang login) | Baru | P5-09 | Sedang | D-010, D-019, D-153 |
| U-05 | Kalender pendidikan: seed libur nasional, kalender rujukan wilayah, tabel hari dan minggu efektif | Perluas | P5-03 (seed), P5-04 (Prota/Promes) | Tinggi | D-018, D-214 A11 |
| U-06 | Susun draf jadwal otomatis dari alokasi JP | Baru | P5-03e (sedang berjalan) atau sesudahnya | Tinggi | D-247…D-251 |
| U-07 | Kelompok piket otomatis | Perluas | P5-07 | Rendah | `duty_rosters` |
| U-08 | Pindai LJK lewat kamera (OMR), fitur inti | Baru | P5-08 | Tinggi | Wawancara 2026-11-09, D-036, A7 |
| U-09 | Pengolahan nilai rapor dengan bobot, hanya dari sumatif | Perluas | P5-08 | Tinggi | D-205, D-214 |
| U-10 | Kenaikan kelas membawa saldo tabungan | Perluas | P5-07, P5-08 | Sedang | D-215 H3/H4, D-219 |
| U-11 | Sumbangan sukarela kelas (per jenis, diterima, sisa) | Perluas | P5-07 | Sedang | D-158, D-219, wawancara 2026-11-09 |
| U-12 | Status kesiapan RPM per mapel dan Kabar | Baru | P5-04, P5-07 | Tinggi | `content-engine.md` |
| U-13 | Kunci semester 2 dari `academic_years` | Ada | P5-04 | — | migrasi `academic_years` |
| U-14 | Bunyi dan notifikasi layar saat pembuatan selesai | Baru | P5-04 | Rendah | `design-system.md` (sound toggle) |
| U-15 | Sampul dokumen bersama, margin bawaan per jenis dokumen, Pengaturan Cetak | Perluas | P5-04 | Sedang | XP-7, D-031, D-125 |

## 4. Keputusan pemilik dari wawancara 9 November 2026

| # | Pertanyaan | Keputusan |
|---|---|---|
| W-1 | Bahasa dokumen usulan | Bahasa Indonesia. Claude Code membuat draft berbahasa Inggris untuk repo |
| W-2 | Verifikasi pengesahan dokumen | **Hanya untuk pengguna yang login.** Tidak ada halaman publik. D-010 tetap utuh |
| W-3 | Posisi pindai LJK (OMR) | **Fitur inti di slice Asesmen (P5-08)**, bukan add-on |
| W-4 | Keuangan Lain / iuran kelas | **Dicatat sebagai sumbangan sukarela.** Tidak ada kata "tunggakan" atau "kurang bayar". Sekolah bisa mematikan fiturnya |

## 5. Rincian usulan

Setiap butir memakai susunan yang sama: **Di aplikasi lama**, **Di SIGRAK sekarang**, **Usulan**, **Data**, **Pembeda**, **Kriteria terima**, dan **Terbuka** bila ada.

### U-01 Mesin Kelengkapan Administrasi (`app/Progress/`)

**Di aplikasi lama.** Panel "Progres Administrasi" di portal guru berisi kolom Target, Tersedia, Belum, dan Status untuk rentang tanggal tertentu. Baris yang punya target: Modul Ajar, Jurnal, Refleksi, dan Absensi. Sekitar 20 baris lain hanya menampilkan jumlah "aktivitas". Rumus yang sama disalin ke super admin dan monitor pengawas. Aturan yang perlu diadopsi:

- mapel yang diampu guru mapel pengguna aplikasi tidak dihitung sebagai target guru kelas;
- mapel yang dipegang guru di luar aplikasi didaftar terpisah;
- kokurikuler yang diampu guru mapel menjadi target guru mapel itu;
- rincian hanya menampilkan yang belum ada, beserta hari dan tanggalnya.

Lihat `data/aturan-progres-aplikasi-lama.json`.

**Di SIGRAK sekarang.** Hari Ini ("yang masih perlu diisi, progres minggu ini") dan Ringkasan Kepala Sekolah ("kelengkapan per guru") sudah disebut di `product-concept.md`, tapi belum ada rumus atau service.

**Usulan.**

1. Domain baru `app/Progress/` dengan satu titik masuk:
   - `ProgressCalculator::forTeacher(User $teacher, AcademicYear $year, CarbonPeriod $range): ProgressReport`
   - `ProgressCalculator::forSchool(School $school, AcademicYear $year, CarbonPeriod $range): SchoolProgressReport`

   `forSchool` menyusun hasil dari `forTeacher` dan **tidak pernah** menghitung ulang dengan rumus lain.
2. `ProgressReport` berisi baris `ProgressRow` dengan field `kind`, `target` (nullable), `available`, `missing`, `status`, dan `missingItems` (tanggal, kelas, mapel, tautan ke halaman pengisian).
3. Jenis baris dan targetnya:

| Jenis | Target | Sumber "tersedia" | Catatan |
|---|---|---|---|
| RPM | Slot pelajaran guru dalam rentang, pada hari efektif, dikurangi pertemuan `not_done` dan hari kegiatan sekolah (U-02) | `meetings` dengan `planned_on` di slot itu | Guru hanya dihitung untuk mapel yang ia ampu (`teaching_assignments`, D-240). Mapel "Belum ada guru" muncul sebagai info, bukan target |
| Jurnal mengajar | Slot yang sama, tapi hanya sampai hari ini | `journals` berstatus `done` atau `not_done` dengan alasan | `not_done` dengan alasan tetap dihitung **tersedia**, karena guru sudah melapor |
| Kehadiran murid | Hari efektif dalam rentang, sampai hari ini | `attendance_records` per hari per kelas | Khusus wali kelas (D-029). Guru mapel tidak punya baris ini |
| Refleksi guru | 1 per semester | Refleksi semester (A4) | **Bukan per pertemuan** (pembeda P6) |
| Dokumen tahunan dan semester | 1 per mapel per tahun (Prota), 1 per mapel per semester (Promes, rencana asesmen) | dokumen terkait | Target biner: ada atau belum |
| Asesmen diagnostik | 1 per kelas di awal tahun | A2 | Wali kelas |
| Lainnya (piket, inventaris, catatan murid, mutasi, sumbangan, tabungan) | Tanpa target | Jumlah entri | Ditampilkan sebagai "Ada (n entri)" atau "Belum ada" |

4. Status ditampilkan dengan teks dan warna, tidak boleh dengan warna saja (`design-system.md`): **Lengkap**, **Belum lengkap: n**, **Belum dimulai**, **Tidak berlaku**.
5. Tampilan per peran:
   - **Guru (Hari Ini):** miliknya sendiri, lengkap dengan rincian dan tautan "Isi sekarang".
   - **Kepala sekolah (Ringkasan, Pantau Guru):** per guru, dengan rincian.
   - **Pengawas:** **hanya rekap sekolah**, berupa persentase kelengkapan per jenis dan sebaran status, tanpa nama guru (D-224).
   - **Console Tim SIGRAK:** sama dengan pengawas. Detail per guru hanya dibuka saat menangani tiket bantuan, dan aksesnya dicatat.
6. Kinerja: hasil per guru per hari disimpan di Redis dan dibuang ketika ada event yang mengubah sumbernya (jurnal disimpan, pertemuan berubah, jadwal berubah, kalender berubah). Setiap Senin pukul 00.30 WIB sebuah job menulis `progress_snapshots` per guru untuk grafik tren mingguan. Grafik tren ini tidak ada di aplikasi lama.
7. Komponen "format Kudus" (24 untuk guru kelas, 18 untuk guru mapel) dipakai sebagai **profil cetak opsional** untuk sekolah yang masih memakai instrumen itu (`data/komponen-administrasi-pembelajaran.json`). Profil bawaannya S-01 dan S-15.

**Data.** Tabel baru `progress_snapshots` (`school_id`, `teacher_id`, `academic_year_id`, `week_starts_on`, `kind`, `target`, `available`), unik per (`teacher_id`, `week_starts_on`, `kind`). Tidak ada kolom progres di tabel lain.

**Pembeda.** P1 (satu service), P3 (rekap pengawas), P6 (refleksi semester), dan tren mingguan.

**Kriteria terima.**
- Test fitur: untuk satu guru dan rentang yang sama, angka di Hari Ini, Pantau Guru, dan rekap pengawas identik (rekap = jumlah dari `forTeacher`).
- Test: guru kelas tidak mendapat target untuk mapel yang `teaching_assignments`-nya milik guru mapel.
- Test: pertemuan `not_done` mengurangi target RPM, tetapi tidak mengurangi target jurnal.
- Test: rekap pengawas tidak memuat `teacher_id` maupun nama guru (uji bentuk respons Inertia).

**Terbuka.** Rentang bawaan: "awal tahun ajaran sampai hari ini" (seperti aplikasi lama) atau "minggu ini" untuk Hari Ini? Usulan: Hari Ini memakai minggu ini, Pantau Guru memakai semester berjalan.

### U-02 Pertemuan tidak terlaksana dan tanda "Kegiatan sekolah"

**Di aplikasi lama.** Di jurnal, guru bisa menandai pertemuan "tidak terlaksana". Modulnya otomatis bergeser ke slot JP berikutnya, dan pertemuan itu tidak masuk target. Jurnal juga punya tombol "Kegiatan Sekolah" untuk hari tanpa pelajaran.

**Di SIGRAK sekarang.** `meetings.status` sudah mengenal `not_done`, dan aturan penggeserannya ada di `content-engine.md`. Belum ada alasan, dan belum ada cara menandai satu hari sebagai kegiatan sekolah untuk satu kelas.

**Usulan.**

1. `journals` dan `meetings` mendapat `not_done_reason` ∈ {`school_event`, `teacher_duty`, `students_absent`, `disaster_or_weather`, `other`} dan `not_done_note`. Labelnya: Kegiatan sekolah, Guru bertugas di luar, Murid tidak hadir, Bencana/cuaca, Lainnya.
2. **Tanda hari "Kegiatan sekolah".** Wali kelas atau operator bisa menandai satu tanggal sebagai kegiatan sekolah untuk satu kelas atau seluruh sekolah. Usulannya `calendar_events` mendapat kolom `class_id` yang nullable. Bila diisi, kejadian itu hanya berlaku untuk kelas tersebut. Jenis `no_lessons` sudah tersedia. Hari itu tidak punya pertemuan, tidak masuk hari efektif kelas itu, dan pertemuan yang sudah dijadwalkan ikut bergeser.
3. Penggeseran mengikuti aturan yang sudah ada, ditambah satu aturan: bila geseran membuat pertemuan melewati tanggal SAS semester itu, guru dan kepala sekolah menerima Kabar "n pertemuan tidak muat sebelum SAS".
4. Alasan tidak terlaksana direkap untuk kepala sekolah, misalnya "Bulan ini 12 JP hilang karena kegiatan sekolah". Rekap ini bahan evaluasi kalender dan program sekolah, dan tidak ada di aplikasi lama.

**Data.** `meetings.not_done_reason`, `journals.not_done_reason`, `journals.not_done_note`, `calendar_events.class_id` (nullable, FK ke `classes`, harus sekolah yang sama).

**Pembeda.** Alasan tercatat, rekap JP yang hilang, dan peringatan "tidak muat sebelum SAS".

**Kriteria terima.** Satu test per alasan. Test bahwa tanda kegiatan sekolah untuk kelas 5A tidak menggeser pertemuan kelas 5B. Test bahwa Kabar muncul ketika ada pertemuan yang melewati SAS.

### U-03 Unduh Paket Administrasi Pembelajaran

**Di aplikasi lama.** Tombol "Download Paket" mengunduh semua administrasi dalam rentang tanggal. Dokumen kosong tetap tercetak dengan tanda "NIHIL".

**Di SIGRAK sekarang.** Belum ada. XP-6 sudah mengatur bahwa satu paket dibuat dari satu view dengan pemisah halaman, tidak digabung.

**Usulan.**

1. Tombol **Unduh Paket Administrasi** ada di Hari Ini (guru) dan di Pantau Guru (kepala sekolah, untuk satu guru).
2. Pilihan:
   - rentang: semester berjalan, tahun ajaran, atau tanggal bebas;
   - profil: **Standar SIGRAK** (urutan S-01, ditambah S-15 untuk wali kelas) atau **Format Kudus** (24 atau 18 komponen);
   - komponen bisa dicentang atau dilepas;
   - mapel (untuk guru mapel).
3. Susunan paket:
   1. sampul (U-15);
   2. **lembar ringkasan kelengkapan**, berupa tabel butir instrumen dengan status Ada/Belum, jumlah, dan rentang tanggal, diambil dari U-01;
   3. daftar isi dengan nomor halaman;
   4. setiap komponen didahului halaman judul bagian;
   5. komponen kosong dicetak dengan satu halaman "NIHIL" dan keterangan rentang tanggal.

   Lembar ringkasan inilah yang membuat kepala sekolah bisa langsung mengisi S-01 dan S-15.
4. Dibuat sebagai job di antrean `exports`, dengan halaman progres (pola yang sama dengan `architecture.md`). Berkas disimpan 7 hari lalu dihapus otomatis. Bila datanya tidak berubah, berkas yang sama dipakai ulang.
5. Teknis: ikuti XP-6, yaitu satu view `exports/admin-package/pdf.blade.php` yang meng-include view tiap komponen dengan `page-break-before`. Nomor halaman memakai footer Gotenberg (XP-10). Daftar isi dua lintasan: render pertama untuk mengukur halaman, render kedua untuk mengisi nomor. Bila terlalu berat, lihat Terbuka.
6. Ekspor DOCX tidak disediakan untuk paket. Komponen satuan tetap punya DOCX masing-masing.

**Pembeda.** P5 (ringkasan sesuai instrumen), profil standar dan profil daerah, berkas dipakai ulang, PDF teks yang bisa dicari.

**Kriteria terima.** Paket untuk guru tanpa data tetap terbentuk, berisi sampul, ringkasan, daftar isi, dan halaman NIHIL. Nomor halaman di daftar isi cocok dengan halaman sebenarnya (test dengan pdftotext). Pengguna dengan peran guru tidak bisa mengunduh paket guru lain (policy).

**Terbuka.** Kalau paket satu tahun melebihi sekitar 300 halaman, apakah boleh memakai route merge Gotenberg (PDF per komponen lalu digabung)? Itu berarti mengubah pernyataan D-031 bahwa "PDF merging is not needed". Usulan: izinkan merge **hanya** untuk paket, dan catat sebagai keputusan baru.

### U-04 Pengesahan dokumen oleh kepala sekolah dengan kode verifikasi

**Di aplikasi lama.** Guru menekan "Ajukan Pengesahan". Server menghitung sidik jari SHA-256 dari teks dokumen. Setelah kepala sekolah mengesahkan, kode QR dan kode pengesahan dicetak di kolom tanda tangan kepala sekolah, tetapi hanya selama isi dokumen sama persis dengan versi yang disahkan. QR mengarah ke halaman verifikasi publik.

**Di SIGRAK sekarang.** D-019 mengatur validasi untuk perubahan kurikulum dan komentar pada dokumen. Pengesahan dokumen belum ada. D-010 melarang halaman publik.

**Usulan (sesuai W-2: hanya untuk yang login).**

1. Dokumen dari template (RPM, Prota, Promes, rencana asesmen, paket administrasi) mendapat tombol **Ajukan pengesahan**. Kepala sekolah melihatnya di antrean **Validasi**, lalu memilih **Sahkan** atau **Kembalikan dengan catatan**.
2. Saat disahkan, server menghitung `content_hash` = SHA-256 dari teks kanonik revisi itu (HTML tanpa tag, spasi dirapikan, Unicode NFC) dan membuat `code` berisi 10 karakter base32 tanpa huruf yang mirip (contoh: `K7QM-2XRA-9P`).
3. Saat dokumen dicetak, bila revisi aktif sama dengan revisi yang disahkan, kolom tanda tangan kepala sekolah memuat QR, kode, dan tulisan "Disahkan melalui SIGRAK, [tanggal]". Bila isinya sudah berubah, QR tidak dicetak dan dokumen berstatus **Berubah setelah disahkan** sampai diajukan ulang.
4. QR berisi URL di dalam aplikasi, `/verifikasi/{code}`, dan **hanya bisa dibuka setelah login**. Yang boleh melihat hasilnya: warga sekolah itu dan pengawas yang ditugaskan ke sekolah itu (`supervisor_assignments`). Selain itu yang muncul hanya "Kode tidak ditemukan atau Anda tidak berwenang", tanpa membedakan dua keadaan itu agar kode tidak bisa ditebak.
5. Halaman verifikasi menampilkan: status (Sah, Berubah setelah disahkan, atau Dicabut), jenis dokumen, kelas, mapel, semester, nama sekolah, tanggal pengesahan, nama pengesah, dan sidik jari singkat. Ada juga tombol **Cocokkan berkas**: pengguna mengunggah PDF, server mengambil teksnya dan membandingkan hash. Fitur ini tidak ada di aplikasi lama.
6. Kode juga bisa diketik manual di menu **Periksa kode pengesahan** (portal kepala sekolah dan pengawas), untuk cetakan yang QR-nya rusak.
7. Istilahnya **pengesahan dokumen**. Jangan menyebutnya "tanda tangan elektronik", karena istilah itu punya arti hukum tersendiri (UU ITE dan PP 71/2019; TTE tersertifikasi memerlukan penyelenggara sertifikasi). Pengesahan ini adalah bukti internal sekolah. Tanda tangan basah tetap bisa dibubuhkan.

**Data.** `document_approvals`: `public_id`, `school_id`, `document_id`, `document_revision_id`, `content_hash` (char 64), `code` (unik), `status` ∈ {`requested`,`approved`,`returned`,`revoked`}, `requested_by`, `decided_by`, `decided_at`, `note`.

**Benturan.** D-153 menyimpan 5 revisi terakhir. Revisi yang sedang disahkan **tidak boleh** ikut dipangkas oleh job prune, jadi perlu keputusan baru.

**Pembeda.** P3 (tidak ada halaman publik), cocokkan berkas, istilah yang tidak menyesatkan secara hukum.

**Kriteria terima.** Test: edit setelah disahkan menghapus QR dari cetakan. Test: pengawas yang tidak ditugaskan mendapat respons yang sama dengan kode yang tidak ada. Test: revisi yang disahkan tetap ada setelah job prune berjalan.

### U-05 Kalender pendidikan: libur nasional, kalender rujukan, dan tabel hari efektif

**Di aplikasi lama.** Kalender pendidikan disusun sekali lalu dipakai bersama oleh guru-guru satu sekolah. Ada fitur "Pilih rujukan". Libur nasional dan cuti bersama (25 tanggal untuk 2026/2027) sudah ditandai otomatis dari `libur-nasional.js`, yang bersumber dari SKB 3 Menteri. Tabel hari efektif menampilkan per bulan: hari berjadwal, libur, PTS/SAS, kegiatan, dan hari efektif. Ada juga total per semester dan per tahun, misalnya 143 + 136 = 279 hari ≈ 46,5 minggu.

**Di SIGRAK sekarang.** `calendars` dimiliki wilayah atau sekolah, dan `calendar_events.kind` ∈ {holiday, exam, school_event, no_lessons}. A11 (minggu efektif dicetak bersama Prota/Promes) sudah disetujui. Belum ada data libur nasional dan belum ada tabel hari efektif.

**Usulan.**

1. **Tabel rujukan `national_holidays`** (`date`, `name`, `kind` ∈ {`national_holiday`,`joint_leave`}, `decree`, `academic_year`). Ini bukan bagian dari `calendars`, karena berlaku nasional dan hanya-baca. Semua tampilan kalender menggabungkannya otomatis. Seed awal: `data/libur-nasional-cuti-bersama.json`, setelah dicocokkan dengan SKB resmi.
2. Perintah `php artisan calendar:import-holidays <file>` (idempoten per tanggal). Setiap September, setelah SKB tahun berikutnya terbit, Tim SIGRAK memperbarui data lewat console. Sampai SKB terbit, tahun yang belum tercakup diisi dari libur bertanggal tetap (1 Januari, 1 Mei, 1 Juni, 17 Agustus, 25 Desember) dan diberi tanda "sementara".
3. **Kalender rujukan wilayah** (sudah ada di model sebagai kalender milik `region`) dibuat Tim SIGRAK dari kalender pendidikan dinas, misalnya Kabupaten Kudus. Operator sekolah memakai **Salin dari kalender wilayah**, lalu menambah kegiatan sekolah. Yang menyusun kalender sekolah adalah operator, bukan guru per guru seperti di aplikasi lama. Guru hanya membaca, dan bisa menambah tanda kegiatan sekolah untuk kelasnya (U-02).
4. **Tabel hari efektif dan minggu efektif** dihitung per kelas, karena jadwal per kelas bisa berbeda (5 atau 6 hari):
   - hari efektif = hari yang punya slot pelajaran di jadwal kelas, dikurangi libur nasional, cuti bersama, kejadian `holiday`, `exam` (STS, SAS), `school_event` dan `no_lessons` sekolah atau kelas itu;
   - minggu efektif = hari efektif ÷ jumlah hari sekolah per minggu kelas itu, dibulatkan satu desimal di tampilan, dengan angka utuh disimpan;
   - kolom tabel: Bulan, Hari sekolah, Libur, Asesmen (STS/SAS), Kegiatan, Hari efektif, Minggu efektif. Ditambah subtotal semester 1 (sampai sehari sebelum `even_semester_starts_on`), subtotal semester 2, dan total setahun.
   Tabel ini menjadi sumber "rincian minggu efektif" untuk S-01.1, Prota dan Promes (A11), target U-01, dan penjadwalan RPM.
5. **Pemeriksa bentrok** dipakai saat operator menambah kejadian. Menaruh asesmen di hari libur nasional ditolak. Kegiatan di hari Minggu atau bertepatan dengan rapat memunculkan peringatan.

**Data.** Tabel `national_holidays`, kolom `calendar_events.class_id` (U-02), dan service `app/Calendar/EffectiveDays.php` yang dipakai U-01, U-05, U-12, dan template Prota/Promes.

**Pembeda.** Kalender milik sekolah, bukan salinan per guru. Hari efektif per kelas. Data libur bisa diperbarui terpusat. Tanggal yang belum pasti diberi tanda sementara.

**Kriteria terima.** Dengan kalender contoh 2026/2027 dan jadwal 6 hari, tabel hari efektif menghasilkan angka yang sama dengan contoh aplikasi lama (143 dan 136 hari) bila kejadiannya sama. Test: libur nasional tetap muncul walau sekolah belum membuat kalender.

**Terbuka.** Kabupaten mana saja yang perlu kalender rujukan selain Kudus (Jepara dan Pati sudah ada di direktori sekolah, D-170)?

### U-06 Susun draf jadwal otomatis

**Di aplikasi lama.** Guru menekan "Susun Otomatis" dan mengisi jadwal kelasnya sendiri, satu kelas saja, tanpa mengetahui guru mapel yang mengajar di kelas lain.

**Di SIGRAK sekarang.** P5-03e (jadwal) sedang dikerjakan dengan aturan D-247 (baris istirahat, upacara, kegiatan), D-248 (bentrok guru ditolak), D-249 (guru diambil dari `teaching_assignments`), D-250 (JP harus sama dengan alokasi), dan D-251 (agama paralel). Operator mengisi jadwal baris demi baris.

**Usulan.**

1. Tombol **Susun draf jadwal** di halaman Jadwal operator, untuk **satu kelas** atau **semua kelas sekaligus**.
2. Masukannya:
   - kerangka hari yang sudah dibuat operator (baris waktu, termasuk istirahat dan upacara, D-247);
   - alokasi JP per mapel per kelas (`SubjectsForGrade`, D-230);
   - guru tiap mapel (`teaching_assignments`);
   - slot yang sudah terisi dan dikunci operator.
3. Algoritmanya pencarian dengan batasan (greedy dengan backtracking terbatas):
   - **Batas keras:** tidak ada bentrok guru (D-248) maupun bentrok kelas, kecuali mapel agama yang memang boleh paralel (D-251). JP per mapel sama dengan alokasi (D-250). Slot yang dikunci tidak disentuh.
   - **Urutan:** isi lebih dulu mapel yang paling terbatas, yaitu guru mapel yang mengajar di banyak kelas (PJOK, PAI, Bahasa Inggris, Bahasa Jawa).
   - **Preferensi lunak (bobot bisa diatur sekolah):** paling banyak 2 JP berurutan per mapel per hari (3 untuk PJOK bila diizinkan); sebaran merata dalam seminggu; PJOK sebelum pukul 10.00; Matematika dan Bahasa Indonesia di jam awal; JP satu mapel tidak dipisah oleh istirahat.
4. Keluarannya **draf**, tidak langsung disimpan. Layar perbandingan menampilkan draf dan jadwal sekarang, JP per mapel (terjadwal dibanding alokasi), dan daftar JP yang **tidak bisa ditempatkan** beserta alasannya, misalnya "Guru PJOK sudah penuh di Selasa jam 1–4". Operator bisa **Terapkan semua**, **Terapkan kelas ini**, atau mengubah per slot. Setiap penyimpanan tetap melewati validasi D-248 s.d. D-251.
5. Batas waktu proses sekitar 10 detik per sekolah, dijalankan di job antrean `default`. Hasilnya bisa diulang dengan **Acak ulang**.

**Pembeda.** Disusun untuk seluruh sekolah dengan memperhatikan guru lintas kelas, draf yang bisa dibandingkan, dan alasan untuk JP yang tidak tertempatkan.

**Kriteria terima.** Untuk data contoh 6 kelas dengan 3 guru mapel bersama, draf memenuhi semua batas keras. Test acak (property test): draf apa pun yang disimpan lolos validator D-248 s.d. D-251.

**Terbuka.** Masuk P5-03e sekarang, atau menjadi tugas lanjutan P5-03e2 setelah pengisian manual selesai? Usulan: P5-03e2, agar P5-03e tidak tertunda.

### U-07 Kelompok piket otomatis

**Di aplikasi lama.** Fitur "Susun Otomatis" membagi murid ke regu per hari, lalu guru bisa mengatur regu per hari.

**Di SIGRAK sekarang.** Menu Kelas Saya > Piket dan tabel `duty_rosters` sudah ada. Aturan pembagiannya belum ada.

**Usulan.**

1. Tombol **Bagi otomatis** membagi murid aktif kelas ke 5 atau 6 hari, sesuai hari sekolah kelas itu. Jumlah murid per hari dibuat seimbang (selisih paling banyak 1), dan **komposisi laki-laki dan perempuan juga seimbang**.
2. Opsi **Rotasi**: kelompok berganti hari setiap 1, 2, atau 4 minggu, supaya murid tidak terus mendapat hari yang sama.
3. Opsi **Tugas per murid**, misalnya menyapu, menghapus papan, merapikan meja, membuang sampah, atau menyiram tanaman. Tugas bergilir di dalam kelompok. Daftar tugas bisa diubah wali kelas.
4. Murid yang mutasi keluar otomatis dilepas dari kelompoknya. Murid baru masuk ke kelompok yang paling kecil, dan wali kelas mendapat Kabar.
5. Hasilnya berupa draf yang bisa diubah dengan seret-lepas, lalu disimpan. Cetakannya memakai sampul U-15. Jadwal piket juga menjadi bukti untuk S-15.9.

**Pembeda.** Seimbang jenis kelamin, rotasi, tugas bergilir, dan tetap benar ketika ada mutasi.

**Kriteria terima.** Untuk 20 murid (6 laki-laki, 14 perempuan) dan 6 hari, selisih jumlah per hari paling banyak 1, dan selisih jumlah laki-laki per hari paling banyak 1.

### U-08 Pindai LJK lewat kamera (OMR), fitur inti P5-08

**Di aplikasi lama.** Aplikasi mencetak lembar jawaban dengan 4 kotak hitam di sudut dan kode QR. Guru memindai dengan kamera HP. Soal PG dan PG kompleks dinilai langsung. Untuk soal isian, **potongan gambar tulisan tangan murid dikirim ke Gemini**. Semua hasil diperiksa guru, lalu dimasukkan ke tabel analisis.

**Di SIGRAK sekarang.** Belum ada. P5-08 mencakup analisis butir (A7) dan per TP. `assessment_items` sudah menyimpan `answer_key`.

**Usulan (sesuai W-3: fitur inti).**

1. **Cetak LJK** dari halaman asesmen. Satu lembar per murid, nama murid tercetak, dengan 4 penanda sudut dan QR. QR hanya berisi `public_id` lembar (acak, bukan NISN atau nama). Server menyimpan pasangan lembar → asesmen → murid → versi tata letak. Tersedia ukuran A4 dan F4. Hitam-putih, aman untuk difotokopi.
2. Bentuk soal yang dipindai: **pilihan ganda** (A–D atau A–E), **pilihan ganda kompleks** (lebih dari satu bulatan), dan **benar-salah**. Isian singkat dan uraian diisi manual di tabel nilai.
3. **Tidak ada AI untuk membaca jawaban murid.** Tulisan tangan murid adalah karya murid, dan free tier Gemini bisa ditinjau manusia (`ai-provider-engine.md`). Ini pembeda privasi yang tegas dibanding aplikasi lama (P3, D-036).
4. **Pemindaian berjalan di perangkat.** Bingkai kamera diolah di peramban: deteksi penanda sudut, koreksi perspektif, lalu ukur tingkat gelap setiap bulatan. Pustaka dibundel dan versinya dikunci (D-033), dan dimuat hanya saat halaman pindai dibuka. Gambar lembar **tidak diunggah**. Yang dikirim ke server hanya jawaban terbaca dan skor keyakinan.
5. Alur: arahkan kamera ke lembar → terbaca otomatis saat lembar stabil → bunyi pendek dan getar → lanjut ke lembar berikutnya. Jawaban dengan keyakinan rendah (bulatan tipis, dua bulatan, coretan) ditandai kuning di layar **Periksa hasil**. Setelah guru mengonfirmasi, tekan **Simpan ke nilai**, yang menulis ke `scores`. Pindai ulang lembar yang sama memperbarui hasil, tidak menggandakan.
6. **Bisa dipakai tanpa sinyal.** Hasil pindaian antre di perangkat dan dikirim saat online, memakai pola `client_uuid` dari Di Kelas. Ini memperluas D-016 dan perlu keputusan baru.
7. Setelah tersimpan, analisis butir (A7), analisis per TP, dan daftar remedial/pengayaan (A1) langsung terisi. **Kunci jawaban yang diubah setelah pemindaian** membuat skor dihitung ulang otomatis dari jawaban tersimpan, tanpa memindai ulang.
8. LJK yang sama menjadi **cadangan Simulasi TKA versi kertas** bila sekolah tidak punya perangkat (slice TKA). Format butirnya mengikuti bentuk yang bisa dipindai.

**Data.** `answer_sheets` (`public_id`, `assessment_id`, `student_id`, `layout_version`, `printed_at`) dan `sheet_scans` (`answer_sheet_id`, `answers` JSON, `confidence` JSON, `scanned_by`, `scanned_at_device`, `client_uuid` unik, `confirmed_at`).

**Pembeda.** Tanpa AI untuk jawaban murid, gambar tidak diunggah, bisa luring, penilaian ulang otomatis bila kunci diubah, dan cadangan TKA.

**Kriteria terima.**
- Pada set uji 200 lembar cetak dan fotokopi (cahaya kelas, HP kelas menengah), akurasi pembacaan bulatan ≥ 99,5%, dan semua sisa kesalahan tertandai keyakinan rendah.
- Waktu pindai ≤ 2 detik per lembar.
- Test server: jawaban tersimpan tetap valid saat kunci diubah (skor dihitung ulang).

**Terbuka.** Pilihan pustaka (OpenCV.js atau pemrosesan sendiri berbasis canvas) diputuskan lewat spike satu hari pada HP Android kelas bawah.

### U-09 Pengolahan nilai rapor dengan bobot, hanya dari sumatif

**Di aplikasi lama.** Fitur "Konversi" memakai Bobot TP (%), Bobot STS (%), dan Bobot SAS (%), dengan format nilai angka atau ketercapaian (T/R). Hasilnya nilai rapor, lalu ada "Inject e-Rapor".

**Di SIGRAK sekarang.** D-205: nilai formatif tidak pernah menjadi nilai rapor, dan deskripsi diambil dari capaian TP tertinggi dan terendah pada asesmen formatif. D-214: KKTP per TP, dan Fase A boleh tanpa angka.

**Usulan.**

1. **Rumus nilai rapor per mapel** = rata-rata berbobot dari tiga komponen:
   - rerata **asesmen sumatif lingkup materi** (pengganti "Bobot TP" aplikasi lama; bukan formatif);
   - **STS**, bila sekolah melaksanakannya;
   - **SAS**.

   Bobot diatur sekolah di Pengaturan Asesmen, dan jumlahnya harus 100%. Komponen yang tidak dilaksanakan otomatis dikeluarkan, lalu bobot sisanya dinormalkan.
2. Asesmen yang ditandai formatif **tidak bisa** dipilih sebagai komponen. Validasi ini ada di server dan diuji.
3. **Remedial.** Nilai setelah remedial menggantikan nilai lingkup materi itu, dengan batas atas mengikuti kebijakan sekolah (KKTP atau tanpa batas). Riwayat nilai sebelum remedial tetap disimpan.
4. **Fase A tanpa angka** (`report_cards.numeric_grades = false`): yang dihasilkan hanya deskripsi ketercapaian, tanpa nilai akhir.
5. Pratinjau **leger** menampilkan nilai per murid per mapel dan sumber setiap angka (komponen dan bobot). Setelah wali kelas dan guru mapel mengonfirmasi, nilai dikunci untuk semester itu. Membuka kunci hanya bisa lewat kepala sekolah, dan tercatat.
6. Ekspor ke e-Rapor mengikuti D-218 (tugas P4-10).

**Pembeda.** Patuh D-205, bobot transparan sampai ke sumber, kunci nilai beserta jejak audit.

**Kriteria terima.** Test: asesmen formatif ditolak sebagai komponen. Test normalisasi bobot ketika STS tidak dilaksanakan. Test: nilai remedial menggantikan nilai lama dan nilai lama tersimpan.

**Terbuka.** Bobot bawaan. Usulan: lingkup materi 50%, STS 25%, SAS 25%, dan sekolah bisa mengubahnya. Batas atas nilai remedial: KKTP (bawaan) atau tanpa batas?

### U-10 Kenaikan kelas membawa saldo tabungan

**Di aplikasi lama.** Layar kenaikan kelas menampilkan kolom tabungan dan saldo di samping status Naik/Lulus. Tersedia tombol "Tandai semua naik", "Tandai semua lulus", "Simpan draf", dan "Tetapkan kenaikan kelas".

**Di SIGRAK sekarang.** H3 (kenaikan dengan lembar rapat dan berita acara), H4 (kelulusan), dan T1 (tabungan murid, D-219) sudah disetujui, tetapi belum ada aturan tentang saldo saat murid pindah kelas.

**Usulan.**

1. Buku tabungan melekat pada **murid**, bukan pada kelas. `savings_entries` menyimpan `student_id` dan `class_id` pada saat transaksi (untuk rekap per kelas). Saldo = jumlah seluruh transaksi murid di sekolah itu.
2. Saat kenaikan ditetapkan, saldo **otomatis ikut** ke kelas baru tanpa transaksi pindahan. Rekap kelas lama ditutup dengan baris "Saldo dibawa ke kelas [x]". Wali kelas baru melihat saldo awal per murid.
3. Untuk **lulus kelas 6** dan **mutasi keluar**: sistem menyiapkan **berita acara penyerahan tabungan** (nama murid, saldo, tanggal, tanda tangan wali kelas, orang tua/wali, dan kepala sekolah) sekaligus transaksi penarikan penutup. Kelulusan atau mutasi tetap bisa ditetapkan walau tabungan belum diserahkan, tetapi statusnya "Tabungan belum diserahkan" dan muncul di Kabar wali kelas sampai ditutup.
4. Layar kenaikan menampilkan saldo per murid dan total kelas, dengan tanda pada murid yang saldonya belum diserahkan.

**Data.** Buku tabungan T1 (D-219) belum punya tabel di `data-model.md`. Usulan nama dan bentuknya: `savings_entries` (`school_id`, `student_id`, `class_id` saat transaksi, `kind` ∈ {`deposit`,`withdrawal`,`closing_withdrawal`}, `amount` bigint > 0, `occurred_on`, `recorded_by`), ditambah `handover_records` (`student_id`, `amount`, `reason` ∈ {`graduation`,`transfer_out`}, `handed_over_on`, `document_id`).

**Pembeda.** Saldo tidak pernah "tertinggal" di kelas lama, ada berita acara serah terima, dan pengingat sampai urusan tabungan selesai.

**Kriteria terima.** Test: setelah kenaikan, saldo di kelas baru sama dengan saldo sebelumnya. Test: murid lulus dengan saldo di atas 0 tampil di daftar "Tabungan belum diserahkan".

### U-11 Sumbangan sukarela kelas

**Di aplikasi lama.** Menu "Keuangan Lain" mencatat iuran per jenis (misalnya Komputer), lengkap dengan tanggal, nama siswa, nominal bayar, total bayar, dan **kurang bayar**.

**Di SIGRAK sekarang.** `class_finance_entries` (pemasukan dan pengeluaran kelas) bisa dimatikan sekolah, dan Keuangan serta Inventaris kelas adalah add-on (D-158). Pencatatan per murid per jenis belum ada.

**Usulan (sesuai W-4).**

1. Submenu **Sumbangan sukarela** di Kelas Saya > Keuangan. Wali kelas membuat **jenis sumbangan** (nama, keperluan, nominal kesepakatan per murid yang **opsional**, periode, dan tautan ke notulen rapat kelas atau rapat orang tua bila ada).
2. Setiap penerimaan dicatat per murid: tanggal, jumlah, dan pencatat. Penerimaan otomatis masuk `class_finance_entries` sebagai pemasukan, sehingga kas kelas hanya punya satu sumber.
3. Rekap per jenis menampilkan jumlah diterima, jumlah murid yang sudah ikut, dan **sisa dari kesepakatan** (hanya bila nominal kesepakatan diisi). Kata "tunggakan", "kurang bayar", dan "belum lunas" **tidak dipakai** di UI maupun cetakan.
4. **Tidak ada pengingat otomatis ke orang tua soal uang.** Pesan WhatsApp untuk orang tua (H6) tidak menyediakan templat penagihan.
5. **Laporan transparansi kas kelas** berisi pemasukan (sumbangan dan lainnya), pengeluaran, dan saldo, siap dibagikan ke paguyuban. Laporan ini tidak memuat rincian per murid kecuali wali kelas memilihnya.
6. Di Pengaturan Sekolah, saat fitur dinyalakan, muncul catatan: "Di sekolah negeri, iuran wajib tidak diperbolehkan. Catat hanya sumbangan sukarela yang disepakati orang tua." Rujukan yang perlu dicek: Permendikbud 44/2012 (pungutan dan sumbangan di SD) dan Permendikbud 75/2016 (Komite Sekolah).

**Data.** `class_contribution_kinds` (`school_id`, `class_id`, `name`, `purpose`, `agreed_amount` nullable, `period_from`, `period_to`, `minutes_book_id` nullable) dan `class_contributions` (`kind_id`, `student_id`, `amount` bigint > 0, `received_on`, `recorded_by`, `finance_entry_id`).

**Pembeda.** Bahasa yang patuh aturan, satu sumber kas, laporan transparansi, dan tidak ada tekanan penagihan.

**Kriteria terima.** Test: string terlarang ("tunggakan", "kurang bayar", "belum lunas") tidak ada di `lang/id` untuk modul ini. Test: setiap penerimaan membuat satu entri pemasukan kas kelas.

### U-12 Status kesiapan RPM per mapel dan Kabar

**Di aplikasi lama.** Kartu per mapel di generator RPM diberi warna: hijau "modul siap", kuning "segera habis", merah "ada hari tanpa modul". Per ATP terlihat rentang tanggal, jumlah fokus, JP, dan jumlah modul. Ada penanda "jadwal sisa 4 JP sebelum SAS" dan pesan "Belum ada modul sejak 14 Jul".

**Di SIGRAK sekarang.** Template RPM dan penjadwalan pertemuan sudah dirancang. Indikator kesiapan belum ada.

**Usulan.**

1. Service `app/Content/Readiness/RpmReadiness.php` menghitung, untuk setiap (kelas, mapel) yang diampu guru:
   - **Siap** (hijau): semua slot pelajaran dalam **10 hari efektif** ke depan sudah punya pertemuan RPM;
   - **Segera habis** (kuning): RPM habis dalam 4–10 hari efektif;
   - **Perlu RPM** (merah): ada slot dalam **3 hari efektif** ke depan tanpa RPM, atau ada slot yang sudah lewat tanpa RPM. Pesannya menyebut tanggal pertama yang kosong;
   - **Tidak ada jadwal** atau **Belum ada ATP** (abu-abu).

   Ambang 10 dan 3 diatur Tim SIGRAK di console, bukan per guru.
2. **Neraca JP sebelum SAS** per mapel = JP dari ATP yang tersisa dibanding JP slot yang tersedia sampai tanggal SAS (U-05). Kemungkinan hasilnya:
   - **Pas**;
   - **Materi tidak muat: kurang n JP**, dengan saran digabung atau dipadatkan;
   - **Ada n JP cadangan**, dengan saran dipakai untuk remedial atau pengayaan (A1). Saran ini tidak ada di aplikasi lama.
3. Tampil di **Buat** (kartu mapel dengan bilah progres ATP), di **Hari Ini** (hanya yang kuning dan merah), dan di **Pantau Guru** milik kepala sekolah (jumlah mapel merah per guru).
4. **Kabar** dikirim dalam dua bentuk: ringkasan setiap Senin pukul 06.00 WIB ("2 mapel perlu RPM minggu ini"), dan pemberitahuan langsung ketika sebuah mapel berubah menjadi merah (paling banyak 1 per mapel per hari).

**Pembeda.** Neraca JP sebelum SAS beserta sarannya, terhubung ke remedial/pengayaan, dan ambang yang diatur terpusat.

**Kriteria terima.** Test per status dengan jadwal dan kalender contoh. Test: hari libur tidak dihitung sebagai slot. Test: Kabar merah tidak terkirim dua kali di hari yang sama.

### U-13 Kunci semester 2

**Di aplikasi lama.** Modul semester 2 terkunci sampai guru mengisi "Tanggal Mulai Semester Genap" di profilnya.

**Di SIGRAK sekarang.** **Sudah terpenuhi di tingkat data.** `academic_years.even_semester_starts_on` wajib diisi, dengan CHECK `starts_on < even_semester_starts_on < ends_on` (migrasi P5-03d). Tanggal ini diisi operator, bukan guru. `content-engine.md` sudah mengatur bahwa RPM semester 2 menunggu tanggal tersebut.

**Usulan (penyempurnaan).**

1. Guru **boleh menyiapkan RPM semester 2 lebih awal** (misalnya saat libur Desember) sebagai draf. Pertemuannya baru ditempatkan pada slot mulai `even_semester_starts_on`. Di Buat, tampil keterangan "Dijadwalkan mulai [tanggal]". Ini pengganti kunci keras ala aplikasi lama.
2. Bila operator mengubah `even_semester_starts_on`, pertemuan semester 2 dijadwal ulang otomatis, guru terdampak menerima Kabar, dan riwayat jadwal disimpan (`meeting_schedule_history`).
3. Tabel hari efektif (U-05) memakai tanggal ini sebagai batas semester.

**Kriteria terima.** Test: RPM semester 2 yang dibuat di bulan November punya pertemuan pertama pada atau setelah `even_semester_starts_on`. Test penjadwalan ulang ketika tanggal itu diubah.

### U-14 Bunyi dan notifikasi layar saat pembuatan selesai

**Di aplikasi lama.** Pengaturan "Bunyi Saat Modul Selesai" menyediakan pola Chime, Lonceng, dan Bip tegas, dibunyikan 2 kali, dengan tombol hidup/mati. Notifikasi layar hanya muncul bila tab tidak terlihat. Semua pengaturan tersimpan per perangkat.

**Di SIGRAK sekarang.** Pembuatan konten berjalan di latar belakang dan guru diberi tahu setelah selesai (`product-concept.md`). `design-system.md` mewajibkan tombol untuk mematikan suara.

**Usulan.**

1. Pemicu: pembuatan konten AI selesai atau gagal, ekspor atau paket selesai (U-03), dan sinkronisasi hasil pindai LJK selesai (U-08).
2. Selalu ada **toast** dan entri **Kabar**. Bunyi dan notifikasi hanya tambahan, sehingga informasi tidak pernah bergantung pada suara saja.
3. **Bunyi**: tiga pola pendek (Lembut, Lonceng, Tegas) yang disintesis dengan Web Audio, tanpa berkas audio. Hanya dibunyikan bila tab sedang terlihat. Bunyi gagal dibedakan dari bunyi selesai.
4. **Notifikasi layar** lewat Notification API, hanya bila tab tidak terlihat. Mengekliknya membuka hasil. Izin peramban **diminta pada saat pembuatan pertama**, bukan saat halaman dibuka.
5. Pengaturan di **Akun > Tampilan**: bunyi hidup/mati, pola, dan notifikasi layar hidup/mati. Pengaturan **disimpan di server per pengguna**, jadi ikut ke HP dan laptop. Di aplikasi lama pengaturan ini per perangkat.
6. Web Push (notifikasi saat aplikasi tertutup) **tidak** masuk usulan ini. Itu butuh service worker dan kunci VAPID, dan bisa diusulkan terpisah.

**Kriteria terima.** Test komponen: dengan bunyi dimatikan tidak ada `AudioContext` yang dibuat. Test bahwa izin notifikasi tidak diminta saat halaman dimuat.

**Terbuka.** Bunyi menyala atau mati sebagai bawaan? Usulan: menyala, dengan pola Lembut.

### U-15 Sampul dokumen bersama, margin bawaan, dan Pengaturan Cetak

**Di aplikasi lama.** Satu desain sampul hitam-putih untuk semua dokumen cetak. Urutannya dari atas: logo dinas; kata "BUKU"/judul; kelas, mapel, semester, tahun ajaran; disusun oleh (nama dan NIP); dinas, nama sekolah, tahun dokumen. Margin bawaan atas 15, kanan 12, bawah 15, kiri 25 mm (kiri untuk jilid). Orientasi otomatis menjadi lanskap bila tabel punya 9 kolom atau lebih. Tabel kosong diberi baris "NIHIL". Lihat `data/aturan-cetak-aplikasi-lama.json`.

**Di SIGRAK sekarang.** XP-7 (kertas dan margin dari Pengaturan Cetak), XP-8 (kop berupa gambar, hanya di halaman pertama, D-031), XP-10 (nomor halaman), D-125 (dua huruf cetak). `school_print_settings` sudah punya margin, tetapi hanya satu nilai untuk semua dokumen. Sampul belum ada.

**Usulan.**

1. **Sampul bersama** (`exports/layouts/cover.blade.php`) untuk dokumen jenis "buku" dan paket, seperti buku administrasi, Prota/Promes, daftar nilai, dan paket administrasi. Isinya:
   - judul dokumen;
   - kelas, mapel, semester, tahun ajaran;
   - nama sekolah, NPSN, kecamatan, kabupaten/kota;
   - disusun oleh (nama, NIP bila ada, jabatan);
   - tahun dokumen.

   **Aturan tahun dokumen** diambil dari aplikasi lama: semester 1 memakai tahun pertama tahun ajaran, semester 2 tahun kedua, dokumen tahunan memakai tahun berjalan.

   **Logo sampul opsional**: sekolah mengunggah satu logo (PNG, latar transparan) di Pengaturan Cetak. Tanpa logo, sampul tetap rapi. SIGRAK tidak membundel logo resmi pemerintah. Desain hitam-putih. Sampul tidak diberi nomor halaman.
2. **Profil margin per jenis dokumen** di Pengaturan Cetak (`school_print_settings.margin_profiles` JSON):

| Profil | Atas | Kanan | Bawah | Kiri | Dipakai untuk |
|---|---|---|---|---|---|
| Buku administrasi (jilid kiri) | 15 | 12 | 15 | 25 | Paket, buku administrasi, Prota/Promes |
| Dokumen ajar | 20 | 20 | 20 | 20 | RPM, LKPD, soal |
| Lembar tunggal | 15 | 15 | 15 | 15 | Kartu, LJK, lembar presensi |

   Semua nilai dalam mm dan bisa diubah sekolah. Ada opsi **Cetak bolak-balik**: margin kiri dan kanan dicerminkan pada halaman genap memakai `@page :left/:right`.
3. **Orientasi otomatis**: tabel dengan 9 kolom atau lebih dicetak lanskap, kecuali template menetapkan lain.
4. **Aturan NIHIL**: tabel tanpa baris mendapat satu baris "NIHIL" selebar tabel. Komponen paket yang kosong mendapat satu halaman NIHIL (U-03).
5. Kop (D-031) tetap hanya di halaman pertama isi. Pada dokumen bersampul, kop berada di halaman setelah sampul.
6. Usulan aturan baru di `export-localization.md`, melanjutkan nomor terakhir XP-38:
   - **XP-39 Sampul:** layout bersama, tanpa nomor halaman, tahun dokumen menurut semester;
   - **XP-40 Profil margin:** margin dipilih menurut jenis dokumen dari `margin_profiles`, dengan opsi cetak bolak-balik;
   - **XP-41 Orientasi otomatis:** lanskap bila ada tabel dengan 9 kolom atau lebih;
   - **XP-42 NIHIL:** tabel kosong dan komponen kosong;
   - **XP-43 Paket:** daftar isi dengan nomor halaman, halaman judul bagian, ringkasan kelengkapan (U-03);
   - **XP-44 LJK:** tata letak tetap per `layout_version`, penanda sudut, tanpa skala "fit to page" (U-08).

**Pembeda.** Margin per jenis dokumen (aplikasi lama hanya punya satu nilai bawaan untuk portal kepala sekolah), cetak bolak-balik, PDF teks dari server, logo opsional tanpa membundel logo resmi.

**Kriteria terima.** Test visual (snapshot PDF ke PNG) untuk sampul A4 dan F4. Test: margin kiri halaman genap menjadi margin kanan saat cetak bolak-balik aktif. Test: tabel 9 kolom menghasilkan halaman lanskap.

**Terbuka.** Apakah nilai bawaan profil "Dokumen ajar" (20/20/20/20) sudah sesuai kebiasaan sekolah?

## 6. Peta ke slice, tugas, dan dokumen repo

### 6.1 Usulan tugas

| Tugas usulan | Isi | Bergantung pada |
|---|---|---|
| **P4-13** Draft `legacy-upgrade-01.md` (bahasa Inggris) dan keputusan baru | Menerjemahkan dan merapikan dokumen ini, mencatat W-1…W-4 dan jawaban §8 sebagai D-252 dst. | Jawaban pemilik untuk §8 |
| **P5-03h** Libur nasional dan hari efektif | `national_holidays`, `calendar:import-holidays`, `EffectiveDays`, kalender rujukan Kudus (U-05) | P5-03e, validasi data libur |
| **P5-03e2** Draf jadwal otomatis | U-06 | P5-03e selesai |
| P5-04 (tambahan) | U-12, U-13, U-14, U-15 | P5-03h |
| P5-07 (tambahan) | U-01, U-02, U-03 (versi guru), U-07, U-10, U-11 | P5-04 |
| P5-08 (tambahan) | U-08, U-09, U-10 (bagian kelulusan) | P5-07 |
| P5-09 (tambahan) | U-01 (tampilan kepala sekolah dan pengawas), U-03 (versi kepala sekolah), U-04 | P5-08 |
| P4-07, P4-08 (desain) | Masukkan U-10, U-11 ke desain wali kelas, dan U-01, U-04 ke desain portal | — |

### 6.2 Dokumen repo yang perlu diperbarui setelah disetujui

| Dokumen | Perubahan |
|---|---|
| `docs/final/product-concept.md` | Baris baru di *Feature status* (status **Add**) untuk U-01, U-03, U-04, U-06, U-08, U-12, U-14. Istilah menu (lihat §7, B-5) |
| `docs/final/data-model.md` | Tabel baru: `progress_snapshots`, `document_approvals`, `national_holidays`, `answer_sheets`, `sheet_scans`, `savings_entries` (bila belum ditambahkan oleh desain T1), `handover_records`, `class_contribution_kinds`, `class_contributions`. Kolom baru: `meetings.not_done_reason`, `journals.not_done_reason`, `journals.not_done_note`, `calendar_events.class_id`, `school_print_settings.margin_profiles`, `school_print_settings.duplex`, `school_print_settings.cover_logo_path` |
| `docs/final/content-engine.md` | Alasan tidak terlaksana, peringatan "tidak muat sebelum SAS" (U-02), status kesiapan dan neraca JP (U-12), draf RPM semester 2 lebih awal (U-13) |
| `docs/final/export-localization.md` | XP-39…XP-44 (U-15), nama berkas ekspor paket dan LJK |
| `docs/final/design-system.md` | Glosarium §2 ke daftar *Terms*, aturan bunyi (U-14), status teks dan warna (U-01, U-12) |
| `docs/final/architecture.md` | Pemindaian di perangkat dan antrean luring LJK (U-08), job paket (U-03), job snapshot progres (U-01) |
| `docs/drafts/teacher-supervision-instruments.md` | S-01 dan S-15 menunjuk sumber data U-01 dan lembar ringkasan U-03 |
| `docs/data/` | Data dari folder `data/` setelah divalidasi (lihat `data/README.md`) |

## 7. Benturan dengan keputusan yang ada

| # | Keputusan | Benturan | Usulan penyelesaian |
|---|---|---|---|
| B-1 | D-010 (tanpa halaman publik) | Verifikasi pengesahan di aplikasi lama bersifat publik | **Selesai lewat W-2**: verifikasi hanya untuk yang login (U-04) |
| B-2 | D-016 (luring hanya Di Kelas) | Pindai LJK perlu jalan tanpa sinyal di kelas | Perluas D-016: hasil pindai LJK ikut antre luring dengan pola `client_uuid` (U-08) |
| B-3 | D-031 ("PDF merging is not needed") | Paket satu tahun bisa sangat tebal | Izinkan merge Gotenberg **hanya** untuk paket bila satu view terlalu berat (U-03) |
| B-4 | D-153 (simpan 5 revisi terakhir) | Revisi yang disahkan bisa terpangkas | Revisi yang terkait `document_approvals` berstatus `approved` dikecualikan dari pemangkasan (U-04) |
| B-5 | Menu "Di Kelas > Absensi" di `product-concept.md` | Glosarium mengusulkan "Kehadiran" | Pemilik memilih: tetap "Absensi" (sudah akrab di sekolah) atau "Kehadiran" (sesuai istilah rapor). Usulan: **Kehadiran**, dengan kata "absensi" tetap dikenali di pencarian bantuan |
| B-6 | `content-engine.md` (RPM semester 2 menunggu tanggal) | U-13 mengizinkan menyiapkan draf lebih awal | Tidak bertentangan: penempatan pertemuan tetap menunggu tanggal, yang dibuka hanya penyusunan draf. Perlu dicatat di dokumen agar jelas |

## 8. Pertanyaan terbuka untuk pemilik

| # | Pertanyaan | Usulan jawaban |
|---|---|---|
| T-1 | Rentang bawaan progres (U-01) | Hari Ini: minggu ini. Pantau Guru: semester berjalan |
| T-2 | Merge PDF untuk paket tebal (U-03, B-3) | Izinkan hanya untuk paket |
| T-3 | Revisi yang disahkan dikecualikan dari pemangkasan (U-04, B-4) | Ya |
| T-4 | Kabupaten untuk kalender rujukan selain Kudus (U-05) | Kudus dulu, lalu Jepara dan Pati |
| T-5 | Draf jadwal masuk P5-03e atau tugas lanjutan (U-06) | Tugas lanjutan P5-03e2 |
| T-6 | Pindai LJK luring (U-08, B-2) | Ya, perluas D-016 |
| T-7 | Bobot bawaan rapor dan batas nilai remedial (U-09) | 50/25/25. Batas remedial = KKTP, bisa diubah sekolah |
| T-8 | Bunyi bawaan (U-14) | Menyala, pola Lembut |
| T-9 | Margin bawaan "Dokumen ajar" (U-15) | 20/20/20/20 mm |
| T-10 | Nama menu "Absensi" atau "Kehadiran" (B-5) | Kehadiran |
| T-11 | Ambang status kesiapan RPM, 10 dan 3 hari efektif (U-12) | Setuju, bisa diatur di console |

## 9. Rujukan

**Repo `arumiforge/sinaubareng`.** `CLAUDE.md`, `docs/agent/DECISIONS.md` (D-010, D-016, D-019, D-029, D-031, D-036, D-153, D-158, D-205, D-210, D-213…D-219, D-221, D-224, D-240, D-247…D-251), `docs/final/product-concept.md`, `docs/final/data-model.md`, `docs/final/content-engine.md`, `docs/final/export-localization.md`, `docs/final/design-system.md`, `docs/drafts/administration-features.md`, `docs/drafts/teacher-supervision-instruments.md`, `docs/data/regulations/cocurricular-guide-bskap.json`, dan migrasi `academic_years`.

**Aplikasi lama (aset statis, dibaca 8–9 November 2026).** `libur-nasional.js`, `shared-cocurricular-catalog.js`, `shared-cocurricular-autofill.js`, `aplikasi-lama-progres.js`, `aplikasi-lama-cover.js`, `aplikasi-lama-pengesahan.js`, `edx-bunyi.js`, `administrasi.html`, `sandi.html`, `rpm-generator.html`, `kepala-sekolah/principal-supervision.js`, `kepala-sekolah/principal-document-output.js`, `kepala-sekolah/principal-conflict-guard.js`, `pengawas/`, `superadmin.html`. Ukuran dan SHA-256 tercatat di `data/*.json`.

**Aturan (perlu dicek ke dokumen resmi saat dipakai).**
- Permendikdasmen 1/2026 (istilah murid), 13/2025 (struktur kurikulum, kokurikuler, ekstrakurikuler), dan 3/2025 (mutasi).
- Kepmendikdasmen 126/P/2025 (pembelajaran mendalam).
- Panduan Pembelajaran dan Asesmen 2025, Panduan Kokurikuler 2025.
- SKB 3 Menteri tentang hari libur nasional dan cuti bersama 2026 dan 2027.
- UU 11/2008 jo. UU 1/2024 (ITE) dan PP 71/2019 (istilah tanda tangan elektronik).
- Permendikbud 44/2012 (pungutan dan sumbangan di SD) dan Permendikbud 75/2016 (Komite Sekolah).
