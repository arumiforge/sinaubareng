# Konsep Produk SIGRAK

<p class="source-note">Diterjemahkan dari <code>docs/final/product-concept.md</code> pada 10 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-02; keputusan berikutnya diterapkan di P4-24 (D-210…D-371)
- **Terakhir diperbarui:** 2026-11-10
- **Sumber:** `Konsep_Awal_Produk_SIGRAK.md`, `Rebranding_dan_Perombakan_Fitur_Aplikasi_Lama.md`, `Dokumentasi-Fungsionalitas-Aplikasi-Lama.pdf` (di `docs/sources/`)

## Tujuan

Dokumen ini menetapkan apa itu SIGRAK, siapa penggunanya, dan apa isi v1. Dokumen ini menjadi rujukan produk untuk setiap tugas rancangan dan pembangunan berikutnya. Dua dokumen pendamping membahas sisi teknis: `content-engine.md` dan `ai-provider-engine.md`. Keputusan dicatat di `docs/agent/DECISIONS.md` (D-007 sampai D-037 untuk versi pertama; baris yang lebih baru menyebut nomornya sendiri).

## Ringkasan

SIGRAK (nama sementara) adalah aplikasi web untuk guru dan pimpinan SD/MI. Aplikasi ini mengerjakan dua hal, dan keduanya ada di v1:

1. **Pembuatan konten dengan bantuan AI.** Guru membuat RPM, bank soal, LKPD, game kelas, dan paket latihan TKA. Guru memilih templat dan menjawab pertanyaan pedagogis. Guru tidak pernah menulis prompt. Templat buatan pengembang menetapkan struktur dan desain, dan AI hanya mengisi kontennya. Tidak ada generator umum "buat apa saja", karena guru sudah bisa mendapatkannya langsung dari Gemini (D-024).
2. **Administrasi sekolah.** Kehadiran, jurnal mengajar, catatan murid, asesmen dan rapor, data kelas, Berkas Administrasi, serta portal kepala sekolah dan pengawas yang dibawa dari aplikasi lama dan ditingkatkan (lihat Status fitur).
3. **Ujian latihan TKA (CBT).** Ujian berbasis komputer yang bekerja seperti Tes Kemampuan Akademik nasional untuk kelas 6. Siswa mengerjakannya di situs ujian terpisah dengan kartu ujian dan token proktor (D-026, D-027).

Siswa ("murid" di teks UI, D-210) bukan pengguna aplikasi. Setiap halaman ada di balik login, dan tidak ada halaman publik. Satu-satunya tempat yang disentuh siswa adalah situs ujian, dan hanya dengan kartu ujian untuk satu sesi.

## Yang berubah dari catatan aplikasi lama

| Topik | Catatan aplikasi lama (29 Okt) | SIGRAK sekarang | Keputusan |
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
| Aturan dan istilah | Istilah sebelum 2025 (siswa, P5, taraf serap, Absensi, teguran) | Aturan dan istilah 2025–2026: murid, kokurikuler, daya serap, Kehadiran, Pengingat | D-210, D-302, D-311, D-322 |

Semua yang lain yang disepakati di catatan aplikasi lama tetap berlaku: PDF berbasis teks dengan pengaturan cetak per sekolah, tombol "Buka di Canva", alur validasi materi, dan Di Kelas yang bisa dipakai tanpa sinyal. Yang tidak dibawa (D-322): generator bebas dan Bahan Tayang, jenjang SMP–SMA, kunci AI wajib saat aktivasi, kata sandi bawaan, PDF tangkapan layar peramban, keluar otomatis setelah 15 menit diam, mode ujian, AI yang membaca gambar tulisan tangan murid (D-287; D-370 nanti membaca tulisan tangan di ponsel dan hanya mengirim teksnya), halaman verifikasi publik, serta istilah siswa, P5, taraf serap, dan teguran. Ditunda ke usulan-02: draf skor AI untuk S-04 Telaah RPM dan "Cocokkan berkas".

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

## Landasan kebijakan

**Posisi: *Berpijak pada kebijakan, leluasa berinovasi*** (D-352). Standar nasional menetapkan apa dan batasnya: capaian, struktur, isi minimum dokumen, perlindungan anak. Sekolah dan guru memilih caranya. SIGRAK memberi mereka alat untuk cara belajar yang baru, dan setiap inovasi bisa menunjukkan unsur nasional mana yang dilayaninya. Rinciannya: `docs/drafts/policy-innovation-layers.md`.

- **Aturan 2025–2026 (D-210).** Rapor memuat 12 komponen minimum (Fase A boleh tanpa angka); kokurikuler dinilai pada 8 dimensi profil lulusan (DPL); ekstrakurikuler dengan Pramuka ditawarkan; beban kerja dihitung dalam JTM; kesepakatan kelas dan disiplin positif; refleksi guru paling sedikit sekali per semester; mutasi mengikuti Permendikdasmen 3/2025. Teks UI memakai **murid**; "siswa" hanya tersisa di nama tabel dan kutipan lama.
- **Kekuatan aturan (D-353).** Setiap aturan punya kekuatan: `mandatory`, `minimum`, `maximum`, `recommended`, `not_recommended`, `example`, `school_discretion`, `teacher_discretion`, `free`. Aplikasi hanya memblokir aturan wajib nasional yang jelas, memberi peringatan di bawah batas minimum atau di atas batas maksimum, memberi saran untuk yang dianjurkan dan tidak dianjurkan, dan membebaskan sisanya. Nilai bawaan yang hanya contoh diberi label "Contoh". Pagar pengaman, bukan gerbang.
- **Aturan berversi (D-354, D-355).** Sebelum P5-04, `curriculum_versions` mendapat `effective_on`, `applies_from_school_year`, dan `supersedes_id`, dan resolver profil kebijakan (tahun ajaran, jenjang, daerah) menggantikan penunjuk di `config/curriculum.php`. Dokumen menyimpan kode set aturan saat dibuat dan tidak pernah berubah diam-diam; aturan yang terbit di tengah tahun berlaku mulai tahun ajaran berikutnya. Target nanti (P5-09 atau irisan sendiri): `regulations`, `regulation_relations`, `rule_sets`, `rules`, konsol untuk setiap ranah, dan Kabar Regulasi.
- **Profil Jenjang (D-356, D-368).** SD dan MI berbagi satu inti. Satu catatan kecil berversi per jenjang (`sd`, `mi`) menyebut kementerian, kode set aturan per ranah, kerangka karakter (MI menambah KBC; kolom KBC untuk SD mati secara bawaan dan admin bisa menyalakannya), kolom tambahan RPM, tujuan ekspor rapor (e-Rapor SD atau RDM), JTM bawaan wali kelas (D-212), dan asesmen eksternal. Kode membaca profil dan tidak pernah bercabang menurut jenjang.
- **Taksonomi induk (D-357).** Satu kode per konsep di `docs/data/taxonomy/` (faset Kebijakan, Struktur kurikulum, Profil dan karakter, Pembelajaran, Asesmen, Konten, Konteks, Administrasi), dengan padanan antarversi (misalnya P5 → kokurikuler). Fitur menandai kontennya dengan taksonomi ini.
- **Dasar Kebijakan dan Cek Keselarasan (D-358).** Setiap dokumen menampilkan panel singkat berisi CP, TP, DPL, prinsip, dan versi set aturan yang menjadi pijakannya. Satu pemeriksa memberi baris blokir, peringatan, dan saran menurut kekuatan aturan saat menyimpan dan saat ditinjau kepala sekolah dan pengawas, dan tidak pernah menulis ulang karya guru. Keduanya dimulai dengan RPM di P5-04.

## Model akses

- **Hanya dengan login.** Satu-satunya layar tanpa sesi adalah login, penerimaan undangan, dan reset kata sandi. Tidak ada landing page, tautan berbagi publik, atau profil publik di dalam aplikasi. Situs pemasaran, bila dibuat, adalah proyek terpisah.
- **Siswa berupa catatan data, bukan akun.** Siswa punya nama, NISN, kelas, absensi, catatan, dan nilai, semuanya dikelola guru. Siswa tidak pernah masuk ke aplikasi.
- **Satu-satunya pengecualian adalah ujian latihan TKA** (D-027). Siswa masuk ke situs ujian terpisah (misalnya `ujian.<domain>`) dengan kartu ujian (nama pengguna + kata sandi) yang hanya berlaku untuk satu sesi ujian, ditambah token proktor yang berlaku. Situs ujian hanya menampilkan soal ujian, dan tidak pernah memanggil AI. Ketentuan Gemini melarang pemakaian API di layanan yang kemungkinan dipakai orang di bawah 18 tahun.
- **Game sampai ke siswa lewat dua cara** (D-011):
  - *Tayangkan* (mode tayang): guru menjalankan game layar penuh dari sesinya sendiri di proyektor, TV, atau perangkatnya.
  - *Unduh game*: satu berkas HTML mandiri yang bisa dimainkan offline tanpa login dan tidak membuat koneksi jaringan. Guru bisa membagikannya, misalnya lewat WhatsApp. Isinya hanya konten pembelajaran, tidak pernah data siswa.
- **Orang tua bukan pengguna.** Pesan untuk orang tua berupa teks siap kirim yang dibuka guru di WhatsApp-nya sendiri lewat tautan `wa.me` biasa, dengan catatan kontak: tanpa API WhatsApp dan tanpa tautan ke SIGRAK (D-215). Portal orang tua ditampilkan sebagai segera hadir (D-220).
- Sesi murid berbatas sesi untuk LKPD interaktif direncanakan nanti, dibangun seperti situs ujian (D-331).
- Akun dibuat lewat undangan. Tidak ada pendaftaran bebas ke sebuah sekolah.

## Peran

| Peran (nama di UI) | Siapa | Tugas utama di SIGRAK | Perangkat |
|---|---|---|---|
| Guru kelas (wali kelas) | Mengajar mata pelajaran kelas di satu kelas: Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS, Seni dan Budaya, dan yang lain yang tidak tercantum untuk guru mapel | Membuat konten, Kehadiran harian, jurnal, asesmen, rapor, data kelas, tugas wali kelas (catatan rapor, pesan ke orang tua, kenaikan, kelulusan, tabungan) | Ponsel, laptop di rumah |
| Guru mapel (khusus) | Mengajar satu mata pelajaran khusus lintas kelas: **PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, Pendidikan Agama** (di SD satu mata pelajaran per agama: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu; di MI mata pelajaran Kemenag Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab, selalu diajar guru mapel) | Membuat konten yang dipakai ulang lintas kelas. Jurnal, catatan murid, dan asesmen untuk mata pelajarannya (Kehadiran bersifat harian dan menjadi tugas wali kelas) | Ponsel |
| Kepala sekolah | Kepala sekolah | Memantau kelengkapan dan konten yang dibuat, memvalidasi perubahan kurikulum sekolah, Berkas Administrasi, pengesahan dokumen, Pengingat, supervisi, pengesahan leger, pembagian tugas, buku sekolah, pengaturan cetak, langganan | Laptop, ponsel |
| Operator sekolah | Staf administrasi sekolah | Tahun ajaran (dan Arsip Tahun Ajaran), kelas, mengundang guru, jadwal pelajaran, kalender, profil sekolah, mutasi, inventaris sekolah | Laptop |
| Pengawas / korwil | Pengawas beberapa sekolah | Mengajukan sekolah binaan (disetujui Tim SIGRAK), melihat rekap tingkat sekolah (hanya-baca), validasi akhir perubahan kurikulum, PKKS kepala sekolah, laporan supervisi | Laptop |
| Tim SIGRAK | Staf internal | Sebelas peran staf dengan cakupan masing-masing (D-338, D-339): registri templat dan penyetelan prompt, kurikulum, sekolah dan langganan, pembayaran, dukungan, penjualan. Memakai konsol internal terpisah (Filament), bukan aplikasi sekolah; lihat Konsol internal | Laptop |

Pembagian mata pelajaran bawaan ini mengikuti cara SD/MI menempatkan gurunya (D-032). Operator tetap bisa menugaskan mata pelajaran mana pun di sebuah kelas kepada guru mapel, atau mata pelajaran khusus kepada wali kelas bila sekolah tidak punya guru khususnya.

Guru pendamping khusus (GPK) adalah penugasan guru untuk murid tertentu, bukan peran baru (D-348). Wali kelas dihitung 2 JTM di SD dan 6 JTM di MI secara bawaan; sekolah bisa mengubah nilai ini (D-212).

Kepala sekolah, operator, atau pengawas yang juga mengajar tetap punya lima menu guru dan mendapat tombol untuk berpindah ke portalnya. Kepala sekolah tidak butuh kunci AI kecuali membuat konten sendiri.

**Peserta ujian** bukan sebuah peran. Murid mendapat kredensial ujian per sesi untuk satu sesi latihan TKA, dan kredensial itu tidak berlaku lagi saat sesi ditutup.

## Pasar

- **Jenjang:** SD/MI, kelas 1–6. Kelas 7–12 disembunyikan sampai data kurikulumnya ada (data saat ini, SIGRAK-SD-2026-R1 dan SIGRAK-MI-2026-R1, hanya mencakup SD/MI; D-363). Struktur dan JP mengikuti Permendikdasmen 13/2025 untuk SD dan KMA 1503/2025 untuk MI; perbedaannya disimpan di Profil Jenjang (D-356). Bahasa Inggris dan IPAS di kelas 1–2 adalah konten opsional yang bisa dinyalakan sekolah (D-204). SD menawarkan keenam mata pelajaran agama (D-034). MI mengikuti Kementerian Agama: Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, dan Bahasa Arab sebagai ganti satu mata pelajaran PAI (D-035). Rinciannya di `religious-education.md`.
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
2. **Administrasi:** Kehadiran dan jurnal untuk satu kelas selesai di ponsel dalam waktu kurang dari satu menit, dengan atau tanpa sinyal.
3. **Kepemimpinan:** kepala sekolah dan pengawas melihat kelengkapan tanpa ada yang menyerahkan berkas.

## Navigasi

Aplikasi guru punya lima menu (D-021). Di ponsel berupa bilah bawah, dan di laptop berupa sidebar dengan urutan yang sama.

```
Hari Ini | Di Kelas | Buat | Asesmen | Kelas Saya

Hari Ini    Today's schedule, what still needs filling, this week's progress, Kabar (news)
            Siapkan besok, Pusat Tindak Lanjut (own follow-ups)
Di Kelas    Kehadiran, Jurnal, Catatan Murid, observation and rubric entry (works offline)
Buat        Galeri templat: RPM, LKPD, Bank Soal, Paket Soal TKA, Quiz Battle,
            assessment instruments (more games later)
            Kurikulum (CP, TP, ATP, Prota, Promes), as reference and for school changes
            Kokurikuler
Asesmen     Rencana penilaian, Nilai, Pindai LJK, Analisis (per TP), Rapor
            Simulasi TKA: sessions, exam cards, proctor token, results
            "Buat soal" is a shortcut to the Bank Soal template
Kelas Saya  Murid (incl. pindah/keluar, kenaikan), Jadwal, Piket, Ekstrakurikuler,
            Keuangan and Tabungan (school may turn off), Inventaris

Account menu (profile icon): Dokumen Saya (incl. Berkas Administrasi), Kunci AI Saya,
Paket Saya, Akun, Bantuan, Minta Bantuan
```

Ringkasan diagram: *Hari Ini* berisi jadwal hari ini, yang belum diisi, kemajuan minggu ini, Kabar, Siapkan besok, dan Pusat Tindak Lanjut (tindak lanjut milik sendiri). *Di Kelas* berisi Kehadiran, Jurnal, Catatan Murid, serta isian observasi dan rubrik (bekerja offline). *Buat* berisi galeri templat (RPM, LKPD, Bank Soal, Paket Soal TKA, Quiz Battle, instrumen asesmen), Kurikulum, dan Kokurikuler. *Asesmen* berisi rencana penilaian, nilai, Pindai LJK, analisis per TP, rapor, dan Simulasi TKA. *Kelas Saya* berisi murid, jadwal, piket, ekstrakurikuler, keuangan dan tabungan, dan inventaris. Menu akun berisi Dokumen Saya (termasuk Berkas Administrasi), Kunci AI Saya, Paket Saya, Akun, Bantuan, dan Minta Bantuan.

Portal, dibuka lewat tombol pindah peran:

| Portal | Bagian |
|---|---|
| Kepala Sekolah | Ringkasan, Pantau Guru (Berkas Administrasi per guru atau seluruh sekolah, Pengingat), Validasi (termasuk pengesahan dokumen, pengesahan leger), Supervisi, Pusat Tindak Lanjut, Program Sekolah, Pembagian Tugas (JTM, SK), Agenda & Rapat, Buku Sekolah (tamu, humas, notulen), Budaya Sekolah, Data Sekolah, Pengaturan Sekolah (cetak, langganan) |
| Operator Sekolah | Tahun Ajaran (termasuk Arsip Tahun Ajaran), Kelas, Guru (undang), Jadwal, Kaldik, Profil Sekolah, Mutasi, Inventaris Sekolah |
| Pengawas | Sekolah Binaan (ajukan sekolah; Tim SIGRAK menyetujui, D-309), Pantau (hanya-baca, rekap tingkat sekolah), Validasi, Supervisi (rekap guru dan PKKS kepala sekolah), Pusat Tindak Lanjut, Laporan Kepengawasan |

Nama menunya *Kehadiran*, dengan path `/kehadiran`; pencarian bantuan tetap menemukan "absensi" (D-302). *Berkas Administrasi* adalah bundel dokumen guru per semester (D-278); Hari Ini menampilkan pintasannya menjelang akhir semester. *Pusat Tindak Lanjut* adalah satu daftar tindak lanjut per peran yang dibaca dari catatan yang sudah ada: guru melihat miliknya sendiri, kepala sekolah milik sekolah, pengawas hanya butir tingkat sekolah dan PKKS (D-310). *Arsip Tahun Ajaran*: tahun ajaran yang sudah ditutup hanya bisa dilihat dan diunduh (D-318). *Supervisi* di portal pengawas mencakup kepala sekolah: instrumen PKKS disusun dari aturan yang berlaku, varian daerah seperti Kudus disimpan sebagai data, dan skor manual dengan bukti opsional untuk butir yang datanya tidak ada di SIGRAK (D-308, D-324, D-327, D-328).

### Konsol internal

Konsol internal untuk Tim SIGRAK adalah panel Filament terpisah dengan login sendiri. Konsol ini bukan bagian dari aplikasi sekolah.

- **Peran staf (D-314, D-338, D-339, D-343).** Sebelas peran, semuanya aktif: Pemilik, Admin, Validator, Penyusun Konten, Kurator Kurikulum, Support, Pemasaran, Penjualan, Koordinator Wilayah, Keuangan, Teknis. Wewenang ditetapkan di kode, dengan matriks *Hak akses* yang hanya bisa dibaca; admin hanya mengubah cakupan tiap orang (daerah, jenjang, bidang), dan perubahannya dicatat. Kode saat ini masih punya tiga peran (admin, support, content) sampai irisan konsol. Masuk dua langkah dengan aplikasi autentikator (TOTP) wajib untuk Pemilik dan Admin.
- **Model AI (D-315, D-326).** Model aktif dan cadangannya per keperluan, diuji dengan kunci milik Tim SIGRAK. Adaptor yang kompatibel dengan OpenAI dan adaptor Anthropic melayani pekerjaan Tim SIGRAK sendiri; kunci guru tetap hanya Gemini.
- **Kesehatan Sistem (D-316):** antrean, job gagal, disk, jumlah pembuatan, galat AI, cadangan terakhir.
- **Kurikulum (D-317, D-305):** bandingkan, terbitkan, kembalikan ke versi sebelumnya, laporan dampak, audit JP; katalog aplikasi lama diimpor sekali setelah ditinjau.
- **Retensi (D-318):** per jenis data, dengan batas bawah (rapor, leger, data kelulusan, dan buku induk tidak pernah dihapus otomatis; hasil supervisi dan PKKS disimpan paling sedikit 5 tahun).
- **Kabar bersasaran (D-319)** per kabupaten, kecamatan, jenjang, dan peran; **status permintaan bantuan** Baru, Diproses, Selesai (D-320); **pengajuan sekolah binaan** disetujui atau ditolak (D-309).
- **Pemetaan paket (D-211):** fitur mana yang inti dan mana yang tambahan diatur di sini, bukan di kode.
- **Pembayaran (D-340):** Penjualan mencatat klaim pembayaran; Keuangan mengonfirmasinya, atau pemilik selama belum ada yang memegang peran itu.
- **Akses support (D-341):** support membuka tampilan sekolah secara hanya-baca berdasarkan MoU yang ditandatangani tiap sekolah; data agama dan kesehatan tetap tersembunyi; satu baris internal per sesi support.
- **Mitra (D-342):** komunitas guru (KKG, KKM) dan reseller mendapat kode rujukan dan laporan mitra, bukan akun konsol.

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
2. Di awal hari, buka *Di Kelas → Kehadiran*. Semua murid awalnya hadir, dan guru mengetuk murid yang tidak hadir.
3. Setelah pelajaran, *Jurnal* sudah terisi dari RPM. Guru menandainya terlaksana, terlaksana sebagian (sisanya dilanjutkan), atau tidak terlaksana dengan alasan, lalu menambah catatan (D-267, D-268).
4. Catatan murid, observasi, dan skor rubrik ditulis saat ada kejadian. Semuanya tersimpan tanpa sinyal dan dikirim saat koneksi kembali (D-289, D-349).

**Guru atau operator: ujian latihan TKA (CBT)**

1. **Siapkan soal:** buat *Paket Soal TKA* (Bahasa Indonesia atau Matematika, 30 butir dalam bentuk soal TKA) dengan templat, lalu periksa dan sunting.
2. **Buat sesi:** pilih paket, kelas 6 yang ikut, tanggal, dan durasi (bawaan 75 menit). Sistem mencetak *kartu login* untuk setiap siswa.
3. **Jalankan:** pada hari ujian, guru (sebagai proktor) membuka sesi dan membacakan token yang berlaku, yang berganti setiap 15 menit. Siswa masuk di situs ujian, dan proktor memantau kemajuan serta bisa mereset peserta yang tertahan.
4. **Hasil:** nilai 0–100, ditampilkan dalam empat tingkat (Istimewa, Baik, Memadai, Kurang). Data dan ekspor menyimpan kategori resmi (Baik, Memadai, Kurang) dan predikat Istimewa (nilai ≥ 95,00) secara terpisah, sehingga cocok dengan SHTKA. UI mencatat bahwa ini hasil latihan. Lalu analisis per kompetensi dan ekspor Excel/PDF (D-371).

**Operator: awal tahun ajaran.** Atur tahun ajaran dan kelas → undang guru → isi jadwal pelajaran → atur kalender.

**Kepala sekolah: mingguan.** *Ringkasan* (kelengkapan per guru minggu ini, dari satu layanan kelengkapan, D-263) → *Pantau Guru* (siapa yang tertinggal, konten apa yang dibuat, kirim *Pengingat*, tidak pernah "teguran", D-311) → *Validasi* (setujui atau kembalikan perubahan kurikulum sekolah dengan catatan).

**Pengawas: per kunjungan.** Pilih sekolah → lihat kelengkapan dan rekap supervisi tingkat sekolah (hanya-baca) → validasi akhir, PKKS kepala sekolah → unduh laporan supervisi.

**Validasi (D-019).** Konten yang dihasilkan langsung bisa dipakai. Kepala sekolah dan pengawas bisa melihat dokumen guru dan memberi komentar. Hanya kurikulum yang *diubah atau ditambah* sekolah yang melewati guru → kepala sekolah → pengawas. Kurikulum bawaan yang dipakai apa adanya tidak pernah masuk antrean. Dokumen yang sudah jadi bisa disahkan kepala sekolah (*pengesahan dokumen*, dengan kode verifikasi; tidak pernah disebut "tanda tangan elektronik"), dan revisi yang sudah disahkan tidak pernah dipangkas (D-281, D-282).

**Supervisi guru (D-222…D-225).** Ke-18 instrumen dirilis bersamaan sebagai templat yang bisa disunting, dengan skala observasi Ruang GTK dan rentang nilai yang bisa diubah sekolah. Hanya kepala sekolah yang mengobservasi dan menelaah guru; pengawas hanya melihat rekap sekolah; umpan balik murid dimasukkan sebagai jumlah per kelas.

## Model bisnis

Paket dibayar per tahun ajaran (Juli–Juni) **hanya oleh guru dan sekolah**. Siswa dan orang tua tidak pernah membayar dan tidak pernah melihat harga (D-157). Kunci AI milik guru (D-012; hanya paket gratis Gemini, D-023), jadi pemakaian AI tidak pernah menjadi biaya SIGRAK. **Langganan sekolah dibayar dari dana selain BOS** (D-025). Materi penjualan tidak boleh menyiratkan BOS sebagai sumber pembayaran. SIGRAK tidak membuat laporan BOS atau RKAS (D-219, D-328).

| Paket | Untuk | Isi | Harga per tahun | Pembayar |
|---|---|---|---|---|
| Uji coba | Satu guru | Semua fitur selama 1 minggu (D-160). Setelah itu akun hanya bisa melihat dan mengunduh datanya sampai paket dibeli | Gratis | — |
| Sigrak Guru | Satu guru | Semua fitur guru | Rp60.000, atau Rp50.000 bila sekolah guru itu berlangganan Sigrak Sekolah | Guru |
| Sigrak Sekolah | Satu sekolah | 2 akun, kepala sekolah dan operator, dengan portalnya: validasi, pengaturan cetak sekolah, undangan, data sekolah. Guru membeli Sigrak Guru sendiri | Rp100.000 | Sekolah (dana non-BOS) |
| Fitur tambahan | Satu sekolah yang berlangganan Sigrak Sekolah | Satu fitur tambahan, diaktifkan sekolah untuk semua akunnya (D-158) | Rp50.000 per fitur | Sekolah (dana non-BOS) |
| Sigrak Binaan | Satu pengawas atau gugus KKG | Portal pengawas. Tidak dijual: terbuka gratis begitu 5 atau lebih sekolah binaannya berlangganan Sigrak Sekolah | Gratis | — |

**Fitur tambahan (D-158):** Simulasi TKA · Buku Induk dan sinkronisasi Dapodik (segera hadir, D-161) · Keuangan dan Inventaris kelas · Buku Sekolah · Game kelas (Quiz Battle, *Tayangkan*, *Unduh game*; berkembang menjadi mode belajar IFP, D-171, [`ifp-classroom.md`](ifp-classroom.md)) · Rapor (leger, draf deskripsi, ekspor rapor). Fitur tambahan lain bisa menyusul. Guru boleh mencoba Game kelas selama 7 hari, satu kali, walaupun sekolahnya belum mengaktifkannya (D-159). Daftar ini hanya konfigurasi awal: Tim SIGRAK mengatur fitur mana yang inti dan mana yang tambahan, per fitur, di konsol (D-211). Fitur yang diterima sejak P4-04, dan semua fitur LKPD, dimulai sebagai fitur inti (D-211, D-336).

## Status fitur

Ini 43 fitur aplikasi lama ditambah fitur baru, direvisi untuk konsep baru. Nilai status: **Pertahankan**, **Gabung**, **Ganti**, **Hapus**, **Tambah**, **Nanti**. Semua yang tidak bertanda *Hapus* atau *Nanti* masuk v1.

| Area | Fitur aplikasi lama | Status | Menjadi | Alasan |
|---|---|---|---|---|
| Beranda | Beranda: kemajuan administrasi | Ganti | Hari Ini: yang harus diisi hari ini + kemajuan minggu ini, dari satu layanan kelengkapan dengan jendela waktu dan potret mingguan; "Siapkan besok"; Kabar kesiapan RPM dan neraca JP hanya untuk guru | Guru perlu tahu apa yang harus dikerjakan sekarang (D-263…D-266, D-270, D-271) |
| Beranda | Papan pengumuman | Gabung | Kabar (dengan Pemberitahuan) | Dua tempat berita membingungkan |
| Beranda | Kutipan hari ini, tips & motivasi | Hapus | — | Hiasan yang menghabiskan ruang layar ponsel |
| Beranda | Pemberitahuan | Gabung | Kabar | Satu kotak masuk |
| Beranda | Mode demo/inspeksi | Ganti | Mode Contoh untuk demo penjualan | Nama lebih jelas bagi pembeli |
| Beranda | Sesi terputus setelah 15 menit diam | Ganti | Sesi tetap. Formulir tersimpan otomatis | Guru sering berhenti di tengah formulir. Alasan biaya lama hilang dengan SQL |
| Buat | Kurikulum (CP, TP, ATP, Prota, Promes) | Gabung | Buat > Kurikulum, dengan "kembalikan ke bawaan" dan Promes rencana vs pelaksanaan (Promes Realisasi). Prota/Promes mencetak minggu efektif | Dasar perencanaan dan konten (D-214, D-274, D-276) |
| Buat | Generator RPM | Pertahankan | Buat > templat RPM, 4 langkah (Kapan, Apa, Cara, Periksa) di mesin templat. Peningkatan: geser otomatis dengan pratinjau dan urungkan, Neraca JP sebelum SAS, draf semester 2 lebih awal, salin ke kelas paralel tanpa AI | Fitur paling berharga (D-269, D-272, D-274, D-275) |
| Buat | Modul mandiri (mode manual) | Gabung | Pilihan "RPM lepas jadwal" di langkah 1, lalu ditempatkan dengan "Pasang ke jadwal" | Satu pintu untuk setiap RPM (D-273) |
| Buat | Modul kokurikuler (7 KAIH, projek, lintas disiplin) | Pertahankan | Buat > Kokurikuler, varian templat RPM yang membaca rencana sekolah. Bentuknya mengikuti Panduan Kokurikuler 2025: Lintas Disiplin, 7 KAIH, Cara Lainnya (projek termasuk di sini). Koding dan Kecerdasan Artifisial berjalan di sini (D-208). Dinilai pada 8 DPL; target JP tahunan otomatis | Fitur yang sudah ada (D-210, D-214, D-277) |
| Buat | Worksheet / LKPD | Gabung | Buat > templat LKPD, dengan blok untuk kelas 1–3 dan kolom `pattern`. Juga lampiran RPM | LKPD bagian dari rencana (D-296, D-329) |
| Buat | Bank Modul / penyimpanan RPM | Gabung | Dokumen Saya | Satu arsip untuk setiap dokumen |
| Buat | Unduh / impor cadangan | Ganti | Simpan otomatis di server. Ekspor data lengkap di Akun | Cadangan manual membuat guru ragu datanya aman |
| Buat | Mencocokkan tanggal dengan konten (kokurikuler) | Ganti | Penyesuaian otomatis + Kabar saat jadwal berubah | Guru tidak perlu tahu mekanismenya |
| Buat | PDF tangkapan layar (html2canvas) | Ganti | PDF teks dari server + pratinjau sebelum unduh | Berkas ringan, teks bisa dicari, tanpa garis terpotong |
| Asesmen | Bank Soal (PH, STS, SAS, US, kisi-kisi, kartu soal) | Gabung | Buat > templat Bank Soal, dengan data per butir, enam cetakan turunan, dan pustaka butir lintas dokumen. Asesmen > pintasan "Buat soal" | Soal terkait TP, satu mesin konten (D-293, D-294) |
| Asesmen | "Mode Ujian" Bank Soal | Nanti (tidak aktif) | — | Dimatikan untuk sementara (D-026). Ujian latihan TKA satu-satunya ujian online di v1 |
| Asesmen | Program penilaian | Gabung | Asesmen > Rencana Penilaian, dengan KKTP per TP, asesmen diagnostik, dan komponen sumatif berbobot | (D-214, D-290) |
| Asesmen | Analisis nilai, taraf serap | Pertahankan | Asesmen > Analisis, otomatis per TP: cetakan daya serap, Kelompok belajar, analisis butir, remedial dan pengayaan (batas nilai remedial) | "Daya serap" istilah yang berlaku sekarang (D-214, D-291, D-295) |
| Asesmen | Leger dan "inject e-Rapor" | Ganti | Asesmen > Rapor: leger dengan konfirmasi guru dan pengesahan kepala sekolah yang menguncinya, draf deskripsi, draf AI catatan wali kelas tanpa data murid. Rapor memuat 12 komponen minimum (nilai hanya dari hasil sumatif, deskripsi capaian kompetensi, deskripsi kokurikuler, ekstrakurikuler, ketidakhadiran, catatan wali kelas, tanggapan orang tua), dengan pilihan Fase A tanpa angka. Ekspor dalam tata letak impor e-Rapor SD dan RDM, dibangun tepat setelah irisan Asesmen | "Inject" terlalu teknis (D-205, D-210, D-213, D-214, D-218, D-292) |
| Asesmen | Jenjang SMP–SMK, kelas 7–12 | Hapus | Kembali bila data SMP/MTs ada | Pilihan tanpa data berakhir dengan galat |
| Di Kelas | Absensi | Ganti | Di Kelas > **Kehadiran**: **harian** (D-029), bawaan "semua hadir", ketuk yang tidak hadir, bekerja offline. Rekap per minggu, bulan, dan semester; peringatan dini di Hari Ini wali kelas | Diisi di ponsel pada awal hari (D-302, D-303) |
| Di Kelas | Jurnal dan refleksi | Ganti | Di Kelas > Jurnal terisi dari RPM, dengan hasil terlaksana, sebagian (dilanjutkan), atau tidak terlaksana dengan alasan, dan rekap JP yang hilang. Refleksi guru paling sedikit sekali per semester, terisi awal dari jurnal dan nilai | Lebih sedikit mengetik ulang (D-214, D-267, D-268) |
| Di Kelas | Catatan anekdot, konseling (BK), prestasi | Gabung | Di Kelas > Catatan Murid (satu formulir, jenisnya dipilih; tag sekali ketuk; catatan BK tidak pernah masuk Berkas, tampilan pengawas, atau AI) | Kolom yang sama di tiga buku (D-210) |
| Di Kelas | Buku tamu, buku humas, notulen | Gabung | Portal Kepala Sekolah > Buku Sekolah | Milik sekolah, bukan kelas |
| Kelas Saya | Data siswa | Pertahankan | Kelas Saya > Murid (hanya catatan data, D-010) | "Murid" di teks UI (D-210) |
| Kelas Saya | Mutasi siswa | Gabung | Kelas Saya > Murid > Pindah atau keluar. Berupa draf sampai ditetapkan; operator mendapat pengingat Dapodik 14 hari kerja | Bagian dari riwayat murid (D-217, D-304) |
| Kelas Saya | Kenaikan kelas | Gabung | Kelas Saya > Murid > Kenaikan, aktif menjelang akhir tahun; berupa draf sampai ditetapkan, dengan lembar rapat dan berita acara. Kelulusan kelas 6 dengan data SKL dan ijazah | Dipakai setahun sekali (D-215, D-304) |
| Kelas Saya | Kalender dan jadwal pelajaran | Pertahankan | Kelas Saya > Jadwal. Sekolah menyalin kaldik daerahnya, dengan tawaran pembaruan (kalender Jawa Tengah 6 hari untuk Kudus, Pati, Jepara); hari libur nasional dan cuti bersama terpusat; acara per kelas; hari dan minggu efektif; satu pemeriksa bentrok; pratinjau dampak perubahan; pembuat draf jadwal | D-252…D-262, D-323 |
| Kelas Saya | Piket | Pertahankan | Kelas Saya > Piket: kelompok yang dibuat otomatis dengan rotasi, tugas, dan tanda tidak hadir; tampil di layar diam Game kelas bila sekolah memilihnya | D-297, D-298 |
| Kelas Saya | Keuangan kelas | Pertahankan | Kelas Saya > Keuangan, sekolah bisa mematikannya. Laporan dibagikan manual, tanpa pengingat uang; *Sumbangan sukarela* (tidak pernah "tunggakan") | Tidak semua sekolah mengizinkannya (D-299, D-300) |
| Kelas Saya | Inventaris kelas | Pertahankan | Kelas Saya > Inventaris, tampilan per ruang dari inventaris sekolah | D-217, D-306 |
| Akun | Pengaturan (tema, kertas, profil, kata sandi) | Ganti | Akun (profil, kata sandi, tema, gambar kop sendiri) + Pengaturan Sekolah (kertas, gambar kop bawaan, penanda tangan) | Sekolah mengatur bawaan; guru boleh memakai gambar kop sendiri (D-031) |
| Akun | Profil sekolah "yang pertama menyimpan menang" | Ganti | Dimiliki operator/kepala sekolah. Guru mengusulkan koreksi | Pemilik data jelas |
| Akun | API key Gemini wajib saat aktivasi guru | Ganti | Kunci AI Saya: beberapa kunci, ditanyakan saat pertama memakai AI, dengan tombol uji dan status | Pendaftaran cepat. AI tidak pernah menghalangi fitur lain |
| Akun | API key wajib di profil kepala sekolah | Hapus | — | Kepala sekolah tidak butuh AI |
| Akun | Kata sandi bawaan bersama untuk pengguna baru | Ganti | Tautan undangan, pengguna mengatur kata sandinya sendiri | Keamanan akun |
| Akun | Tutorial | Ganti | Bantuan: bantuan singkat per halaman + pusat bantuan | Bantuan saat dibutuhkan |
| Akun | Arsip | Ganti | Dokumen Saya: RPM, soal, LKPD, game, laporan, dengan status validasi dan komentar | |
| Akun | Hubungi admin / kotak masuk | Pertahankan | Menu akun > Minta Bantuan, dengan status Baru, Diproses, Selesai | D-320 |
| Akun | Permintaan pindah sekolah | Pertahankan | Akun > Pindah Sekolah, disetujui sekolah tujuan; aturan tetap tentang apa yang ikut pindah dan apa yang tinggal | D-307 |
| Portal | Portal kepala sekolah | Ganti | Bagian yang tercantum di Navigasi. Ditambah supervisi akademik, bukti dukung kinerja (K3, termasuk milik kepala sekolah sendiri), pembagian tugas dengan JTM dan SK, budaya sekolah aman (tata tertib, SOP, log insiden internal), inventaris sekolah, Pusat Tindak Lanjut, Pengingat | Disusun dari pekerjaan kepala sekolah (D-217, D-308, D-310, D-311) |
| Portal | Portal pengawas | Ganti | Bagian yang tercantum di Navigasi. Ditambah pengajuan sekolah binaan, PKKS kepala sekolah, rekap lintas sekolah, pendampingan Pembelajaran Mendalam | D-217, D-308, D-309, D-324, D-327, D-328 |
| Portal | Super Admin / Control Center | Ganti | Konsol Filament internal + langganan + registri templat; daftar lengkap di Konsol internal | Bukan bagian aplikasi sekolah (D-314…D-320, D-326, D-338…D-343) |
| Baru | Galeri templat | Tambah | Buat > Galeri templat | Mesin konten (D-013) |
| Baru | Game Quiz Battle | Tambah | Buat > Quiz Battle, dari set Bank Soal atau soal baru | Game pertama (D-014) |
| Baru | Mode tayang dan unduh game | Tambah | Tombol *Tayangkan* dan *Unduh game* di setiap game | Cara game sampai ke siswa (D-011) |
| Baru | Usulan fitur dari templat | Tambah | Tautan "Usulkan fitur" di wizard game | Pengganti kode game buatan AI di v1 (D-015) |
| Baru | Templat paket latihan TKA | Tambah | Buat > Paket Soal TKA | D-026 |
| Baru | Ujian latihan TKA (CBT): sesi, kartu ujian, token berganti, tampilan proktor, hasil | Tambah | Asesmen > Simulasi TKA + situs ujian terpisah | D-026, D-027 |
| Baru | Beberapa kunci AI (paket gratis Gemini) dengan rotasi dan failover | Tambah | Kunci AI Saya | D-012, D-023 |
| Baru | Riwayat pemakaian AI | Tambah | Kunci AI Saya > Riwayat | Guru melihat mengapa AI berhenti |
| Baru | Revisi satu bagian dengan AI | Tambah | "Perbaiki bagian ini" di setiap bagian pratinjau | Tanpa membuat ulang seluruhnya |
| Baru | Draf deskripsi rapor per TP | Tambah | Asesmen > Rapor | Disusun dari capaian TP formatif tertinggi dan terendah (D-205) |
| Baru | Templat awal KSP (Kurikulum Satuan Pendidikan) | Tambah | Portal Kepala Sekolah > Program Sekolah | Format KSP bebas; SIGRAK memberi draf awal yang mengikuti komponen resmi, lalu sekolah menyesuaikannya (D-209) |
| Baru | Di Kelas offline | Tambah | Menu Di Kelas: Kehadiran, Jurnal, Catatan Murid, ditambah pertemuan RPM hari ini dan besok (hanya-baca), antrean Pindai LJK, serta isian observasi dan rubrik. Banner "Versi baru tersedia" menyinkronkan antrean sebelum memuat ulang | D-016, D-289, D-321, D-349 |
| Baru | Validasi kurikulum guru → kepala sekolah → pengawas | Tambah | Status di Dokumen Saya + antrean portal | D-019 |
| Baru | Komentar kepala sekolah/pengawas pada dokumen | Tambah | Tampilan dokumen | D-019 |
| Baru | Pengaturan cetak: kop sebagai **gambar yang diunggah** (bawaan sekolah, guru boleh mengunggah sendiri) atau dibuat otomatis dari profil sekolah, penanda tangan, A4/F4, font, sampul, tiga profil margin, cetak bolak-balik, lanskap otomatis, halaman NIHIL | Tambah | Pengaturan Sekolah > Cetak; Akun > Kop saya | Guru mengatur tata letak dan logo kop di dalam gambarnya sendiri (D-031, D-283, D-284) |
| Baru | Berkas Administrasi: bundel per semester (sampul, lembar kelengkapan, daftar isi, halaman NIHIL) sebagai satu PDF gabungan atau ZIP; profil Standar SIGRAK, Format Kudus, K3 | Tambah | Dokumen Saya > Berkas Administrasi; kepala sekolah per guru atau seluruh sekolah | Pengganti pengumpulan berkas (D-278…D-280) |
| Baru | Pengesahan dokumen dengan kode verifikasi; revisi yang sudah disahkan disimpan | Tambah | Tampilan dokumen, kepala sekolah | D-281, D-282 |
| Baru | Bunyi dan notifikasi layar saat selesai | Tambah | Pengaturan per pengguna | D-285 |
| Baru | Supervisi: 18 instrumen guru sebagai templat yang bisa disunting, saran skor S-01/S-15 dari kelengkapan, bank temuan per butir | Tambah | Portal Kepala Sekolah > Supervisi | D-222…D-225, D-312, D-313 |
| Baru | Pindai LJK: tata letak lembar jawaban milik sekolah dengan penanda sudut dan QR, dibaca di peramban guru oleh pustaka OMR JavaScript; hanya jawaban dan tingkat keyakinan yang sampai ke server, foto tidak pernah diunggah; antrean offline; grid cepat | Tambah | Asesmen > Pindai LJK | D-286, D-287, D-289, D-325, D-369 |
| Baru | Fitur asesmen dan kelas untuk guru: asesmen diagnostik, pelacak 7 KAIH dengan lembar rumah, kesepakatan kelas dan catatan disiplin positif, portofolio karya dengan foto | Tambah | Asesmen, Di Kelas | D-214 |
| Baru | Instrumen asesmen: data dan tag Peta Asesmen, Rubrik Kinerja, Observasi DPL (isian di ponsel, offline), Cek Kesiapan Awal, Akomodasi Murid, kartu penilaian diri dan teman, tag miskonsepsi. AI asesmen tidak pernah melihat data murid; hasil yang sensitif mengikuti aturan data kesehatan | Tambah | Buat > Galeri templat; Di Kelas | Gelombang A (D-344…D-351) |
| Baru | Fitur wali kelas: peringatan dini ketidakhadiran, lembar rapat kenaikan, kelulusan, kunjungan rumah dan data kesehatan ringan (tidak pernah ke AI), pesan ke orang tua lewat tautan `wa.me` | Tambah | Kelas Saya, Di Kelas | D-215, D-303 |
| Baru | Ekstrakurikuler dengan Pramuka ditawarkan secara bawaan: kegiatan, pembina, anggota, kehadiran, deskripsi rapor | Tambah | Kelas Saya > Ekstrakurikuler, di awal v1 | Kepanduan wajib ditawarkan (D-216) |
| Baru | Tabungan murid: buku tabungan per murid yang ikut berpindah bersama murid, rekap per kelas dan sekolah, berita acara saat murid keluar | Tambah | Kelas Saya > Keuangan | D-219, D-301 |
| Baru | Bentuk LKPD bergelombang: Gelombang 1 Detektif Teks, Peta Cerita, Data Kelasku, Etnomatematika, Misi Petualangan, Tiga Jalur, Tiket Keluar, Kotak Terkunci, Pikir Komputasional Tanpa Komputer; profil Konteks Daerah; preset sekolah; QR ke URL pihak ketiga; sematan berlisensi; nanti pemutar H5P dan penampil 3D yang dihosting sendiri | Nanti (gelombang setelah v1) | Buat > LKPD, semuanya inti | D-329…D-337 |
| Baru | Buka di Canva | Tambah | Tombol di pratinjau RPM, LKPD, soal (unduh + buka canva.com) | |
| Baru | Langganan dan penagihan | Tambah | Pengaturan Sekolah > Langganan (paket dan fitur tambahan). Akun > Paket Saya | Model bisnis (D-157) |
| Baru | Buku Induk dan sinkronisasi Dapodik: impor ekspor Excel Dapodik (siswa, rombel, ekstrakurikuler, dan data PTK seperti gaji berkala dan kepangkatan) dan menjaga data sekolah tetap selaras | Nanti | Portal operator > Data Sekolah, sebagai fitur tambahan, ditampilkan sebagai segera hadir | Sekolah sudah menyimpan data ini di Dapodik (D-158). Ditahan sebagai segera hadir; draf rancangan P4-01 ditunda (D-161) |
| Baru | Mengundang guru | Tambah | Portal operator > Guru | Pengganti kata sandi bawaan |
| Baru | Bawaan muatan lokal dan kalender per daerah | Tambah | Konsol internal + Pengaturan Sekolah | D-018 |
| Baru | Landasan kebijakan: Profil Jenjang, set aturan berversi, panel Dasar Kebijakan, Cek Keselarasan | Tambah | Setiap dokumen, dimulai dengan RPM di P5-04 | D-352…D-358 (lihat Landasan kebijakan) |
| Baru | Pengenalan 3 langkah | Tambah | Tampil sekali setelah mendaftar: kelas, jadwal, siswa | Siap sejak hari pertama |
| Nanti | Templat game menurut fungsi belajar: Lomba Tim, Pilah & Kelompokkan, Pasangkan lebih dulu, lalu Urutkan, Papan Petak, Kotak Kejutan, Tiket Keluar, Pemanasan TKA | Nanti | Templat game berikutnya di Game kelas ([`ifp-classroom.md`](ifp-classroom.md)) | Setelah Quiz Battle siap untuk IFP (D-176 menggantikan daftar "nanti" di D-014) |
| Nanti | Kode game buatan AI atas permintaan | Nanti | Butuh rancangan sandbox dan tinjauan | D-015 |
| Nanti | Canva Connect API (impor langsung ke Canva guru) | Nanti | | Tombol tingkat 1 cukup untuk v1 |
| Nanti | Offline di luar Di Kelas (menyunting, AI, dan yang lainnya) | Nanti | | D-016; pengecualian v1 ada di D-289 dan D-349 |
| Nanti | Membaca jawaban Uraian dan Isian tulisan tangan di ponsel; hanya teks dengan kode `nomor absen-nomor soal` (misalnya `12-3`) yang dikirim ke AI, guru yang memutuskan | Nanti | Asesmen, setelah Pindai LJK di P5-08 | D-370 |
| Nanti | Lembar jawaban sebagai cadangan kertas untuk Simulasi TKA | Nanti | | D-288 |
| Nanti | Segera hadir (ditampilkan): portal orang tua, SPP, log MBG, perpustakaan. Nanti (tidak ditampilkan): kartu pelajar, persuratan, laporan bulanan, kesiapan akreditasi. Penyusun dan evaluasi KSP setelah v1 | Nanti | | D-217, D-220 |
| Dihapus | Generator konten umum ("Buat bebas") | Hapus | — | Guru bisa langsung melakukannya di Gemini (D-024) |
| Dihapus | Slide per pertemuan ("Bahan Tayang") | Hapus | — | Tidak diperlukan; guru menayangkan dengan alat mereka sendiri (D-033) |
| Nanti | Kehadiran per jam pelajaran untuk guru mapel | Nanti | | Kehadiran harian di v1 (D-029) |
| Dihapus | Laporan BOS/RKAS; kehadiran guru dan tenaga kependidikan | Hapus | — | ARKAS dan e-RKAM menangani laporan BOS; sistem kepegawaian menangani kehadiran staf (D-217, D-219) |
| Nanti | Pendidikan Kepercayaan terhadap Tuhan YME | Nanti | | Tidak di v1 (D-037) |
| Nanti | SMP/MTs | Nanti | | Butuh data kurikulum |

## Cakupan v1

v1 berisi setiap baris di atas yang tidak bertanda *Hapus* atau *Nanti*. Itu rilis yang besar, jadi Fase 2 sebaiknya memotongnya menjadi irisan yang bisa dirilis satu per satu. Urutan, sudah dikonfirmasi pemilik (D-162):

1. Fondasi: autentikasi, undangan, sekolah dan peran, tahun ajaran dan kelas, impor kurikulum, jadwal pelajaran.
2. Inti konten: Mesin Penyedia AI, mesin templat, templat RPM, Dokumen Saya, ekspor PDF/DOCX.
3. Templat lain: Bank Soal, LKPD, Quiz Battle (tayang + unduh).
4. TKA: templat Paket Soal TKA, situs ujian, sesi, kartu ujian, token, hasil. Target: musim TKA 2028 (D-162).
5. Di Kelas dengan sinkronisasi offline, Jurnal dari RPM.
6. Asesmen: rencana, nilai, Pindai LJK, analisis per TP, rapor; ekspor e-Rapor SD dan RDM tepat sesudahnya (D-218).
7. Portal: kepala sekolah, operator, pengawas. Validasi dan komentar.
8. Bisnis: uji coba, langganan, fitur tambahan, penagihan, Mode Contoh.

Sebelum irisan 2 (P5-04), pemversian aturan minimum dan Profil Jenjang sudah ada (D-355). Ekstrakurikuler dan Pramuka datang di awal v1, tidak bersama Buku Induk (D-216). Bagian konsol datang bersama P5-04 dan P5-10 (D-314…D-320).

## Pertanyaan terbuka

- [x] Harga: uji coba 1 minggu (D-160), Sigrak Guru Rp60.000, Sigrak Sekolah Rp100.000, fitur tambahan Rp50.000 per fitur (D-157, D-158).
- [ ] Nama akhir: periksa "Sigrak" (dan cadangan "Kapur") di [PDKI](https://pdki-indonesia.dgip.go.id) kelas 9, 41, 42, lalu daftarkan domain .id dan .com bersamaan. Risiko nama lebih tinggi sekarang karena pasarnya nasional.
- [ ] Logo: brief untuk desainer (berwarna, satu warna, negatif, minimal 16 px).
- [x] Modul kokurikuler masuk v1 sebagai varian templat RPM (D-014).
- [ ] Uji menu lima tab dengan 5 guru kelas dan 2 guru mapel memakai prototipe klik (tanpa data sungguhan).
- [ ] Presentasikan versi singkat ke kepala sekolah dan pengawas, lalu catat keberatan mereka.
- [x] Klausul batas usia di bawah 18 tahun dari Gemini: siswa hanya menerima konten AI yang sudah jadi dan diperiksa guru, dan tidak pernah memicu AI (D-124).
- [ ] Jadwal TKA 2028 untuk SD/MI, begitu diumumkan, untuk menentukan waktu rilis fitur TKA (D-162).
- [ ] Pemetaan paket di konsol (D-211): apakah kunci leger dan bobot nilai masuk Asesmen inti atau fitur tambahan Rapor, dan apakah sumbangan dan tabungan masuk fitur tambahan Keuangan.
- [ ] Peninjau data agama dan salinan SK 9941/2025 dari kanal resmi (lihat `religious-education.md`, bagian Pertanyaan terbuka).
