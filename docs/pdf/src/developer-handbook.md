# Buku Panduan Pengembang SIGRAK

<p class="source-note">Diterjemahkan dari <code>docs/final/developer-handbook.md</code> pada 11 November 2026.</p>

- **Status:** final (permintaan pemilik 2026-11-09, D-361)
- **Terakhir diperbarui:** 2026-11-11 (P5-03i…l dibangun: kalender, draf jadwal, kolom versi, Profil Jenjang; D-387)
- **Pembaca:** setiap pengembang atau agen baru yang bergabung dengan SIGRAK, sebelum membaca dokumen lain. PDF berbahasa Indonesia ini, `docs/pdf/developer-handbook.pdf`, adalah satu bacaan lengkap untuk pengembang baru (D-361).
- **PDF berbahasa Indonesia:** `docs/pdf/developer-handbook.pdf`, diterjemahkan di `docs/pdf/src/developer-handbook.md`

## Cara membaca buku panduan ini

Buku panduan ini menceritakan seluruh SIGRAK di satu tempat: apa produknya, siapa penggunanya, aturan nasional mana yang menjadi pijakannya, bagaimana satu tahun ajaran mengalir di aplikasi dari undangan pertama sampai rapor terakhir, bagaimana kodenya disusun, bagaimana AI dijaga tetap aman, bagaimana ekspor dan server bekerja, dan bagaimana tim serta agennya bekerja. Bentuknya cerita. Buku ini **tidak** menggantikan dokumen rinci. Setiap bab diakhiri dengan, atau menunjuk ke, dokumen rinci dan ID keputusan (`D-NNN` di `docs/agent/DECISIONS.md`) yang memuat aturan persisnya.

Tiga label status pembangunan dipakai di seluruh buku:

| Label | Arti |
|---|---|
| **Dibangun** | Kodenya sudah ada di `main`, dengan tes, per 2026-11-09 |
| **Didesain** | Sudah disepakati di dokumen final, draf, atau keputusan, tetapi belum dikodekan |
| **Nanti** | Diterima sebagai fitur masa depan atau ditampilkan sebagai "segera hadir"; bukan bagian v1 |

Beberapa aturan yang perlu diingat selama membaca:

- **Keputusan yang lebih baru menang.** Dokumen final di `docs/final/` disetujui pada 2026-10-31 (D-123) dan di beberapa bagian sudah diperbarui sejak itu. Banyak keputusan setelah tanggal itu (D-203 dan seterusnya, terutama D-252…D-367) belum diterapkan ke setiap dokumen final. Bila dokumen final dan keputusan yang lebih baru berbeda, buku ini mengikuti keputusan yang lebih baru dan menyebutkannya. Daftar perbedaan yang diketahui ada di bab 12.
- **Tidak ada yang baru di sini.** Buku ini hanya menceritakan ulang apa yang sudah ada di dokumen final, draf yang diterima, keputusan, dan kode. Jika sesuatu tidak ada di sumber itu, berarti tidak ada di SIGRAK.
- **Nama.** Produk pemilik sebelumnya selalu disebut "aplikasi lama" dalam bahasa Indonesia dan "the legacy app" dalam bahasa Inggris (D-360).
- **Riset.** Sebelum meneliti sebuah peraturan, studi, atau vendor, baca dulu `docs/final/research-digest.md`, lalu tambahkan temuan Anda ke sana sesudahnya (D-362).

Isi:

1. Apa itu SIGRAK dan posisinya
2. Pengguna, peran, dan akses
3. Fondasi kebijakan
4. Alur dari awal sampai akhir
5. Arsitektur
6. Tur model data
7. Lapisan AI dan perlindungan data anak
8. Ekspor, lokalisasi, dan penulisan UI
9. Infrastruktur dan operasional
10. Cara kami bekerja
11. Peta jalan
12. Peta keputusan
13. Glosarium
14. Bacaan lanjutan

## 1. Apa itu SIGRAK dan posisinya

### 1.1 Produk dalam satu paragraf

SIGRAK (nama sementara sampai pemeriksaan merek di PDKI selesai; D-017, D-069) adalah aplikasi web untuk guru dan pimpinan sekolah dasar di Indonesia: **SD** (Sekolah Dasar, di bawah Kementerian Pendidikan Dasar dan Menengah, Kemendikdasmen) dan **MI** (Madrasah Ibtidaiyah, di bawah Kementerian Agama, Kemenag), kelas 1–6. Aplikasi ini mengerjakan tiga hal, dan ketiganya bagian dari v1 (D-009, D-026):

1. **Konten ajar dengan bantuan AI.** Guru membuat rencana pembelajaran (RPM), bank soal (Bank Soal), lembar kerja (LKPD), game kelas (Quiz Battle, yang berkembang menjadi Game kelas), dan paket latihan TKA. Guru memilih templat dan menjawab pertanyaan pedagogis. Guru tidak pernah menulis prompt (D-024).
2. **Administrasi sekolah.** Kehadiran, jurnal mengajar, catatan murid, asesmen dan rapor, data kelas, serta portal untuk kepala sekolah, operator sekolah, dan pengawas. Sebagian besar fitur ini berasal dari aplikasi lama, dibangun ulang di stack baru dengan model data baru (D-020).
3. **Ujian latihan TKA (CBT).** Ujian latihan berbasis komputer untuk kelas 6 yang bekerja seperti Tes Kemampuan Akademik nasional. Murid mengerjakannya di situs ujian terpisah dengan kartu ujian untuk satu sesi dan token proktor (D-026, D-027, D-089).

### 1.2 Siapa yang membayar

Hanya guru dan sekolah yang membayar. Murid dan orang tua tidak pernah membayar dan tidak pernah melihat harga (D-157). Harga berlaku per tahun ajaran (Juli–Juni):

| Paket | Untuk | Harga per tahun |
|---|---|---|
| Uji coba | Satu guru, semua fitur selama 7 hari (D-160) | Gratis |
| Sigrak Guru | Satu guru, semua fitur guru | Rp60.000, atau Rp50.000 bila sekolahnya punya Sigrak Sekolah |
| Sigrak Sekolah | Dua akun (kepala sekolah, operator) dengan portalnya | Rp100.000 |
| Fitur tambahan | Satu fitur ekstra untuk sekolah yang punya Sigrak Sekolah | Rp50.000 per fitur |
| Sigrak Binaan | Portal pengawas; tidak dijual, terbuka gratis begitu 5 sekolah binaan atau lebih punya Sigrak Sekolah | Gratis |

Setelah uji coba, akun yang belum membayar hanya bisa melihat dan mengunduh datanya. Tidak ada paket gratis permanen dan tidak ada watermark (D-157, D-160, XP-13). Fitur tambahan awalnya adalah Simulasi TKA, Buku Induk dan sinkronisasi Dapodik (ditahan sebagai "segera hadir", D-161), Keuangan dan Inventaris kelas, Buku Sekolah, Game kelas, dan Rapor (D-158). Guru boleh mencoba Game kelas selama 7 hari, satu kali, walaupun sekolah belum mengaktifkannya (D-159). Fitur mana yang inti dan mana yang tambahan **tidak dikunci di kode**: Tim SIGRAK mengatur pemetaannya per fitur di konsol internal, dan daftar D-158 hanya konfigurasi awal (D-211).

Tiga aturan bisnis penting bagi pengembang:

- **AI tidak pernah menjadi biaya SIGRAK.** Setiap guru membawa kunci Gemini tingkat gratis miliknya sendiri (D-012, D-023).
- **Langganan sekolah dibayar dari dana selain BOS** (bantuan operasional sekolah). Materi penjualan tidak pernah menyebut BOS sebagai sumber pembayaran (D-025).
- **Pembayaran manual.** Sekolah atau guru membayar di luar aplikasi, misalnya lewat transfer bank. Penjualan mencatat klaimnya, lalu Keuangan, atau pemilik selama belum ada yang memegang peran itu, mengonfirmasinya di konsol. Tidak ada payment gateway di v1 (D-091, D-340).

### 1.3 Prinsip

Konsep produk mencantumkan tujuh prinsip menurut urutan prioritas (`product-concept.md`):

1. Guru menjawab pertanyaan yang mereka pahami secara pedagogis, tidak pernah prompt.
2. Pengembang membangun struktur; AI mengisi konten.
3. Guru tetap menjadi penulis. Setiap hasil AI bisa diubah, dan satu bagian bisa direvisi sendiri.
4. Data yang sudah dimiliki sistem tidak pernah ditanyakan lagi (kelas dan mapel dari jadwal, JP dan tanggal dari kurikulum dan kalender).
5. Aplikasi berjalan di ponsel murah dengan sinyal lemah di bagian yang penting (Di Kelas luring, file game luring).
6. Data sekolah milik sekolah; kunci AI dan pemakaian AI milik guru. Data pribadi murid tidak pernah dikirim ke AI.
7. Hanya bangun apa yang tidak bisa diberikan Gemini sendiri: struktur, data kurikulum, format sekolah, atau penyajian di kelas.

Slogannya "Kerja guru, lebih sigrak." dan janjinya "Bahan ajar siap pakai dan administrasi beres, tanpa menulis prompt." (D-017). Nilainya: Cekatan, Jelas, Bisa diandalkan, Membumi, dan Menghormati guru.

### 1.4 Posisi: *Berpijak pada kebijakan, leluasa berinovasi*

Pada 2026-11-09 pemilik menetapkan posisi SIGRAK sebagai *Berpijak pada kebijakan, leluasa berinovasi* (D-352):

- **Standar nasional menetapkan *apa* dan batasnya:** profil lulusan, capaian pembelajaran (CP), beban belajar (JP), isi minimum rencana pembelajaran dan rapor, serta perlindungan anak.
- **Sekolah dan guru memilih *bagaimana*:** kurikulum sekolah (KSP), TP dan ATP, KKTP, metode, media, instrumen, tema, dan jadwal.
- **SIGRAK memberi mereka alat** untuk cara belajar baru, dan setiap inovasi bisa menunjukkan unsur nasional mana yang dilayaninya.

Riset di baliknya (P4-21) menemukan bahwa aturan 2025–2026 mengunci capaian, struktur, dan pelaporan, tetapi tidak mengunci metode. Standar Proses (Permendikdasmen 1/2026) hanya menetapkan *isi minimum* dokumen perencanaan, dan templat ATP serta rencana pembelajaran di panduan resmi dinyatakan sebagai contoh. Jadi mengikuti kebijakan nasional dengan cermat tidak mempersempit inovasi; justru menunjukkan di mana batas-batasnya. Bab 3 menjelaskan bagaimana hal ini menjadi kode.

### 1.5 Yang bukan SIGRAK

- **Bukan chat AI umum atau generator "buat apa saja".** Tidak ada generator bebas dan tidak ada "Buat bebas" (D-024). Guru sudah bisa mendapatkannya dari Gemini.
- **Bukan aplikasi murid.** Murid adalah catatan data, tidak pernah pengguna. Tidak ada akun murid, tidak ada akun orang tua, dan tidak ada halaman publik atau tautan berbagi (D-010). Satu-satunya tempat yang disentuh murid adalah situs ujian TKA, dengan kartu yang berlaku untuk satu sesi (D-027).
- **Bukan AI yang membaca anak.** Tidak ada data pribadi murid yang dikirim ke penyedia AI, dan di v1 AI tidak pernah membaca tulisan tangan atau suara anak (D-023, D-347).
- **Bukan sistem keuangan atau BOS.** Laporan BOS dan RKAS milik aplikasi pemerintah (ARKAS, e-RKAM) dan di luar cakupan (D-219, D-328).
- **Bukan alat presentasi.** Tidak ada fitur slide ("Bahan Tayang" dihapus, D-033).
- **Bukan ujian resmi.** Simulasi TKA adalah simulasi latihan; nilainya berupa perkiraan (D-089).
- **Bukan salinan aplikasi lama.** Beberapa fitur aplikasi lama sengaja tidak dibawa: generator bebas dan Bahan Tayang, jenjang SMP–SMA, kunci AI wajib saat aktivasi, kata sandi bawaan, PDF dari tangkapan layar browser, logout setelah 15 menit diam, mode ujian, halaman verifikasi publik, serta istilah lama *siswa*, *P5*, *taraf serap*, dan *teguran* (D-322).
- **Belum SMP/MTs.** Kelas 7–12 tetap tersembunyi sampai data kurikulumnya ada (D-018).

Pasar: SD/MI di seluruh Indonesia (D-018). Kudus adalah wilayah uji coba dan menjadi sumber nilai bawaan; data wilayah pertama mencakup Kudus, Jepara, dan Pati di Jawa Tengah (D-170, D-257, D-323).

Bacaan lanjutan: `product-concept.md`, `docs/drafts/policy-innovation-layers.md` §1, D-009, D-017, D-024, D-025, D-157…D-160, D-211, D-322, D-352.

## 2. Pengguna, peran, dan akses

### 2.1 Peran sekolah

Pengguna sekolah adalah orang-orang di sebuah sekolah. Mereka masuk ke aplikasi sekolah di `app.sigrak.com`. Pengguna **tidak punya kolom peran**: peran berasal dari keanggotaan (`school_memberships`), sehingga satu orang bisa menjadi guru dan operator sekaligus, atau kepala sekolah di satu sekolah dan guru di sekolah lain (D-010, model data).

| Peran | Siapa | Tugas utama |
|---|---|---|
| Guru kelas (wali kelas) | Guru wali satu kelas | Mapel kelas, konten, kehadiran harian, jurnal, asesmen, rapor, data kelas |
| Guru mapel | Guru satu mapel khusus di beberapa kelas | Konten yang dipakai ulang di beberapa kelas, jurnal, catatan, dan asesmen mapelnya |
| Kepala sekolah | Pimpinan sekolah | Kelengkapan, validasi, supervisi, program sekolah, pengaturan cetak, langganan |
| Operator sekolah | Staf administrasi sekolah | Tahun ajaran, kelas, mengundang guru, jadwal, kalender, profil sekolah |
| Pengawas / korwil | Pengawas beberapa sekolah | Tampilan baca-saja sekolah binaan, validasi akhir kurikulum, laporan supervisi |

Pembagian mengajar bawaan (D-032, D-228): di SD, PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, dan mapel Pendidikan Agama diberikan ke guru mapel; mapel lain ke guru kelas. Di MI, mapel Kemenag (Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI) dan Bahasa Arab juga bawaannya ke guru mapel. Operator bisa memindahkan mapel mana pun di kelas mana pun. Guru pendamping khusus (GPK) **bukan** peran; GPK adalah guru yang ditugaskan untuk murid tertentu (D-348). Kepala sekolah, operator, atau pengawas yang juga mengajar tetap memakai menu guru dan berpindah ke portalnya dengan tombol ganti peran.

Pengawas adalah kasus khusus. Pengawas **tidak punya keanggotaan sekolah**. Jangkauannya berasal dari `supervisor_assignments`, dibaca menurut `user_id` lintas sekolah dan tidak dibatasi oleh "sekolah saat ini" (D-122, D-168). Pengawas melihat total tingkat sekolah dan tidak pernah melihat baris tingkat murid (D-224, D-350). Akun pengawas dibuat bersama portal pengawas di P5-09 (D-184); dalam alur yang didesain, pengawas mengajukan sekolah sebagai *sekolah binaan*, Tim SIGRAK menyetujuinya di konsol, dan kepala sekolah diberi tahu (D-309).

### 2.2 Murid, peserta ujian, dan orang tua

- **Murid adalah catatan data.** Murid punya nama, NISN, jenis kelamin, tanggal lahir, agama, keanggotaan kelas, kehadiran, catatan, dan nilai, semuanya dikelola guru. Murid tidak pernah masuk ke aplikasi (D-010). UI menyebut mereka *murid*, mengikuti Permendikdasmen 1/2026 (D-210).
- **Peserta ujian bukan peran.** Untuk sesi latihan TKA, setiap murid mendapat kartu ujian untuk sesi itu (nama pengguna dan kata sandi) dan membutuhkan token proktor yang berganti. Kredensialnya berhenti berlaku saat sesi ditutup, dan peserta disimpan di `exam_participants`, bukan di `users` (D-027).
- **Orang tua tidak punya akun.** Guru mengirim pesan siap pakai lewat WhatsApp mereka sendiri dengan tautan `wa.me` biasa (D-215). Portal orang tua hanya ditampilkan sebagai "segera hadir" (D-220).
- **Sesi murid untuk LKPD interaktif** direncanakan nanti, dibatasi per sesi seperti situs ujian (D-331). Ini tidak ada di v1.

### 2.3 Staf internal (Tim SIGRAK)

Staf memakai konsol Filament terpisah di `admin.sigrak.com`, dengan tabelnya sendiri (`staff_users`), guard-nya sendiri (`staff`), dan Cloudflare Access di depannya. Akun sekolah tidak pernah bisa menjadi staf (D-010, D-055, D-088).

- **Dibangun:** tiga peran staf, `admin`, `support`, dan `content` (D-167). `php artisan staff:create <email>` membuat admin pertama di sebuah server.
- **Didesain:** sebelas peran, semuanya aktif sejak awal (D-339): Pemilik (`owner`), Admin (`admin`), Validator (`validator`, Tim Validasi SIGRAK), Penyusun Konten (`content`), Kurator Kurikulum (`curriculum`), Support (`support`), Pemasaran (`marketing`), Penjualan (`sales`), Koordinator Wilayah (`regional_lead`), Keuangan (`finance`), dan Teknis (`engineer`). Satu orang boleh memegang beberapa peran, masing-masing dengan cakupannya sendiri, sehingga `staff_role_assignments` menggantikan `staff_users.role`. Harga dan model AI berpindah dari admin ke pemilik.
- **Wewenang tetap di kode, cakupan berupa data.** Admin hanya mengubah cakupan setiap orang: wilayah (provinsi, kabupaten, kecamatan) atau daftar sekolah tertentu, jenjang (SD/MI), dan bidang (kelompok mapel atau agama). Perubahan cakupan dicatat (D-338). Konsol menampilkan matriks "Hak akses" yang hanya bisa dibaca; matriks wewenang yang bisa diubah ditolak (D-314).
- **Pemisahan tugas.** Validator tidak pernah mengesahkan hasil kerjanya sendiri (D-339). Orang yang mencatat pembayaran bukan orang yang mengonfirmasinya (D-340).
- **Akses Support** ke tampilan sebuah sekolah bersifat baca-saja, dicakup oleh MoU yang ditandatangani setiap sekolah, dengan data agama dan kesehatan disembunyikan; Tim SIGRAK menyimpan satu baris internal per sesi bantuan, bukan per halaman, dan tidak ada log yang ditampilkan ke sekolah (D-341). Ini menggantikan akses yang diberikan sekolah dengan batas waktu, yang semula diusulkan di `docs/drafts/internal-roles.md` §4.
- **Masuk dua faktor** memakai aplikasi autentikator (TOTP, lewat Laravel Fortify). Wajib untuk Pemilik dan Admin, opsional untuk staf lain; tidak ada kode pemulihan, dan admin mengatur ulang 2FA yang hilang, dengan setiap pengaturan ulang dicatat (D-343).
- **Mitra** (komunitas guru seperti KKG dan KKM, serta reseller) mendapat kode referal dan laporan mitra, bukan akun konsol. Syarat dan imbalannya didesain di tugas berikutnya (D-342).

### 2.4 Guard dan host

Satu codebase Laravel melayani tiga hostname, masing-masing dengan guard dan cookie sesi khusus host-nya sendiri (D-048, D-068, D-069, D-164):

| Host | Melayani | Guard (tabel) |
|---|---|---|
| `app.sigrak.com` | Aplikasi sekolah | `web` (`users`) |
| `ujian.sigrak.com` | Situs ujian TKA | `exam` (`exam_participants`) |
| `admin.sigrak.com` | Konsol staf, Horizon, Telescope | `staff` (`staff_users`) |

Hostname diambil dari `APP_HOST`, `EXAM_HOST`, dan `ADMIN_HOST`, sehingga staging bisa memakai `staging-app.`, `staging-ujian.`, dan `staging-admin.sigrak.com` dari build yang sama (D-095, D-164). Di lokal hostname-nya `app.`, `ujian.`, dan `admin.sigrak.localhost` (D-166).

### 2.5 Hanya dengan login

Setiap halaman membutuhkan sesi. Rute aplikasi sekolah yang terbuka tanpa sesi hanyalah masuk (`/masuk`), atur ulang kata sandi (`/lupa-kata-sandi`, `/kata-sandi-baru/{token}`), dan penerimaan undangan (`/undangan/{invitation}`) (D-010). `tests/Feature/Auth/SessionRequiredTest.php` gagal bila ada rute lain yang dibiarkan terbuka. Satu-satunya pengecualian teknis adalah rute kesehatan Laravel `/up`, yang tidak menampilkan data (D-134). Halaman verifikasi pengesahan `/verifikasi/{code}` juga membutuhkan login dan hanya berfungsi untuk anggota sekolah itu dan pengawas yang ditugaskan (D-281). Tidak ada halaman publik, tautan berbagi, atau rute pengalihan publik; kode QR di LKPD langsung menunjuk ke URL pihak ketiga yang ada di daftar izin (D-330).

### 2.6 Siapa yang boleh melihat data sensitif

| Data | Terlihat oleh | Tidak pernah |
|---|---|---|
| Agama murid | Guru kelas itu, operator, kepala sekolah | Pengawas, prompt AI, situs ujian, cache luring (D-036, D-051) |
| Data kesehatan | Guru kelas itu, operator, kepala sekolah | AI, pengawas (D-215) |
| Hasil sosial-emosional, kebutuhan khusus, minat | Guru kelas itu dan GPK yang ditugaskan, operator, kepala sekolah | AI, label permanen; pengawas hanya melihat total (D-350) |
| Catatan BK (konseling) | Penulis, wali kelas, kepala sekolah | Berkas, tampilan pengawas, AI (model data; Bagian B B-09) |
| Hasil supervisi guru | Guru itu dan kepala sekolah | Pengawas, yang hanya melihat rekap sekolah (D-224) |
| Hasil PKKS | Kepala sekolah yang dinilai dan pengawasnya | Orang lain (D-308) |
| Kunci AI guru | Hanya generasi milik pemiliknya yang memakainya; orang lain hanya melihat status | Props browser, log (D-012, D-023) |

Bacaan lanjutan: `product-concept.md`, `data-model.md`, `architecture.md`, `docs/drafts/internal-roles.md`, D-010, D-027, D-036, D-048, D-122, D-167, D-168, D-182…D-184, D-215, D-224, D-309, D-338…D-343, D-348, D-350.

## 3. Fondasi kebijakan

Posisi SIGRAK (D-352) hanya berjalan bila aplikasi tahu aturan nasional mana yang berlaku, seberapa kuat setiap aturan, dan versi mana yang berlaku saat sebuah dokumen dibuat. Bab ini menjelaskan model yang diterima pemilik pada 2026-11-09 (D-352…D-359). Sebagian besar masih **Didesain**; bagian yang sudah ada hari ini disebutkan.

### 3.1 Lapisan L0–L6

Setiap aturan di SIGRAK berada di sebuah lapisan yang menunjukkan siapa penetapnya. Lapisan yang lebih tinggi membatasi lapisan di bawahnya, tidak pernah sebaliknya. Lapisan yang lebih rendah selalu boleh *melampaui* batas bawah (asesmen lebih banyak dari minimum, isi rapor lebih banyak dari yang diwajibkan) kecuali ada batas atas (D-353; `policy-innovation-layers.md` §2).

| Lapisan | Penetap | Contoh | Di SIGRAK |
|---|---|---|---|
| L0 Undang-undang | DPR, Presiden | UU 20/2003 Sisdiknas, PP 57/2021 | Hanya rujukan |
| L1 Standar nasional yang mengikat | Menteri (Kemendikdasmen; Kemenag untuk madrasah) | SKL, Standar Isi, Standar Proses, Standar Penilaian, Kurikulum (12/2024 diubah 13/2025), KMA 1503/2025 | Dikunci, berversi, diubah staf setelah ditinjau Tim Validasi |
| L2 Rujukan nasional | Kepmen, Kepala BSKAP/BKPDM, Dirjen Pendis, panduan resmi | Keputusan CP, Pembelajaran Mendalam (126/P/2025), KKA (127/P/2025), panduan 2025, KMA 512/2026 (KBC), kerangka TKA | Teks CP mengikat; aturan panduan membawa kekuatannya sendiri, banyak yang hanya "contoh" |
| L3 Daerah | Pemda, Dinas, Kanwil, Kemenag kabupaten | Muatan lokal, kalender pendidikan provinsi, format supervisi daerah | `region_subjects`, kalender wilayah, varian daerah |
| L4 Sekolah | Kepala sekolah | KSP, pilihan TP/ATP, pendekatan KKTP, kriteria kenaikan, mapel yang diaktifkan | `school_subjects`, perubahan kurikulum sekolah, preset, penyesuaian |
| L5 Guru dan kelas | Guru | RPM, metode, media, LKPD, instrumen, game, kesepakatan kelas | Setiap templat konten |
| L6 Inovasi SIGRAK | SIGRAK | Game kelas, Pindai LJK, templat berbantuan AI, Peta Asesmen, Konteks Daerah | Harus menyebut unsur L1/L2 yang dilayaninya; tidak boleh bertentangan dengan aturan wajib L1 |

### 3.2 Kekuatan aturan dan reaksi aplikasi

Setiap aturan membawa kekuatan (`force`), dan kekuatan itulah satu-satunya penentu perilaku aplikasi (D-353). Kosakatanya memperluas label `force` yang sudah ada di data peraturan P4-03 (D-203).

| Kekuatan | Arti | Perilaku aplikasi |
|---|---|---|
| `mandatory` | Harus dilakukan persis | **Blokir** (tidak bisa disimpan) |
| `minimum` | Batas bawah; lebih boleh | **Peringatan** di bawah batas |
| `maximum` | Batas atas; kurang boleh | **Peringatan** di atas batas |
| `recommended` | Disarankan panduan | **Petunjuk**, bawaannya aktif |
| `not_recommended` | Tidak disarankan panduan | **Petunjuk**, tetap diizinkan |
| `example` | Hanya contoh | **Nilai bawaan** berlabel "Contoh" yang bisa diganti |
| `school_discretion` | Sekolah yang memutuskan | Bebas, diatur kepala sekolah atau operator |
| `teacher_discretion` | Guru yang memutuskan | Bebas, diatur guru |
| `free` | Tidak ada aturan | Bebas |

Aplikasi hanya memblokir aturan wajib L1/L2 yang jelas. Contoh yang sudah ada di keputusan: sekolah tidak bisa mematikan mapel wajib (`mandatory`, D-230); nilai formatif tidak pernah boleh menjadi nilai rapor (`mandatory`, D-205, D-290); batas muatan lokal SD 72 JP per tahun memberi peringatan tetapi tidak memblokir (`maximum`, D-232); tes tertulis di Fase A diizinkan dengan petunjuk (`not_recommended`, D-206). Dengan begitu SIGRAK menjadi pagar pengaman, bukan gerbang: sekolah yang berinovasi tidak pernah dihentikan, hanya diperlihatkan hubungan pilihannya dengan aturan. Kolom formulir dan nilai bawaan diberi label "Wajib", "Disarankan", "Contoh", atau "Bebas" supaya guru melihat bagian mana yang berasal dari aturan.

### 3.3 Set aturan berversi dan profil kebijakan per tahun ajaran

Aturan berubah setiap tahun, jadi aturan disimpan sebagai data dengan tanggal berlaku, bukan konstanta di kode (D-354).

**Model sasaran (Didesain):**

- `regulations`: satu baris per dokumen hukum, dengan jenis, nomor, penerbit, kementerian, tanggal penetapan, pengundangan, dan berlaku, status (draf, berlaku, diubah, dicabut), file sumber dengan SHA-256, dan tingkat verifikasi (salinan resmi, mirror, sekunder).
- `regulation_relations`: mengubah, mencabut, melaksanakan, merujuk (misalnya Permendikdasmen 13/2025 *mengubah* Permendikbudristek 12/2024; Permendikdasmen 1/2026 *mencabut* Permendikbudristek 16/2022).
- `rule_sets`: paket aturan berversi untuk satu domain (struktur dan JP, CP, nilai bawaan TP/ATP, asesmen dan rapor, kokurikuler, ekstrakurikuler, kalender, beban JTM, supervisi, TKA, taksonomi), dengan `applies_from_school_year`, cakupan jenjang dan wilayah, masa transisi (bertahap per kelas), dan `supersedes_id`.
- `rules`: aturan satuan di dalam set aturan, masing-masing dengan kunci, kekuatan, nilai, dan rujukan sumber.
- `curriculum_versions` menjadi jenis set aturan untuk kurikulum **dengan menambah kolom**, bukan membangunnya ulang.

**Kebijakan yang berlaku** ditentukan dari tiga masukan: tahun ajaran, jenjang, dan wilayah. Untuk setiap domain, set aturan aktif yang rentang tahun ajarannya mencakup tahun itu yang dipakai. Dua aturan lahir dari sini:

- **Dokumen mengingat set aturannya.** RPM, rencana asesmen, atau rapor menyimpan kode set aturan saat dokumen itu dibuat, sama seperti dokumen menyimpan versi templatnya. Perubahan aturan tidak pernah diam-diam mengubah dokumen yang sudah ada.
- **Aturan mengikuti tahun ajaran.** Aturan yang terbit di tengah tahun berlaku mulai tahun ajaran berikutnya, kecuali peraturannya sendiri menetapkan tanggal lebih awal. Contoh: aturan struktur yang terbit selama 2026/2027 dicantumkan di set aturan struktur 2027/2028, dan 2026/2027 tetap memakai yang lama. (Bahasa Inggris SD bukan kasus seperti ini: wajib di kelas 3–6 dan pilihan di kelas 1–2 mulai 2026/2027, D-387.)

**Yang sudah ada hari ini (Dibangun):** `curriculum_versions` dengan `code`, `name`, `status` (draft, active, retired), `meta`, `imported_at`, dan sejak P5-03k `effective_on`, `applies_from_school_year`, serta `supersedes_id`. Versi yang berlaku diambil dari `PolicyProfileResolver` yang membaca berkas Profil Jenjang di `docs/data/profiles/` (D-378); sebelum P5-03k versi itu ditunjuk lewat konfigurasi, `config/curriculum.php` `allocation_versions` (`sd` → `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, `mi` → `ALOKASI-MI-KMA-1503-2025`, D-230).

**Minimum sebelum P5-04 (D-355), selesai di P5-03k dan P5-03l:** `curriculum_versions` mendapat `effective_on`, `applies_from_school_year`, dan `supersedes_id`; resolver profil kebijakan menggantikan penunjuk `allocation_versions` tanpa mengubah perilaku; dan data Profil Jenjang sudah ada. Alasannya: RPM pertama yang dibuat sudah harus mencatat set aturannya. Tabel `regulations` dan `rules` lengkap, konsol untuk setiap domain, dan *Kabar Regulasi* (pemberitahuan ke sekolah saat set aturan baru terbit, beserta dokumen yang terdampak) datang bersama P5-09 atau slice tersendiri.

Alur kerja untuk peraturan baru: pantau kanal resmi (situs JDIH, peraturan.go.id, situs kurikulum dan Kemenag); simpan PDF-nya di `docs/sources/` dengan SHA-256 dan tingkat verifikasi; susun draf set aturan dengan rujukan halaman (Kurator Kurikulum); tinjau oleh Tim Validasi (D-113); terbitkan lewat konsol kurikulum dengan perbandingan, laporan dampak, dan pembatalan (D-317); beri tahu sekolah lewat Kabar Regulasi.

### 3.4 Profil Jenjang: SD dan MI sebagai data di atas satu inti

SD dan MI mengikuti standar nasional yang sama dan berbeda di beberapa domain. SIGRAK memakai satu codebase dengan inti bersama, dan setiap jenjang (`sd`, `mi`) punya catatan **Profil Jenjang** yang berversi (D-356):

| Pengaturan | SD | MI |
|---|---|---|
| Kementerian | Kemendikdasmen | Kemenag (Ditjen Pendis) |
| Struktur dan JP | Permendikdasmen 13/2025 | KMA 1503/2025, tambahan sampai 6 JP per minggu |
| Mapel agama | Enam mapel Pendidikan Agama | Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, ditambah Bahasa Arab |
| Kerangka karakter | 8 DPL, 7 KAIH | 8 DPL, 7 KAIH, KBC / Panca Cinta |
| JTM wali kelas bawaan | 2 | 6 (D-212) |
| Kolom tambahan RPM | — | Tema KBC, Materi Insersi (D-126) |
| Tujuan ekspor rapor | e-Rapor SD | RDM (D-218) |
| Asesmen eksternal | AN, TKA | TKA; AKMI perlu diverifikasi |
| Data induk | Dapodik | EMIS |

Kode membaca profil; kode **tidak pernah** bercabang dengan `level == mi`. Jenjang baru (SDLB, atau kelak SMP/MTs) menjadi profil baru, bukan cabang kode baru. Hari ini jenjang disimpan di `schools.level` ∈ {`sd`, `mi`} dan menentukan daftar mapel serta alokasi (Dibangun); catatan profilnya sendiri masih Didesain dan harus ada sebelum P5-04.

### 3.5 Taksonomi induk

Satu taksonomi induk menamai setiap konsep satu kali, dengan kode tetap, label berbahasa Indonesia, induk, cakupan jenjang, sumber, kekuatan, status, dan versi, ditambah pemetaan antarversi (misalnya Profil Pelajar Pancasila → 8 DPL, P5 → kokurikuler, RPP/Modul Ajar → RPM, peserta didik → murid) (D-357). Taksonomi disimpan sebagai JSON yang ditinjau di `docs/data/taxonomy/` (`status: draft` sampai ditinjau Tim Validasi) dan punya delapan faset:

| Faset | Isi |
|---|---|
| A Kebijakan | Jenis peraturan, standar, kekuatan aturan, lapisan |
| B Struktur kurikulum | Jenjang, fase, kelas, semester, jenis pembelajaran, jenis mapel, mapel, elemen CP, CP, TP, ATP, topik, fokus |
| C Profil dan karakter | 8 DPL, 7 KAIH, Panca Cinta, budaya sekolah aman |
| D Pembelajaran | 3 prinsip, 3 pengalaman, 4 bagian kerangka, praktik, moda, media |
| E Asesmen | Peta Asesmen: domain, tujuan, metode, media, penskoran (D-344) |
| F Konten | Jenis konten, pola LKPD, templat game, jenis soal |
| G Konteks | Provinsi, kabupaten, konteks lokal, bahasa daerah, muatan lokal |
| H Administrasi | Peran sekolah dan staf, dokumen, indikator kinerja, instrumen supervisi |

Daftar ditutup hanya bila aturan menetapkannya (8 DPL, 3 prinsip, fase); daftar terbuka (metode, media, konteks lokal) memungkinkan sekolah menambah butirnya sendiri. Data awalnya adalah tugas P4-22, yang menggabungkan dataset Peta Asesmen dan katalog SIGRAK di `docs/data/catalogs/` (P4-15, D-367) alih-alih menduplikasinya. **Status: Didesain; data awalnya belum ditulis.**

### 3.6 Panel Dasar Kebijakan dan Cek Keselarasan

Dua fitur membuat inovasi bisa dipertanggungjawabkan tanpa memperlambat guru (D-358):

- **Panel Dasar Kebijakan.** Setiap dokumen menampilkan panel singkat: CP dan TP yang dilayaninya, DPL dan prinsip yang disentuhnya, serta peraturan dan versi set aturan yang diikutinya. Datanya berasal dari label taksonomi yang diisi templat.
- **Cek Keselarasan.** Satu pemeriksa membaca kekuatan aturan dan mengembalikan baris blokir, peringatan, dan petunjuk. Pemeriksa berjalan saat menyimpan dan saat ditinjau kepala sekolah dan pengawas. Pemeriksa ini **tidak pernah menulis ulang** pekerjaan guru.

Keduanya dimulai dengan RPM di P5-04 dan tumbuh di setiap slice. **Status: Didesain.**

### 3.7 Kurikulum Berbasis Cinta (KBC) dan KMA 512/2026

Keputusan Menteri Agama No. 512 Tahun 2026 (Pedoman Kurikulum Berbasis Cinta, ditetapkan dan berlaku 31 Maret 2026) adalah sumber KBC. KMA ini menggantikan SK Dirjen Pendis 6077/2025, yang tetap berguna untuk contoh pembelajarannya (D-359). KMA mempertahankan lima tema Panca Cinta (cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air) dan menambahkan enam nilai dasar (spiritual, personal, sosial, ekologis, kebangsaan, intelektual), enam prinsip, 12 metode yang disarankan, 10 instrumen asesmen, dan tiga tingkat praktik (merintis, berkembang, mekar).

Artinya bagi SIGRAK:

- KBC **bukan mapel** dan tidak mengubah JP. Hasilnya dilaporkan lewat Akhlak atau Budi Pekerti dan catatan sikap, tidak pernah sebagai label atau peringkat.
- Untuk MI, wizard RPM menambahkan dua kolom opsional, *Tema KBC* dan *Materi Insersi*. AI mengisi saran awal dari CP dan materi; guru boleh mengubah atau mengosongkan keduanya (D-126, D-142). **Status: Didesain (P5-04).**
- KMA 512/2026 juga mencakup guru agama di sekolah umum, termasuk SD. Guru agama SD tidak melihat kolom KBC (Tema KBC, Materi Insersi, nilai dasar) secara bawaan; admin SIGRAK dapat menyalakannya sebagai pengaturan Profil Jenjang atau konsol (D-368).

### 3.8 Di mana inovasi terbuka lebar

Di dalam batas-batas itu, SIGRAK memberi ruang seluas mungkin: metode dan model (PjBL, inkuiri, berbasis game, di luar kelas), pengorganisasian (tematik, blok), media dan moda (kertas, IFP, kelas tanpa ponsel, embed AR/VR berlisensi), asesmen di atas minimum (rubrik, observasi, portofolio, cek kesiapan), tema kokurikuler apa pun, konteks lokal, sebaran mingguan JP tahunan, dan bantuan AI lewat templat. Satu-satunya batas tetap adalah aturan wajib L1, aturan data anak (D-023, D-347, D-350), tidak ada label permanen pada anak, lisensi (D-337), dan aturan bahwa guru yang memutuskan sementara AI hanya menyarankan.

Bacaan lanjutan: `docs/drafts/policy-innovation-layers.md`, `religious-education.md`, `docs/data/regulations/README.md`, D-203, D-210, D-230, D-232, D-352…D-359.

## 4. Alur dari awal sampai akhir

Bab ini mengikuti satu sekolah selama satu tahun ajaran. Setiap bagian menyebut siapa yang mengerjakan langkahnya, layar dan URL yang sudah diketahui (path yang dilihat orang berbahasa Indonesia, D-149), data yang disentuh, aturan yang berlaku, keputusan, dan status pembangunannya.

```
Tim SIGRAK creates school ─► invites first operator/principal
        │
        ▼
Operator: year ─► classes ─► teachers ─► subjects ─► timetable ─► calendar
        │
        ▼
Curriculum (imported once, versioned) feeds every wizard
        │
        ▼
Teacher: Buat (RPM, Bank Soal, LKPD, games, TKA package)
        │
        ▼
Di Kelas: attendance, journal from RPM, notes, observation (offline)
        │
        ▼
Asesmen: plan, scores, per-TP analysis, remedial ─► leger ─► rapor
        │
        ▼
Portals: principal and pengawas see the same data, validate, supervise
```

Ringkasan diagram: Tim SIGRAK membuat sekolah dan mengundang operator atau kepala sekolah pertama. Operator menyiapkan tahun ajaran, kelas, guru, mapel, jadwal, dan kalender. Kurikulum diimpor sekali, berversi, dan mengisi setiap wizard. Guru membuat konten di menu Buat, mencatat kehadiran, jurnal, catatan, dan observasi di Di Kelas (bisa luring), lalu mengelola asesmen sampai leger dan rapor. Kepala sekolah dan pengawas melihat data yang sama di portalnya, memvalidasi, dan menyupervisi.

### 4.1 Orientasi awal: direktori sekolah, undangan, akun pertama, ganti peran

**Siapa:** Tim SIGRAK (konsol), lalu operator, lalu guru.

**Langkah.**

1. **Direktori sekolah.** Tim SIGRAK menyimpan tabel rujukan SD dan MI dari situs referensi Kemendikdasmen, `school_directory_entries` (NPSN, nama, jenjang, alamat, desa, kecamatan, wilayah). Isinya Kabupaten Kudus (570 sekolah), Jepara (772), dan Pati (825), diambil manual dengan `scripts/school-directory/fetch.cjs` dan dimuat dengan `php artisan school-directory:import <file>`, idempoten menurut NPSN (D-169, D-170). Baris direktori bukan tenant.
2. **Membuat tenant.** Staf membuat sekolah di konsol (Sekolah, `/sekolah` di host admin). Mengetik NPSN yang dikenal mengisi kolom kosong dari direktori. Satu sekolah adalah satu tenant; `schools.level` bernilai `sd` atau `mi`, dan setiap sekolah termasuk ke satu wilayah (`regions`: provinsi, kabupaten).
3. **Operator atau kepala sekolah pertama.** Staf mengundang operator atau kepala sekolah pertama dari konsol (Undangan, `/undangan`). Di aplikasi sekolah, hanya operator yang mengundang, dan hanya guru (D-182).
4. **Surel undangan dan penerimaan.** Tabel `invitations` hanya menyimpan hash SHA-256 dari token yang dikirim. Tautan berlaku 7 hari. Mengundang ulang sekolah, surel, dan peran yang sama memperbarui undangan terbuka dengan token baru. Penerima membuka `/undangan/{invitation}?token=…`, membuat kata sandi, dan mendapat keanggotaan. Tautan yang tidak dikenal, sudah dipakai, atau kedaluwarsa menampilkan satu halaman netral yang tidak membocorkan apa pun. Menerima undangan menandai surel sebagai terverifikasi (D-182, D-183).
5. **Masuk dan akun.** `/masuk` (masuk), `POST /keluar` (keluar), `/lupa-kata-sandi` dan `/kata-sandi-baru/{token}` (atur ulang), `/akun` (profil, dan kata sandi di `/akun/kata-sandi`). Kata sandi sekolah minimal 8 karakter dengan huruf dan angka; setelah 5 kali gagal masuk per surel dan IP ada jeda 1 menit; batas per IP dibuat longgar karena satu sekolah bisa berbagi satu IP. Permintaan atur ulang mendapat jawaban yang sama, ada atau tidak surelnya. Surel di Akun hanya bisa dibaca; mengubahnya lewat Tim SIGRAK untuk saat ini (D-183). Surel dikirim lewat SMTP Gmail, hanya surel transaksional (D-110).
6. **Guru.** Operator membuka Guru (`/guru`), mengundang guru, dan bisa mengirim ulang (`kirim-ulang`) atau membatalkan undangan.
7. **Ganti peran.** Pengguna dengan beberapa keanggotaan berganti peran lewat `PUT /ganti-peran`. Keanggotaan yang dipilih disimpan di sesi; `SetSchoolContext` membacanya (atau keanggotaan aktif tertua milik pengguna) dan berjalan sebelum route model binding, sehingga `{model}` milik sekolah lain menghasilkan 404. Keanggotaan punya `public_id`, sehingga id bigint tidak pernah sampai ke props (D-182, DB-11).
8. **Penugasan pengawas.** Staf menugaskan akun pengguna yang sudah ada ke sekolah di Penugasan pengawas (`/penugasan-pengawas`). Pembuatan akun pengawas serta alur pengajuan dan persetujuan datang bersama P5-09 (D-184, D-309).

**Data:** `regions`, `schools`, `school_directory_entries`, `users` (dengan `nip`), `school_memberships`, `supervisor_assignments`, `staff_users`, `invitations`.

**Aturan:** satu kepala sekolah aktif per sekolah (indeks unik pada kolom generated); setiap tabel milik sekolah dibatasi oleh `BelongsToSchool`, dengan prinsip gagal-tertutup; `FindInvitation` termasuk sedikit tempat yang membaca tanpa batasan sekolah, karena tokennya sendiri yang memberi izin.

**Juga didesain:** konsol mendapat kolom kecamatan di `schools` untuk Kabar bertarget dan filter (D-319); orientasi 3 langkah yang tampil sekali setelah mendaftar (kelas, jadwal, murid) ada di konsep produk; *Pindah Sekolah* (perpindahan guru, disetujui sekolah tujuan) menyimpan kehadiran, jurnal, nilai, catatan, rapor, dan salinan baca-saja dokumen di sekolah lama, sementara guru membawa salinan dokumennya sendiri, kunci AI, dan paketnya (D-307).

**Status:** **Dibangun** (P5-03a, P5-03b, P5-03f): inti tenancy, masuk, atur ulang, undangan, Akun, ganti peran, halaman konsol untuk wilayah, sekolah, staf, keanggotaan, undangan, penugasan pengawas, direktori sekolah, Sebaran sekolah (`/sebaran-sekolah`) dengan unduhan Excel, dan Peta sekolah (`/peta-sekolah`, peta Leaflet yang dibundel). **Didesain:** akun dan pengajuan pengawas (P5-09), perpindahan guru, orientasi 3 langkah, target kecamatan, 2FA untuk staf.

### 4.2 Persiapan tahun ajaran: tahun, kelas, penugasan, mapel, jadwal, kalender

**Siapa:** operator. Kepala sekolah dan guru mendapat tampilan baca nanti.

**Tahun ajaran (Tahun Ajaran, `/tahun-ajaran`).** Nama tahun mengikuti tanggal mulainya (`2026/2027`) dan tidak pernah diketik. Lamanya paling banyak 12 bulan, tidak tumpang tindih dengan tahun lain di sekolah itu, dan punya tanggal mulai semester genap (`even_semester_starts_on`). Formulir menyarankan 1 Juli sampai 30 Juni, dengan semester 2 mulai 1 Januari. Tahun "saat ini" adalah tahun yang mencakup hari ini, kalau tidak ada tahun berikutnya, kalau tidak ada tahun terakhir. Tahun yang sudah punya kelas tidak bisa dihapus (D-241).

**Kelas (Kelas, `/kelas`).** Kelas punya tingkat (1–6), nama bebas sampai 20 karakter yang unik per tahun, dan wali kelas yang boleh kosong. Modelnya `SchoolClass` karena `Class` adalah kata khusus di PHP. URL memakai `public_id`: `/kelas/{class}`, `/kelas/{class}/ubah` (D-241).

**Salin kelas.** Bila tahun baru masih kosong, operator bisa menyalin kelas tahun sebelumnya naik satu tingkat (`POST /tahun-ajaran/{academicYear}/salin-kelas`). Guru dikosongkan; murid pindah nanti lewat Kenaikan. Kelas 6 tidak disalin; satu kotak centang (bawaannya aktif) membuat ulang kelas 1 tahun lalu; nama yang diawali angka tingkatnya mendapat angka baru (`4A` → `5A`), nama lain tetap; nama yang bentrok ditambah tingkatnya; menyalin ke tahun yang sudah punya kelas ditolak (D-242…D-246).

**Mapel sebuah kelas.** Kelas mendapat mapel wajib yang punya alokasi intrakurikuler untuk tingkatnya di versi struktur jenjangnya, ditambah muatan lokal wilayah kecuali sekolah mematikannya, ditambah apa pun yang diaktifkan sekolah. Konten pilihan (Bahasa Inggris dan IPAS kelas 1–2, Tata Boga, Seni Suara Daerah) bawaannya mati dan tidak masuk rapor kecuali `in_report` diaktifkan. Mapel wajib tidak bisa dimatikan. Logika ini ada di `app/Curriculum/SubjectsForGrade` (D-204, D-230, D-231). Halaman operator untuk menyalakan dan mematikan mapel sekolah, serta peringatan batas muatan lokal (D-232), belum dibangun.

**Penugasan mengajar (`/kelas/{class}/guru-mapel`).** `teaching_assignments.teacher_id` bernilai `NOT NULL`: mapel kelas tanpa baris berarti belum punya guru. Saat kelas mendapat wali kelas, setiap mapel guru kelas yang belum punya baris diberikan kepadanya; mapel guru mapel menunggu operator. Wali kelas baru mengambil alih baris yang dipegang wali kelas sebelumnya; mengubah tingkat kelas menghapus baris yang tidak dipakai tingkat baru; baris untuk mapel yang kemudian dimatikan sekolah ditampilkan sebagai "Tidak diajarkan lagi" (D-240).

**Jadwal.** Operator mengisi `/kelas/{class}/jadwal`; guru melihat Kelas Saya > Jadwal di `/kelas-saya/jadwal`. Aturannya (D-247…D-251):

- Baris berupa pelajaran atau baris non-pelajaran: Istirahat, Upacara, Kegiatan (dengan label opsional seperti "Senam"). Baris non-pelajaran tidak punya mapel dan bernilai 0 JP.
- Operator mengetik jam mulai dan selesai; `period_no` dihitung dari jam itu.
- Guru sebuah pelajaran dibaca lewat penugasan mengajar kelas itu; **tidak ada kolom `teacher_id`** di `timetable_slots`. Mapel tanpa guru tidak bisa dijadwalkan.
- Bentrok guru dengan kelas lain ditolak, dengan menyebut kelas dan jamnya. Baris dalam satu kelas tidak boleh tumpang tindih, kecuali enam mapel agama yang boleh berjalan bersamaan.
- JP mingguan sebuah mapel harus sama dengan alokasinya. Melebihi alokasi ditolak; kekurangan ditampilkan, dan jadwal dianggap "lengkap" hanya bila semuanya cocok. 1 JP = 35 menit.

**Kalender (Kaldik).** Dibangun di P5-03i (D-252…D-261, D-323, D-377), di `/kalender` untuk setiap peran, dengan halaman konsol Libur nasional dan Kalender wilayah:

- `national_holidays` adalah satu tabel pusat baca-saja (libur nasional dan cuti bersama, dengan dasar keputusan dan penanda sementara), digabungkan ke setiap tampilan kalender dan diperbarui Tim SIGRAK setiap tahun setelah SKB terbit; `php artisan calendar:import-holidays <file>` memuatnya, setelah pengecekan daring terhadap SKB resmi dan pengesahan Tim Validasi (D-253, D-254). Cuti bersama bawaannya dihitung libur (D-255).
- Tim SIGRAK menyusun kalender rujukan wilayah. Untuk Kudus, Pati, dan Jepara, ini satu kalender Jawa Tengah dari tanggal versi 6 hari di pedoman provinsi (D-257, D-323). Operator menyalinnya dengan "Salin dari kalender wilayah", menambah kegiatan sekolah, dan nanti melihat "Terapkan perubahan wilayah" saat kalender wilayah berubah (D-256).
- `calendar_events.class_id` membuat sebuah kegiatan hanya berlaku untuk satu kelas (D-258).
- `app/Calendar/EffectiveDays` menghitung hari dan minggu efektif per kelas. Uji penerimaan: SD/MI 6 hari di Jawa Tengah tahun 2026/2027 punya 254 hari efektif (134 + 120) (D-259, D-323).
- `app/Calendar/ConflictChecker` memberi satu set pesan bentrok (D-260); pratinjau dampak ("12 pertemuan RPM di 6 kelas akan bergeser") muncul sebelum sebuah perubahan menggeser pertemuan RPM (D-261).

**Penyusun draf jadwal (P5-03j, D-262).** Dibangun di `/jadwal/susun-draf`: "Susun draf jadwal" untuk satu kelas atau semua kelas, dari kerangka hari, alokasi, penugasan, dan slot yang dikunci. Batas keras tidak bisa diubah; preferensi lunak berupa sakelar nyala/mati per sekolah (tanpa bobot angka). Hasilnya selalu draf dengan layar perbandingan. Pekerjaan ini berjalan di antrean `default`.

**Data:** `academic_years`, `classes`, `teaching_assignments`, `timetable_slots` (dengan `is_locked`), `timetable_settings`, `timetable_drafts`, `school_subjects`, `region_subjects`, `calendars`, `calendar_events`, `national_holidays`. Berkas data kalender: `docs/data/calendar/` (draf sampai disetujui Tim Validasi).

**Status:** **Dibangun** (P5-03d, P5-03e, P5-03h): tahun ajaran, kelas, salin kelas, penugasan mengajar, jadwal dengan semua pemeriksaannya. **Didesain:** halaman sakelar mapel sekolah dan peringatan muatan lokal, kalender (P5-03i), penyusun draf jadwal (P5-03j). P5-03 dibuka lagi untuk P5-03i dan P5-03j (D-252); keduanya datang sebelum atau bersamaan dengan P5-04.

### 4.3 Kurikulum: data, impor, versi, penyesuaian, validasi

**Siapa:** Tim SIGRAK (data, impor, konsol), Tim Validasi SIGRAK (tinjauan), sekolah (perubahan), kepala sekolah dan pengawas (validasi).

**File data.** Semua data kurikulum disimpan di repo sebagai JSON di `docs/data/curriculum/` dan `docs/data/regulations/`, masing-masing dengan asal-usul dan `status: draft` sampai ditinjau Tim Validasi (D-113, D-203):

| File | Isi |
|---|---|
| `subjects.json` | Katalog mapel: kode, jenis, guru bawaan, agama, jenjang, alias, bawaan daerah (D-227) |
| `curriculum-structure-permendikdasmen-13-2025.json` | Struktur dan JP SD, termasuk kokurikuler |
| `mi-allocation-kma-1503-2025.json` | Struktur dan JP MI (terverifikasi, D-038) |
| `cp-pendidikan-agama-sd-bkpdm-020-2026.json` | CP enam agama SD |
| `cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` | CP mapel Kemenag MI dan Bahasa Arab |
| `sigrak-sd-2026-r1-umum.json` | Topik SIGRAK SD untuk mapel umum, PAI, muatan lokal, dan KKA: 432 topik, 1.372 TP, 2.021 fokus, 12 mapel |
| `sigrak-sd-2026-r1-agama.json` | Topik SIGRAK SD untuk lima agama lainnya: 300 topik |
| `sigrak-mi-2026-r1.json` | Topik SIGRAK MI untuk lima mapel Kemenag: 224 topik |
| `crosswalk-legacy-2026.json` | Khusus tim, tidak pernah diimpor: kode lama ke kode SIGRAK (D-365) |

**Bentuknya.** Sebuah mapel punya topik (materi pokok, baris ATP) per kelas dan semester, dengan JP, minggu, dan rujukan. Setiap topik punya butir fokus dan tujuan pembelajaran (TP). `focus_item_objectives` menghubungkan fokus ke TP dan diisi lewat tugas data. Versi yang hanya berisi CP menyimpan baris CP di `learning_outcomes` (D-226). `subject_allocations` menyimpan JP per mapel, kelas, dan jenis pembelajaran (intrakurikuler atau kokurikuler).

**Impor (`php artisan curriculum:import`).** Tanpa nama file, perintah ini memuat file yang tercantum di `config/curriculum.php` secara berurutan. Aturannya (D-229): setiap file satu transaksi; baris dicocokkan menurut `code` dan tidak pernah dihapus; versi baru berstatus `draft` dan impor tidak pernah mengubah status versi yang sudah ada; ketidakcocokan (jumlah JP terhadap target semester tiap mapel, fase dan kelas, tautan fokus–TP di luar topiknya) dilaporkan dan tidak menghentikan impor kecuali `--strict` dipakai. Topik hanya datang dari berkas SIGRAK (D-363); format aplikasi lama ditolak, dan kode lama tetap di crosswalk (D-365). Berkas yang berbagi satu versi masing-masing bertanggung jawab atas mapelnya sendiri. Pemangkasan Tata Boga D-234 sudah ada di data; `app/Curriculum/Import/SourceOverrides.php` kosong tetapi dipertahankan untuk koreksi yang disetujui pemilik di kemudian hari (D-366). Baris KKA kelas 5–6 tidak diimpor sebagai intrakurikuler; KKA berjalan lewat kokurikuler (D-208).

**Versi di v1.** `SIGRAK-SD-2026-R1`, `SIGRAK-MI-2026-R1` (topik, satu versi per jenjang dan tahun, D-363), `CP-PABP-BKPDM-020-2026`, `CP-PAI-BA-MADRASAH-9941-2025`, `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, `ALOKASI-MI-KMA-1503-2025` (model data). Alokasi SD hanya berasal dari struktur resmi (D-366).

**Kurasi per mata pelajaran (D-364).** Setiap mapel di berkas SIGRAK punya `curation.status` ∈ {`converted`, `in_review`, `curated`} beserta peninjau dan tanggal; hanya mapel `curated` yang aktif bagi guru (ditegakkan mulai P5-04). Mapel umum SD mulai sebagai `converted`, draf agama sebagai `in_review`. Tugas kurasi P4-29…P4-31 mengikuti daftar periksa: CP sesuai keputusan resmi, TP mencakup CP, urutan ATP dan JP sesuai struktur, fokus tertaut ke TP, masalah yang diketahui ditutup, rujukan dicatat. Tambahan versi dari D-355 datang sebelum P5-04 (bagian 3.3).

**Rantai CP → TP → ATP → fokus di aplikasi.** Di setiap wizard rantai ini hanya dipilih: Kelas → Mata pelajaran → Semester → Materi pokok (ATP) → Fokus → TP, semuanya dibaca dari data (mesin konten, aturan wizard 4). Guru tidak pernah mengetik CP atau JP.

**Penyesuaian sekolah.** Sekolah bisa memakai nilai bawaan, mengubah sebuah topik, atau menambah topik sendiri (`school_topic_changes`, dengan `base_topic_id` NULL untuk topik baru). Hanya perubahan inilah yang melewati validasi: guru → kepala sekolah → pengawas, dicatat tanpa diubah di `curriculum_validations`, dan setiap pengembalian disertai catatan (D-019). Kurikulum bawaan yang dipakai apa adanya tidak pernah masuk antrean. "Pulihkan bawaan" per topik dan "Reset semua penyesuaian" memungkinkan kepala sekolah atau operator memulihkan nilai bawaan dengan konfirmasi dan log audit; perubahan lama tetap menjadi riwayat dan RPM yang sudah ada tidak berubah (D-276).

**Tim Validasi SIGRAK.** Data kurasi (kurikulum, data agama, katalog, daftar awal, paket konten) ditinjau dan disahkan oleh Tim Validasi SIGRAK, dicatat atas nama tim beserta tanggal tinjauan, bukan nama guru perorangan (D-112, D-113). Dengan D-339, Validator menjadi peran konsol yang tercatat.

**Konsol kurikulum (D-317).** Staf membandingkan versi draf dengan versi aktif, menerbitkan, membatalkan, dan melihat laporan dampak (sekolah dan RPM yang terdampak). Audit JP menandai RPM yang JP-nya tidak lagi cocok dengan ATP terbaru; guru mendapat Kabar dan memutuskan. Tidak ada draf AI dari Buku Guru (lisensi SIBI). Dengan D-354 konsol ini diperluas ke setiap domain set aturan.

**Agama.** SD menawarkan enam mapel agama (D-034), masing-masing 3 JP per minggu (D-141). MI mengajarkan mapel Kemenag alih-alih satu mapel PAI (D-035, D-146). `students.religion` hanya ada untuk mengelompokkan kelas agama dan bersifat sensitif (D-036). Pendidikan Kepercayaan tidak ada di v1 (D-037); CP pendidikan khusus termasuk cakupan untuk sekolah inklusif dan masih harus ditranskripsi (D-155).

**Status:** **Dibangun** (P5-03c, P5-03g, P4-28): tabel kurikulum, `learning_outcomes`, perintah impor, katalog mapel, `SubjectsForGrade`, dataset SIGRAK R1 dengan status kurasi per mapel. **Didesain:** kolom versi dan resolver (D-355), perubahan topik sekolah dan validasi (P5-09), konsol kurikulum (D-317), Kabar Regulasi. **Data masih draf:** setiap file kurikulum dan peraturan menunggu tinjauan Tim Validasi.

Bacaan lanjutan: `data-model.md`, `religious-education.md`, `docs/data/curriculum/README.md`, `docs/drafts/curriculum-source-check-2026.md`, D-019, D-020, D-113, D-204, D-208, D-226…D-234, D-276, D-317, D-363…D-366.

### 4.4 Pembuatan konten dengan AI: kunci, templat, wizard, dokumen, ekspor

**Siapa:** guru (dan kepala sekolah yang membuat konten). Tim SIGRAK menyetel templat di konsol.

**Langkah 0: Kunci AI Saya.** Saat pertama kali memakai fitur AI, SIGRAK meminta kunci Gemini (D-012, D-023). Guru menempelkan kunci dan mengetik label project; server menjalankan uji pembuatan kecil dan hanya menyimpan kunci bila lolos. Kunci ganda ditolak lewat sidik jarinya. Kunci dari project yang sudah punya kunci mendapat peringatan bahwa kuotanya dipakai bersama. Kunci kedua dan seterusnya membutuhkan centang persetujuan, karena memutar beberapa project gratis bisa melanggar ketentuan Google dan risikonya ditanggung guru. Kunci bisa ditandai *cadangan* dan hanya dipakai bila semua kunci biasa tidak bisa dipakai (D-119). Halaman ini menampilkan status setiap kunci, masa jeda, dan sisa permintaan hari ini (D-128). Kepala sekolah tidak butuh kunci kecuali membuat konten. Rinciannya ada di bab 7.

**Satu templat per jenis konten.** Tidak ada generator umum (D-024). Setiap jenis konten punya kelas templat, wizard, job antrean, dan renderer sendiri, dan hanya berbagi pipeline, gerbang AI, kerangka editor, dan ekspor. Sebuah templat punya sembilan bagian: metadata, konfigurasi wizard, skema masukan, instruksi prompt, skema keluaran, desain, aturan validasi, renderer, dan versi. Definisinya ada di kode; database menyimpan registri (`templates`) dan `template_versions` yang tidak bisa diubah, dan staf bisa menimpa susunan kata prompt di konsol, yang membuat versi baru tanpa deploy (D-013).

**Aturan wizard.** Tanyakan hanya yang diketahui guru secara pedagogis (kelas, mapel, topik, tujuan, kondisi murid, durasi, tingkat kesulitan, jumlah soal, model pembelajaran, jenis asesmen). Jangan pernah menanyakan yang sudah diketahui sistem (JP, pertemuan, CP, fase, kode TP, identitas sekolah, tanggal). Paling banyak 4 langkah, masing-masing dengan nilai bawaan, dan "Biarkan otomatis" bila AI bisa memilih. Satu catatan opsional sampai 300 karakter masuk ke prompt sebagai *data* yang dibatasi, dengan petunjuk "Jangan tulis nama atau data pribadi siswa".

**Pipeline.**

```
Wizard answers
  -> SpecificationBuilder (validate, enrich from DB)
  -> Specification (stored, immutable)
  -> for each step: PromptBuilder -> AIGateway -> OutputValidator
       invalid? -> OutputRepairer (fixes, then at most 1 AI repair)
  -> Document saved: content JSON + template key + version
```

Ringkasan diagram: jawaban wizard divalidasi dan dilengkapi dari database menjadi spesifikasi yang disimpan dan tidak berubah. Untuk setiap langkah, prompt disusun, dikirim lewat gerbang AI, lalu keluarannya divalidasi; keluaran yang tidak valid diperbaiki dulu secara deterministik, lalu paling banyak satu kali perbaikan oleh AI. Hasil akhirnya disimpan sebagai dokumen beserta kunci dan versi templat.

Kolom data (identitas, CP, kode TP, pertemuan, menit, rujukan) digabungkan dari database setelah pembuatan; keluaran AI untuk kolom itu diabaikan. Templat besar berjalan bertahap (untuk RPM: desain, pertemuan per kelompok, asesmen), semuanya memakai kunci yang sama. Setiap jenis konten punya job sendiri: `GenerateRpm`, `GenerateBankSoal`, `GenerateLkpd`, `GenerateQuizBattle`, `GenerateTkaPackage`, ditambah `ReviseSection`. Halaman menampilkan "Langkah 2 dari 4"; guru boleh meninggalkan halaman dan mendapat pemberitahuan di Kabar saat selesai (D-152), dengan toast, serta bunyi dan notifikasi layar sesuai pengaturan guru (D-285).

**Mengedit.** Pratinjau berupa editor terstruktur per bagian, tidak pernah JSON mentah, dan teks AI selalu di-escape. "Perbaiki bagian ini" merevisi satu bagian (*sederhanakan*, *buat diferensiasi*, *sesuaikan untuk siswa berkebutuhan khusus*, *perpanjang*, *perpendek*) dan menampilkan versi lama dan baru berdampingan. Setiap simpan atau revisi menyimpan konten sebelumnya; 5 revisi terakhir disimpan (D-153), dan revisi yang terkait pengesahan tidak pernah dipangkas (D-282).

**RPM.** Langkah wizard-nya *Kapan* (tanggal mulai, atau "RPM lepas jadwal"), *Apa* (materi pokok dan fokus; JP dan jumlah pertemuan dihitung dan dikunci), *Cara* (model pembelajaran per pertemuan, atau otomatis), *Periksa*. Bagian-bagiannya mengikuti kerangka resmi pembelajaran mendalam: Identifikasi, Desain Pembelajaran, Pengalaman Belajar (per pertemuan, dengan Memahami, Mengaplikasi, Merefleksi dan prinsip berkesadaran, bermakna, menggembirakan), Asesmen, ditambah lampiran dan kesinambungan. Delapan dimensi profil lulusan (DPL1…DPL8) adalah himpunan tetap. MI menambah kolom KBC yang opsional (bagian 3.7). **Varian kokurikuler** punya skemanya sendiri mengikuti Panduan Kokurikuler 2025, dengan bentuk Lintas Disiplin, Gerakan 7 KAIH (kebiasaan ketujuh "Tidur Cepat", D-207), dan Cara Lainnya; KKA berjalan di sini (D-208). Target JP kokurikuler tahunan diambil dari `subject_allocations` (D-277).

**Penjadwalan RPM (P5-04, didesain rinci oleh D-267…D-275).** Pertemuan ditempatkan di jadwal kelas mulai tanggal awal. Hasil jurnal menggerakkan jadwal: `not_done` menggeser pertemuan berikutnya; `partial` tidak menggeser, tetapi pengalaman belajar yang belum selesai dibawa ke blok "Lanjutan" di pertemuan berikutnya (D-267). Setiap pergeseran diterapkan otomatis, ditampilkan ("2 pertemuan bergeser…"), dan bisa dibatalkan dengan Batalkan, dan cetakan Promes mendapat tampilan Realisasi (D-274). RPM semester 2 boleh disusun lebih awal dan ditempatkan mulai awal semester genap (D-272). RPM lepas jadwal nanti bisa ditempatkan dengan "Pasang ke jadwal" (D-273). "Pakai untuk kelas lain" menyalin RPM ke kelas paralel tanpa panggilan AI (D-275). Satu neraca JP per kelas dan mapel sebelum SAS (*Neraca JP*: Pas, Kurang n JP, Ada n JP cadangan) menggantikan beberapa peringatan (D-269). Kesiapan RPM per mapel (Siap, Segera habis, Perlu RPM) memakai nilai bawaan 10 dan 3 hari efektif yang diatur di konsol (D-270), dengan Kabar ringkasan setiap Senin hanya untuk guru (D-271).

**Bank Soal.** Wizard-nya mencakup cakupan (Penilaian Harian, STS/SAS, Ujian Sekolah), komposisi per jenis (PG, PG Kompleks, PGK Kategori, Benar-Salah, Menjodohkan, Isian, Uraian), campuran tingkat kesulitan dan level kognitif, serta waktu pelaksanaan. Di Fase A sebuah catatan menyarankan observasi dan unjuk kerja, tetapi soal tertulis tidak pernah diblokir (D-206). AI hanya mengisi data per butir; kisi-kisi, naskah, kunci, pembahasan, indikator, dan kartu soal dirender dari data itu tanpa panggilan AI tambahan (D-293). `question_items` mengindeks setiap butir di semua dokumen; "Ambil dari soal saya dulu" memakai ulang butir yang cocok dan hanya meminta AI mengisi kekurangannya, dan butir yang baru dipakai ditandai "pernah dipakai" (D-294). Pengecoh boleh diberi label miskonsepsi (D-351).

**LKPD.** v1 berupa LKPD saat ini (judul, tujuan, petunjuk, langkah, tabel, pertanyaan, refleksi) ditambah satu set blok tetap untuk kelas 1–3 yang digambar renderer: `trace`, `match_lines`, `cut_and_stick`, `colour_by_rule`, `count_pictures`, `fill_table`, `short_answer` (D-296). Skema keluarannya hanya mendapat kolom `pattern`, sehingga pustaka pola (*Bentuk LKPD*) datang bertahap tanpa migrasi (D-329). Gelombang 1 berisi sembilan pola: Detektif Teks, Peta Cerita, Data Kelasku, Etnomatematika, Misi Petualangan, Tiga Jalur, Tiket Keluar, Kotak Terkunci, Pikir Komputasional Tanpa Komputer (D-332). Gelombang berikutnya menambah blok numerasi, literasi, game, dan hibrida, profil *Konteks Daerah* sekolah dengan daftar awal yang ditinjau untuk Kudus, Jepara, dan Pati, sehingga AI tidak pernah mengarang fakta lokal (D-333), preset sekolah yang hanya dibagi di dalam satu sekolah (D-335), QR yang langsung menuju URL pihak ketiga di daftar izin (D-330), embed hanya dengan lisensi yang jelas (D-337), serta pemutar H5P dan penampil 3D yang dihosting sendiri (D-334). Semua fitur LKPD termasuk inti (D-336). Langkah interaktif berjalan daring di aplikasi guru atau sesi IFP (D-331).

**Quiz Battle dan Game kelas.** Quiz Battle memakai set Bank Soal yang tersimpan (jenis yang bisa dinilai otomatis) atau soal baru, dengan main beregu atau perorangan, pengatur waktu, poin, dan ronde. Runtime vanilla JS memegang tata letak, kontrol, penilaian, dan animasi; AI hanya memasok konten dan nilai konfigurasi yang diizinkan; tidak ada kode tulisan AI (D-011, D-015). Ada dua cara sampai ke murid:

- *Tayangkan*: rute layar penuh di sesi guru.
- *Unduh game*: satu file HTML luring dengan runtime yang disisipkan, konten sebagai JSON yang di-escape, dan CSP yang memblokir semua akses jaringan. Isinya hanya konten ajar.

Game kelas (fitur tambahan, D-171) mengembangkannya menjadi mode belajar IFP: daftar putar dari satu pertemuan RPM (tujuan, cek pengetahuan awal, alat peraga, latihan tim, tiket keluar, refleksi). Game kelas menyimpan ringkasan tingkat kelas per butir, tim, dan TP tanpa data murid (D-172), yang mengisi "Perlu diulang", ide tindak lanjut AI dari TP dan pola jawaban salah, jurnal, dan Asesmen. Di panel bersama, guru memasangkan ponselnya dengan *Kode Tayang* 6 digit di `/pid` pada host aplikasi alih-alih mengetik kata sandi (D-173, D-233). Templat baru mengikuti fungsi belajar: Lomba Tim, Pilah & Kelompokkan, Pasangkan lebih dulu (D-176). Alat peraga dan alat bantu kelas termasuk Sigrak Guru (D-175). Bundel luring mingguan ikut dengan inti Game kelas (D-181). Kartu Jawab (kartu A–D cetak yang dipindai dengan ponsel) datang paling akhir (D-174). Perangkat rujukannya IFP Android 75 inci dari Kemendikdasmen; versi WebView-nya harus dicek di unit sungguhan (D-179).

**Paket Soal TKA.** Paket latihan Bahasa Indonesia (hanya membaca) atau Matematika, bawaannya 30 butir, dalam tiga bentuk TKA: PG, PGK MCMA, PGK Kategori, dengan kompetensi AN literasi dan numerasi ikut ditawarkan (D-111). Visual stimulus tidak pernah berupa gambar AI: tabel dan diagram berupa data terstruktur, bangun datar dari set gambar yang dibundel, dan susunan kubus berupa peta ketinggian yang digambar dengan three.js dengan kunci yang dihitung kode (D-030). PGK Kategori memakai pasangan label Benar/Salah, Sesuai/Tidak Sesuai, Setuju/Tidak Setuju (D-045). Paket ini menjadi bahan Simulasi TKA (bagian 4.8).

**Templat asesmen baru (Didesain, D-346).** Rubrik Kinerja, Observasi DPL, Cek Kesiapan Awal, Akomodasi Murid, serta kartu penilaian diri dan teman sejawat, masing-masing templat kecil tersendiri (lihat bagian 4.6).

**Dokumen Saya.** Setiap hasil menjadi dokumen di Dokumen Saya (menu akun) dengan versi templat, revisi, status validasi, komentar kepala sekolah dan pengawas, serta ekspornya. Konten hasil pembuatan langsung bisa dipakai; kepala sekolah dan pengawas bisa melihat dan berkomentar (D-019). RPM, Prota, Promes, rencana asesmen, dan Berkas juga bisa diajukan untuk *pengesahan* (bagian 4.9).

**Ekspor.** PDF lewat Gotenberg, DOCX lewat PhpWord, Excel lewat Laravel Excel, HTML game lewat `GameExporter`, semuanya dari JSON konten dan tidak pernah dari tangkapan layar. *Buka di Canva* mengunduh file lalu membuka canva.com (tingkat 1). Rinciannya di bab 8.

**Status:** **Didesain** untuk seluruh bagian ini. Belum ada yang dibangun dari mesin konten, lapisan AI, Dokumen Saya, atau ekspor; semuanya ada di P5-04 (inti konten: mesin penyedia AI, mesin templat, RPM, Dokumen Saya, ekspor PDF/DOCX) dan P5-05 (Bank Soal, LKPD, Quiz Battle). File `lang/id` untuk area ini baru berupa kerangka. Game kelas di luar Quiz Battle, gelombang LKPD, dan templat asesmen adalah gelombang **Nanti** setelah v1.

### 4.5 Di kelas: Di Kelas, sinkronisasi luring, kehadiran, jurnal, observasi, alat kelas

**Siapa:** guru di ponsel, biasanya saat atau tepat setelah pelajaran.

**Hari Ini** adalah beranda guru: jadwal hari ini, apa yang belum diisi, kemajuan minggu ini, dan Kabar. Halaman ini menampilkan peringatan kesiapan (hanya kuning dan merah) dan, untuk slot besok yang belum punya pertemuan RPM, tombol "Siapkan besok" yang membuka wizard RPM yang sudah terisi (D-266).

**Di Kelas** menyimpan catatan harian:

- **Kehadiran.** Harian, satu catatan per murid per hari, diisi wali kelas; semua murid dianggap hadir dan guru mengetuk yang tidak hadir (D-029). Menunya bernama *Kehadiran* (path `/kehadiran`), bukan *Absensi* (D-302). Rekap per minggu, bulan, dan semester bisa dicetak dan diekspor ke Excel. Peringatan dini menandai murid bila ketidakhadiran tanpa keterangan membuat kehadirannya di bawah 90% dalam satu bulan kalender, atau setelah 3 hari sekolah berturut-turut tidak hadir tanpa keterangan; kedua ambang itu pengaturan sekolah (D-303).
- **Jurnal.** Diisi dari pertemuan RPM: guru menandai hasilnya `done`, `partial` (dengan pengalaman belajar yang belum selesai), atau `not_done` (dengan alasan wajib: Kegiatan sekolah, Guru bertugas di luar, Murid tidak hadir, Bencana/cuaca, Lainnya). Status pertemuan diturunkan dari jurnal (D-267, D-268).
- **Catatan Siswa.** Satu formulir untuk catatan anekdot, konseling (BK), dan prestasi; catatan BK punya visibilitas terbatas. Label sekali ketuk dan "masuk rapor" untuk prestasi (Bagian B B-09).
- **Isian observasi dan rubrik** untuk instrumen baru: satu ketukan per tingkat indikator per murid (D-349).

**Luring.** Aplikasi sekolah adalah PWA yang bisa dipasang (D-093). Pekerjaan luring sengaja dibatasi:

| Bisa luring | Keputusan |
|---|---|
| Kehadiran, Jurnal, Catatan Siswa | D-016 |
| Pertemuan RPM hari ini dan besok, baca-saja, hanya teks | D-289 |
| Antrean pindai Pindai LJK (jawaban dan tingkat keyakinan, tanpa gambar) | D-289 |
| Isian observasi dan rubrik | D-349 |

Selain itu (mengedit, AI, ekspor) membutuhkan koneksi.

```
Phone (online) caches the Di Kelas shell and today's data
Phone (offline) saves rows in IndexedDB with a client_uuid
Phone (back online) sends the queue: POST /sync (session + CSRF)
Server upserts by client_uuid (repeats are no-ops), checks policy
```

Ringkasan diagram: saat daring, ponsel menyimpan kerangka Di Kelas dan data hari ini. Saat luring, baris disimpan di IndexedDB dengan `client_uuid`. Saat daring lagi, antrean dikirim lewat sesi web dan CSRF. Server melakukan upsert menurut `client_uuid`, sehingga pengiriman ulang tidak menggandakan data, dan memeriksa izin setiap baris.

Sinkronisasi memakai sesi web dan token CSRF biasa, bukan token API. Baris yang mengantre tetap ada walau sesi kedaluwarsa, sampai guru masuk lagi. Data murid yang disimpan di perangkat dihapus saat keluar dan tidak pernah berisi agama (D-051). Penulisan terakhir yang menang per baris, dengan waktu perangkat disimpan di `recorded_at_device`. Setelah rilis baru, aplikasi menampilkan "Versi baru tersedia, muat ulang" dan menyinkronkan antrean luring sebelum memuat ulang (D-321).

**Kelengkapan.** Satu layanan, `app/Progress/`, menghasilkan baris slot (guru, kelas, mapel, tanggal, butir, status ∈ selesai, belum, belum waktunya, dikecualikan) untuk RPM, Jurnal, Kehadiran, refleksi guru, Prota/Promes/rencana asesmen, asesmen diagnostik, dan JP kokurikuler. Setiap layar dan setiap peran membaca baris yang sama (D-263). Jendela waktunya Hari ini, Minggu ini, Semester ini (D-264). Job setiap Senin menulis `progress_snapshots` mingguan untuk tren (D-265).

**Kelas Saya** menyimpan data kelas: Murid (dengan perpindahan dan Kenaikan), Jadwal (sudah dibangun), Piket, Keuangan, Inventaris.

- **Piket:** *Bagi otomatis* membagi murid aktif ke hari sekolah kelas itu, seimbang menurut jumlah dan jenis kelamin, dengan rotasi dan tugas per murid yang opsional; tugas murid yang tidak hadir dialihkan ke teman sekelompoknya (D-297). Sekolah boleh menampilkan kelompok hari ini di layar diam Game kelas (D-298).
- **Keuangan kelas** (sekolah boleh mematikannya): pemasukan dan pengeluaran dalam rupiah bulat; *Sumbangan sukarela* mencatat sumbangan sukarela, dan kata "tunggakan", "kurang bayar", dan "belum lunas" tidak pernah muncul (D-300). Wali kelas boleh membagikan laporan transparansi secara manual lewat WhatsApp; aplikasi tidak pernah mengirim pengingat uang (D-299).
- **Tabungan:** buku tabungan per murid yang ikut berpindah bersama murid antarkelas; saat lulus atau keluar, aplikasi menyiapkan berita acara penyerahan (D-219, D-301).
- **Inventaris:** tampilan ruang dari inventaris sekolah; wali kelas mengubah ruangnya sendiri (D-306).
- **Fitur kewalikelasan lain** yang diterima di P4-04: kesepakatan kelas dan catatan disiplin positif, pelacak 7 KAIH dengan lembar rumah yang bisa dicetak, kunjungan rumah, data kesehatan ringan, pesan ke orang tua berupa teks `wa.me` biasa dengan log kontak, serta catatan ekstrakurikuler dan Pramuka sejak awal v1 (D-214…D-216).

**Berkas Administrasi.** Bundel semester di Dokumen Saya: sampul, ringkasan kelengkapan, daftar isi, halaman judul per bagian, dan halaman NIHIL untuk bagian yang kosong, sebagai satu PDF gabungan (rute merge Gotenberg, satu-satunya pengecualian dari "tidak ada penggabungan") atau ZIP. Profilnya: Standar SIGRAK, Format Kudus, dan preset K3. File disimpan 7 hari. Catatan BK tidak pernah dimasukkan (D-278, D-280).

**Status:** **Dibangun:** hanya Kelas Saya > Jadwal. **Didesain:** semua yang lain di bagian ini ada di slice P5-07 (Di Kelas dengan sinkronisasi luring, Jurnal dari RPM), ditambah sebagian P5-04 dan P5-08.

Bacaan lanjutan: `content-engine.md`, `ifp-classroom.md`, `docs/drafts/lkpd-exploration.md`, `docs/drafts/legacy-upgrade-01.md`, `docs/drafts/administration-features.md`, `architecture.md`, D-011…D-016, D-024, D-029, D-171…D-181, D-263…D-278, D-289, D-293…D-303, D-329…D-337, D-349.

### 4.6 Asesmen

**Siapa:** guru (guru kelas dan guru mapel) untuk mapelnya sendiri; kepala sekolah mengatur pengaturan asesmen untuk seluruh sekolah.

**Rencana dan asesmen.** *Rencana Penilaian* (`assessment_plans`) mendaftar asesmen per kelas, mapel, dan semester. Sebuah asesmen (`assessments`) bersifat formatif, sumatif (per lingkup materi), STS, atau SAS. Bila asesmen memakai dokumen Bank Soal, butirnya **disalin** ke `assessment_items` sebagai potret beku dengan `kode_tp`, level kognitif, dan skor maksimum, sehingga perubahan dokumen di kemudian hari tidak pernah mengubah nilai yang sudah ada (arsitektur: "data yang melintasi batas modul adalah potret"). Nilai disimpan di `scores` per butir dan murid, `decimal(5,2)`. Grid nilai cepat (*grid cepat*) termasuk P5-08 (D-286).

**KKTP dan analisis per TP.** Pendekatan KKTP (deskripsi, rubrik, interval, atau persentase) adalah pilihan sekolah, dengan ambang angka per sekolah bila dipakai (D-214 A3; kekuatan `school_discretion`). Analisis per TP berjalan otomatis. "Analisis Daya Serap" adalah tata letak cetak dan alias pencarian untuk analisis per TP, bukan halaman terpisah. *Kelompok belajar* per TP (perlu bimbingan, cukup, siap pengayaan) bisa disesuaikan guru, dan wizard RPM berikutnya menawarkan **jumlah** anggota kelompok (tidak pernah nama) sebagai kondisi murid (D-295). Analisis butir menulis kembali tingkat kesukaran dan daya beda yang sebenarnya ke `question_items` (D-214 A7, D-294).

**Remedial dan pengayaan.** Direncanakan dari analisis per TP (D-214 A1). Nilai remedial menggantikan nilai lingkup materi, dibatasi di KKTP secara bawaan (sekolah boleh menghapus batas itu); nilai sebelum remedial tetap disimpan (D-291). Kelebihan JP sebelum SAS memunculkan saran remedial atau pengayaan (D-269).

**Diagnostik dan refleksi.** Asesmen diagnostik awal tahun dengan butir prasyarat singkat dan daftar cek non-kognitif (D-214 A2); refleksi guru minimal sekali per semester, terisi awal dari jurnal dan nilai (D-214 A4); umpan balik murid yang diminta Standar Proses dikumpulkan di kertas dan hanya disimpan sebagai total kelas (D-225).

**Pindai LJK (memindai lembar jawaban).** Termasuk inti di P5-08 (D-286). Lembar cetaknya mirip lembar yang sudah dipakai sekolah: kepala berisi Mata Pelajaran, Kelas, Nama, Nomor; kotak huruf yang disilang murid; baris bernomor untuk Isian dan Uraian; empat penanda sudut dan QR yang hanya berisi `public_id` acak milik lembar itu (D-287, D-325). Bentuk yang dipindai: PG, PG Kompleks, Benar-Salah dan PGK Kategori, Menjodohkan. Isian dan Uraian diisi manual dengan nilai parsial. Tidak ada AI yang membaca jawaban murid, dan gambar lembar tidak pernah diunggah (D-287). Antrean pindai bisa luring dan baru masuk ke `scores` setelah sinkronisasi dan konfirmasi guru (D-289). Kode kamera yang sama nanti dipakai Kartu Jawab (D-174) dan cadangan kertas untuk Simulasi TKA (D-288, setelah P5-08).

**Di mana bulatan dibaca.** Di peramban pada ponsel guru, dengan pustaka OMR JavaScript; hanya jawaban yang terbaca beserta tingkat keyakinannya yang dikirim ke server (D-369, yang menggantikan rumusan "OMR di server SIGRAK sendiri" pada D-347). **Jawaban tulisan tangan** (Uraian, Isian) menyusul setelah Pindai LJK: ponsel membacanya menjadi teks, guru memperbaiki teks di samping fotonya (foto tidak pernah keluar dari ponsel), dan hanya teksnya, diberi kode nomor absen + nomor soal seperti `12-3`, yang dikirim ke AI bersama kunci atau rubrik. AI menyarankan nilai dan umpan balik; guru yang memutuskan (D-370).

**Peta Asesmen dan instrumen baru (Didesain, D-344…D-351).** *Master Assessment Landscape* milik pemilik menjadi satu dataset kurasi di `docs/data/assessment/` (domain, tujuan, metode, media, penskoran, pemanfaatan), ditinjau Tim Validasi, ditambah label di setiap instrumen (D-344). Desainnya menjadi tugas tersendiri, P4-19 (D-345). Templat baru: Rubrik Kinerja (praktik, demonstrasi, proyek, produk, praktik ibadah MI), Observasi DPL (indikator perilaku yang diamati beberapa kali, ditampilkan sebagai tren), Cek Kesiapan Awal (berupa profil, bukan nilai), Akomodasi Murid, serta kartu penilaian diri dan teman sejawat (D-346). Wizard "lima pertanyaan" di Asesmen (*Apa yang diukur? Untuk apa? Bagaimana? Bukti apa? Tindak lanjutnya apa?*) memilih dari taksonomi. Gelombang A mencakup dataset v1, label di Bank Soal dan LKPD, Rubrik Kinerja, Observasi DPL dengan isian di ponsel, Cek Kesiapan Awal, dan label miskonsepsi pada pengecoh (D-351). P5-08 sendiri tidak berubah.

**Data sensitif dan AI.** AI di sisi guru membuat instrumen, memberi label miskonsepsi, dan menyusun draf umpan balik dari pola kelas. Nama murid di kertas dibaca dengan pengenalan teks di perangkat guru dan dicocokkan dengan daftar murid di sana. Teks jawaban uraian, **tanpa data murid apa pun**, boleh dikirim ke Google AI untuk membantu penilaian, dan guru yang menentukan nilainya. Membaca tulisan tangan dan suara dengan AI datang nanti (D-347). Hasil sosial-emosional, kebutuhan khusus, dan minat mengikuti aturan data kesehatan: hanya guru kelas itu (dan GPK yang ditugaskan), operator, dan kepala sekolah; tidak pernah ke AI; tidak pernah menjadi label permanen atau diagnosis; pengawas hanya melihat total (D-350). Tidak direncanakan: IRT/Rasch per sekolah, mesin adaptif, model prediksi pada anak, AI pada masukan anak, salinan survei nasional (`assessment-landscape.md` §6–§7).

**Status:** **Didesain** (P5-08 untuk intinya; P4-19 dan Gelombang A untuk lanskapnya). Belum ada yang dibangun.

### 4.7 Rapor dan ekspor

**Siapa:** guru mapel dan wali kelas (nilai, deskripsi, catatan), kepala sekolah (pengesahan leger), operator (ekspor).

**Rapor 12 komponen.** Mengikuti Panduan Pembelajaran dan Asesmen 2025, rapor punya dua belas komponen minimum: identitas, sekolah, kelas, semester, mapel, nilai, deskripsi capaian kompetensi, deskripsi kokurikuler, ekstrakurikuler, ketidakhadiran, catatan wali kelas, dan tanggapan orang tua (D-210, D-214 H1). Fase A boleh melapor tanpa angka (`report_cards.numeric_grades = false`).

**Nilai.** Nilai rapor sebuah mapel adalah rata-rata berbobot dari rata-rata sumatif lingkup materi, STS, dan SAS, bawaannya 50/25/25, diatur per sekolah di Pengaturan Asesmen. Komponen yang tidak dilaksanakan dilewati dan bobot lainnya dinormalkan. **Asesmen formatif tidak pernah boleh menjadi komponen**; ini diperiksa di server dan punya tes (D-205, D-290).

**Deskripsi.** Deskripsi capaian per mapel disusun dari capaian TP tertinggi dan terendah dalam hasil formatif (D-205). Draf AI hanya mengirim tingkat penguasaan per TP untuk "Murid A" yang anonim; nama aslinya digabungkan setelah pembuatan di server (`ai-provider-engine.md`, aturan privasi 2). Catatan wali kelas juga boleh disusun AI dari jumlah kehadiran, jenis catatan, dan hasil per TP, tidak pernah nama atau data pribadi, lalu wali kelas mengubah dan menyetujuinya (D-213).

**Kokurikuler dan ekstrakurikuler.** Kokurikuler dinilai pada 8 DPL; rapor mendapat deskripsi dimensi yang sudah menguat dan yang masih berkembang (D-210, D-214 A5). Catatan ekstrakurikuler (kegiatan, pembina, anggota, kehadiran, deskripsi rapor), dengan Pramuka ditawarkan secara bawaan, dibangun sejak awal v1 (D-216); `report_extracurriculars` menyimpan baris rapornya (D-205).

**Leger dan pengesahan.** Setiap guru mapel mengonfirmasi nilai mapelnya, wali kelas mengonfirmasi leger kelas, lalu kepala sekolah mengesahkan dan leger terkunci. Cetakan dan ekspor rapor selalu diambil dari versi yang terkunci. Hanya kepala sekolah yang bisa membuka kunci, dengan alasan yang dicatat. "Cek kesiapan rapor" memeriksa 12 komponen per murid dan memberi peringatan tanpa memblokir (D-292).

**Ekspor.** v1 mengekspor data rapor dalam tata letak Excel milik SIGRAK (D-144). Ekspor dalam format impor **e-Rapor SD** dan **RDM** (Rapor Digital Madrasah) dibangun tepat setelah slice 6 (Asesmen), karena menghapus input ganda (D-218). File untuk sistem itu mengikuti spesifikasi sistemnya sendiri (XP-24). File per murid dinamai menurut kelas dan nomor absen, tidak pernah nama atau NISN (D-107, XP-32).

**Kenaikan dan kelulusan.** Kenaikan (naik, tinggal, lulus) dan mutasi (masuk, keluar, pindah rombel) berupa draf yang bisa diubah dan dicetak sampai *Tetapkan*; draf kenaikan menjadi bahan lembar rapat dewan guru dan berita acara (D-215 H3, D-304). Kelulusan kelas 6 mencakup data SKL dan ijazah (D-215 H4). Saldo tabungan ikut dengan murid; saat lulus atau keluar, berita acara penyerahan disiapkan (D-301).

**Status:** **Didesain** (P5-08, lalu tugas ekspor e-Rapor/RDM P4-10).

### 4.8 Ujian latihan TKA

**Siapa:** guru atau operator membuat sesi; guru menjadi proktor; murid kelas 6 mengerjakannya di situs ujian; kepala sekolah melihat hasilnya.

**Apa ini.** Simulasi latihan Tes Kemampuan Akademik nasional untuk SD/MI, untuk drill. Simulasi ini tidak harus persis sama dengan TKA resmi; kerangka resmi (`docs/data/tka/kerangka-tka-sd.json`, Perka BSKAP 047/H/AN/2025) menjadi rujukan gaya soal, bukan sasaran kepatuhan. Durasi (75 menit), jumlah butir (30), batas kategori, dan penilaian adalah nilai bawaan latihan yang bisa diubah guru (D-089). Ini fitur tambahan *Simulasi TKA* (D-158). Sasarannya musim TKA 2028 (D-162). UI tidak pernah menulis "TKA" saja, selalu "Simulasi TKA" (sistem desain).

**Alurnya.**

1. **Siapkan soal.** Buat *Paket Soal TKA* dengan templatnya (bagian 4.4), lalu periksa dan ubah.
2. **Buat sesi.** Pilih paket, kelas 6, tanggal, rentang waktu, dan durasi. Satu sesi adalah satu kelas berisi 10–25 peserta (D-092). Paket **dibekukan** ke `exam_items` saat sesi dijadwalkan; perubahan berikutnya tidak pernah mengubah ujian yang sedang berjalan (D-124).
3. **Kartu login.** Sistem mencetak satu kartu per murid (nama, kelas, sesi, nama pengguna, kata sandi), beberapa per halaman, lewat antrean `exports`. Kredensialnya hanya berlaku untuk sesi itu.
4. **Jalankan.** Proktor membuka sesi dan membacakan token saat ini: 6 karakter yang diturunkan dari rahasia sesi dan jendela 15 menit yang sedang berjalan (gaya TOTP), sehingga token tidak pernah disimpan (D-027). Murid masuk di `ujian.sigrak.com` dengan nama pengguna, kata sandi, dan token. Proktor memantau kemajuan, bisa mengatur ulang login peserta yang macet, atau menambah waktu untuk satu peserta; keduanya dicatat di `exam_events`.
5. **Klien ujian.** Layar sederhana mirip TKA: area soal, kisi nomor, tanda *ragu-ragu*, pengatur waktu, kontrol ukuran huruf A−/A/A+ dengan ukuran bawaan standar (D-121). Urutan butir dan pilihan diacak per peserta, dengan kelompok stimulus tetap bersama. Setiap jawaban tersimpan otomatis dengan nomor urut (idempoten), sehingga koneksi yang tersambung lagi melanjutkan dari titik berhenti. Waktu ditegakkan oleh server; pengatur waktu di klien hanya menampilkannya. Tidak ada panggilan AI, tidak ada tautan ke aplikasi, tidak ada data di luar ujian peserta itu sendiri (D-027, D-094).
6. **Hasil.** Nilai 0–100 per mapel, dengan nilai parsial untuk bentuk PGK, dinormalkan dan diberi label perkiraan latihan (D-030, D-042). Guru dan murid melihat empat tingkat: Istimewa, Baik, Memadai, Kurang. `exam_results` dan ekspor menyimpan kategori resmi (Baik, Memadai, Kurang) dan predikat Istimewa (95,00 ke atas) secara terpisah, sesuai SHTKA (D-371, melengkapi D-041 dan D-120). UI mencatat bahwa ini hasil latihan. Ada analisis per kompetensi dan per butir, ekspor Excel dan PDF, dan salinan opsional ke Asesmen.

**Integritas dan kapasitas.** Pembatasan login di situs ujian didasarkan pada sesi dan nama pengguna, bukan IP, karena satu kelas berbagi satu IP sekolah; aturan Cloudflare mengizinkan lonjakan 30–40 login dari satu IP (D-054). Kata sandi dienkripsi supaya kartu bisa dicetak ulang, dan dihapus 30 hari setelah sesi ditutup. Situs ujian tidak bisa dipasang sebagai PWA (D-093).

**Mengapa murid boleh memakainya.** Ketentuan Gemini melarang layanan yang kemungkinan dipakai orang di bawah 18 tahun. Jawaban SIGRAK: AI hanya berjalan di sesi guru *sebelum* ujian untuk menyiapkan soal; murid menerima konten yang sudah jadi dan diperiksa guru, dan tidak pernah memakai atau memicu AI (D-124).

**Status:** **Didesain** (P5-06). Yang sudah dibangun: grup rute dan guard `ujian.`, entry Inertia + Svelte sendiri di `resources/exam-client/`, halaman beranda sementara, dan model `ExamParticipant` yang dipakai guard `exam`.

Bacaan lanjutan: `content-engine.md`, `architecture.md`, `docs/drafts/assessment-landscape.md`, `docs/drafts/administration-features.md`, D-026, D-027, D-030, D-041…D-047, D-089, D-092, D-094, D-111, D-120, D-124, D-144, D-205, D-213…D-218, D-286…D-295, D-301, D-304, D-344…D-351.

### 4.9 Portal: kepala sekolah, operator, pengawas

**Siapa:** kepala sekolah, operator, dan pengawas, lewat tombol ganti peran. Ketiganya membaca data yang sama yang dihasilkan guru, lewat layanan yang sama, sehingga setiap peran melihat angka yang sama (`legacy-upgrade-01.md` §1, aturan P1).

**Bagian portal** (konsep produk):

| Portal | Bagian |
|---|---|
| Kepala Sekolah | Ringkasan, Pantau Guru, Validasi, Supervisi, Program Sekolah, Agenda & Rapat, Buku Sekolah, Data Sekolah, Pengaturan Sekolah (cetak, langganan) |
| Operator Sekolah | Tahun Ajaran, Kelas, Guru (undang), Jadwal, Kaldik, Profil Sekolah |
| Pengawas | Sekolah Binaan, Pantau (baca-saja), Validasi, Supervisi, Laporan Kepengawasan |

Keputusan yang lebih baru menambahkan Pusat Tindak Lanjut, Arsip Tahun Ajaran, dan PKKS untuk pengawas (`legacy-upgrade-01.md` §4).

**Memantau kelengkapan.** *Ringkasan* dan *Pantau Guru* membaca layanan kelengkapan (D-263) dengan jendela bawaan Semester ini; kepala sekolah dan pengawas juga boleh memilih rentang tanggal bebas untuk cetakan (D-264). Pantau Guru menampilkan jumlah kesiapan per guru (D-270), JP yang hilang per alasan tidak terlaksana (D-268), dan tren dari potret mingguan (D-265). Kepala sekolah tidak mendapat Kabar kesiapan; guru yang mendapatkannya (D-271). Pengawas dan konsol hanya melihat total sekolah.

**Pengingat dan tindak lanjut.** *Pengingat* dari kepala sekolah ke guru bergerak dari Terkirim → Dibaca → Ditindaklanjuti; pengawas mengirim pengingat ke kepala sekolah, tidak pernah ke guru; paling banyak satu per butir per guru per minggu; kata "teguran" tidak pernah dipakai (D-311). *Pusat Tindak Lanjut* adalah satu daftar tindak lanjut per peran yang membaca catatan yang sudah ada (RTL dari supervisi, program sekolah, kunjungan pengawas, PKKS, validasi yang dikembalikan), tanpa tabel baru (D-310).

**Validasi dan komentar.** Dua alur terpisah:

- **Validasi kurikulum (D-019).** Hanya kurikulum yang diubah atau ditambah sekolah yang melewati guru → kepala sekolah → pengawas, dengan catatan di setiap pengembalian.
- **Pengesahan dokumen (D-281).** RPM, Prota, Promes, rencana asesmen, dan Berkas Administrasi bisa dikirim dengan *Ajukan pengesahan*. Kepala sekolah menyetujui atau mengembalikannya di antrean Validasi. Persetujuan menyimpan hash SHA-256 dari revisi dan kode 10 karakter; cetakan memuat QR, kode, dan "Disahkan melalui SIGRAK, [tanggal]" hanya selama revisi aktif sama dengan revisi yang disahkan. `/verifikasi/{code}` hanya berfungsi setelah login, untuk anggota sekolah itu dan pengawas yang ditugaskan. Hanya kepala sekolah yang bisa mencabutnya. Istilahnya "pengesahan dokumen", tidak pernah "tanda tangan elektronik". Revisi yang disahkan tidak pernah dipangkas (D-282).

Kepala sekolah dan pengawas bisa berkomentar di dokumen mana pun (`document_comments`).

**Instrumen supervisi.** Kepala sekolah menyupervisi guru dengan 18 instrumen, S-00…S-17, yang dirilis bersama sebagai templat yang bisa diubah (D-221, D-222): program dan jadwal, telaah dokumen (CP–TP–ATP, Prota/Promes, RPM, asesmen, analisis dan tindak lanjut, kokurikuler), siklus observasi (pra-observasi, observasi, pasca-observasi, pemantauan RTL), refleksi, umpan balik murid, cek administrasi wali kelas, serta rekap sekolah dan program tindak lanjut. Skalanya: observasi kelas memakai 3 tingkat Ruang GTK (Belum dilakukan, Dilakukan tetapi belum efektif, Dilakukan dan efektif); telaah dokumen memakai 1–4 dengan deskriptor; daftar cek tetap 0/1/2; rentang nilai akhir 91–100 Sangat Baik, 81–90 Baik, 71–80 Cukup, di bawah 71 Kurang (D-223). Hanya kepala sekolah yang mengobservasi; pengawas hanya melihat rekap sekolah (D-224). Untuk S-01 dan S-15, SIGRAK menyarankan nilai dari data kelengkapan, dan sebuah butir baru dihitung setelah dikonfirmasi kepala sekolah (D-312). Bank temuan berisi frasa siap pakai per butir, bisa diubah, tanpa AI (D-313). Set instrumen Kudus menjadi varian daerah (D-327).

**Supervisi kepala sekolah (PKKS).** Pengawas menyupervisi kepala sekolah di v1 dengan set instrumen PKKS / supervisi manajerial yang disusun dari aturan yang berlaku, disimpan sebagai templat dengan varian daerah (D-308, D-324). Butir yang tidak dihasilkan SIGRAK (dokumen pendirian, RKAS, SPJ BOS, surat-menyurat, buku tamu) dinilai manual dengan unggahan bukti opsional; butir yang didukung data SIGRAK mendapat saran nilai yang dikonfirmasi pengawas (D-328). Unduhan bukti *Kinerja Kepala Sekolah* melayani Ruang GTK (D-308).

**Fitur kepala sekolah dan operator lainnya (D-217).** Supervisi akademik untuk Pembelajaran Mendalam; unduhan bukti dukung kinerja untuk Ruang GTK; pembagian tugas dengan JTM dan SK yang dibuat dari data SIGRAK (JTM wali kelas bawaannya 2 di SD dan 6 di MI, D-212); budaya sekolah aman dengan tata tertib, SOP, dan log kejadian internal (tanpa kanal publik); mutasi murid lengkap dengan pengingat Dapodik 14 hari kerja; inventaris sekolah; rekap lintas sekolah dan pendampingan Pembelajaran Mendalam untuk pengawas; templat awal KSP (D-209). Kepala sekolah bisa mengunduh Berkas Administrasi semua guru untuk satu semester sebagai satu ZIP (D-279). Pemakaian Game kelas per kelas dan mapel tampil untuk kepala sekolah dan pengawas (D-178). Penyusun dan evaluasi KSP datang setelah v1.

**Arsip Tahun Ajaran.** Tahun ajaran yang ditutup menjadi baca-saja: bisa dilihat dan diunduh, tidak bisa diubah. Rapor, leger, data kelulusan, dan buku induk tidak pernah dihapus otomatis; hasil supervisi dan PKKS disimpan minimal 5 tahun (D-318).

**Status:** **Dibangun:** halaman Guru, Tahun Ajaran, Kelas, dan Jadwal milik operator. **Didesain:** semua yang lain (P5-09, dengan P4-08 untuk deskriptor per butir). Kaldik ada di P5-03i.

### 4.10 Konsol internal dan bisnis

**Siapa:** Tim SIGRAK.

**Konsol.** Panel Filament 5 di akar `admin.sigrak.com`, dengan halaman masuk di `/masuk`, di balik guard `staff` dan Cloudflare Access. Horizon dan Telescope berada di balik guard yang sama di host yang sama (D-055, D-164). Wewenang staf ditetapkan di kode dan ditampilkan di halaman "Hak akses" yang hanya bisa dibaca (D-314).

| Fitur konsol | Status | Keputusan |
|---|---|---|
| Wilayah, Sekolah, Akun staf | Dibangun | P5-03a, D-167 |
| Anggota sekolah, Undangan, Penugasan pengawas | Dibangun | P5-03b |
| Direktori sekolah, Sebaran sekolah, Peta sekolah | Dibangun | D-169, D-170 |
| Registri templat dan penimpaan prompt | Didesain | D-013 |
| Model AI per tujuan dengan urutan cadangan, tercatat | Didesain | D-315 |
| Kesehatan Sistem (antrean, job gagal, disk, pembuatan, galat AI, cadangan terakhir) | Didesain | D-316 |
| Konsol kurikulum: bandingkan, terbitkan, batalkan, dampak, audit JP | Didesain | D-317 |
| Pengaturan retensi per jenis data, tercatat | Didesain | D-318 |
| Kabar bertarget (semua, kabupaten, kecamatan, jenjang, peran) | Didesain | D-319 |
| Minta Bantuan dengan status Baru, Diproses, Selesai | Didesain | D-320 |
| Libur nasional dan kalender wilayah | Didesain | D-253, D-256 |
| Pengajuan sekolah binaan oleh pengawas | Didesain | D-309 |
| Pemetaan fitur inti/tambahan per fitur | Didesain | D-211 |
| Sebelas peran staf dengan cakupan, 2FA | Didesain | D-338…D-343 |

**Penyedia AI untuk staf.** Selain Gemini, `app/AI/` mendapat dua adapter untuk dipakai di konsol: adapter yang kompatibel dengan OpenAI (base URL, kunci, model; mencakup OpenAI, OpenRouter, DeepSeek) dan adapter untuk API milik satu penyedia model komersial lain. Admin bisa menguji kunci penyedia ini dan memilihnya sebagai model atau cadangan per tujuan, dengan setiap perubahan dicatat. **Kunci guru tetap hanya Gemini** (D-023). Sebelum sebuah tujuan yang berjalan pada pembuatan konten guru memakai kunci Tim SIGRAK dari penyedia lain, pemilik memutuskan siapa yang membayar (D-326). Uji pengaturan model selalu memakai kunci Tim SIGRAK, tidak pernah kunci guru (D-315).

**Support.** Support melihat data sekolah secara baca-saja berdasarkan MoU sekolah, dengan agama dan kesehatan disembunyikan, satu baris internal per sesi bantuan (D-341). Status permintaan bantuan terlihat oleh pengirimnya (D-320).

**Uji coba, langganan, fitur tambahan.** `subscriptions` punya tepat satu pemilik (sekolah, pengguna, atau pengawas) dan paket ∈ {`trial`, `game_trial`, `teacher`, `school`, `cluster`}; `school_addons` menyimpan fitur tambahan sekolah per tahun ajaran dan membutuhkan langganan sekolah yang aktif (model data). Pemeriksaan hak pakai kecil membaca keduanya sebelum sebuah fitur, pembuatan konten, atau ekspor dipakai (arsitektur). Setelah uji coba 7 hari, akun hanya bisa melihat dan mengunduh (D-157, D-160). Pemetaan fitur ke paket datang dari konsol (D-211).

**Pembayaran manual.** Penjualan mencatat klaim pembayaran; Keuangan, atau pemilik, mengonfirmasinya, lalu paketnya diaktifkan (D-091, D-340). Tidak ada payment gateway di v1.

**Mode Contoh.** Sekolah demo yang diisi data fiktif saja, untuk demo penjualan. Panggilan AI diizinkan dengan kunci Gemini milik tenaga penjual sendiri, dengan aturan kunci yang sama seperti guru (D-090). Data demo hanya ada di factory dan `DemoSeeder` yang tidak pernah berjalan di produksi (DB-14).

**Mitra.** Kode referal dan laporan mitra untuk komunitas guru dan reseller, didesain di tugas berikutnya (D-342).

**Status:** **Dibangun:** halaman konsol yang berstatus Dibangun di atas, `php artisan staff:create`, tiga peran staf. **Didesain:** slice P5-10 (uji coba, langganan, fitur tambahan, pembayaran manual, Mode Contoh) dan fitur konsol, banyak di antaranya bersamaan dengan P5-04 dan P5-09.

Bacaan lanjutan: `product-concept.md`, `docs/drafts/teacher-supervision-instruments.md`, `docs/drafts/administration-features.md`, `docs/drafts/internal-roles.md`, `docs/drafts/legacy-upgrade-01.md`, D-019, D-090, D-091, D-157…D-160, D-209…D-212, D-217, D-221…D-224, D-263…D-271, D-278…D-282, D-308…D-328, D-338…D-343.

## 5. Arsitektur

### 5.1 Satu aplikasi, satu server

SIGRAK adalah **satu aplikasi Laravel 13** di **satu VPS Ubuntu Server 24.04** (IDCloudHost, Jakarta; 8 vCPU, RAM 16 GB, NVMe 320 GB) di belakang Cloudflare, yang hanya bisa dicapai lewat Cloudflare Tunnel (D-070, D-086…D-088, D-096). Aplikasi ini melayani tiga front end di tiga hostname, masing-masing dengan guard sendiri (bagian 2.4). Pekerjaan latar belakang berjalan di antrean Redis di bawah Horizon. Gotenberg adalah satu-satunya layanan Docker. Satu-satunya layanan luar yang dipanggil server adalah API Gemini (ditambah server surel).

```
Users -> Cloudflare (DNS, proxy, TLS, WAF)
      -> Cloudflare Tunnel (outbound only)
      -> Nginx 127.0.0.1:8080 (app., ujian., admin.)
      -> PHP-FPM: Laravel (web / exam / staff guards)
           -> MySQL 8.4   -> Redis 8.2   -> local disk
Supervisor -> Horizon (ai, exports, default) + schedule:work
Docker     -> Gotenberg 8 (localhost, basic auth, no egress)
Outbound   -> Gemini API, Gmail SMTP
```

Ringkasan diagram: pengguna melewati Cloudflare lalu Cloudflare Tunnel yang hanya membuat koneksi keluar, menuju Nginx di localhost yang melayani tiga hostname. PHP-FPM menjalankan Laravel dengan tiga guard dan memakai MySQL, Redis, dan disk lokal. Supervisor menjalankan Horizon dan penjadwal. Docker hanya menjalankan Gotenberg tanpa akses keluar. Koneksi keluar hanya ke API Gemini dan SMTP Gmail.

### 5.2 Front end

- **Aplikasi sekolah:** Inertia.js dengan Svelte 5 biasa (runes) dan TypeScript, build dengan Vite, Tailwind dengan komponen TailAdmin yang dipindahkan ke Svelte (D-008, D-075, D-076). Halaman ada di `resources/js/Pages/<Area>/<Name>.svelte`. URL dibuat dengan `route()` bertipe dari Ziggy; setiap root view hanya mengirim grup rutenya sendiri, sehingga rute staf dan vendor tidak pernah sampai ke browser (D-163). Teks UI berasal dari `lang/id` lewat satu prop Inertia bersama dan satu helper `t()` (D-080, D-097).
- **Klien ujian:** entry dan root view Inertia + Svelte sendiri di `resources/exam-client/`, guard dan hostname sendiri, tidak berbagi bundle dengan aplikasi sekolah; penyimpanan otomatis jawaban dan heartbeat lewat satu endpoint JSON kecil (D-094).
- **Konsol staf:** Filament 5, dengan tema sendiri memakai warna merek (sistem desain).
- **Runtime game:** JS vanilla dan CSS per templat game di `resources/game-runtime/`, bertipe dengan JSDoc dan `// @ts-check`, bisa dimainkan mandiri dan luring (D-011, D-075).
- **Pustaka:** three.js, KaTeX, ApexCharts, Lucide, Plus Jakarta Sans, dan fon Arab, semuanya disematkan ke versi persis dan dibundel, tidak pernah dari CDN; pustaka berat dimuat saat dibutuhkan (D-033, D-083, SV-11).

### 5.3 Batas modul

```
app/
  AI/          Gemini only: keys, selection, errors, privacy guard,
               usage logs. Knows nothing about education.
  Content/     Templates, generation pipeline, export.
               Knows nothing about Gemini.
  Tenancy/     School context, BelongsToSchool, memberships, invitations
  Curriculum/  Import, versions, SubjectsForGrade, school changes
  Classroom/   Di Kelas: attendance, journal, notes, offline sync
  Exam/        TKA sessions, snapshot, cards, token, scoring
  Calendar/    EffectiveDays, ConflictChecker (designed)
  Progress/    CompletenessLedger, ProgressReport (designed)
  Filament/    Staff console only
```

Ringkasan diagram: `AI` hanya mengurus Gemini (kunci, pemilihan, galat, penjaga privasi, log pemakaian) dan tidak tahu apa pun soal pendidikan; `Content` mengurus templat, pipeline, dan ekspor dan tidak tahu apa pun soal Gemini; `Tenancy`, `Curriculum`, `Classroom`, `Exam`, `Calendar`, dan `Progress` masing-masing memegang satu urusan; `Filament` hanya untuk konsol staf.

`Exam` dan `Classroom` tidak pernah bergantung pada `AI`; `Content` memakai `AI` hanya lewat `AIGateway`; `Filament` adalah tempat yang wajar untuk melewati batasan sekolah (D-049; tes arsitektur Pest menegakkan aturan ini, D-074). Operasi bisnis ada di kelas Action bertujuan tunggal dengan satu metode `handle()`, di folder modul atau di `app/Actions/<Domain>/` (D-078). **Sudah dibangun:** `app/Tenancy`, `app/Curriculum`, `app/Actions/SchoolYear`, `app/Actions/Timetable`, `app/Filament`, `app/Enums`. `app/AI`, `app/Content`, `app/Classroom`, `app/Exam`, `app/Calendar`, dan `app/Progress` belum ada.

### 5.4 Antrean, Horizon, dan penjadwal

- Tiga antrean: `ai` (pembuatan konten), `exports` (PDF, DOCX, Excel, HTML game, Berkas), `default` (surel, notifikasi, penilaian, impor, draf jadwal). Horizon memberi `ai` supervisor sendiri, sehingga panggilan AI yang lama tidak menghambat ekspor atau surel.
- Job pembuatan konten punya `tries = 1`, tanpa percobaan ulang di tingkat antrean, dan memakai `ShouldBeUniqueUntilProcessing` ditambah `WithoutOverlapping` berkunci id pembuatan, sehingga job bisa mengirim dirinya lagi untuk langkah berikutnya (D-050).
- Batas waktu worker di atas batas waktu klien AI terlama (180 detik untuk langkah RPM yang panjang), dan `retry_after` di atasnya lagi.
- Supervisor menjalankan Horizon dan `schedule:work`; tidak ada entri cron (D-064). Pekerjaan terjadwal mencakup penyapu langkah macet, pemangkasan revisi dan log pemakaian, pemeliharaan ujian, pembersihan cache ekspor, dan nanti potret kelengkapan mingguan serta Kabar hari Senin (D-265, D-271).
- Redis memakai database (atau prefiks) terpisah untuk cache, sesi, antrean/Horizon, dan status AI, sehingga `cache:clear` tidak pernah menghapus sesi, job, atau masa jeda kunci (D-053). Redis berjalan dengan `noeviction` dan AOF (D-058).
- Horizon mengirim surel bila antrean menunggu terlalu lama (`ai` 600 detik, `exports` 120 detik, `default` 60 detik), dan penjadwal mengirim heartbeat (D-137).

### 5.5 Gotenberg

`gotenberg/gotenberg:8.39.0-chromium`, dipublikasikan di `127.0.0.1:3000` dengan basic auth dan batas memori 1 GB, di jaringan Docker sendiri tanpa koneksi keluar (D-059). Setiap aset (HTML, fon, skrip, gambar, kop) dikirim di dalam request (D-052), sehingga string berbahaya di sebuah dokumen tidak bisa membuat Chromium mengambil apa pun. Laravel memanggilnya lewat `spatie/laravel-pdf` dengan driver Gotenberg (D-028, D-073). Hanya Berkas Administrasi yang memakai rute merge (D-280).

### 5.6 PWA dan luring

Aplikasi sekolah adalah PWA yang bisa dipasang (manifest, ikon, service worker). Situs ujian tidak bisa dipasang (D-093). Service worker menyimpan kerangka Di Kelas; IndexedDB menyimpan data hari ini dan antrean luring, yang disinkronkan menurut `client_uuid` lewat sesi web dan CSRF (D-016, D-051, D-289, D-349). Spanduk "Versi baru tersedia" memuat ulang setelah sinkronisasi (D-321). Rinciannya di bagian 4.5.

### 5.7 Penyimpanan file

File (kop, ekspor, unggahan) disimpan di disk lokal VPS. Cloudflare R2 hanya tempat simpan opsional untuk file kecil yang jarang dibaca dalam batas tingkat gratis, dan tujuan cadangan di luar server (D-071, D-114). Ekspor disimpan sebagai cache di `document_exports` menurut hash konten dan disajikan lewat rute yang diotorisasi, tidak pernah URL publik. Pemakaian disk dibatasi pengaturan retensi (D-318) dan umur file Berkas 7 hari (D-278); batas foto portofolio masih harus didesain.

### 5.8 Batas keamanan secara ringkas

Tiga guard dengan tabel terpisah dan cookie khusus host; hanya dengan login; tenancy gagal-tertutup dengan global scope `BelongsToSchool`, policy di setiap aksi, dan satu tes IDOR per sumber daya; peran dari keanggotaan; aturan data sensitif (bagian 2.6); kunci AI yang hanya bisa ditulis; privasi prompt; keluaran AI hanya dirender sebagai teks; isolasi ujian; file game tanpa jaringan; Gotenberg tanpa akses keluar; WAF Cloudflare dan tanpa IP publik. Tabel lengkapnya ada di `architecture.md`.

Bacaan lanjutan: `architecture.md`, `coding-standards.md`, D-048…D-055, D-059, D-064, D-074…D-083, D-093, D-094, D-114, D-163, D-164.

## 6. Tur model data

Bab ini menjelaskan bentuknya. Daftar kolom ada di `data-model.md`; migrasi di `database/migrations/` adalah kebenaran untuk apa yang sudah dibangun.

### 6.1 Konvensi yang membentuk setiap tabel

- Nama tabel bahasa Inggris, jamak snake_case; model tunggal StudlyCase.
- Primary key `bigint` untuk join. Baris yang tampil di URL atau props juga mendapat `public_id` (UUIDv7), dan id bigint tidak pernah muncul di URL atau props (DB-11).
- Setiap foreign key dideklarasikan dan diindeks dengan `ON DELETE` yang eksplisit.
- Himpunan tetap berupa `varchar` ditambah `CHECK (col IN (…))` yang dihasilkan dari enum PHP di `app/Enums/`.
- Uang berupa rupiah bulat (`bigint`); nilai berupa `decimal(5,2)`.
- Waktu disimpan dan ditampilkan dalam WIB: `config/app.php` memakai `Asia/Jakarta` dan sesi MySQL `+07:00`; sistem operasi server tetap UTC (D-165).
- **Tanpa soft delete.** Hapus permanen setelah konfirmasi yang menyebut nama butirnya; siklus hidup memakai `status` yang eksplisit.
- JSON hanya untuk konten yang ditentukan templat dan blob rujukan yang diimpor, tidak pernah untuk kolom yang perlu dikueri.
- Setiap tabel milik sekolah punya `school_id NOT NULL`, diindeks, dan menjadi kolom pertama di indeks gabungan.
- Migrasi hanya maju; `down()` opsional (D-085).

### 6.2 Kelompok utama dan hubungannya

```
regions ─< schools ─< school_memberships >─ users
                  └─< invitations
users ─< supervisor_assignments >─ schools      (no school scope)
schools ─< academic_years ─< classes ─< teaching_assignments
classes ─< timetable_slots          classes ─< enrollments >─ students
curriculum_versions ─< topics ─< focus_items / learning_objectives
curriculum_versions ─< subject_allocations, learning_outcomes
subjects ─< region_subjects, school_subjects, topics
users ─< ai_credentials ;  users ─< generations ─< generation_steps
generations ─> documents ─< revisions, comments, exports, meetings
meetings ─< journals ;  assessments ─< assessment_items ─< scores
documents (tka_package) ─> exam_sessions ─< exam_items, participants
```

Ringkasan diagram: wilayah punya sekolah; sekolah punya keanggotaan pengguna dan undangan; pengawas terhubung ke sekolah lewat penugasan tanpa batasan sekolah. Sekolah punya tahun ajaran, kelas, penugasan mengajar, jadwal, dan keanggotaan murid di kelas. Versi kurikulum punya topik, fokus, TP, alokasi, dan CP. Guru punya kunci AI dan pembuatan konten yang menghasilkan dokumen; dokumen punya revisi, komentar, ekspor, dan pertemuan; pertemuan punya jurnal. Asesmen punya butir dan nilai. Paket TKA menjadi sesi ujian dengan butir dan peserta.

- **Akun dan sekolah (Dibangun).** `regions`, `schools`, `users` (tanpa kolom peran), `school_memberships` (peran ∈ kepala sekolah, operator, guru; satu kepala sekolah aktif per sekolah), `supervisor_assignments`, `staff_users`, `invitations` (hash token, `invited_by` atau `invited_by_staff_id`), dan tabel rujukan `school_directory_entries`.
- **Tahun ajaran (Dibangun).** `academic_years`, `classes`, `teaching_assignments` (unik per kelas dan mapel; `teacher_id NOT NULL`), `timetable_slots` (jenis pelajaran, istirahat, upacara, kegiatan; guru lewat penugasan).
- **Murid (Didesain).** `students` (catatan, tidak pernah akun; `religion` sensitif) dan `enrollments` (satu keanggotaan aktif per murid per tahun; mencakup perpindahan dan kenaikan).
- **Kurikulum (Dibangun).** `curriculum_versions`, `subjects`, `subject_allocations`, `learning_outcomes`, `topics`, `focus_items`, `learning_objectives`, `focus_item_objectives`, `region_subjects`, `school_subjects`. Didesain: `school_topic_changes`, `curriculum_validations`.
- **Kalender dan pertemuan (Didesain).** `calendars`, `calendar_events` (dengan `class_id`), `national_holidays`, `meetings` (status diturunkan dari jurnal), `meeting_schedule_history`.
- **Konten (Didesain).** `templates`, `template_versions` (tidak bisa diubah setelah dipakai), `generations` (unik per pengguna dan token klien), `generation_steps` (keluaran ditulis sekali lewat compare-and-set), `documents`, `document_revisions`, `document_comments`, `document_exports`, `template_feature_requests`; nanti `question_items`, `document_approvals`.
- **AI (Didesain).** `ai_credentials` (tanpa `school_id`: kunci milik guru), `ai_credential_cooldowns`, `ai_usage_logs` (tanpa kunci, tanpa teks prompt).
- **Administrasi (Didesain).** `journals`, `attendance_records`, `student_notes`, `assessment_plans`, `assessments`, `assessment_items`, `scores`, `report_descriptions`, `report_cards`, `report_extracurriculars`, `class_finance_entries`, inventaris, jadwal piket, Kabar, Pengingat, supervisi, buku dan program sekolah, `school_print_settings`, `letterheads`, `subscriptions`, `school_addons`.
- **Ujian (Didesain).** `exam_sessions`, `exam_items` (potret beku), `exam_participants`, `exam_answers` (upsert hanya bila `client_seq` lebih besar), `exam_results`, `exam_events` (hanya ditambah).

**Potret saat melintasi batas.** Butir Bank Soal disalin ke `assessment_items`; paket TKA disalin ke `exam_items`; sebuah pembuatan konten menyimpan spesifikasi dan keluaran tiap langkahnya. Perubahan sumber di kemudian hari tidak pernah mengubah ujian yang sedang berjalan atau nilai yang sudah ada.

### 6.3 Tabel yang ditambahkan keputusan yang lebih baru

`legacy-upgrade-01.md` §4 mendaftar apa yang perlu ditambahkan ke `data-model.md`; P4-24 menambahkannya pada 10 November 2026. Yang utama: `national_holidays`, `progress_snapshots`, `document_approvals`, catatan job Berkas, `answer_sheets`, `sheet_scans`, `question_items`, `savings_entries`, `handover_records`, `class_contribution_kinds`, `class_contributions`, status leger dengan konfirmasi dan log buka kunci, pengaturan retensi dan model AI dengan log, pengumuman staf dengan target, status permintaan bantuan; kolom baru seperti `calendar_events.class_id`, `journals.status` dengan `partial` dan alasan tidak terlaksana, `school_print_settings.margin_profiles`, `schools.district`, status pengajuan di `supervisor_assignments`, dan penanda tutup di `academic_years`. Peran internal menambah `staff_role_assignments` (D-339).

### 6.4 Tabel versi, sekarang dan rencana

| Area | Sekarang | Rencana |
|---|---|---|
| Kurikulum dan aturan | `curriculum_versions` (code, status, meta); penunjuk konfigurasi untuk alokasi | `effective_on`, `applies_from_school_year`, `supersedes_id` sebelum P5-04 (D-355); lalu `regulations`, `regulation_relations`, `rule_sets`, `rules` (D-354) |
| Mapel | Global, tidak berversi | `since_rule_set`, `retired_in_rule_set` (`policy-innovation-layers.md` §6.2) |
| Templat | — | `template_versions`, tidak bisa diubah setelah dipakai (D-013) |
| Dokumen | — | Menyimpan versi templat dan kode set aturan; `document_revisions` (5 terakhir, yang disahkan tetap disimpan) |
| Pertemuan | — | `meeting_schedule_history` untuk pembatalan (D-274) |
| Jenjang | `schools.level` | Profil Jenjang berversi (D-356) |
| Taksonomi | — | Kode di `docs/data/taxonomy/` dengan `since` dan `retired_in` (D-357) |

Bacaan lanjutan: `data-model.md`, `coding-standards.md` §4, `docs/drafts/legacy-upgrade-01.md` §4, D-007, D-085, D-165, D-182, D-226, D-240, D-241, D-247…D-249, D-354…D-357.

## 7. Lapisan AI dan perlindungan data anak

### 7.1 Aturannya dalam satu daftar

1. **Hanya Gemini tingkat gratis, kunci milik guru.** Setiap guru menambahkan satu kunci atau lebih dari project Google miliknya. Tidak ada kunci sekolah atau platform, dan biaya AI tidak pernah menjadi biaya SIGRAK (D-012, D-023).
2. **Hanya orang dewasa.** Pengguna AI adalah guru. Murid tidak pernah memakai atau memicu AI, dan situs ujian, Tayangkan, serta game yang diunduh hanya menyajikan konten jadi yang sudah diperiksa guru (D-027, D-124).
3. **Tidak ada data pribadi murid di prompt mana pun:** tidak ada nama, NISN, tanggal lahir, kehadiran, catatan, agama, data kesehatan, atau nilai perorangan yang terkait identitas. `app/AI/Privacy/PromptDataGuard` memeriksa setiap request yang disusun dari data sekolah (D-023, D-036, D-215, D-347, D-350).
4. **Templat, bukan prompt.** Guru menjawab pertanyaan pedagogis; instruksi hanya berasal dari templat; catatan guru dan teks kurikulum masuk ke blok data yang dibatasi; keluaran hanya diterima lewat validasi skema (D-024).
5. **Guru yang memutuskan.** AI menyarankan; setiap hasil bisa diubah; AI tidak pernah menilai anak sendirian (D-347).

### 7.2 Kunci dan rotasi

- **Penyimpanan.** Rahasia kunci memakai cast `encrypted` Laravel; sidik jari (HMAC-SHA256) mendeteksi kunci ganda; hanya empat karakter terakhir yang ditampilkan. Kunci hanya bisa ditulis: tidak pernah ada di props Inertia, tidak pernah di log, hanya didekripsi di `ApiKeyManager` dan `GeminiClient`, hanya dikirim sebagai header `x-goog-api-key`, tidak pernah di URL.
- **Kuota berlaku per project Google dan per model,** dan kuota harian diatur ulang pada tengah malam waktu Pasifik (pukul 14.00 atau 15.00 WIB). Dua kunci dari satu project berbagi satu kuota.
- **Persetujuan.** Kunci kedua dan seterusnya membutuhkan centang persetujuan yang jelas, karena memutar beberapa project gratis bisa dianggap mengakali batas; risikonya ditanggung guru. SIGRAK tidak pernah membuatkan project atau kunci untuk guru (D-023).
- **Pemilihan.** Round robin di antara kunci guru yang bisa dipakai, disaring menurut status dan masa jeda per model, dengan kunci yang menempel per pembuatan konten, pindah kunci untuk galat tertentu, circuit breaker per kunci, dan kunci cadangan paling akhir (D-119). Paling banyak 2 pembuatan konten berjalan bersamaan per guru.
- **Penghitung harian.** SIGRAK menghitung permintaan setiap kunci per model per hari di Redis dan melewati kunci yang sudah mencapai batas sampai diatur ulang; *Kunci AI Saya* dan wizard menampilkan "sekitar N RPM lagi hari ini" (D-128). Galat 429 yang tetap muncul mendapat masa jeda 60 detik.
- **Galat** diklasifikasikan sekali (batas per menit atau per hari, request salah, project tidak tersedia, kunci tidak valid, model tidak tersedia, sementara, habis waktu, diblokir, keluaran rusak), dan setiap kelas punya satu tindakan. Paling banyak 4 percobaan dan 5 menit per langkah.
- **Duplikat.** Gemini tidak punya kunci idempotensi, jadi saat habis waktu dicoba sekali lagi dengan kunci yang sama sebelum pindah kunci, setiap percobaan dicatat, keluaran sebuah langkah diterima tepat sekali, dan klik ganda dicegah dengan token klien.

### 7.3 Tingkat gratis dan model

Model bawaannya Gemini 3.8 Flash; batas tingkat gratisnya saat diperiksa adalah 5 permintaan per menit, 250.000 token per menit, dan 20 permintaan per hari per project (D-117). Templat menyebut **tujuan** (misalnya `document_long`, `repair`), tidak pernah nama model. `config/ai.php` menyimpan peta awal tujuan ke model; admin mengatur model aktif dan urutan cadangan per tujuan di konsol, dengan pencatatan (D-315). `temperature`, `top_p`, dan `top_k` tidak dikirim. Pembuatan konten memakai panggilan sesedikit mungkin: tanpa ping latar belakang, tanpa pembuatan ulang otomatis, hanya model stabil.

### 7.4 Kapan AI yang berkaitan dengan murid diizinkan

| Pemakaian | Masukan yang diizinkan |
|---|---|
| Draf deskripsi rapor | Tingkat penguasaan per TP untuk "Murid A" yang anonim; nama digabungkan di server sesudahnya |
| Draf catatan wali kelas | Jumlah kehadiran, jenis catatan, hasil per TP; tanpa nama atau data pribadi (D-213) |
| RPM berikutnya setelah analisis | Jumlah anggota kelompok per TP, tidak pernah nama (D-295) |
| Tindak lanjut Game kelas | TP dan pola jawaban salah kelas itu (D-172) |
| Bantuan menilai uraian | Hanya teks uraian, tanpa data murid apa pun; guru yang menentukan nilai (D-347) |
| Label miskonsepsi | Dibuat saat pengecoh ditulis, sebelum ada murid yang menjawab |

Nama di kertas dibaca di perangkat guru, tidak pernah oleh penyedia AI (D-347). Membaca tulisan tangan atau suara dengan AI datang nanti.

### 7.5 Pemakaian dan visibilitas

`ai_usage_logs` punya satu baris per percobaan (pengguna, sekolah, id kunci, pembuatan konten, langkah, model, tujuan, hasil, token, latensi), tidak pernah berisi kunci dan tidak pernah berisi teks prompt atau respons. Baris rinci disimpan 1 bulan lalu dipangkas; agregat bulanan tetap disimpan (D-118). Guru melihat status setiap kunci dan kegagalan terbarunya; kepala sekolah hanya melihat total per guru; Tim SIGRAK melihat galat menurut kelas dan templat di konsol.

### 7.6 Penyedia di konsol

Adapter khusus konsol untuk API yang kompatibel dengan OpenAI dan untuk satu penyedia komersial lain ada untuk pekerjaan Tim SIGRAK sendiri (D-326). Adapter ini tidak pernah mengubah model kunci guru. Lihat bagian 4.10.

**Status:** **Didesain** (P5-04). Belum ada yang dibangun di `app/AI`.

Bacaan lanjutan: `ai-provider-engine.md`, D-012, D-022…D-024, D-036, D-050, D-116…D-119, D-124, D-128, D-152, D-213, D-315, D-326, D-347, D-350.

## 8. Ekspor, lokalisasi, dan penulisan UI

### 8.1 UI berbahasa Indonesia lewat `lang/id`

- Semua teks yang dilihat pengguna ada di `lang/id/*.php`, satu file per area, dengan kunci berbahasa Inggris yang menyebut maknanya (`journals.saved`). Tidak ada string literal di PHP, Blade, atau Svelte, dan tidak ada `lang/en`; locale dan fallback sama-sama `id` (TR-1…TR-8).
- `common`, `nav`, `terms`, dan `enums` selalu dibagikan. Sebuah halaman menambah filenya dengan middleware rute `translations:<files>`; file khusus server (`export`, `mail`, `notifications`, `validation`) ditolak (D-097). Svelte membaca teks lewat satu helper `t('key', params)` (D-080).
- Glosarium istilah baku ada di `lang/id/terms.php`, dan sebuah tes menolak istilah lama yang dilarang di nilai mana pun (D-098). Perintah `lang:audit` melaporkan string yang hilang, tidak terpakai, dan tertulis langsung; kunci yang hilang membuat CI gagal (D-100).
- Pesan validasi berawal dari salinan sekali jadi file bahasa Indonesia `laravel-lang` (MIT), disunting sesuai gaya bahasa SIGRAK (D-101). Notifikasi database menyimpan kunci dan parameter, sehingga perubahan teks juga sampai ke notifikasi lama (D-102).
- **Dibangun:** middleware `translations:` (`ShareTranslations`), prop bersama, dan file `auth`, `account`, `school`, `classes`, `timetable`, serta `validation` yang sudah terisi.

### 8.2 Penulisan UI

SIGRAK berbicara seperti rekan guru yang cekatan dan sopan (sistem desain):

- Kata kerja di tombol ("Buat RPM", "Simpan jurnal", "Unduh PDF", "Tayangkan").
- Huruf kapital hanya di awal kalimat, tanpa tombol berhuruf kapital semua.
- Satu istilah untuk satu hal.
- Pesan galat menyebut apa yang terjadi dan apa yang harus dilakukan, dengan rincian teknis di balik "Lihat detail".
- Konfirmasi hapus menyebut nama butirnya dan akibatnya.
- "Anda" untuk semua peran.
- Tanpa emoji, dan tanpa "cerdas", "canggih", atau "AI" sebagai hiasan.
- **Tidak pernah meminta prompt.**

Istilah baku antara lain *Unduh* (bukan Download), *Buat* (bukan Generate), *Templat* (ejaan KBBI, D-151), *Kunci AI*, *Kop*, *Kabar*, *Pengingat*, *Simulasi TKA*, *Kartu login*, *Dokumen Saya*, *RPM lepas jadwal*. Keputusan yang lebih baru menambahkan *Kehadiran* (D-302), *murid* (D-210), *Berkas Administrasi*, *pengesahan dokumen*, *Sumbangan sukarela*, serta kata terlarang *tunggakan*, *kurang bayar*, *belum lunas*, *teguran*, dan *tanda tangan elektronik* (D-281, D-300, D-311).

Tampilannya seperti buku kerja guru yang rapi: permukaan polos, satu warna merek teal, garis tipis, Plus Jakarta Sans, ikon Lucide dengan teks, token sebagai CSS custom property yang dipetakan ke Tailwind. Setiap layar diperiksa pada lebar 360 px dan 1280 px; tabel berubah menjadi daftar kartu di bawah 640 px; area sentuh minimal 44 px; status tidak pernah hanya ditunjukkan dengan warna (SV-8…SV-10). Teks UI berasal dari spreadsheet parafrase berisi 1.499 string aplikasi lama, dilacak baris demi baris di `docs/data/ui-copy/key-map.csv` (D-099).

### 8.3 URL

Path yang dilihat orang berbahasa Indonesia sederhana dalam kebab-case (`/masuk`, `/lupa-kata-sandi`, `/dokumen/{document}/ubah`, `/kunci-ai/{credential}`); kata kerja resource-nya `buat` dan `ubah` lewat `Route::resourceVerbs`; nama rute, parameter, dan kode tetap bahasa Inggris (`login`, `documents.edit`). Path teknis yang tidak dibaca orang (`/up`, aset hasil build) boleh tetap bahasa Inggris (D-149).

### 8.4 Tanggal, angka, uang

Semua pemformatan lewat `App\Support\IndonesianFormat` (PHP) dan `resources/js/lib/format.ts` (TS), yang berbagi satu API dan satu set fixture tes (XP-26):

| Nilai | Keluaran |
|---|---|
| Tanggal | `31/10/2026` |
| Tanggal dalam kalimat | `31 Oktober 2026` |
| Jam | `14.00` ("WIB" hanya untuk kejadian luar seperti pengaturan ulang kuota dan jendela ujian, D-108) |
| Angka | `1.234.567,89` |
| Rupiah | `Rp1.234.567` (tanpa spasi, tanpa desimal, tidak dibuat oleh ICU, D-104) |
| Nilai | `76,67` |

### 8.5 PDF, DOCX, Excel, CSV

- **Umum (XP-1…XP-5).** Dirender dari JSON konten atau data kueri, tidak pernah dari tangkapan layar; di antrean `exports` bila bisa memakan lebih dari sekitar 2 detik; setiap label dari `lang/id/export.php`; tidak pernah memuat data lebih banyak dari yang bisa dilihat pengguna di aplikasi; agama hanya untuk yang berhak melihatnya.
- **PDF (Gotenberg).** Satu view Blade per PDF. Kertas A4 atau F4 (215 × 330 mm, D-109). Kop adalah **satu gambar yang diunggah** (bawaan sekolah, atau milik guru sendiri), diletakkan di atas halaman pertama, paling tinggi 4 cm (D-031); sekolah yang belum punya bisa menekan "Buat kop otomatis" (D-284). Fon cetak Arial atau Times New Roman, dirender sebagai Liberation Sans atau Serif; teks Arab dengan Noto Naskh Arabic; matematika dengan fon KaTeX (D-105, D-125). Nomor halaman "Halaman X dari Y"; penanda tangan berupa teks; judul tabel berulang di setiap halaman; tanpa watermark. Aturan cetak: sampul hitam-putih bersama untuk dokumen berbentuk buku, tiga profil margin yang bisa diubah sekolah ditambah opsi bolak-balik, lanskap untuk tabel dengan 9 kolom atau lebih, dan baris "NIHIL" untuk tabel kosong (D-283).
- **DOCX (PhpWord).** Tata letak milik SIGRAK per templat, dibuat di kode; kop di header halaman pertama; bahasa `id-ID`; gaya heading Word; tanpa templat Word unggahan (XP-15…XP-19).
- **Excel (Laravel Excel 4).** Judul kolom dan nama sheet berbahasa Indonesia; tipe sel asli (angka, tanggal Excel, "Ya"/"Tidak"); NISN, NIP, dan NPSN sebagai teks; sheet "Info" terpisah; perlindungan dari formula injection (XP-20…XP-23).
- **CSV.** Pemisah `;`, UTF-8 dengan BOM, CRLF, desimal koma; XLSX adalah bawaan (D-103).
- **Impor membaca kembali apa yang ditulis ekspor.** Judul kolom dicocokkan tanpa memedulikan huruf besar-kecil, spasi berlebih, `*` atau `:` di akhir, atau urutan kolom; label judul lama tetap berlaku selamanya sebagai alias; nilai berbahasa Indonesia ("Hadir", "Ya", `dd/mm/yyyy`, desimal koma) dibaca kembali menjadi nilai database berbahasa Inggris (D-150).
- **Nama file.** `<kind>-<context>-<date>.<ext>`, paling panjang 80 karakter, tanpa nama murid atau guru (`rapor-kelas-6a-no-07-semester-1.pdf`) (D-107, XP-31…XP-34).
- **Arab dan RTL.** `dir="auto"` dan `<bdi>` untuk teks campuran, blok RTL terpisah untuk `arabic_passages`, run RTL di DOCX, urutan baca RTL di Excel (LOC-32…XP-38).

**Dibangun:** ekspor Excel Sebaran sekolah (`app/Exports/SchoolDirectoryExport`). Selebihnya di bab ini **Didesain** dan datang mulai P5-04.

Bacaan lanjutan: `export-localization.md`, `design-system.md`, `coding-standards.md` §7–§8, D-031, D-097…D-109, D-125, D-149…D-151, D-280, D-283, D-284.

## 9. Infrastruktur dan operasional

### 9.1 Produksi

| Butir | Pilihan | Keputusan |
|---|---|---|
| Server | IDCloudHost Cloud VPS, Jakarta, 8 vCPU / 16 GB / NVMe 320 GB | D-070, D-087, D-096 |
| Sistem operasi | Ubuntu Server 24.04 LTS, jam server dalam UTC | D-086, D-066 |
| Web | Paket Nginx Ubuntu di `127.0.0.1:8080` | D-061, D-088 |
| PHP | 8.5 (≥ 8.5.12) dari `ppa:ondrej/php`, PHP-FPM | D-056 |
| Database | MySQL 8.4 LTS dari repositori Oracle | D-007, D-057 |
| Cache, antrean | Redis 8.2, `noeviction`, AOF | D-058 |
| PDF | Gotenberg 8.39.0 (Chromium) di Docker | D-028, D-059 |
| Node | 24 LTS, hanya untuk build (sebaiknya di CI) | D-060 |
| Domain | `sigrak.com`: `app.`, `ujian.`, `admin.`, `ssh.` | D-069 |
| Surel | SMTP Gmail dengan App Password, hanya transaksional | D-110 |

**Cloudflare Tunnel.** VPS **tidak punya IP publik**. Tunnel `cloudflared` yang dikelola lokal meneruskan `app.`, `ujian.`, `admin.`, dan `ssh.` ke localhost. UFW menolak semua lalu lintas masuk. SSH dan `admin.` berada di balik Cloudflare Access. Nginx memulihkan IP klien asli dari `CF-Connecting-IP`, yang hanya dipercaya dari 127.0.0.1. HTML yang membutuhkan login tidak pernah di-cache Cloudflare (D-088).

**Deploy.** Folder rilis (`releases/`, `shared/`, symlink `current`), menyimpan 5 rilis; PHP-FPM dimuat ulang di setiap deploy karena OPcache tidak memeriksa stempel waktu (D-063). Aset dibangun di CI atau di lokal. Setelah deploy yang membawa data rujukan baru, jalankan perintah impor (`curriculum:import`, `school-directory:import`). `.env` server membutuhkan `APP_HOST`, `EXAM_HOST`, `ADMIN_HOST`, kunci Gotenberg, dan kunci surel (D-073, D-164).

### 9.2 Staging

Staging adalah situs kedua di VPS produksi, dipisahkan dengan pengguna sistem, pool PHP-FPM, database dan pengguna database, nomor database dan prefiks Redis, serta program Horizon dan penjadwal sendiri. Staging memakai `staging-app.`, `staging-ujian.`, dan `staging-admin.sigrak.com` di tunnel yang sama di balik Cloudflare Access, hanya dengan data contoh. MySQL, Redis, dan Gotenberg dipakai bersama, jadi hanya rilis aplikasi yang diuji di staging; pembaruan sistem operasi dan layanan dilatih di VM sementara atau snapshot (D-095). Telescope boleh di staging, tidak pernah terbuka di produksi.

### 9.3 Cadangan

Setiap hari pukul 01.30 WIB: `mysqldump` ditambah tarball `storage/app`. Disimpan 14 hari di server dan 30 hari di luar server di Cloudflare R2 lewat remote `rclone` crypt yang terenkripsi dengan token khusus bucket. Uji pemulihan setiap bulan; pemulihan dari luar server dua kali setahun. `APP_KEY` disimpan di pengelola kata sandi, tidak pernah di cadangan (D-065, D-071). Skrip cadangan mengirim heartbeat saat mulai, berhasil, dan gagal (D-136).

### 9.4 Pemantauan

- **Healthchecks.io** (paket Hobbyist gratis) adalah layanan peringatan utama, sebagai dead man's switch berbasis push yang cocok untuk server tanpa IP publik. Ada 13 pemeriksaan; yang mendesak dikirim lewat Telegram dan surel, yang ringan hanya lewat surel. Peringatan hanya dikirim ke pemilik (D-129, D-130, D-144).
- Satu skrip, `/usr/local/sbin/sigrak-health`, berjalan setiap menit, setiap 15 menit, dan setiap hari; sebuah pemeriksaan harus gagal dua kali berturut-turut sebelum melapor (D-131).
- UptimeRobot (gratis) memeriksa `/up` di `app.` dan `ujian.` dari luar (D-132). Notifikasi Cloudflare Tunnel dikirim lewat surel (D-133).
- Ambang: mendesak bila disk ≥ 90%, RAM tersedia < 1,5 GB, ada OOM kill, atau Redis ≥ 90% dari `maxmemory`; ringan bila disk ≥ 80%, swap ≥ 50%, load15 ≥ 8, koneksi MySQL ≥ 160, Redis ≥ 70%, ≥ 5 job gagal dalam 15 menit, atau ada pembaruan keamanan yang tertunda (D-135). Staging hanya memberi peringatan lewat surel (D-138).
- Halaman *Kesehatan Sistem* di konsol akan menampilkan antrean, job gagal, disk, jumlah pembuatan konten, galat AI, dan cadangan terakhir; peringatan tetap diurus pemantauan server (D-316).

**Status:** P5-02 (penyiapan server, staging, dan pemantauan) masih **todo**: panduannya sudah final, tetapi VPS yang berjalan belum tercatat sebagai keluaran. Pemilik mengatur kunci surel server pada 2026-11-07 (hasil P5-03b).

### 9.5 Pengembangan lokal (WSL2)

Pengembang di Windows memakai WSL2 dengan Ubuntu 24.04, bukan Laragon, karena Horizon butuh `pcntl` dan `posix`, dan Ubuntu 24.04 sama dengan VPS (D-166). PHP, MySQL, Redis, dan Node berjalan di dalam Ubuntu; Gotenberg berjalan di Docker Desktop dengan integrasi WSL. Simpan kode di dalam Linux (`~/sigrak`), jangan di `/mnt/c`. Jalankan pertama kali: `composer install && npm ci`, lalu `php artisan key:generate && php artisan migrate --seed`. Sehari-hari: `composer dev` (serve di port 8000, `queue:listen`, `pail`, Vite); `php artisan horizon` untuk mencoba antrean bernama. Host lokalnya `app.`, `ujian.`, dan `admin.sigrak.localhost:8000`. Sebelum push, jalankan pemeriksaan yang sama dengan CI (bagian 10.5).

Bacaan lanjutan: `server-setup.md`, `server-monitoring.md`, `local-setup-wsl2.md`, D-056…D-073, D-086…D-088, D-095, D-096, D-110, D-129…D-138, D-166.

## 10. Cara kami bekerja

### 10.1 Fase dan tugas

Pekerjaan dibagi menjadi **fase** (`P0`, `P1`, …) dan setiap fase dibagi menjadi **tugas** (`P5-03`, `P5-03e`, …), cukup kecil untuk satu agen atau sesi, dengan masukan, keluaran, dan ketergantungan yang jelas. Tugas yang tidak saling bergantung berjalan paralel, sebagai sub-agen atau sesi terpisah; dua tugas tidak pernah mengubah file yang sama. Setiap tugas punya file di `docs/agent/tasks/` (dari `docs/templates/TASK.md`) (D-006, `WORKFLOW.md`).

| Fase | Isi | Status |
|---|---|---|
| P0 | Konvensi repositori | Selesai |
| P1 | Dokumen konsep produk | Selesai |
| P2 | Dokumen data dan teknis | Selesai |
| P3 | Dokumen final dan PDF berbahasa Indonesia, skrip build PDF | Selesai |
| P4 | Desain fitur tambahan dan tindak lanjut | Sebagian selesai, lainnya todo atau ditahan |
| P5 | Pembangunan v1 | P5-01 selesai; P5-03 terbuka untuk P5-03i dan P5-03j; sisanya todo |

### 10.2 File agen

- File aturan repositori di akar adalah titik masuk dan memuat aturan keras. `docs/agent/CONTEXT.md` adalah ringkasan stack dan konvensi. Baca keduanya, lalu `PROGRESS.md`, lalu file tugas Anda.
- `docs/agent/PROGRESS.md` adalah satu-satunya sumber kebenaran tentang apa yang sedang terjadi: fase, status tugas (`todo`, `in-progress`, `blocked`, `review`, `done`), pemilik, dan catatan serah terima singkat, yang terbaru di atas.
- `docs/agent/DECISIONS.md` adalah log yang hanya ditambah, satu entri per keputusan (`D-NNN`, status, tanggal, keputusan, alasan). Keputusan yang berubah ditulis sebagai entri baru yang menggantikan entri lama. Baca file ini bila sebuah pilihan menyentuh keputusan sebelumnya.
- `docs/agent/WORKFLOW.md` menjelaskan cara mengklaim, memecah, dan menyelesaikan tugas.

**Siklus tugas.** Klaim di `PROGRESS.md` (ubah menjadi `in-progress` dengan label pemilik Anda) → kerjakan, dengan hanya membaca yang dicantumkan tugas → catat keputusan baru di `DECISIONS.md` (dan `CONTEXT.md` bila stack atau konvensi berubah) → selesaikan dengan `review` atau `done`, isi bagian Result di file tugas, tambahkan catatan serah terima → setelah merge, hapus branch tugas. Tugas yang terhambat ditandai `blocked` beserta alasannya. File bersama hanya diubah sedikit, per baris.

### 10.3 Alur dokumen

| Tahap | Folder | Bahasa |
|---|---|---|
| Draf | `docs/drafts/` | Inggris |
| Final | `docs/final/` (disetujui pemilik, dipindahkan dengan `git mv`) | Inggris |
| PDF | `docs/pdf/` | Indonesia |

Terjemahan berbahasa Indonesia setiap dokumen final di-commit di `docs/pdf/src/<name>.md` (D-156). Saat dokumen final berubah, terjemahkan hanya baris yang berubah lalu bangun ulang dengan `node scripts/docs-pdf/build.cjs <name>` (Pandoc, lalu Chromium tanpa layar lewat Playwright, A4, footer "Halaman X dari Y"). Build gagal bila ada tabel atau blok kode yang lebih lebar dari halaman; `node scripts/docs-pdf/check.cjs` mendaftar sisa kalimat berbahasa Inggris. Blok kode, identifier, perintah, path, ID keputusan, URL, serta istilah Indonesia atau Arab dibiarkan apa adanya. Bahan pemilik di `docs/sources/` disimpan seperti diterima (dengan pengecualian merek D-360). Buku panduan ini diperbarui saat sebuah fase selesai atau saat keputusan besar mengubah alurnya (D-361).

### 10.4 Branch, identitas, atribusi

- **Satu branch: `main`.** Sebuah tugas boleh memakai branch berumur pendek bernama `<phase>/<task-id>-<slug>`; setelah di-merge, branch itu dihapus di remote dan di lokal. Di antara tugas, remote hanya berisi `main` (D-148).
- **Identitas pemilik.** Setiap commit ditulis dan di-commit sebagai `Arumi Studios <arumiforge@gmail.com>`; sebuah hook awal sesi di repositori mengaturnya di setiap sesi. Periksa `git config user.email` sebelum commit pertama (D-148).
- **Tanpa atribusi AI di mana pun** (D-001): tidak ada baris co-author atau sesi di commit, tidak ada di judul atau deskripsi PR, komentar, kode, atau dokumen. Aturan ini mengalahkan kebiasaan atribusi bawaan alat atau agen mana pun.
- **Merek lama.** Jangan pernah menyebut nama aplikasi pemilik sebelumnya; tulis "aplikasi lama" atau "the legacy app" (D-360).
- **Kebijakan bahasa.** Markdown repo, identifier kode, tabel, branch, dan commit dalam bahasa Inggris; URL dalam bahasa Indonesia; teks UI, ekspor, dan PDF yang dihasilkan dalam bahasa Indonesia; sumber dari pemilik disimpan seperti diterima.

### 10.5 Standar kode, tes, dan CI

Pokok-pokok dari `coding-standards.md`:

- **PHP.** `declare(strict_types=1)` di mana-mana; Pint (PSR-12); Larastan mulai level 6 (D-077); kode bertipe; enum backed untuk himpunan tetap; Form Request dan Policy di setiap aksi; controller tipis yang memanggil satu Action; `$fillable` eksplisit; `config()` alih-alih `env()`; tanpa N+1; tanggal lewat `now()` dan `CarbonImmutable`.
- **Database.** Satu tabel per migrasi; jangan pernah mengubah migrasi yang sudah ada di `main`; constraint `CHECK` dari enum; tanpa soft delete; seeder data rujukan berupa upsert yang idempoten.
- **Tenancy.** Setiap model milik sekolah memakai `BelongsToSchool` (tes arsitektur gagal bila tidak); `withoutSchoolScope()` hanya di `app/Filament/` dan di job atau perintah yang menyebut sekolahnya, masing-masing dengan komentar; `public_id` milik sekolah lain menghasilkan 404, bukan 403; satu tes IDOR per sumber daya dan aksi.
- **Svelte.** TypeScript, runes, satu halaman per rute, props dibangun di server (tidak pernah seluruh model), hanya `public_id`, tanpa `{@html}` pada teks pengguna atau AI, hanya token Tailwind, tanpa CDN.
- **Tes.** Pest dengan tes arsitektur (D-074) di MySQL 8, bukan SQLite (D-079). Tidak ada panggilan AI sungguhan; `Http::preventStrayRequests()` di test case dasar; fixture Gemini rekaman; Gotenberg dipalsukan di CI. Factory menghasilkan baris yang valid di dalam satu sekolah; nama palsu dari `fake('id_ID')`, tidak pernah data orang sungguhan. Perbaikan bug disertai tes yang gagal tanpa perbaikan itu.
- **CI.** GitHub Actions di setiap push dan pull request ke `main` (`.github/workflows/ci.yml`, D-084): layanan MySQL 8.4, PHP 8.5, Node 24, `composer audit`, `npm audit --omit=dev`, Pint, Larastan, Pest, ESLint, `svelte-check`, dan build. PR hanya di-merge bila hijau. Di lokal: `vendor/bin/pint --test && vendor/bin/phpstan analyse --memory-limit=1G && composer test`, lalu `npm run lint && npm run check && npm run build`.
- **Commit dan PR.** Bahasa Inggris, kalimat perintah, subjek paling panjang 72 karakter; satu tugas per branch dan PR; PR menjelaskan apa yang berubah, mengapa (ID tugas dan keputusan), bagaimana diuji, dengan tangkapan layar ponsel dan laptop untuk perubahan UI; jangan pernah meng-commit `.env`, kunci, data murid sungguhan, dump, atau hasil build.
- **Dependensi.** File lock di-commit; versi persis untuk pustaka front end yang memengaruhi keluaran; image Docker disematkan ke tag; dependensi baru butuh alasan, lisensinya, dan kondisi pemeliharaannya.

### 10.6 Aturan ringkasan riset

Sebelum riset apa pun (peraturan, studi lapangan atau jurnal, benchmark, ketentuan vendor), periksa `docs/final/research-digest.md`. Pakai temuan yang sudah tercantum dan ikuti tautannya; periksa ulang hanya bila temuannya sudah lama, ditandai sekunder atau belum terverifikasi, atau menyangkut uang, hukum, atau ketentuan vendor. Setelah meneliti, tambahkan temuan ke sana dalam bentuk yang sama (sumber, tanggal pemeriksaan, tingkat verifikasi, temuan, implikasi, keputusan, dokumen repo) dan simpan rinciannya di draf tugas (D-362).

Bacaan lanjutan: `docs/agent/WORKFLOW.md`, `docs/agent/PROGRESS.md`, `coding-standards.md`, `scripts/docs-pdf/README.md`, D-001, D-002, D-006, D-074…D-085, D-148, D-149, D-156, D-360…D-362.

## 11. Peta jalan

### 11.1 Fase 4: tugas desain dan data yang masih terbuka

| Tugas | Isi | Status | Bergantung pada |
|---|---|---|---|
| P4-01 | Desain Buku Induk dan sinkronisasi Dapodik | Terhambat, ditahan sebagai "segera hadir" (D-161) | Pemilik memulai lagi |
| P4-05 | Dokumen final dan glosarium untuk aturan 2025–2026 ("murid", rapor 12 komponen, ekskul, JTM) + PDF | Review (dikerjakan bersama di P4-24) | D-210…D-221 |
| P4-06 | Desain: perluasan asesmen dan rapor | Todo | P4-05 |
| P4-07 | Desain: perluasan kelas dan kewalikelasan | Todo | D-214…D-219 |
| P4-08 | Desain: administrasi kepala sekolah, operator, pengawas (deskriptor per butir) | Todo | P4-12 |
| P4-09 | Desain: pemetaan inti vs tambahan di konsol | Todo | D-211 |
| P4-10 | Desain: ekspor e-Rapor SD dan RDM | Todo | D-218, P5-08 |
| P4-11 | Desain: Penyusun KSP dan evaluasi KSP (setelah v1) | Todo | D-209, D-217 |
| P4-13 | Keputusan peningkatan aplikasi lama | Review | D-251 |
| P4-14 | Terapkan peningkatan aplikasi lama ke `docs/final/` + PDF | Review (dikerjakan bersama di P4-24) | P4-13 |
| P4-29 | Kurasi mapel inti SD (PP, BIN, MTK, IPAS) | Todo | P4-23 |
| P4-30 | Kurasi mapel SD lainnya (SB, PJOK, BIG, BJW, KTB, SSD, KKA) | Todo | P4-23 |
| P4-31 | Kurasi mapel agama, SD dan MI | Todo | P4-23 |
| P4-19 | Desain: lanskap asesmen (Peta Asesmen, label, wizard, Gelombang A) | Todo | D-344…D-351 |
| P4-20 | Terapkan D-329…D-351 ke `docs/final/` + PDF | Review (dikerjakan bersama di P4-24) | P4-16…P4-18 |
| P4-22 | Data awal taksonomi dan registri peraturan di `docs/data/taxonomy/` | Todo | P4-21 |
| P4-23 | Ambil dan ekstrak peraturan yang belum ada, verifikasi KMA 512/2026 | Todo | — |
| P4-24 | Terapkan fondasi kebijakan ke `docs/final/` + PDF | Review (dikerjakan bersama di P4-24) | P4-21 |

Yang sudah selesai di Fase 4: P4-02 (Game kelas), P4-03 dan P4-03b (dokumen resmi), P4-04 (fitur administrasi), P4-12 (instrumen supervisi), P4-16 (LKPD), P4-17 (peran internal), P4-18 (lanskap asesmen), P4-21 (fondasi kebijakan), P4-25 (buku panduan ini), P4-26 (ringkasan riset), P4-27 (penghapusan merek lama), P4-28 (dataset kurikulum SIGRAK), P4-15 (katalog SIGRAK).

Tugas pembaruan dokumen P4-05, P4-14, P4-20, dan P4-24 dikerjakan sebagai satu tugas (P4-24, 10 November 2026) dan menutup selisih antara dokumen final dan keputusan yang lebih baru (§12.1).

### 11.2 Fase 5: slice v1 menurut urutan

Pemilik menetapkan urutannya (D-162): fondasi, inti konten, templat tambahan, TKA, Di Kelas, Asesmen, portal, bisnis, setelah kerangka proyek dan server.

| Slice | Isi | Status | Bergantung pada |
|---|---|---|---|
| P5-01 | Kerangka proyek dan CI | Selesai | — |
| P5-02 | Penyiapan server, staging, dan pemantauan | Todo | — |
| P5-03 | Fondasi: autentikasi, undangan, sekolah dan peran, tahun dan kelas, impor kurikulum, jadwal, direktori sekolah, salin kelas | Selesai kecuali P5-03i, P5-03j | P5-01 |
| P5-03i | Kalender (D-252…D-261, D-323) | Todo | P5-03e |
| P5-03j | Penyusun draf jadwal (D-262) | Todo | P5-03i |
| P5-04 | Inti konten: mesin penyedia AI, mesin templat, RPM, Dokumen Saya, ekspor PDF/DOCX | Todo | P5-03 termasuk P5-03i, minimum D-355 |
| P5-05 | Bank Soal, LKPD, Quiz Battle | Todo | P5-04 |
| P5-06 | TKA: paket, situs ujian, sesi, kartu, token, hasil (musim 2028) | Todo | P5-05 |
| P5-07 | Di Kelas dengan sinkronisasi luring, Jurnal dari RPM | Todo | P5-03 (dan P5-04 untuk alur RPM) |
| P5-08 | Asesmen: rencana, nilai, analisis per TP, rapor, Pindai LJK | Todo | P5-05 |
| — | Ekspor e-Rapor SD dan RDM, tepat setelah P5-08 | Todo | P5-08 (D-218) |
| P5-09 | Portal: kepala sekolah, operator, pengawas; validasi dan komentar | Todo | P5-03 |
| P5-10 | Bisnis: uji coba, langganan, fitur tambahan, pembayaran manual, Mode Contoh | Todo | P5-03 |

```
P5-01 ─► P5-03 (a…h done) ─► P5-03i ─► P5-03j
                         │        │
                         │        └─► D-355 minimum ─► P5-04 ─► P5-05
                         │                                       │
                         │                     P5-06 ◄───────────┤
                         │                     P5-08 ◄───────────┘
                         │                       └─► e-Rapor / RDM export
                         ├─► P5-07 (needs P5-04 for RPM-driven journal)
                         ├─► P5-09
                         └─► P5-10
P5-02 runs in parallel with all of it.
```

Ringkasan diagram: setelah P5-01 dan P5-03a…h, jalurnya P5-03i lalu P5-03j; dari P5-03i juga menuju minimum versi D-355, lalu P5-04 dan P5-05; P5-05 membuka P5-06 dan P5-08; P5-08 diikuti ekspor e-Rapor/RDM. P5-07 (yang butuh P5-04 untuk jurnal dari RPM), P5-09, dan P5-10 bergantung pada P5-03. P5-02 berjalan paralel dengan semuanya.

### 11.3 Yang dibangun berikutnya

Selesai pada 2026-11-11: P5-03i (kalender), P5-03j (draf jadwal), minimum versi D-355 dan data Profil Jenjang (P5-03k, P5-03l), P4-22 (benih taksonomi, dalam tinjauan Tim Validasi), dan P4-23 (enam dari sepuluh peraturan yang belum ada; empat menunggu owner, P4-32).

1. **P5-04, inti konten,** dengan keputusan tambahan dari P4-13 (D-267, D-269, D-270, D-272…D-277, D-283…D-285) dan bagian konsol D-315 dan D-316, ditambah panel Dasar Kebijakan dan aturan Cek Keselarasan pertama pada RPM (D-358).
2. Secara paralel, pekerjaan data dan dokumen yang membuka jalan bagi slice berikutnya: kurasi kurikulum P4-29…P4-31, empat PDF peraturan yang belum ada (P4-32), serta tinjauan Tim Validasi atas katalog, taksonomi, kalender, dan data peraturan. Pertanyaan owner yang masih terbuka dikumpulkan di `docs/drafts/open-questions.md`.

Tambahan per slice dari peningkatan aplikasi lama tercantum di `legacy-upgrade-01.md` §5: P5-05 mendapat D-293, D-294, D-296; P5-07 mendapat D-263…D-271 (sisi guru), D-278, D-280, D-289, D-297…D-303, D-321; P5-08 mendapat D-286…D-292, D-295, D-301, D-304; P5-09 mendapat tampilan portal serta D-279, D-281, D-282, D-304 (mutasi), D-306…D-313; konsol mendapat D-314 dan D-317…D-320.

Bacaan lanjutan: `docs/agent/PROGRESS.md`, `docs/agent/tasks/P5-03-foundation.md`, `docs/drafts/legacy-upgrade-01.md` §5, D-162, D-252, D-355.

## 12. Peta keputusan

Keputusan disimpan di `docs/agent/DECISIONS.md`. Rentang di bawah mengelompokkannya menurut topik. D-185…D-202 tidak dipakai dan D-235…D-239 dicadangkan.

| Rentang | Topik | Dalam satu baris |
|---|---|---|
| D-001…D-006 | Aturan repositori | Tanpa atribusi AI, kebijakan bahasa, stack inti, alur dokumen |
| D-007…D-037 | Fondasi produk | MySQL, Svelte, cakupan, pengguna, game, kunci guru, templat, ujian, dokumen, agama |
| D-038…D-047 | Data kurikulum dan TKA | Alokasi MI, bentuk CP MI, kategori TKA, bentuk soal, penilaian |
| D-048…D-055 | Arsitektur | Satu codebase, tiga host dan guard, modul, job, Redis, batas ujian |
| D-056…D-073 | Server | PHP 8.5, MySQL 8.4, Redis 8.2, Gotenberg, deploy, cadangan, domain, VPS |
| D-074…D-085 | Kode dan CI | Pest, TypeScript, runes, Larastan, Action, tes MySQL, CI, migrasi maju |
| D-086…D-096 | Pilihan pemilik | Ubuntu 24.04, ukuran VPS, tunnel, TKA sebagai latihan, Mode Contoh, pembayaran manual, PWA, staging |
| D-097…D-110 | Lokalisasi dan cetak | Grup terjemahan, glosarium, CSV, rupiah, fon, F4, nama file, surel |
| D-111…D-128 | Tinjauan data dan AI | Kompetensi AN, Tim Validasi, penyimpanan, model, kunci cadangan, persetujuan, KBC, penghitung harian |
| D-129…D-147 | Pemantauan dan data agama | Healthchecks.io, ambang, versi TP/ATP agama dan MI |
| D-148…D-156 | Identitas repo dan dokumen | Hanya `main`, penulis pemilik, URL berbahasa Indonesia, impor, Templat, revisi, terjemahan |
| D-157…D-162 | Bisnis dan urutan | Harga, fitur tambahan, uji coba, Buku Induk ditahan, urutan pembangunan v1 |
| D-163…D-170 | Kerangka dan fondasi | Ziggy, host dari env, WIB, WSL2, peran staf, cakupan pengawas, direktori sekolah |
| D-171…D-181 | Game kelas | Mode IFP, hasil kelas, Kode Tayang, Kartu Jawab, templat, paket konten |
| D-182…D-184 | Autentikasi | Undangan, kata sandi, akun pengawas nanti |
| D-203…D-209 | Dokumen resmi 2025 | Data peraturan, konten pilihan, baris rapor, Fase A, 7 KAIH, KKA, KSP |
| D-210…D-221 | Fitur administrasi | Ikuti aturan 2025–2026, fitur inti, JTM, rapor, kewalikelasan, portal |
| D-222…D-225 | Supervisi guru | 18 instrumen, skala, observasi hanya oleh kepala sekolah, umpan balik murid |
| D-226…D-234 | Impor kurikulum | Tabel CP, mapel, aturan impor, mapel kelas, muatan lokal, path Kode Tayang |
| D-240…D-251 | Tahun ajaran dan jadwal | Penugasan, tahun, kelas, salin kelas, aturan jadwal |
| D-252…D-262 | Kalender | Libur, kalender wilayah, kegiatan kelas, hari efektif, bentrok, draf jadwal |
| D-263…D-277 | Kelengkapan dan alur RPM | Satu layanan kelengkapan, hasil jurnal, neraca JP, kesiapan, pergeseran |
| D-278…D-285 | Berkas dan cetak | Berkas Administrasi, penggabungan, pengesahan, aturan cetak, kop otomatis, bunyi |
| D-286…D-296 | Asesmen dan rapor | Pindai LJK, cakupan luring, bobot, remedial, kunci leger, Bank Soal, blok LKPD |
| D-297…D-307 | Kelas Saya | Piket, kas kelas, sumbangan, tabungan, Kehadiran, draf, katalog, perpindahan |
| D-308…D-313 | Portal supervisi | PKKS, pengajuan sekolah binaan, tindak lanjut, Pengingat, saran nilai |
| D-314…D-321 | Konsol | Peran tetap, model AI per tujuan, kesehatan sistem, konsol kurikulum, retensi |
| D-322…D-328 | Penutup peningkatan aplikasi lama | Yang tidak dibawa, kalender Jawa Tengah, instrumen daerah, tata letak LJK, adapter konsol |
| D-329…D-337 | LKPD | Pola bertahap, QR, sesi murid nanti, Gelombang 1, konteks lokal, embed |
| D-338…D-343 | Peran internal | Cakupan, sebelas peran, pembayaran, akses support, mitra, 2FA |
| D-344…D-351 | Lanskap asesmen | Peta Asesmen, instrumen, AI tanpa data murid, GPK, luring, Gelombang A |
| D-352…D-359 | Fondasi kebijakan | Posisi, kekuatan aturan, versi, Profil Jenjang, taksonomi, panel, KMA 512 |
| D-360…D-362 | Repositori dan dokumen | Merek lama dihapus, buku panduan ini, ringkasan riset |
| D-363…D-367 | Kurikulum dan katalog SIGRAK | Satu format kurikulum, kurasi per mapel, crosswalk, alokasi SD dari struktur, katalog |
| D-368…D-372 | Jawaban pemilik 2026-11-10 | Kolom KBC untuk SD mati secara bawaan, OMR di peramban, jawaban tulisan tangan dibaca di ponsel, empat tingkat TKA, P4-13 ditutup |

### 12.1 Di mana dokumen final berbeda dari keputusan yang lebih baru

Saat ini tidak ada. Tugas konsolidasi dokumen P4-24 (yang menggabungkan P4-05, P4-14, dan P4-20) menerapkan semua keputusan yang lebih baru ke dokumen final pada 10 November 2026, termasuk daftar sebelumnya di bagian ini; daftar periksanya ada di `docs/agent/tasks/P4-24-final-docs-sync.md`. Ketidakselarasan soal tempat lembar jawaban dibaca sudah diputuskan oleh D-369 (di peramban guru). Bila keputusan baru mengubah dokumen final, perbarui dokumen itu dan PDF-nya dalam tugas yang sama; catat di sini celah yang masih terbuka.

## 13. Glosarium

Istilah pendidikan Indonesia dan istilah produk yang dipakai di buku ini dan di komentar kode. "SIGRAK" menandai istilah produk.

| Istilah | Arti |
|---|---|
| AKM | Asesmen Kompetensi Minimum, bagian literasi dan numerasi dari AN |
| AKMI | Asesmen Kompetensi Madrasah Indonesia (Kemenag); statusnya di 2026 belum terverifikasi |
| AN | Asesmen Nasional, survei sistem nasional, bukan nilai murid |
| ATP | Alur Tujuan Pembelajaran: urutan TP; di data berupa topik (materi pokok) |
| Berkas Administrasi | SIGRAK: bundel dokumen guru per semester (D-278) |
| BK | Bimbingan dan Konseling; catatan BK dibatasi |
| BOS | Bantuan Operasional Sekolah; tidak pernah menjadi sumber pembayaran SIGRAK |
| CP | Capaian Pembelajaran: capaian per mapel dan fase, ditetapkan secara nasional |
| Cuti bersama | Hari cuti bersama yang ditetapkan SKB; bawaannya dihitung libur (D-255) |
| Dapodik | Sistem data induk sekolah Kemendikdasmen untuk SD |
| DPL | Dimensi Profil Lulusan: 8 dimensi profil lulusan (DPL1…DPL8) |
| e-Rapor SD | Aplikasi rapor pemerintah untuk SD; SIGRAK mengekspor ke format impornya |
| EMIS | Sistem data induk madrasah milik Kemenag |
| Fase | A (kelas 1–2), B (3–4), C (5–6) |
| Fokus (materi) | Butir fokus di dalam sebuah topik, biasanya satu per minggu |
| GPK | Guru Pendamping Khusus; di SIGRAK berupa penugasan guru, bukan peran (D-348) |
| Guru kelas | Guru yang mengajar mapel kelas di satu kelas |
| Guru mapel | Guru satu mapel khusus di beberapa kelas |
| IFP | Interactive Flat Panel, layar sentuh di kelas |
| JP | Jam Pelajaran: satu periode pelajaran, 35 menit di SD/MI |
| JTM | Jam Tatap Muka: satuan beban kerja mingguan guru |
| Kabar | SIGRAK: kotak masuk berita dan notifikasi di aplikasi |
| Kaldik | Kalender pendidikan, kalender sekolah |
| KAIH | Gerakan 7 Kebiasaan Anak Indonesia Hebat: tujuh kebiasaan, yang ketujuh "Tidur Cepat" |
| KBC | Kurikulum Berbasis Cinta (KMA 512/2026), pendidikan nilai di madrasah |
| Kenaikan | Naik ke tingkat berikutnya, diputuskan sekolah |
| KKA | Koding dan Kecerdasan Artifisial; di SIGRAK lewat kokurikuler (D-208) |
| KKG / KKM | Kelompok kerja guru (Kelompok Kerja Guru / Madrasah) |
| KKTP | Kriteria Ketercapaian Tujuan Pembelajaran: kriteria tercapainya sebuah TP |
| Kokurikuler | Pembelajaran kokurikuler yang menguatkan DPL; menggantikan P5 |
| Kop | Kop surat; di SIGRAK berupa satu gambar yang diunggah |
| Korwil | Koordinator wilayah, peran pengawas wilayah di sekolah |
| KSP | Kurikulum Satuan Pendidikan: kurikulum sekolah, ditetapkan kepala sekolah |
| Leger | Daftar nilai kelas yang menjadi dasar rapor |
| LJK | Lembar Jawaban Komputer; *Pindai LJK* memindainya |
| LKPD | Lembar Kerja Peserta Didik: lembar kerja murid |
| MI | Madrasah Ibtidaiyah: sekolah dasar Islam di bawah Kemenag |
| Muatan lokal | Mapel muatan lokal (misalnya Bahasa Jawa) |
| Murid | Peserta didik; istilah UI resmi yang berlaku (D-210) |
| Mutasi | Perpindahan murid masuk atau keluar, atau antar-rombel |
| NISN, NIP, NPSN | Nomor induk murid nasional, nomor induk pegawai, nomor pokok sekolah |
| Operator | Staf sekolah yang mengelola data sekolah |
| Panca Cinta | Lima tema cinta dalam KBC |
| Pembelajaran Mendalam | Prinsip berkesadaran, bermakna, menggembirakan; pengalaman memahami, mengaplikasi, merefleksi |
| Pengawas | Pengawas sekolah yang membina beberapa sekolah |
| Pengesahan dokumen | SIGRAK: persetujuan internal kepala sekolah atas sebuah dokumen, dengan kode (D-281) |
| PG, PGK MCMA, PGK Kategori | Pilihan ganda; pilihan ganda kompleks; benar/salah per pernyataan |
| PKKS | Penilaian Kinerja Kepala Sekolah: supervisi kepala sekolah oleh pengawas |
| Prota, Promes | Program tahunan dan program semester |
| Rapor | Laporan hasil belajar |
| RDM | Rapor Digital Madrasah, aplikasi rapor Kemenag |
| Rombel | Rombongan belajar, kelompok kelas di Dapodik |
| RPM | Rencana pembelajaran SIGRAK (Rencana Pembelajaran Mendalam), pengganti RPP/modul ajar |
| RTL | Rencana Tindak Lanjut setelah supervisi |
| Ruang GTK | Platform kinerja guru dan tenaga kependidikan nasional |
| SAS, STS | Asesmen sumatif akhir semester dan tengah semester |
| SD | Sekolah Dasar di bawah Kemendikdasmen |
| Simulasi TKA | SIGRAK: ujian latihan TKA |
| SKB | Surat keputusan bersama menteri tentang libur dan cuti bersama |
| SKL | Standar Kompetensi Lulusan |
| Tayangkan | SIGRAK: mode tayang di proyektor atau IFP |
| Tim Validasi SIGRAK | Tim peninjau SIGRAK yang mengesahkan data kurasi (D-113) |
| TKA | Tes Kemampuan Akademik: tes akademik nasional untuk kelas 6 |
| TP | Tujuan Pembelajaran |
| Wali kelas | Guru wali sebuah kelas |

## 14. Bacaan lanjutan

**Dokumen final** (bahasa Inggris di `docs/final/`, PDF berbahasa Indonesia di `docs/pdf/`):

| Dokumen | Dibaca untuk |
|---|---|
| `product-concept.md` | Produk, peran, navigasi, alur, paket, status fitur |
| `content-engine.md` | Templat, wizard, pipeline, RPM, Bank Soal, LKPD, game, TKA, ekspor |
| `ai-provider-engine.md` | Kunci, pemilihan, galat, duplikat, privasi, log pemakaian |
| `data-model.md` | Tabel, kunci, invarian, tenancy |
| `architecture.md` | Komponen, modul, alur, deployment, keamanan |
| `design-system.md` | Token, komponen, gaya bahasa, istilah |
| `coding-standards.md` | PHP, database, tenancy, Svelte, tes, commit |
| `export-localization.md` | `lang/id`, aturan ekspor, pemformatan, RTL |
| `religious-education.md` | Enam agama SD, mapel MI, KBC |
| `ifp-classroom.md` | Game kelas dan IFP |
| `server-setup.md` | VPS, langkah demi langkah |
| `server-monitoring.md` | Pemeriksaan kesehatan dan peringatan |
| `local-setup-wsl2.md` | Pengembangan lokal di Windows |
| `research-digest.md` | Semua temuan riset sejauh ini; periksa sebelum meneliti (D-362) |

**Draf penting** (rincian desain di balik dokumen final; bagian yang diterima sudah masuk dokumen final):

| Draf | Isi |
|---|---|
| `policy-innovation-layers.md` | Lapisan, kekuatan aturan, versi, Profil Jenjang, taksonomi (D-352…D-359) |
| `legacy-upgrade-01.md` | Peta peningkatan aplikasi lama ke D-252…D-328, perubahan dokumen dan tugas |
| `administration-features.md` | Aturan 2025–2026 dan fitur administrasi (D-210…D-221) |
| `teacher-supervision-instruments.md` | 18 instrumen supervisi (D-222…D-225) |
| `assessment-landscape.md` | Peta Asesmen dan instrumen baru (D-344…D-351) |
| `lkpd-exploration.md` | Pola LKPD, blok, konteks lokal (D-329…D-337) |
| `internal-roles.md` | Peran staf, cakupan, matriks (D-338…D-343) |
| `dokumen-resmi-sd-mi.md` | Apa yang diubah dokumen resmi 2025 (D-203…D-209) |
| `buku-induk-dapodik.md` | Buku Induk dan sinkronisasi Dapodik, ditahan (D-161) |
| `curriculum-source-check-2026.md` | Temuan tentang file sumber kurikulum lama (riwayat) |
| `open-questions.md` | Pertanyaan yang masih menunggu pemilik |

**Data:** `docs/data/curriculum/README.md` (file kurikulum dan asal-usulnya), `docs/data/regulations/README.md` (aturan resmi 2025 sebagai data), `docs/data/catalogs/README.md` (katalog SIGRAK, D-367), `docs/data/schools/` (direktori sekolah), `docs/data/tka/` (kerangka TKA). Direncanakan: `docs/data/taxonomy/`, `docs/data/assessment/`, `docs/data/catalogs/`.

**File agen:** `docs/agent/CONTEXT.md`, `docs/agent/PROGRESS.md`, `docs/agent/DECISIONS.md`, `docs/agent/WORKFLOW.md`, dan file tugas di `docs/agent/tasks/`.
