# Konsep Produk SIGRAK

<p class="source-note">Diterjemahkan dari <code>docs/final/product-concept.md</code> pada 1 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-02
- **Terakhir diperbarui:** 2026-11-01
- **Sumber:** `Konsep_Awal_Produk_SIGRAK.md`, `Rebranding_dan_Perombakan_Fitur_EDUNEXA.md`, `EDUNEXA-Dokumentasi-Fungsionalitas.pdf` (di `docs/sources/`)

## Tujuan

Dokumen ini menetapkan apa itu SIGRAK, siapa penggunanya, dan apa isi v1. Dokumen ini menjadi rujukan produk untuk setiap tugas rancangan dan pembangunan berikutnya. Dua dokumen pendamping membahas sisi teknis: `content-engine.md` dan `ai-provider-engine.md`. Keputusan dicatat di `docs/agent/DECISIONS.md` (D-007 sampai D-037).

## Ringkasan

SIGRAK (nama sementara) adalah aplikasi web untuk guru dan pimpinan SD/MI. Aplikasi ini mengerjakan dua hal, dan keduanya ada di v1:

1. **Pembuatan konten dengan bantuan AI.** Guru membuat RPM, bank soal, LKPD, game kelas, dan paket latihan TKA. Guru memilih templat dan menjawab pertanyaan pedagogis. Guru tidak pernah menulis prompt. Templat buatan pengembang menetapkan struktur dan desain, dan AI hanya mengisi kontennya. Tidak ada generator umum "buat apa saja", karena guru sudah bisa mendapatkannya langsung dari Gemini (D-024).
2. **Administrasi sekolah.** Absensi, jurnal mengajar, catatan siswa, asesmen dan rapor, data kelas, serta portal kepala sekolah dan pengawas yang dibawa dari EDUNEXA.
3. **Ujian latihan TKA (CBT).** Ujian berbasis komputer yang bekerja seperti Tes Kemampuan Akademik nasional untuk kelas 6. Siswa mengerjakannya di situs ujian terpisah dengan kartu ujian dan token proktor (D-026, D-027).

Siswa bukan pengguna aplikasi. Setiap halaman ada di balik login, dan tidak ada halaman publik. Satu-satunya tempat yang disentuh siswa adalah situs ujian, dan hanya dengan kartu ujian untuk satu sesi.

## Yang berubah dari catatan EDUNEXA

| Topik | Catatan EDUNEXA (29 Okt) | SIGRAK sekarang | Keputusan |
|---|---|---|---|
| Inti produk | Administrasi ("Administrasi beres sambil mengajar") | Pembuatan konten **dan** administrasi | D-009 |
| Stack | CodeIgniter 4 + MySQL 8 + vanilla JS + PWA | Laravel + MySQL 8 + Inertia/Svelte + Tailwind/TailAdmin + Filament. Vanilla JS hanya untuk runtime game | D-003, D-007, D-008 |
| Pengguna | Guru, kepala sekolah, pengawas, operator, super admin | Peran sama, tetapi **siswa jelas berupa catatan data, tidak pernah pengguna**. Hanya dengan login, tanpa halaman publik | D-010 |
| Kunci AI | Satu kunci Gemini per guru, kunci cadangan sekolah opsional | **Hanya paket gratis Gemini.** Beberapa kunci per guru dari project Google milik guru sendiri, dengan round robin dan failover, dengan risiko ditanggung guru. Tanpa kunci sekolah atau kunci platform | D-012, D-023 |
| Pembuatan AI | Prompt per fitur | Satu templat khusus per jenis konten, masing-masing dengan wizard dan job sendiri. Tanpa generator umum. Guru tidak pernah menulis prompt | D-013, D-014, D-024 |
| Ujian | "Mode Ujian" Bank Soal | Mode Ujian tidak aktif untuk sementara. Baru: **ujian latihan TKA (CBT)** dengan kartu ujian dan token proktor | D-026, D-027 |
| Game | Tidak ada | Quiz Battle di v1, dimainkan di kelas (mode tayang) atau sebagai berkas HTML offline yang diunduh | D-011, D-014 |
| Pasar | Hanya Kudus, SD/MI | SD/MI di seluruh Indonesia. Kudus menjadi uji coba dan sumber nilai bawaan | D-018 |
| Menu guru | Hari Ini · Di Kelas · Rencana · Asesmen · Kelas Saya | Hari Ini · Di Kelas · **Buat** · Asesmen · Kelas Saya | D-021 |
| Migrasi | Firebase → SQL bertahap dengan `legacy_id` | Dibangun baru. Hanya data kurikulum yang diimpor, dan guru diundang ulang | D-020 |

Semua yang lain yang disepakati di catatan EDUNEXA tetap berlaku: PDF berbasis teks dengan pengaturan cetak per sekolah, tombol "Buka di Canva", alur validasi materi, dan Di Kelas yang bisa dipakai tanpa sinyal.

## Posisi dan prinsip

**Kategori:** platform berbantuan AI untuk membuat konten pembelajaran, digabung dengan administrasi guru, untuk sekolah dasar.

Prinsip, berurutan menurut prioritas:

1. **Guru menjawab pertanyaan yang mereka pahami secara pedagogis, tidak pernah menulis prompt.** Setiap fitur AI berupa wizard: kelas, mata pelajaran, topik, tujuan pembelajaran, kondisi siswa, durasi, tingkat kesulitan. Sistem sudah tahu JP, jumlah pertemuan, teks CP, dan identitas sekolah, jadi tidak pernah menanyakannya.
2. **Pengembang membangun struktur, AI mengisi konten.** Templat menetapkan tata letak, mekanisme game, dan aturannya. AI menulis soal, pembahasan, dan kegiatan. Hasilnya konsisten dalam mutu, tata letak, dan perilaku.
3. **Guru tetap menjadi penulis.** Setiap hasil AI bisa disunting sebelum dipakai. Setiap bagian bisa direvisi dengan AI tanpa mengulang seluruh dokumen.
4. **Data yang sudah dimiliki sistem tidak pernah ditanyakan lagi.** Kelas dan mata pelajaran berasal dari jadwal pelajaran. Kurikulum, JP, dan tanggal berasal dari data kurikulum dan kalender sekolah.
5. **Bisa dipakai di ponsel murah dengan sinyal lemah** di bagian yang penting: Di Kelas bekerja offline, dan berkas game yang diunduh bisa dimainkan offline.
6. **Data sekolah milik sekolah. Kunci AI dan pemakaian AI milik guru.** Data pribadi siswa tidak pernah dikirim ke AI (prompt paket gratis bisa dibaca peninjau Google; lihat `ai-provider-engine.md`, bagian Aturan privasi).
7. **Hanya bangun yang tidak bisa diberikan Gemini sendiri.** Setiap fitur AI harus menambah struktur, data kurikulum, format sekolah, atau penyajian di kelas yang tidak didapat guru dengan mengobrol dengan Gemini.

## Model akses

- **Hanya dengan login.** Satu-satunya layar tanpa sesi adalah login, penerimaan undangan, dan reset kata sandi. Tidak ada landing page, tautan berbagi publik, atau profil publik di dalam aplikasi. Situs pemasaran, bila dibuat, adalah proyek terpisah.
- **Siswa berupa catatan data, bukan akun.** Siswa punya nama, NISN, kelas, absensi, catatan, dan nilai, semuanya dikelola guru. Siswa tidak pernah masuk ke aplikasi.
- **Satu-satunya pengecualian adalah ujian latihan TKA** (D-027). Siswa masuk ke situs ujian terpisah (misalnya `ujian.<domain>`) dengan kartu ujian (nama pengguna + kata sandi) yang hanya berlaku untuk satu sesi ujian, ditambah token proktor yang berlaku. Situs ujian hanya menampilkan soal ujian, dan tidak pernah memanggil AI. Ketentuan Gemini melarang pemakaian API di layanan yang kemungkinan dipakai orang di bawah 18 tahun.
- **Game sampai ke siswa lewat dua cara** (D-011):
  - *Tayangkan* (mode tayang): guru menjalankan game layar penuh dari sesinya sendiri di proyektor, TV, atau perangkatnya.
  - *Unduh game*: satu berkas HTML mandiri yang bisa dimainkan offline tanpa login dan tidak membuat koneksi jaringan. Guru bisa membagikannya, misalnya lewat WhatsApp. Isinya hanya konten pembelajaran, tidak pernah data siswa.
- Akun dibuat lewat undangan. Tidak ada pendaftaran bebas ke sebuah sekolah.

## Peran

| Peran (nama di UI) | Siapa | Tugas utama di SIGRAK | Perangkat |
|---|---|---|---|
| Guru kelas (wali kelas) | Mengajar mata pelajaran kelas di satu kelas: Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS, Seni dan Budaya, dan yang lain yang tidak tercantum untuk guru mapel | Membuat konten, absensi harian, jurnal, asesmen, rapor, data kelas | Ponsel, laptop di rumah |
| Guru mapel (khusus) | Mengajar satu mata pelajaran khusus lintas kelas: **PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, Pendidikan Agama** (di SD satu mata pelajaran per agama: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu; di MI mata pelajaran Kemenag Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab, selalu diajar guru mapel) | Membuat konten yang dipakai ulang lintas kelas. Jurnal, catatan siswa, dan asesmen untuk mata pelajarannya (absensi bersifat harian dan menjadi tugas wali kelas) | Ponsel |
| Kepala sekolah | Kepala sekolah | Memantau kelengkapan dan konten yang dibuat, memvalidasi perubahan kurikulum sekolah, supervisi, buku sekolah, pengaturan cetak, langganan | Laptop, ponsel |
| Operator sekolah | Staf administrasi sekolah | Tahun ajaran, kelas, mengundang guru, jadwal pelajaran, kalender, profil sekolah | Laptop |
| Pengawas / korwil | Pengawas beberapa sekolah | Melihat sekolah binaan (hanya-baca), validasi akhir perubahan kurikulum, laporan supervisi | Laptop |
| Tim SIGRAK | Staf internal | Registri templat dan penyetelan prompt, sekolah dan langganan, dukungan. Memakai konsol internal terpisah (Filament), bukan aplikasi sekolah | Laptop |

Pembagian mata pelajaran bawaan ini mengikuti cara SD/MI menempatkan gurunya (D-032). Operator tetap bisa menugaskan mata pelajaran mana pun di sebuah kelas kepada guru mapel, atau mata pelajaran khusus kepada wali kelas bila sekolah tidak punya guru khususnya.

Kepala sekolah, operator, atau pengawas yang juga mengajar tetap punya lima menu guru dan mendapat tombol untuk berpindah ke portalnya. Kepala sekolah tidak butuh kunci AI kecuali membuat konten sendiri.

**Peserta ujian** bukan sebuah peran. Siswa mendapat kredensial ujian per sesi untuk satu sesi latihan TKA, dan kredensial itu tidak berlaku lagi saat sesi ditutup.

## Pasar

- **Jenjang:** SD/MI, kelas 1–6. Kelas 7–12 disembunyikan sampai data kurikulumnya ada (data saat ini, KM-SD-2026-R2, hanya mencakup SD/MI). SD menawarkan keenam mata pelajaran agama (D-034). MI mengikuti Kementerian Agama: Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, dan Bahasa Arab sebagai ganti satu mata pelajaran PAI (D-035). Rinciannya di `religious-education.md`.
- **Wilayah:** seluruh Indonesia (D-018). Muatan lokal (misalnya Bahasa Jawa) dan kalender pendidikan (kaldik) bisa diatur per daerah dan per sekolah. Kudus menjadi uji coba, jadi Bahasa Jawa dan kalender Kudus hanya menjadi nilai bawaan untuk sekolah di Kudus.
- **Risiko nama:** "Sigrak" berasal dari bahasa Jawa. Kata ini mungkin kurang dikenal di luar Jawa, yang penting karena pasarnya kini nasional. Nama cadangan dari catatan rebranding adalah **Kapur**. Nama ini tetap nama sementara sampai pemeriksaan PDKI selesai (lihat Pertanyaan terbuka).

## Visi, misi, nilai

**Visi.** Setiap guru SD/MI bisa menyiapkan bahan ajar dan menyelesaikan administrasinya di sela pelajaran, sehingga sore harinya kembali untuk siswa dan keluarga.

**Misi.**

1. Mengubah administrasi yang berulang (RPM, jurnal, absensi, nilai) menjadi formulir singkat yang selesai dalam hitungan menit.
2. Mengubah persiapan konten menjadi menjawab beberapa pertanyaan pedagogis, di atas templat yang dibangun dan diperiksa mutunya oleh tim SIGRAK.
3. Menyediakan kurikulum, jadwal, dan format dokumen yang sudah sesuai aturan, sehingga guru tidak pernah mulai dari nol.
4. Membuat kepala sekolah dan pengawas bisa melihat kemajuan dari data yang sama dengan yang dipakai guru, tanpa mengumpulkan berkas.
5. Tetap bisa dipakai di ponsel murah dan sinyal lemah.
6. Menjaga data sekolah tetap milik sekolah dan kunci AI tetap milik guru.

**Nilai.** Diambil dari catatan rebranding dan disesuaikan.

| Nilai | Artinya di produk | Contoh keputusan |
|---|---|---|
| Cekatan | Langkah sesedikit mungkin. Data yang sudah dimiliki sistem tidak ditanyakan lagi | Kelas dan mata pelajaran diisi dari jadwal pelajaran. Wizard paling banyak 4 langkah |
| Jelas | Satu istilah untuk satu hal. Setiap layar menjawab "apa yang harus saya lakukan sekarang" | "Unduh", tidak pernah campuran Download/DOCX/Simpan Lokal |
| Bisa diandalkan | Data tidak hilang. Dokumen siap cetak. Kegagalan AI dijelaskan | Simpan otomatis. PDF teks dengan kop sekolah. Kunci AI yang gagal dialihkan ke kunci berikutnya |
| Membumi | Mengikuti cara kerja sekolah dasar di Indonesia, daerah demi daerah | Muatan lokal per daerah, kertas F4, istilah kurikulum resmi |
| Menghormati guru | Guru adalah penulisnya. AI hanya membantu | Semua keluaran AI bisa disunting. Tanpa menulis prompt |

## Brand

| Hal | Isi |
|---|---|
| Nama | SIGRAK (nama sementara). Di teks UI ditulis "Sigrak" (aturan huruf kalimat), dan wordmark-nya huruf kecil "sigrak" |
| Arti | Bahasa Jawa untuk lincah, cekatan, bersemangat |
| Tagline | "Kerja guru, lebih sigrak." |
| Janji brand | "Bahan ajar siap pakai dan administrasi beres, tanpa menulis prompt." (D-017) |
| Nada | Rekan guru yang sigap dan sopan. Lihat `design-system.md` (bagian Nada dan gaya bicara) |
| Visual | Alat kerja yang tenang: satu warna brand teal, permukaan polos. Lihat `design-system.md` |
| Brief logo | Tidak berubah dari catatan rebranding: dua goresan kapur miring yang membentuk tanda centang dan langkah maju. Tanpa gradien, bayangan, maskot, atau batik |

Janji itu harus terlihat di produk sebagai tiga bukti:

1. **Konten:** RPM siap cetak dalam empat langkah, dan Quiz Battle yang bisa dimainkan dari set soal tersimpan dalam waktu kurang dari lima menit.
2. **Administrasi:** absensi dan jurnal untuk satu kelas selesai di ponsel dalam waktu kurang dari satu menit, dengan atau tanpa sinyal.
3. **Kepemimpinan:** kepala sekolah dan pengawas melihat kelengkapan tanpa ada yang menyerahkan berkas.

## Navigasi

Aplikasi guru punya lima menu (D-021). Di ponsel berupa bilah bawah, dan di laptop berupa sidebar dengan urutan yang sama.

```
Hari Ini | Di Kelas | Buat | Asesmen | Kelas Saya

Hari Ini    Today's schedule, what still needs filling, this week's progress, Kabar (news)
Di Kelas    Absensi, Jurnal, Catatan Siswa (works offline)
Buat        Galeri templat: RPM, LKPD, Bank Soal, Paket Soal TKA, Quiz Battle (more games later)
            Kurikulum (CP, TP, ATP, Prota, Promes), as reference and for school changes
            Kokurikuler
Asesmen     Rencana penilaian, Nilai, Analisis (per TP), Rapor
            Simulasi TKA: sessions, exam cards, proctor token, results
            "Buat soal" is a shortcut to the Bank Soal template
Kelas Saya  Siswa (incl. pindah/keluar, kenaikan), Jadwal, Piket, Keuangan (school may turn off), Inventaris

Account menu (profile icon): Dokumen Saya, Kunci AI Saya, Paket Saya, Akun, Bantuan, Minta Bantuan
```

Ringkasan diagram: *Hari Ini* berisi jadwal hari ini, yang belum diisi, kemajuan minggu ini, dan Kabar. *Di Kelas* berisi Absensi, Jurnal, dan Catatan Siswa (bekerja offline). *Buat* berisi galeri templat (RPM, LKPD, Bank Soal, Paket Soal TKA, Quiz Battle), Kurikulum, dan Kokurikuler. *Asesmen* berisi rencana penilaian, nilai, analisis per TP, rapor, dan Simulasi TKA. *Kelas Saya* berisi siswa, jadwal, piket, keuangan, dan inventaris. Menu akun berisi Dokumen Saya, Kunci AI Saya, Paket Saya, Akun, Bantuan, dan Minta Bantuan.

Portal, dibuka lewat tombol pindah peran:

| Portal | Bagian |
|---|---|
| Kepala Sekolah | Ringkasan, Pantau Guru, Validasi, Supervisi, Program Sekolah, Agenda & Rapat, Buku Sekolah (tamu, humas, notulen), Data Sekolah, Pengaturan Sekolah (cetak, langganan) |
| Operator Sekolah | Tahun Ajaran, Kelas, Guru (undang), Jadwal, Kaldik, Profil Sekolah |
| Pengawas | Sekolah Binaan, Pantau (hanya-baca), Validasi, Supervisi, Laporan Kepengawasan |

Konsol internal untuk Tim SIGRAK adalah panel Filament terpisah dengan login sendiri. Konsol ini bukan bagian dari aplikasi sekolah.

## Alur utama

Setiap peran punya satu alur utama dengan paling banyak empat langkah.

**Guru: membuat konten (templat apa pun)**

1. **Pilih:** buka *Buat* dan pilih templat.
2. **Jawab:** jawab wizard. Kelas dan mata pelajaran terisi dari jadwal pelajaran. Topik, fokus, dan tujuan pembelajaran dipilih dari kurikulum. Lalu pertanyaan pedagogis milik templat itu, masing-masing dengan nilai bawaan.
3. **Buat:** pembuatan berjalan di latar belakang. Guru boleh meninggalkan halaman dan mendapat notifikasi saat selesai.
4. **Periksa & pakai:** pratinjau, sunting, atau revisi satu bagian ("Perbaiki bagian ini"). Lalu simpan, *Unduh PDF/Word*, *Buka di Canva*, atau, untuk game, *Tayangkan* / *Unduh game*.

**Guru: RPM.** Empat langkah yang sama, dengan nama wizard seperti di catatan rebranding: *Kapan* (tanggal mulai, atau "RPM lepas jadwal") → *Apa* (materi pokok dan fokus; JP dan jumlah pertemuan dihitung dan dikunci) → *Cara* (model pembelajaran per pertemuan, atau otomatis) → *Periksa*.

**Guru: satu hari mengajar (ponsel)**

1. Buka *Hari Ini* untuk melihat jadwal dan yang belum diisi.
2. Saat pelajaran, buka *Di Kelas → Absensi*. Semua siswa awalnya hadir, dan guru mengetuk siswa yang tidak hadir.
3. Setelah pelajaran, *Jurnal* sudah terisi dari RPM. Guru menandainya selesai dan menambah catatan.
4. Catatan siswa ditulis saat ada kejadian. Semuanya tersimpan tanpa sinyal dan dikirim saat koneksi kembali.

**Guru atau operator: ujian latihan TKA (CBT)**

1. **Siapkan soal:** buat *Paket Soal TKA* (Bahasa Indonesia atau Matematika, 30 butir dalam bentuk soal TKA) dengan templat, lalu periksa dan sunting.
2. **Buat sesi:** pilih paket, kelas 6 yang ikut, tanggal, dan durasi (bawaan 75 menit). Sistem mencetak *kartu login* untuk setiap siswa.
3. **Jalankan:** pada hari ujian, guru (sebagai proktor) membuka sesi dan membacakan token yang berlaku, yang berganti setiap 15 menit. Siswa masuk di situs ujian, dan proktor memantau kemajuan serta bisa mereset peserta yang tertahan.
4. **Hasil:** nilai 0–100 dengan kategori TKA (Kurang, Memadai, Baik, Istimewa), ditambah analisis per kompetensi dan ekspor Excel/PDF.

**Operator: awal tahun ajaran.** Atur tahun ajaran dan kelas → undang guru → isi jadwal pelajaran → atur kalender.

**Kepala sekolah: mingguan.** *Ringkasan* (kelengkapan per guru minggu ini) → *Pantau Guru* (siapa yang tertinggal, konten apa yang dibuat, kirim pengingat) → *Validasi* (setujui atau kembalikan perubahan kurikulum sekolah dengan catatan).

**Pengawas: per kunjungan.** Pilih sekolah → lihat kelengkapan dan hasil supervisi (hanya-baca) → validasi akhir → unduh laporan supervisi.

**Validasi (D-019).** Konten yang dihasilkan langsung bisa dipakai. Kepala sekolah dan pengawas bisa melihat dokumen guru dan memberi komentar. Hanya kurikulum yang *diubah atau ditambah* sekolah yang melewati guru → kepala sekolah → pengawas. Kurikulum bawaan yang dipakai apa adanya tidak pernah masuk antrean.

## Model bisnis

Paket dibayar per tahun ajaran (Juli–Juni) **hanya oleh guru dan sekolah**. Siswa dan orang tua tidak pernah membayar dan tidak pernah melihat harga (D-157). Kunci AI milik guru (D-012; hanya paket gratis Gemini, D-023), jadi pemakaian AI tidak pernah menjadi biaya SIGRAK. **Langganan sekolah dibayar dari dana selain BOS** (D-025). Materi penjualan tidak boleh menyiratkan BOS sebagai sumber pembayaran.

| Paket | Untuk | Isi | Harga per tahun | Pembayar |
|---|---|---|---|---|
| Uji coba | Satu guru | Semua fitur selama 1 minggu (D-160). Setelah itu akun hanya bisa melihat dan mengunduh datanya sampai paket dibeli | Gratis | — |
| Sigrak Guru | Satu guru | Semua fitur guru | Rp60.000, atau Rp50.000 bila sekolah guru itu berlangganan Sigrak Sekolah | Guru |
| Sigrak Sekolah | Satu sekolah | 2 akun, kepala sekolah dan operator, dengan portalnya: validasi, pengaturan cetak sekolah, undangan, data sekolah. Guru membeli Sigrak Guru sendiri | Rp100.000 | Sekolah (dana non-BOS) |
| Fitur tambahan | Satu sekolah yang berlangganan Sigrak Sekolah | Satu fitur tambahan, diaktifkan sekolah untuk semua akunnya (D-158) | Rp50.000 per fitur | Sekolah (dana non-BOS) |
| Sigrak Binaan | Satu pengawas atau gugus KKG | Portal pengawas. Tidak dijual: terbuka gratis begitu 5 atau lebih sekolah binaannya berlangganan Sigrak Sekolah | Gratis | — |

**Fitur tambahan (D-158):** Simulasi TKA · Buku Induk dan sinkronisasi Dapodik (segera hadir, D-161) · Keuangan dan Inventaris kelas · Buku Sekolah · Game kelas (Quiz Battle, *Tayangkan*, *Unduh game*) · Rapor (leger, draf deskripsi, ekspor rapor). Fitur tambahan lain bisa menyusul. Guru boleh mencoba Game kelas selama 7 hari, satu kali, walaupun sekolahnya belum mengaktifkannya (D-159).

## Status fitur

Ini 43 fitur lama EDUNEXA ditambah fitur baru, direvisi untuk konsep baru. Nilai status: **Pertahankan**, **Gabung**, **Ganti**, **Hapus**, **Tambah**, **Nanti**. Semua yang tidak bertanda *Hapus* atau *Nanti* masuk v1.

| Area | Fitur lama (EDUNEXA) | Status | Menjadi | Alasan |
|---|---|---|---|---|
| Beranda | Beranda: kemajuan administrasi | Ganti | Hari Ini: yang harus diisi hari ini + kemajuan minggu ini | Guru perlu tahu apa yang harus dikerjakan sekarang |
| Beranda | Papan pengumuman | Gabung | Kabar (dengan Pemberitahuan) | Dua tempat berita membingungkan |
| Beranda | Kutipan hari ini, tips & motivasi | Hapus | — | Hiasan yang menghabiskan ruang layar ponsel |
| Beranda | Pemberitahuan | Gabung | Kabar | Satu kotak masuk |
| Beranda | Mode demo/inspeksi | Ganti | Mode Contoh untuk demo penjualan | Nama lebih jelas bagi pembeli |
| Beranda | Sesi terputus setelah 15 menit diam | Ganti | Sesi tetap. Formulir tersimpan otomatis | Guru sering berhenti di tengah formulir. Alasan biaya lama hilang dengan SQL |
| Buat | Kurikulum (CP, TP, ATP, Prota, Promes) | Gabung | Buat > Kurikulum | Dasar perencanaan dan konten |
| Buat | Generator RPM | Pertahankan | Buat > templat RPM, 4 langkah (Kapan, Apa, Cara, Periksa) di mesin templat | Fitur paling berharga |
| Buat | Modul mandiri (mode manual) | Gabung | Pilihan "RPM lepas jadwal" di langkah 1 | Satu pintu untuk setiap RPM |
| Buat | Modul kokurikuler (7 KAIH, projek, lintas disiplin) | Pertahankan | Buat > Kokurikuler, varian templat RPM yang membaca rencana sekolah | Fitur yang sudah ada |
| Buat | Worksheet / LKPD | Gabung | Buat > templat LKPD. Juga lampiran RPM | LKPD bagian dari rencana |
| Buat | Bank Modul / penyimpanan RPM | Gabung | Dokumen Saya | Satu arsip untuk setiap dokumen |
| Buat | Unduh / impor cadangan | Ganti | Simpan otomatis di server. Ekspor data lengkap di Akun | Cadangan manual membuat guru ragu datanya aman |
| Buat | Mencocokkan tanggal dengan konten (kokurikuler) | Ganti | Penyesuaian otomatis + Kabar saat jadwal berubah | Guru tidak perlu tahu mekanismenya |
| Buat | PDF tangkapan layar (html2canvas) | Ganti | PDF teks dari server + pratinjau sebelum unduh | Berkas ringan, teks bisa dicari, tanpa garis terpotong |
| Asesmen | Bank Soal (PH, STS, SAS, US, kisi-kisi, kartu soal) | Gabung | Buat > templat Bank Soal. Asesmen > pintasan "Buat soal" | Soal terkait TP, satu mesin konten |
| Asesmen | "Mode Ujian" Bank Soal | Nanti (tidak aktif) | — | Dimatikan untuk sementara (D-026). Ujian latihan TKA satu-satunya ujian online di v1 |
| Asesmen | Program penilaian | Gabung | Asesmen > Rencana Penilaian | |
| Asesmen | Analisis nilai, taraf serap | Pertahankan | Asesmen > Analisis, otomatis per TP | |
| Asesmen | Leger dan "inject e-Rapor" | Ganti | Asesmen > Rapor: leger, draf deskripsi, format ekspor e-Rapor | "Inject" terlalu teknis |
| Asesmen | Jenjang SMP–SMK, kelas 7–12 | Hapus | Kembali bila data SMP/MTs ada | Pilihan tanpa data berakhir dengan galat |
| Di Kelas | Absensi | Ganti | Di Kelas > Absensi: **harian** (D-029), bawaan "semua hadir", ketuk yang tidak hadir, bekerja offline | Diisi di ponsel pada awal hari |
| Di Kelas | Jurnal dan refleksi | Ganti | Di Kelas > Jurnal terisi dari RPM. Refleksi disusun dari jurnal | Lebih sedikit mengetik ulang |
| Di Kelas | Catatan anekdot, konseling (BK), prestasi | Gabung | Di Kelas > Catatan Siswa (satu formulir, jenisnya dipilih) | Kolom yang sama di tiga buku |
| Di Kelas | Buku tamu, buku humas, notulen | Gabung | Portal Kepala Sekolah > Buku Sekolah | Milik sekolah, bukan kelas |
| Kelas Saya | Data siswa | Pertahankan | Kelas Saya > Siswa (hanya catatan data, D-010) | |
| Kelas Saya | Mutasi siswa | Gabung | Kelas Saya > Siswa > Pindah atau keluar | Bagian dari riwayat siswa |
| Kelas Saya | Kenaikan kelas | Gabung | Kelas Saya > Siswa > Kenaikan, aktif menjelang akhir tahun | Dipakai setahun sekali |
| Kelas Saya | Kalender dan jadwal pelajaran | Pertahankan | Kelas Saya > Jadwal. Kaldik diatur sekali oleh sekolah (bawaan per daerah) | |
| Kelas Saya | Piket | Pertahankan | Kelas Saya > Piket | |
| Kelas Saya | Keuangan kelas | Pertahankan | Kelas Saya > Keuangan, sekolah bisa mematikannya | Tidak semua sekolah mengizinkannya |
| Kelas Saya | Inventaris kelas | Pertahankan | Kelas Saya > Inventaris | |
| Akun | Pengaturan (tema, kertas, profil, kata sandi) | Ganti | Akun (profil, kata sandi, tema, gambar kop sendiri) + Pengaturan Sekolah (kertas, gambar kop bawaan, penanda tangan) | Sekolah mengatur bawaan; guru boleh memakai gambar kop sendiri (D-031) |
| Akun | Profil sekolah "yang pertama menyimpan menang" | Ganti | Dimiliki operator/kepala sekolah. Guru mengusulkan koreksi | Pemilik data jelas |
| Akun | API key Gemini wajib saat aktivasi guru | Ganti | Kunci AI Saya: beberapa kunci, ditanyakan saat pertama memakai AI, dengan tombol uji dan status | Pendaftaran cepat. AI tidak pernah menghalangi fitur lain |
| Akun | API key wajib di profil kepala sekolah | Hapus | — | Kepala sekolah tidak butuh AI |
| Akun | Kata sandi bawaan bersama untuk pengguna baru | Ganti | Tautan undangan, pengguna mengatur kata sandinya sendiri | Keamanan akun |
| Akun | Tutorial | Ganti | Bantuan: bantuan singkat per halaman + pusat bantuan | Bantuan saat dibutuhkan |
| Akun | Arsip | Ganti | Dokumen Saya: RPM, soal, LKPD, game, laporan, dengan status validasi dan komentar | |
| Akun | Hubungi admin / kotak masuk | Pertahankan | Menu akun > Minta Bantuan | |
| Akun | Permintaan pindah sekolah | Pertahankan | Akun > Pindah Sekolah, disetujui sekolah tujuan | |
| Portal | Portal kepala sekolah | Ganti | Bagian yang tercantum di Navigasi | Disusun dari pekerjaan kepala sekolah |
| Portal | Portal pengawas | Ganti | Bagian yang tercantum di Navigasi | |
| Portal | Super Admin / Control Center | Ganti | Konsol Filament internal + langganan + registri templat | Bukan bagian aplikasi sekolah |
| Baru | Galeri templat | Tambah | Buat > Galeri templat | Mesin konten (D-013) |
| Baru | Game Quiz Battle | Tambah | Buat > Quiz Battle, dari set Bank Soal atau soal baru | Game pertama (D-014) |
| Baru | Mode tayang dan unduh game | Tambah | Tombol *Tayangkan* dan *Unduh game* di setiap game | Cara game sampai ke siswa (D-011) |
| Baru | Usulan fitur dari templat | Tambah | Tautan "Usulkan fitur" di wizard game | Pengganti kode game buatan AI di v1 (D-015) |
| Baru | Templat paket latihan TKA | Tambah | Buat > Paket Soal TKA | D-026 |
| Baru | Ujian latihan TKA (CBT): sesi, kartu ujian, token berganti, tampilan proktor, hasil | Tambah | Asesmen > Simulasi TKA + situs ujian terpisah | D-026, D-027 |
| Baru | Beberapa kunci AI (paket gratis Gemini) dengan rotasi dan failover | Tambah | Kunci AI Saya | D-012, D-023 |
| Baru | Riwayat pemakaian AI | Tambah | Kunci AI Saya > Riwayat | Guru melihat mengapa AI berhenti |
| Baru | Revisi satu bagian dengan AI | Tambah | "Perbaiki bagian ini" di setiap bagian pratinjau | Tanpa membuat ulang seluruhnya |
| Baru | Draf deskripsi rapor per TP | Tambah | Asesmen > Rapor | |
| Baru | Di Kelas offline | Tambah | Menu Di Kelas | D-016 |
| Baru | Validasi kurikulum guru → kepala sekolah → pengawas | Tambah | Status di Dokumen Saya + antrean portal | D-019 |
| Baru | Komentar kepala sekolah/pengawas pada dokumen | Tambah | Tampilan dokumen | D-019 |
| Baru | Pengaturan cetak: kop sebagai **gambar yang diunggah** (bawaan sekolah, guru boleh mengunggah sendiri), penanda tangan, A4/F4, font | Tambah | Pengaturan Sekolah > Cetak; Akun > Kop saya | Guru mengatur tata letak dan logo kop di dalam gambarnya sendiri (D-031) |
| Baru | Buka di Canva | Tambah | Tombol di pratinjau RPM, LKPD, soal (unduh + buka canva.com) | |
| Baru | Langganan dan penagihan | Tambah | Pengaturan Sekolah > Langganan (paket dan fitur tambahan). Akun > Paket Saya | Model bisnis (D-157) |
| Baru | Buku Induk dan sinkronisasi Dapodik: impor ekspor Excel Dapodik (siswa, rombel, ekstrakurikuler, dan data PTK seperti gaji berkala dan kepangkatan) dan menjaga data sekolah tetap selaras | Nanti | Portal operator > Data Sekolah, sebagai fitur tambahan, ditampilkan sebagai segera hadir | Sekolah sudah menyimpan data ini di Dapodik (D-158). Ditahan sebagai segera hadir; draf rancangan P4-01 ditunda (D-161) |
| Baru | Mengundang guru | Tambah | Portal operator > Guru | Pengganti kata sandi bawaan |
| Baru | Bawaan muatan lokal dan kalender per daerah | Tambah | Konsol internal + Pengaturan Sekolah | D-018 |
| Baru | Pengenalan 3 langkah | Tambah | Tampil sekali setelah mendaftar: kelas, jadwal, siswa | Siap sejak hari pertama |
| Nanti | Drag & Drop, Word Puzzle, Maze Challenge, Flashcard | Nanti | Templat game berikutnya | Setelah Quiz Battle membuktikan polanya |
| Nanti | Kode game buatan AI atas permintaan | Nanti | Butuh rancangan sandbox dan tinjauan | D-015 |
| Nanti | Canva Connect API (impor langsung ke Canva guru) | Nanti | | Tombol tingkat 1 cukup untuk v1 |
| Nanti | Offline di luar Di Kelas | Nanti | | D-016 |
| Dihapus | Generator konten umum ("Buat bebas") | Hapus | — | Guru bisa langsung melakukannya di Gemini (D-024) |
| Dihapus | Slide per pertemuan ("Bahan Tayang") | Hapus | — | Tidak diperlukan; guru menayangkan dengan alat mereka sendiri (D-033) |
| Nanti | Absensi per jam pelajaran untuk guru mapel | Nanti | | Absensi harian di v1 (D-029) |
| Nanti | Pendidikan Kepercayaan terhadap Tuhan YME | Nanti | | Tidak di v1 (D-037) |
| Nanti | SMP/MTs | Nanti | | Butuh data kurikulum |

## Cakupan v1

v1 berisi setiap baris di atas yang tidak bertanda *Hapus* atau *Nanti*. Itu rilis yang besar, jadi Fase 2 sebaiknya memotongnya menjadi irisan yang bisa dirilis satu per satu. Urutan, sudah dikonfirmasi pemilik (D-162):

1. Fondasi: autentikasi, undangan, sekolah dan peran, tahun ajaran dan kelas, impor kurikulum, jadwal pelajaran.
2. Inti konten: Mesin Penyedia AI, mesin templat, templat RPM, Dokumen Saya, ekspor PDF/DOCX.
3. Templat lain: Bank Soal, LKPD, Quiz Battle (tayang + unduh).
4. TKA: templat Paket Soal TKA, situs ujian, sesi, kartu ujian, token, hasil. Target: musim TKA 2028 (D-162).
5. Di Kelas dengan sinkronisasi offline, Jurnal dari RPM.
6. Asesmen: rencana, nilai, analisis per TP, rapor.
7. Portal: kepala sekolah, operator, pengawas. Validasi dan komentar.
8. Bisnis: uji coba, langganan, fitur tambahan, penagihan, Mode Contoh.

## Pertanyaan terbuka

- [x] Harga: uji coba 1 minggu (D-160), Sigrak Guru Rp60.000, Sigrak Sekolah Rp100.000, fitur tambahan Rp50.000 per fitur (D-157, D-158).
- [ ] Nama akhir: periksa "Sigrak" (dan cadangan "Kapur") di [PDKI](https://pdki-indonesia.dgip.go.id) kelas 9, 41, 42, lalu daftarkan domain .id dan .com bersamaan. Risiko nama lebih tinggi sekarang karena pasarnya nasional.
- [ ] Logo: brief untuk desainer (berwarna, satu warna, negatif, minimal 16 px).
- [x] Modul kokurikuler masuk v1 sebagai varian templat RPM (D-014).
- [ ] Uji menu lima tab dengan 5 guru kelas dan 2 guru mapel memakai prototipe klik (tanpa data sungguhan).
- [ ] Presentasikan versi singkat ke kepala sekolah dan pengawas, lalu catat keberatan mereka.
- [x] Klausul batas usia di bawah 18 tahun dari Gemini: siswa hanya menerima konten AI yang sudah jadi dan diperiksa guru, dan tidak pernah memicu AI (D-124).
- [ ] Jadwal TKA 2028 untuk SD/MI, begitu diumumkan, untuk menentukan waktu rilis fitur TKA (D-162).
- [ ] Peninjau data agama dan salinan SK 9941/2025 dari kanal resmi (lihat `religious-education.md`, bagian Pertanyaan terbuka).
