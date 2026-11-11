# Game kelas: Pembelajaran dengan Interactive Flat Panel (IFP) untuk SD/MI

<p class="source-note">Diterjemahkan dari <code>docs/final/ifp-classroom.md</code> pada 10 November 2026.</p>

- **Status:** final (disetujui 2026-11-08); wawancara pemilik selesai 2026-11-04, keputusan D-171…D-181 dan D-233 sudah dicatat (belum masuk cakupan v1); keputusan berikutnya D-210, D-267, D-286, D-298, D-331, D-332, D-369 diterapkan 2026-11-10
- **Tugas:** P4-02
- **Terakhir diperbarui:** 2026-11-10
- **Nama di UI:** *Game kelas*. Add-on yang sudah ada (D-158) tetap memakai namanya dan berkembang menjadi mode IFP ini (D-171)
- **Cakupan:** SD dan MI, kelas 1–6 (Fase A–C). PAUD, SMP, dan SMA di luar cakupan.

## Tujuan

Sebagian besar sekolah kini punya Interactive Flat Panel (IFP, disebut juga *Papan Interaktif Digital* atau PID) dari program digitalisasi Kemendikdasmen. Kementerian menargetkan pengiriman ke lebih dari 288.000 sekolah paling lambat Desember 2025, dan daerah percontohan Kudus sudah menerima unitnya ([ANTARA, 2025-10-07](https://www.antaranews.com/berita/5159673/mendikdasmen-pengiriman-papan-interaktif-ditargetkan-selesai-desember)). Unitnya berupa panel Android all-in-one 75 inci dengan layar 4K UHD, multi-sentuh, speaker, dan mikrofon, dikirim bersama laptop dan hard disk eksternal berisi konten pembelajaran ([BBPMP Jatim](https://bbpmpjatim.kemendikdasmen.go.id/main/syarat-syarat-sekolah-penerima-ifp-dan-tipe-tipe-wilayahnya), [PAUDPEDIA](https://paudpedia.kemendikdasmen.go.id/), [detikEdu, 2025-10-23](https://www.detik.com/edu/sekolah/d-8174445/program-digitalisasi-pendidikan-berlanjut-dalam-5-tahun-sekolah-bakal-punya-6-ifp)). Pada 2025 setiap sekolah mendapat satu unit (149.268 dari sekolah sasaran adalah SD); kementerian menargetkan tiga unit per sekolah pada 2026, jadi satu panel masih dipakai bergantian oleh beberapa kelas. Dalam praktiknya panel sering dipakai sebagai TV besar untuk menayangkan slide.

Situs gratis seperti [papaninteraktif.com](https://papaninteraktif.com/) dan [game.digitaleducation.id](https://game.digitaleducation.id/) menunjukkan bahwa guru menginginkan aktivitas IFP yang siap dimainkan. Situs-situs itu populer, tetapi isinya katalog game: guru memilih game, memainkannya, dan tidak ada yang terhubung ke rencana pembelajaran, tujuan pembelajaran (TP), atau hasil kelas.

Dokumen ini memetakan cara kedua situs itu memakai IFP, menemukan apa yang kurang, dan mengusulkan fitur SIGRAK di mana **setiap aktivitas di panel berawal dari TP, berada di salah satu langkah RPM, dan memberi umpan balik ke asesmen dan pertemuan berikutnya**. Bermain adalah cara menyampaikan, bukan tujuannya.

Jawaban pemilik dari wawancara 2026-11-04 dicatat sebagai D-171…D-181 dan dicantumkan di bagian [Keputusan](#keputusan); jalur halaman Kode Tayang menyusul pada 2026-11-08 (D-233).

## 1. Pembanding: apa yang dilakukan kedua situs

Kedua situs ditinjau pada 2026-11-04. Keduanya **hanya rujukan**: SIGRAK membangun templat dan mekaniknya sendiri, tidak pernah menyalin game, nama, gambar, atau level mereka.

### 1.1 Papan Interaktif (MANDAI)

Portal gratis buatan seorang guru-pengembang perorangan ("Media Anak Nusantara Digital Aktif Inspiratif"). Portal ini mengklaim punya 42+ media, menjangkau 20.000+ sekolah, dan 4.000+ guru.

| Aspek | Yang ditawarkan |
|---|---|
| Jenjang | Dua mode: **UMUM** (SD–SMA) dan **JUNIOR** (PAUD/TK, SD kelas awal) |
| Kategori | Numerasi, Literasi, Bahasa Inggris, Logika, Ice Breaking, Agama, Olahraga |
| Contoh judul | Bidik-dan-tembak jawaban, latihan matriks dan pembagian (*porogapit*), domino matematika, garis bilangan, game pecahan, timbangan, arena bahasa Inggris, memancing kata, pasukan geometri, eksplorasi ruang 3D, game bertema Al-Qur'an, *Lomba 17an* |
| Ide khas IFP | Aktivitas berbasis kamera (basket AI dengan gerakan tubuh, gambar-dan-tebak dengan AI), unggah foto sendiri sebagai puzzle, papan mind mapping |
| Sisi guru | Tanpa login, tanpa pengaturan. Dinding komunitas ("Ruang Sapa Guru"), saluran WhatsApp, tautan jurnal guru |
| Kerangka pembelajaran | Diposisikan terutama sebagai **ice breaking** dan "belajar sambil bermain". Tanpa TP, tanpa peta kurikulum, tanpa hasil |

### 1.2 Game DigiEd (game.digitaleducation.id)

Bagian dari platform pendidikan komersial (PT Digital Education Indonesia), ditawarkan gratis dan tanpa login. Sekitar 70 judul, bisa disaring menurut jenjang (PAUD, kelas 1–2, 3–4, 5–6, SMP, SMA), mata pelajaran, dan jenis game.

| Aspek | Yang ditawarkan |
|---|---|
| Mata pelajaran | Matematika, B. Indonesia, B. Inggris, IPAS, PAI, IPS, PJOK, SBK, IPA, Koding & KA, dasar-dasar PAUD, pengetahuan umum |
| Kelompok mekanik | Lomba tim di layar terbagi (tarik tambang, panjat pinang, balap sepeda), latihan model runner/flappy/labirin, latihan target jatuh, pasangan memori, acak kata, teka-teki silang dan cari kata, permainan papan (ular tangga, beli kota), format kuis TV, game olahraga dengan kuis sebelum setiap servis |
| Simulasi dan lab | Jam analog dengan jarum yang bisa digeser, belanja di pasar dan uang kembalian, memasak dengan perbandingan dan *Isi Piringku*, habitat dan rantai makanan, organ tubuh manusia, peta Indonesia dengan provinsi dan budayanya, robot koding dengan urutan/perulangan/percabangan, Hijaiyah, Asmaul Husna |
| Mode bermain | 1 pemain, 2 pemain, 2–6 tim, seluruh kelas, atau melawan komputer |
| Dukungan belajar | Banyak game menampilkan **penjelasan setelah setiap jawaban**; tingkat kesulitan dari Mudah sampai Tantangan; panduan pakai "pilih game → atur cara main → main dan bahas" |
| Soal sendiri | **Kuis Kelas:** isi templat Excel, unggah, dan soal yang sama dimainkan sebagai tiga game berbeda (tic-tac-toe, kotak misteri, ular tangga). Bisa dibagikan ke murid lewat tautan WhatsApp yang berlaku 30 hari |
| Keterbatasan | Butuh internet untuk memuat; tanpa data murid; hasil tidak disimpan |

### 1.3 Pola umum yang layak dipertahankan

1. **Tanpa pengaturan.** Buka, pilih, main dalam satu menit.
2. **Bermain tim di satu layar.** Zona kiri/kanan terpisah agar dua kelompok bisa menyentuh layar bersamaan.
3. **Umpan balik langsung beserta alasannya.** Penjelasan setelah setiap jawaban itulah yang mengubah game menjadi pembelajaran.
4. **Satu set soal, banyak format.** Guru memakai ulang soalnya sendiri di berbagai mekanik.
5. **Target besar, tanpa mengetik.** Keypad khusus di layar, bukan keyboard bawaan panel.
6. **Simulasi dan alat peraga** (jam, uang, peta, organ tubuh) di mana panel menampilkan sesuatu yang tidak bisa ditampilkan papan tulis.

### 1.4 Yang tidak dimiliki kedua situs (celah yang diisi SIGRAK)

| Celah | Dampak di kelas | Jawaban SIGRAK |
|---|---|---|
| Tidak terhubung ke CP/TP atau rencana pembelajaran | Game hanya pengisi waktu, dipilih karena seru, bukan karena tujuan | Setiap aktivitas membawa `kode_tp` dan ditempatkan di langkah pertemuan RPM |
| Konten tetap dan umum | Bukan kelas, materi, atau jenjang milik guru | AI mengisi templat dari TP, Bank Soal, atau RPM guru (D-024) |
| Tidak ada catatan bagian yang salah dijawab kelas | Guru tidak bisa merencanakan remedial | Ringkasan hasil tingkat kelas per soal dan per TP (lihat §5) |
| Bingkai "ice breaking" | Kepala sekolah dan pengawas melihat bermain, bukan belajar | Setiap aktivitas menyatakan fungsi belajarnya dan fase pembelajarannya |
| Cakupan agama dan MI lemah (hanya Islam, kebanyakan topik SMP/SMA) | Murid MI dan murid non-Muslim di SD tertinggal | Konten dari data kurikulum SIGRAK: enam agama di SD dan lima mata pelajaran MI Kemenag |
| Tidak ada konten lokal | Bahasa Jawa (muatan lokal di daerah percontohan) tidak ada | Templat yang menerima konten Bahasa Jawa dan *aksara Jawa* |
| Hanya daring (DigiEd) | Gagal saat sinyal sekolah putus | *Unduh game* berupa HTML luring, bisa dimainkan dari browser IFP atau flashdisk (D-011) |
| Tidak terhubung ke TKA | Latihan kelas 6 terpisah dari game kelas | Game yang memakai bentuk soal TKA (PG, PGK MCMA, PGK Kategori) sebagai pemanasan |
| Tidak ada panduan menjalankan aktivitas | Guru mengulang game yang sama; murid pendiam tidak pernah menyentuh papan | Lembar panduan bawaan: durasi, peran, giliran, pertanyaan diskusi |

## 2. Kegunaan IFP dalam pembelajaran

Pakai panel untuk hal yang dilakukannya lebih baik daripada papan tulis atau lembar cetak. Game hanyalah satu dari lima kegunaan.

| # | Kegunaan | Fungsi belajar | Fase pembelajaran (RPM) | Contoh di SD/MI |
|---|---|---|---|---|
| 1 | **Memanipulasi** | Membangun konsep dari konkret ke abstrak (CPA) | Memahami | Menggeser blok puluhan, memotong pita pecahan, mengatur jarum jam, memilah makhluk hidup |
| 2 | **Menjelajah / menyimulasikan** | Melihat sebab-akibat, bertanya "bagaimana jika" | Memahami, Mengaplikasi | Daur air, rantai makanan, bayangan dan cahaya, skala peta |
| 3 | **Berlatih dengan umpan balik** | Kelancaran dan koreksi miskonsepsi | Mengaplikasi | Latihan tim di mana setiap pilihan salah menampilkan alasan kenapa salah |
| 4 | **Mengecek pemahaman** | Asesmen formatif, asesmen *untuk* pembelajaran | Pembuka, akhir pelajaran | Cek pengetahuan awal, tiket keluar, pertanyaan kunci di tengah pelajaran |
| 5 | **Berdiskusi dan merefleksi** | Menjelaskan penalaran, metakognisi | Merefleksi | Menampilkan dua jawaban kelas lalu membahasnya, papan suasana hati/refleksi, mind map |

Prinsip desain yang mengikuti pendekatan pembelajaran mendalam yang sudah dipakai di templat RPM (berkesadaran, bermakna, menggembirakan):

- **Berkesadaran:** layar menampilkan tujuan ("Hari ini kita belajar …") sebelum bermain, dan jeda singkat untuk berpikir sebelum jawaban dibuka.
- **Bermakna:** konteks dari kehidupan murid (pasar, ongkos angkot, panen, kalender hijriah), bukan hanya latihan abstrak.
- **Menggembirakan:** bermain tim dan bergerak, tanpa peringkat publik untuk murid perorangan.

## 3. Fitur: Game kelas

### 3.1 Konsep

*Game kelas* (D-171) adalah mode kelas layar penuh untuk IFP, dibangun di atas *Tayangkan* (D-011). Mode ini memutar **rangkaian pelajaran** yang diambil dari pertemuan RPM, bukan satu game saja:

```
RPM meeting (Kegiatan Pembuka → Inti → Penutup)
        │  teacher taps "Buka Game kelas"
        ▼
Game kelas playlist for today
  1. Tujuan hari ini        (goal screen, from TP)
  2. Cek awal               (2–3 quick items, prior knowledge)
  3. Alat peraga / simulasi (manipulative for the core concept)
  4. Latihan tim            (game template, items from Bank Soal or AI)
  5. Tiket keluar           (3 items, exit ticket)
  6. Refleksi               (prompt from the RPM)
        │
        ▼
Class summary (per item, per TP) → Jurnal (D-016) and Asesmen
```

Ringkasan diagram: dari pertemuan RPM, guru mengetuk "Buka Game kelas" dan mendapat daftar putar hari itu (tujuan dari TP, cek awal 2–3 soal, alat peraga, latihan tim dengan templat game, tiket keluar 3 soal, refleksi dari RPM). Hasilnya menjadi ringkasan kelas per soal dan per TP yang masuk ke Jurnal (D-016) dan Asesmen.

**Lanjutan (D-267).** Bila jurnal pertemuan sebelumnya menyatakan *terlaksana sebagian* (`partial`), pengalaman belajar yang belum selesai (memahami, mengaplikasi, merefleksi) membuka daftar putar sebagai blok "Lanjutan", sebelum *Tujuan hari ini*, sama seperti blok itu mengawali pertemuan berikutnya di RPM.

Guru tetap bisa membuka satu game saja dari *Buat*; daftar putar adalah jalur yang dianjurkan.

### 3.2 Templat aktivitas (katalog untuk SD/MI; urutan pembuatan di D-176)

Semua templat mengikuti aturan hibrida: runtime memegang struktur, AI hanya mengisi konten dan konfigurasi yang diizinkan (D-015). Nama-nama di sini adalah nama kerja. Mekaniknya adalah format kelas yang umum, bukan salinan judul pembanding.

**Latihan dan pengecekan (memakai ulang soal Bank Soal):**

| Templat | Mekanik di IFP | Fungsi belajar | Jenis soal | Kelas |
|---|---|---|---|---|
| Quiz Battle (v1, D-014) | Tim menjawab bergiliran, pengatur waktu, penjelasan | Latihan, pengulangan | PG, Benar-Salah | 1–6 |
| **Lomba Tim** | Layar terbagi, 2 tim menjawab bersamaan; jawaban benar memajukan penanda tim | Latihan kelancaran dengan umpan balik | PG, Benar-Salah, jawaban angka singkat lewat keypad | 2–6 |
| **Papan Petak** | Papan petak (kisi atau jalur); menjawab berarti mengambil petak, deretan petak menang | Pengulangan campuran, strategi | PG, Benar-Salah | 3–6 |
| **Kotak Kejutan** | Pilih kotak bernomor, jawab, buka bonus atau tukar | Pengulangan yang menegangkan, cocok untuk 3–6 kelompok | Semua jenis yang bisa dinilai otomatis | 1–6 |
| **Pilah & Kelompokkan** | Geser kartu ke 2–4 wadah | Klasifikasi, batas konsep | Soal kategori (setara PGK Kategori) | 1–6 |
| **Pasangkan** | Tarik garis atau geser pasangan | Kosakata, istilah–definisi, gambar–kata | Menjodohkan | 1–6 |
| **Urutkan** | Geser kartu sesuai urutan | Urutan: peristiwa cerita, daur hidup, garis waktu SKI, langkah wudu | Mengurutkan | 2–6 |
| **Tiket Keluar** | 3 soal, kelas memilih per soal (lihat §6.2), tanpa pengatur waktu | Tiket keluar, cek formatif | PG, Benar-Salah | 1–6 |
| **Pemanasan TKA** | Stimulus di layar, tim menjawab PG/PGK MCMA/PGK Kategori | Membiasakan diri dengan bentuk soal TKA | Bentuk TKA dari Paket Soal TKA | 5–6 |

**Alat peraga dan simulasi (tanpa AI; guru memilih pengaturannya):**

| Alat | Konsep (mata pelajaran) | Kelas |
|---|---|---|
| Blok Puluhan | Nilai tempat, menyimpan dan meminjam (Matematika) | 1–3 |
| Garis Bilangan | Penjumlahan, pengurangan, bilangan negatif, pecahan pada garis | 1–6 |
| Pita Pecahan | Bagian yang sama besar, pecahan senilai, membandingkan | 3–6 |
| Jam Analog | Membaca waktu, lama waktu | 1–3 |
| Uang Rupiah | Membeli, uang kembalian, anggaran sederhana (Matematika, IPAS) | 1–4 |
| Bangun Datar & Jaring-jaring | Bangun, simetri, luas/keliling pada kisi, jaring-jaring bangun ruang (three.js, D-033) | 2–6 |
| Diagram Data | Membuat piktogram atau diagram batang dari data kelas (ApexCharts, D-033) | 2–6 |
| Peta & Skala | Peta Indonesia, provinsi, jarak dengan skala batang (IPAS) | 4–6 |
| Huruf & Suku Kata | Blok suku kata, menyusun kata (B. Indonesia) | 1–2 |
| Hijaiyah & Harakat | Bentuk huruf, harakat, latihan membaca (PAI, Al-Qur'an Hadis) | 1–4 |
| Aksara Jawa | Mencocokkan dan menebalkan huruf (Bahasa Jawa, muatan lokal) | 3–6 |

**Alat bantu kelas:** pengatur waktu dan stopwatch, pembuat kelompok acak dari daftar kelas, giliran ("siapa yang belum maju ke papan"), pemilih nama acak, papan kosong dengan latar kisi dan bergaris, lampu lalu lintas.

**Harga (D-175):** alat peraga dan alat bantu kelas tidak memakai AI dan menjadi bagian dari **Sigrak Guru** untuk setiap guru berbayar, sehingga nilai mode IFP terlihat tanpa add-on. Templat game, daftar putar, dan siklus belajar tetap ada di add-on **Game kelas**.

**Urutan (D-176):** setelah Quiz Battle siap untuk IFP, templat baru pertama adalah **Lomba Tim**, **Pilah & Kelompokkan**, dan **Pasangkan**. Daftar berdasarkan fungsi belajar ini menggantikan daftar mekanik "Drag & Drop, Word Puzzle, Maze, Flashcard" di D-014.

### 3.3 Peta cakupan menurut mata pelajaran dan fase

| Mata pelajaran | Fase A (1–2) | Fase B (3–4) | Fase C (5–6) |
|---|---|---|---|
| Matematika | Blok Puluhan, Jam Analog, Uang Rupiah, Lomba Tim | Pita Pecahan, Garis Bilangan, Diagram Data | Bangun & Jaring-jaring, Peta & Skala, Pemanasan TKA |
| Bahasa Indonesia | Huruf & Suku Kata, Pasangkan (gambar–kata) | Urutkan (alur cerita), Pilah (jenis kata) | Pemanasan TKA (literasi teks informasi/fiksi), Pilah (fakta/opini) |
| IPAS | Pilah (makhluk hidup/benda mati) | Urutkan (daur hidup), Uang Rupiah | Peta & Skala, Pasangkan (organ–fungsi) |
| Pendidikan Pancasila | Pilah (sikap baik/kurang baik) | Pasangkan (simbol–sila) | Urutkan, Papan Petak |
| Bahasa Inggris | Pasangkan (picture–word) | Lomba Tim (vocabulary) | Urutkan (sentence building) |
| PJOK | Kuis sebelum giliran bergerak (aturan, kesehatan) | Sama | Sama |
| Seni dan Budaya | Pasangkan (alat musik–daerah) | Urutkan (pola ritme) | Pilah |
| Bahasa Jawa (mulok) | Pasangkan (*tembung*) | Aksara Jawa | Aksara Jawa, Urutkan (*unggah-ungguh*) |
| Pendidikan Agama (enam agama, SD) | Pasangkan, Urutkan (doa, tata ibadah) | Pilah, Kotak Kejutan | Papan Petak, Urutkan |
| MI: Al-Qur'an Hadis | Hijaiyah & Harakat | Pasangkan (surah–isi) | Urutkan (ayat), Lomba Tim |
| MI: Akidah Akhlak | Pilah (akhlak terpuji/tercela) | Pasangkan (Asmaul Husna–arti) | Kotak Kejutan |
| MI: Fikih | Urutkan (wudu, salat) | Pilah (najis, hadas) | Urutkan (zakat, puasa) |
| MI: SKI | — (mulai kelas 3) | Urutkan (garis waktu) | Pasangkan (tokoh–peristiwa) |
| MI: Bahasa Arab | Pasangkan (mufradat–gambar) | Lomba Tim (mufradat) | Urutkan (*jumlah*) |

Konten selalu berasal dari data kurikulum SIGRAK (`docs/data/curriculum/`) dan TP milik guru, sehingga peta ini tumbuh bersama data, bukan dengan paket game buatan tangan.

## 4. Integrasi dengan modul SIGRAK yang sudah ada

| Modul | Cara Game kelas terhubung |
|---|---|
| **Buat → RPM** | Setiap langkah pertemuan bisa memuat aktivitas Game kelas. Model pembelajaran "Game Based Learning" di wizard RPM menyarankan satu aktivitas. PDF RPM mencetak nama dan tujuan aktivitas, bukan game-nya |
| **Bank Soal** | Templat latihan membaca soal dari set yang tersimpan (aturan sama dengan Quiz Battle: jenis soal yang bisa dinilai otomatis). Penjelasan pengecoh diambil dari *pembahasan* soal |
| **LKPD** | LKPD bisa diakhiri dengan "Lanjut di Game kelas" (kerja kelompok di kertas, dicek di panel), dan alat peraga bisa dicetak sebagai gambar lembar kerja. Langkah LKPD interaktif (embed, H5P, cek mandiri) berjalan daring di aplikasi guru dan di sesi tayang IFP; sesi murid di perangkat menyusul dengan tugas desainnya sendiri (D-331). Kode Kotak Terkunci bisa dicek di IFP (daring, D-332) |
| **Paket Soal TKA** | Pemanasan TKA memakai ulang soal dari sebuah paket tanpa memulai sesi CBT |
| **Hari Ini** | Pelajaran hari ini menampilkan tombol "Buka Game kelas" di samping pertemuan RPM |
| **Di Kelas → Jurnal** | Setelah sesi, entri jurnal terisi otomatis: aktivitas yang dijalankan, waktu yang dipakai, ringkasan hasil kelas (sinkronisasi luring D-016 berlaku untuk jurnal, bukan untuk game). Pertemuan yang ditandai `partial` di jurnal mengisi blok Lanjutan pada daftar putar berikutnya (D-267) |
| **Asesmen** | Ringkasan kelas per TP adalah catatan formatif. Ringkasan ini bisa dilampirkan ke rencana asesmen sebagai bukti *asesmen formatif*; nilai per murid hanya ada jika guru memakai cara merespons di §6.2 |
| **Dokumen Saya** | Aktivitas dan daftar putar adalah dokumen dengan status validasi dan komentar, sama seperti konten lain |
| **Portal Kepala Sekolah / Pengawas** | Laporan penggunaan untuk kepala sekolah dan pengawas (D-178): seberapa sering Game kelas dijalankan per kelas dan mata pelajaran, dan TP mana yang dilatih. Berguna untuk supervisi dan untuk melaporkan pemakaian IFP sekolah |
| **Bisnis (D-158, D-159, D-175)** | Mode IFP adalah isi add-on **Game kelas**. Alat peraga dan alat bantu kelas ada di Sigrak Guru. Uji coba guru 7 hari tetap berlaku |

## 5. Siklus belajar: dari bermain ke bukti

Game saja tidak menunjukkan apa pun kepada guru begitu layar ditutup. Game kelas **selalu menyimpan** ringkasan kecil **tingkat kelas** di sesi guru (D-172); guru bisa menghapusnya:

| Data yang disimpan | Tingkat rincian | Data murid? |
|---|---|---|
| Soal yang ditampilkan, benar/salah per percobaan | Per soal, per tim | Tidak |
| Pilihan salah yang paling banyak dipilih per soal | Per soal | Tidak |
| Hasil per TP (persentase benar) | Per TP, per kelas, per tanggal | Tidak |
| Waktu yang dipakai per aktivitas | Per aktivitas | Tidak |

Dari ringkasan ini SIGRAK menampilkan, setelah pelajaran:

1. **"Perlu diulang"**: TP atau soal di bawah ambang batas (misalnya di bawah 60% benar), beserta jawaban salah yang paling sering dan penjelasannya.
2. **Saran tindak lanjut** (AI, hanya dari TP dan pola jawaban salah, tanpa data murid): aktivitas remedial singkat untuk pertemuan berikutnya, yang bisa dimasukkan guru ke pertemuan RPM berikutnya dengan satu ketukan.
3. Teks **Jurnal**, terisi otomatis.

Ini menghormati aturan mode tayang bahwa nilai tinggal di sesi guru dan tidak ada data murid yang disimpan (D-011): ringkasannya tentang soal dan tim. Data per murid hanya didapat dari Kartu Jawab (§6.2, D-174), pada tahap selanjutnya.

## 6. Persyaratan desain khusus IFP

### 6.1 Layar, sentuhan, dan perangkat

| Persyaratan | Alasan |
|---|---|
| Tata letak lanskap 16:9 pada 1920×1080 dan 3840×2160; tampilan potret hanya sebagai cadangan untuk ponsel | IFP adalah panel lanskap 65–86 inci |
| Target sentuh minimal 80 px pada 1080p, teks minimal 32 px; konten utama di dua pertiga bagian bawah | Murid kelas 1–2 bertubuh pendek dan berdiri dekat layar |
| Multi-sentuh: 2–4 zona terpisah (layar terbagi), masing-masing dengan masukannya sendiri | Tim bermain bersamaan tanpa saling merebut sentuhan |
| Tanpa hover, tanpa klik kanan, tanpa menu tekan-lama, tanpa keyboard sistem (keypad khusus) | Keyboard layar bawaan panel menutupi game |
| Berjalan di browser/WebView Android bawaan IFP, tidak hanya di Chrome desktop | Unit Kemendikdasmen memakai Android; perangkat uji rujukan adalah unit tersebut (D-179). Versi Android dan WebView-nya tidak dipublikasikan, jadi cek pada unit asli di sekolah percontohan sebelum membangun |
| Suara lewat speaker panel dengan tombol bisu global; bermain tidak membutuhkan suara | Kebisingan kelas dan murid dengan gangguan pendengaran |
| Luring: setiap templat juga bisa dipakai sebagai HTML *Unduh game* (D-011) dan bisa dijalankan dari flashdisk | Panel bisa bekerja tanpa internet; koneksi sekolah sering putus |
| Tidak ada panggilan AI atau jaringan selama bermain | Permainan tidak boleh macet karena kuota Gemini (D-012) atau sinyal |

### 6.2 Perangkat bersama dan privasi

IFP adalah layar bersama di depan kelas. Mengetik kata sandi guru di layar itu berisiko (murid melihat, dan browser bisa tetap dalam keadaan masuk).

- **Kode Tayang (D-173).** Guru membuka halaman `/pid` di host aplikasi (D-233) pada IFP, yang menampilkan kode 6 digit atau QR. Guru mengonfirmasinya di ponselnya. IFP lalu mendapat sesi **khusus tayang**: hanya bisa memutar soal Game kelas hari ini dan langkah LKPD interaktif pelajaran hari ini (D-331) dan tidak lebih (tanpa daftar murid, tanpa dokumen, tanpa pengaturan). Sesi berakhir saat guru mengetuk *Selesai* di ponsel atau setelah batas waktu.
- **Ponsel sebagai remote (D-173).** Ponsel yang dipasangkan menampilkan tombol berikutnya/sebelumnya, buka jawaban, kunci jawaban, dan lembar panduan. Guru bisa berkeliling kelas, dan kunci jawaban tidak pernah muncul di layar besar.
- **Kartu Jawab (D-174, tahap terakhir).** Respons per murid tanpa perangkat murid. Setiap murid memegang kartu cetak dengan nomor dan empat sisi (A–D). Guru memindai kartu yang diangkat dengan kamera ponsel. Fitur ini memakai ulang kode kamera dan pencocokan daftar murid dari Pindai LJK (D-286), yang membaca lembar jawaban di browser guru dengan pustaka OMR JavaScript dan hanya mengirim jawaban serta tingkat keyakinannya ke server, tidak pernah foto (D-369). Aplikasi mencocokkan nomor kartu dengan daftar murid di ponsel guru, sehingga tiket keluar memberi hasil per murid tanpa ada murid yang menyentuh perangkat atau masuk akun. Nama tidak pernah tampil di IFP. Ini satu-satunya jalur dari game ke nilai per murid (§5), jadi dikerjakan setelah siklus tingkat kelas terbukti.
- Di IFP, hanya nama tim dan skor tim yang ditampilkan; tidak ada peringkat publik untuk murid perorangan.
- **Piket di layar siaga (D-298).** Bila sekolah menyalakannya (bawaannya mati), layar siaga Game kelas menampilkan kelompok dan tugas piket hari ini, hanya dengan nama depan atau nama pendek.
- Teks UI di panel dan ponsel memakai kata "murid", bukan "siswa" (D-210).

### 6.3 Inklusi

- *Mode Tenang:* tanpa pengatur waktu, tanpa eliminasi, tanpa layar "kalah"; cocok untuk kelas 1 dan untuk murid berkebutuhan khusus (D-155 mencakup sekolah inklusif).
- Pembacaan soal dengan suara untuk pembaca pemula, memakai audio yang dibuat sebelumnya atau mesin suara perangkat bila tersedia (tetap harus bisa dipakai tanpa suara saat luring).
- Tema kontras tinggi dan warna tim yang aman untuk buta warna (token sistem desain).
- Giliran dari daftar kelas agar murid pendiam juga mendapat giliran maju ke papan.

## 7. Batasan: belajar, bukan sekadar bermain

Setiap aktivitas Game kelas harus lolos pengecekan berikut sebelum bisa disimpan (aturan templat, dicek oleh kode):

1. Punya minimal satu `kode_tp` dalam cakupan guru.
2. Menyatakan fase pelajaran (pembuka, inti, penutup) dan durasi (bawaan 5–15 menit; lebih lama harus ada alasannya).
3. Setiap soal yang dinilai otomatis punya kunci **dan** penjelasan singkat; pengecoh sebaiknya mencerminkan miskonsepsi yang nyata.
4. Diakhiri dengan pertanyaan pembahasan ("Kenapa jawaban B salah?") yang tampil di layar.
5. Penilaian berbasis kecepatan nonaktif secara bawaan untuk kelas 1–3; ketepatan diutamakan.
6. Tanpa gambar AI (D-030); gambar berasal dari kumpulan aset bawaan.

## 8. Rekomendasi di luar pembanding

Ide yang tidak dimiliki kedua situs dan cocok dengan kekuatan SIGRAK:

1. **Mulai dari RPM, bukan dari katalog.** Tombol "Buka Game kelas" pada pelajaran hari ini menghilangkan keputusan "game yang mana?" dan menjaga aktivitas tetap pada TP. Ini pembeda SIGRAK yang paling jelas.
2. **Satu set soal, banyak format** (seperti kuis Excel DigiEd), tetapi diambil dari Bank Soal dengan `kode_tp`, sehingga hasil latihan selaras dengan asesmen.
3. **Pengecoh yang sadar miskonsepsi.** Saat AI menulis pilihan jawaban, setiap pilihan salah menyebut miskonsepsi yang diujinya, dan ringkasan kelas melaporkan miskonsepsi, bukan hanya skor.
4. **Kode Tayang + ponsel sebagai remote** (§6.2). Menyelesaikan masalah login di layar bersama yang dihindari situs gratis hanya dengan tidak punya akun.
5. **Kartu Jawab** (§6.2). Data formatif per murid di kelas yang muridnya tidak punya perangkat, yaitu sebagian besar kelas SD/MI.
6. **Paket mingguan offline (D-181, bersama inti).** Unduh semua aktivitas Game kelas minggu depan sebagai satu paket luring (satu HTML per aktivitas, atau satu halaman peluncur) untuk flashdisk.
7. **Paket MI dan agama dari data kurikulum.** Hijaiyah, dasar tajwid, doa harian, langkah wudu dan salat, garis waktu SKI, mufradat; serta paket setara untuk Kristen, Katolik, Hindu, Buddha, Khonghucu di SD. Kedua pembanding belum melayani ini dengan baik.
8. **Bahasa Jawa dan aksara Jawa** untuk daerah percontohan (Kudus, Jepara, Pati).
9. **Dukungan kokurikuler.** Diagram Data serta Peta & Skala memungkinkan kelas mengumpulkan dan menampilkan data proyeknya sendiri di panel.
10. **Pemanasan TKA kelas 6** dalam bentuk game, agar format PGK yang belum akrab sudah dilatih jauh sebelum sesi Simulasi TKA.
11. **Laporan penggunaan untuk kepala sekolah** (§4). Menunjukkan bahwa sekolah memakai IFP-nya untuk pembelajaran, yang membantu supervisi dan pelaporan program.
12. **Lembar panduan guru di setiap templat.** Panduan 5 baris (tujuan, peran, waktu, cara menggilir, pertanyaan pembahasan) yang tampil di ponsel, agar aktivitas berjalan baik sejak pertama kali.
13. **Berbagi di dalam sekolah atau KKG.** Guru bisa membagikan aktivitas Game kelas kepada rekan di sekolah yang sama (tanpa tautan publik, aturan D-010 tetap berlaku).
14. **Aktivitas kamera dan gerak di tahap akhir (D-180).** Papan Interaktif menunjukkan bahwa game gerak tubuh membuat murid antusias, tetapi butuh izin kamera dan pemrosesan yang lebih berat. Aktivitas ini datang paling akhir, dan hanya jika gerakannya bagian dari pembelajaran dan terikat pada TP (PJOK, menghitung lompatan), tidak pernah sebagai hiasan.

## 9. Pentahapan

| Tahap | Isi | Bergantung pada |
|---|---|---|
| 0 | Membuat Quiz Battle siap untuk IFP: persyaratan §6.1, mode tim layar terbagi, layar penjelasan dan pembahasan | P5-05 |
| 1 | Kerangka Game kelas: daftar putar dari pertemuan RPM, layar tujuan, tiket keluar, ringkasan kelas, jurnal terisi otomatis, alat bantu kelas, paket luring mingguan (D-181) | Tahap 0, P5-07 |
| 2 | Tiga templat baru (D-176): Lomba Tim, Pilah & Kelompokkan, Pasangkan | Tahap 1 |
| 3 | Kode Tayang dan ponsel sebagai remote | Tahap 1, tinjauan keamanan |
| 4 | Alat peraga Matematika Fase A–B (Blok Puluhan, Garis Bilangan, Pita Pecahan, Jam Analog, Uang Rupiah) | Tahap 1 |
| 5 | Keterhubungan dengan Asesmen, "Perlu diulang" dan saran tindak lanjut AI; laporan penggunaan untuk kepala sekolah | P5-08, P5-09 |
| 6 | Paket konten, pertama MI (Al-Qur'an Hadis, Bahasa Arab, Fikih, Akidah Akhlak, SKI) dan Bahasa Jawa dengan aksara Jawa (D-177); lalu enam agama di SD. Juga Pemanasan TKA, Urutkan, Papan Petak, Kotak Kejutan | Tahap 2–5, persetujuan data kurikulum (D-113) |
| 7 | Kartu Jawab (D-174), memakai ulang kode kamera dan pencocokan daftar murid dari Pindai LJK (D-286); aktivitas kamera yang terikat pada TP (D-180) | Tahap 1–6, Pindai LJK (P5-08) |

**Ukuran keberhasilan (uji coba):** persentase pertemuan RPM yang memuat aktivitas Game kelas; jumlah sesi per kelas per minggu; persentase sesi yang sampai ke tiket keluar dan pembahasan; penilaian guru atas "membantu murid paham" setelah memakai; waktu dari membuka RPM sampai aktivitas pertama tampil di panel (target di bawah 1 menit).

## Keputusan

Dicatat di `docs/agent/DECISIONS.md` setelah wawancara pemilik pada 2026-11-04.

| ID | Keputusan |
|---|---|
| D-171 | Add-on tetap bernama **Game kelas** dan menjadi mode pembelajaran IFP yang dijelaskan di sini |
| D-172 | Ringkasan hasil tingkat kelas dan tingkat soal (tanpa data murid) selalu disimpan dan mengisi Jurnal dan Asesmen; guru bisa menghapusnya |
| D-173 | Sesi khusus tayang Kode Tayang di IFP, ditambah ponsel sebagai remote |
| D-174 | Respons per murid lewat Kartu Jawab, pada tahap terakhir |
| D-175 | Alat peraga dan alat bantu kelas ada di Sigrak Guru; templat game tetap di add-on |
| D-176 | Peta jalan templat berdasarkan fungsi belajar menggantikan daftar lanjutan di D-014; pertama Lomba Tim, Pilah & Kelompokkan, Pasangkan |
| D-177 | Paket konten pertama: mata pelajaran MI dan Bahasa Jawa dengan aksara Jawa; enam agama di SD menyusul |
| D-178 | Laporan penggunaan untuk kepala sekolah dan pengawas |
| D-179 | Perangkat uji rujukan adalah unit IFP Kemendikdasmen |
| D-180 | Aktivitas kamera dan gerak hanya di tahap akhir dan hanya jika terikat pada TP |
| D-181 | Paket luring mingguan dirilis bersama inti Game kelas |
| D-233 | Halaman Kode Tayang di `/pid` pada host aplikasi (bukan subdomain `papan.`, bukan `/papan`) |
| D-210 | Teks UI memakai kata "murid" |
| D-267 | Pengalaman belajar yang belum selesai dari pertemuan `partial` menjadi blok Lanjutan di pertemuan berikutnya dan di Game kelas |
| D-286 | Kartu Jawab memakai ulang kode kamera dan pencocokan daftar murid dari Pindai LJK |
| D-298 | Piket di layar siaga Game kelas, sakelar sekolah bawaannya mati, hanya nama depan atau nama pendek |
| D-331 | Langkah LKPD interaktif berjalan daring di aplikasi guru dan sesi tayang IFP; sesi murid menyusul |
| D-332 | Kode Kotak Terkunci bisa dicek di IFP (daring) |
| D-369 | Pindai LJK membaca lembar jawaban di browser guru; hanya jawaban dan tingkat keyakinannya yang sampai ke server |

## Pertanyaan terbuka

1. Versi Android dan WebView IFP Kemendikdasmen di sekolah percontohan: perlu dicek pada unit asli (D-179). Siapa yang mengatur akses ke dua atau tiga panel?
2. ~~Nama host atau path untuk halaman Kode Tayang~~: selesai, `/pid` di host aplikasi (D-233).
3. ~~Dokumen final masih mencantumkan daftar game lama D-014~~: selesai, `product-concept.md` dan `content-engine.md` mengikuti D-176 (2026-11-08).
