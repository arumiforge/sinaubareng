# Rebranding dan Perombakan Fitur EDUNEXA

Oct 29, 2026 · @j

## Ringkasan

Rekomendasi utama: ganti nama menjadi **Sigrak** (bahasa Jawa: lincah, cekatan, bersemangat), dengan janji "administrasi beres sambil mengajar". Kedua domain sigrak.id dan sigrak.com masih kosong per 29 Oktober 2026. Aplikasi disusun ulang menjadi lima menu dari sisi guru: Hari Ini, Di Kelas, Rencana, Asesmen, dan Kelas Saya.

| Keputusan wawancara | Pilihan Anda | Dampak pada rebranding |
| --- | --- | --- |
| Jenjang | SD/MI dulu, SMP/MTs nanti | Nama tidak memuat kata "SD"; pilihan jenjang 7–12 disembunyikan sampai datanya siap |
| Wilayah | Kudus saja | Nama berbahasa Jawa aman; muatan lokal (Bahasa Jawa, kaldik Kudus) jadi bawaan |
| Kesan | Cepat dan cekatan | Nada bahasa ringkas, kata kerja di depan, alur paling banyak 4 langkah |
| Bahasa nama | Bebas, asal mudah diucap | Opsi campuran Jawa, Indonesia, Inggris |
| Warna | Teal tua #0F766E | Lanjut dari design system yang sudah disepakati |
| Logo | Simbol bermakna + wordmark | Simbol dari makna nama; final dikerjakan desainer |
| Model bisnis | Gratis dasar, per guru, per sekolah, per pengawas/gugus | Empat paket yang saling bertingkat |
| Pembeli | Kepala sekolah, pengawas/korwil, guru | Pesan berbeda untuk tiap pembeli; guru jadi pintu masuk, sekolah jadi pembayar |

Arah yang sudah disepakati tetap berlaku: kunci AI per guru, mode Di Kelas tanpa sinyal, PDF berbasis teks dengan pengaturan cetak per sekolah, tombol Buka di Canva, alur validasi materi guru → kepala sekolah → pengawas, dan migrasi ke CI4 + MySQL + vanilla JS. Konteks teknis ada di [Catatan Pengembang](https://claude.ai/artifact/WqzXi3r4pN89ZEHQChVbm8) dan [Pemetaan Data Materi](https://claude.ai/artifact/Qtp98W2tt6s5pezxXnTHKW).

## Rekomendasi nama brand

Pilihan pertama **Sigrak**, cadangan **Kapur** bila nanti ingin keluar dari Jawa Tengah. Status domain dicek lewat RDAP registri (Verisign untuk .com, PANDI untuk .id) pada 29 Oktober 2026; "kosong" berarti belum terdaftar, tetapi tetap harus dipastikan di registrar karena bisa saja domain premium.

| Nama | Arti | Alasan | Tagline | Domain | Risiko |
| --- | --- | --- | --- | --- | --- |
| **1. Sigrak** (disarankan) | Jawa: lincah, segar, cekatan, bersemangat | Paling pas dengan kesan "cepat dan cekatan"; dua suku kata, mudah diucap guru Kudus; terdengar ramah, bukan teknis | Kerja guru, lebih sigrak. | sigrak.id kosong, sigrak.com kosong | Kurang dikenal di luar Jawa; tidak ditemukan merek aplikasi serupa lewat pencarian web, tetapi PDKI belum dicek |
| **2. Kapur** | Indonesia: kapur tulis | Simbol paling akrab bagi guru; netral jenjang dan wilayah; mudah dibuat ikon (goresan kapur = tanda centang) | Sesederhana menulis di papan. | kapur.id kosong, kapur.com terdaftar sejak 1995 (tidak aktif) | Kata umum, jadi pendaftaran merek bisa lebih sulit; kapurguru.id baru didaftarkan pihak lain (28 Sep 2026); asosiasi "kapur barus" |
| **3. Rampung** | Jawa/Indonesia: selesai | Langsung menjual hasil: administrasi selesai | Administrasi rampung, waktu kembali ke murid. | rampung.id dan rampung.com terdaftar (tidak aktif); kelasrampung.id, kelasrampung.com, rampung.app kosong | Harus memakai domain gabungan; kata umum di judul berita dan jurnal |
| **4. Trengginas** | Jawa: terampil, cekatan, sigap bekerja | Makna paling kuat untuk "cekatan"; terdengar mantap untuk kepala sekolah dan pengawas | Cekatan mengurus kelas. | trengginas.id kosong, trengginas.app kosong, trengginas.com terdaftar (tidak aktif) | Tiga suku kata dengan "gg", sering salah eja; terasa agak resmi |
| **5. Jangkah** | Jawa: langkah kaki | Cocok untuk progres harian dan alur bertahap (langkah 1–4) | Selangkah lebih ringan setiap hari. | jangkah.com kosong, jangkahguru.id kosong; jangkah.id dipakai "Komunitas Jangkah Nusantara" | Bentrok nama dengan komunitas yang aktif; makna "cepat" kurang langsung |
| **6. Papan Guru** | Indonesia: papan kerja guru | Langsung dipahami siapa pun, termasuk dinas | Semua kerja guru di satu papan. | papanguru.id kosong, papanguru.com kosong | Terlalu deskriptif sehingga lemah sebagai merek dan mudah ditiru; kurang berkarakter |
| **7. Bellwork** | Inggris: kegiatan pembuka saat bel masuk | Terdengar modern; cerita "siap sebelum bel" | Siap sebelum bel berbunyi. | bellwork.id kosong, bellwork.com terdaftar sejak 1999 | Istilah asing bagi guru SD; ejaan dan ucapan tidak konsisten ("bel-wok") |

Dicoret selama penyaringan: **Cakap** (sudah dipakai platform edukasi), **Gesit** (terlalu mirip GESITS, merek motor listrik), **Tangkas** (asosiasi judi "bola tangkas"), **Tata Kelas** (tatakelas.com kini situs judi), **Sigap** (banyak dipakai aplikasi pemerintah daerah).

Sebelum memutuskan, cek nama terpilih di [PDKI](https://pdki-indonesia.dgip.go.id) kelas 9 (perangkat lunak), 41 (pendidikan), dan 42 (layanan perangkat lunak), lalu daftarkan domain .id dan .com sekaligus.

## Visi, misi, dan nilai

Nama kerja di seluruh dokumen ini adalah Sigrak; bila pilihan berubah, cukup ganti namanya.

**Visi.** Setiap guru SD/MI di Kudus menyelesaikan administrasi pembelajarannya di sela jam mengajar, sehingga sore harinya kembali untuk murid dan keluarga.

**Misi.**

1. Mengubah pekerjaan administrasi yang berulang (RPM, jurnal, absensi, nilai) menjadi isian singkat yang selesai dalam hitungan menit.
2. Menyediakan kurikulum, jadwal, dan format dokumen yang sudah sesuai aturan, sehingga guru tidak menyusun dari nol.
3. Membuat kepala sekolah dan pengawas melihat kemajuan sekolah dari data yang sama dengan guru, tanpa meminta setor berkas.
4. Tetap bisa dipakai di HP murah dan sinyal lemah.
5. Menjaga data siswa dan guru tetap milik sekolah.

**Nilai.**

| Nilai | Artinya dalam produk | Contoh keputusan |
| --- | --- | --- |
| Cekatan | Langkah sesedikit mungkin; data yang sudah ada tidak ditanya ulang | Kelas dan mapel terisi dari jadwal, bukan dipilih lagi |
| Jelas | Satu istilah untuk satu hal; tiap layar menjawab "apa yang harus saya lakukan sekarang" | "Unduh", bukan campuran Download/DOCX/Simpan Lokal |
| Bisa diandalkan | Data tidak hilang, dokumen layak cetak dan lolos pemeriksaan | Simpan otomatis; PDF teks dengan kop sekolah |
| Membumi | Mengikuti cara kerja sekolah di Kudus, bukan meniru aplikasi luar | Bahasa Jawa, kaldik daerah, format F4 |
| Menghormati guru | Guru tetap penulis dokumennya; AI hanya membantu | Semua hasil AI bisa diedit sebelum dipakai |

## Kepribadian dan nada bahasa

Sigrak berbicara seperti rekan guru yang cekatan: langsung ke inti, sopan, dan tidak pamer teknologi.

| Sigrak itu | Bukan |
| --- | --- |
| Cekatan: kalimat pendek, kata kerja di depan | Tergesa: memotong informasi penting |
| Ramah: menyapa "Anda", memakai "silakan" seperlunya | Akrab berlebihan: "Yuk, Bapak/Ibu kece!" |
| Jelas: istilah sekolah yang dipakai guru | Teknis: Cloud, Firestore, sinkronisasi, inject, generate |
| Tenang: galat dijelaskan beserta jalan keluarnya | Menyalahkan: "Input tidak valid!" |
| Membantu: menawarkan langkah berikutnya | Menggurui: "Anda harus..." tanpa alasan |

**Aturan menulis teks tampilan**

1. Judul dan tombol memakai kata kerja: Buat RPM, Simpan jurnal, Unduh PDF.
2. Huruf kapital hanya di awal kalimat dan nama diri; tidak ada tombol HURUF BESAR.
3. Satu istilah untuk satu hal (daftar baku ada di tab Istilah pada spreadsheet).
4. Pesan galat = apa yang terjadi + apa yang bisa dilakukan. Kode teknis disembunyikan di "Lihat detail".
5. Konfirmasi hapus menyebut nama benda dan akibatnya; tombolnya menyebut tindakannya ("Hapus jurnal"), bukan "Ya".
6. Pesan berhasil cukup satu baris dan hilang sendiri; tidak memakai dialog yang harus ditutup.
7. Tanpa emoji, tanpa tanda seru berderet, tanpa kata "cerdas", "canggih", atau "AI" sebagai pemanis.
8. Sapaan "Anda" untuk semua peran; "Bapak/Ibu" hanya di pesan siaran dari kepala sekolah.

| Situasi | Sebelum | Sesudah |
| --- | --- | --- |
| Tombol utama | GENERATE MODUL | Buat RPM |
| Galat | Gagal memproses AI: {pesan} | RPM belum berhasil dibuat. Periksa kunci AI Anda, lalu coba lagi. |
| Konfirmasi | Hapus jurnal? / Ya, Hapus | Hapus jurnal 12 Okt? Jurnal ini tidak bisa dikembalikan. / Hapus jurnal |
| Kosong | Data belum tersedia | Belum ada siswa. Tambahkan satu per satu atau impor dari Excel. |

## Target pengguna

Guru kelas adalah pemakai harian terbanyak, sedangkan kepala sekolah dan pengawas adalah pembeli; produk harus memenangkan guru dulu agar kepala sekolah mau membayar.

| Peran | Pekerjaan utama | Masalah sekarang | Yang dijanjikan Sigrak | Perangkat utama |
| --- | --- | --- | --- | --- |
| Guru kelas (wali kelas) | Semua mapel satu kelas: RPM, absensi, jurnal, nilai, rapor, data siswa | Administrasi menumpuk di malam hari; isian berulang di banyak halaman | Satu layar "Hari Ini" berisi yang harus diisi; absensi dan jurnal dari HP saat mengajar | HP, laptop di rumah |
| Guru mapel (PAI, PJOK, Bahasa Jawa, dll.) | Satu mapel di banyak kelas | Harus berpindah kelas aktif untuk tiap rombel | Ganti kelas satu ketuk; RPM dan soal dipakai ulang lintas rombel | HP |
| Kepala sekolah | Memantau kelengkapan administrasi, supervisi, memvalidasi materi, menandatangani dokumen | Menagih berkas satu per satu; data tersebar | Ringkasan kelengkapan per guru, antrean validasi, pengaturan kop dan penandatangan | Laptop, HP |
| Pengawas / korwil | Memantau sekolah binaan, supervisi, validasi akhir materi | Datang ke sekolah untuk melihat berkas | Melihat (tanpa mengubah) data sekolah binaan; laporan kepengawasan siap cetak | Laptop |
| Admin / operator sekolah | Akun guru, kelas, tahun ajaran, profil sekolah | Profil sekolah ditentukan "siapa cepat simpan"; password bawaan sama | Undang guru lewat tautan, atur kelas dan tahun ajaran, satu pemilik profil sekolah | Laptop |

Super admin adalah tim internal Sigrak, bukan target pemasaran, sehingga konsolnya dipisah dari aplikasi sekolah.

## Janji brand dan model bisnis

**Janji brand: "Administrasi beres sambil mengajar."** Tiga bukti yang harus terasa di produk:

1. Absensi dan jurnal satu kelas selesai di HP dalam waktu kurang dari satu menit, dengan atau tanpa sinyal.
2. RPM siap cetak dalam empat langkah, dengan kurikulum, jadwal, dan JP yang sudah terisi.
3. Kepala sekolah dan pengawas melihat kelengkapan tanpa guru menyetor berkas.

Karena pembelinya tiga pihak, paket disusun bertingkat: guru mencoba gratis, lalu sekolah atau pengawas membayar untuk semua guru.

| Paket | Untuk | Isi | Pembayar | Peran dalam penjualan |
| --- | --- | --- | --- | --- |
| Sigrak Dasar (gratis) | Satu guru | Mode Di Kelas (absensi, jurnal, catatan), jadwal, data siswa, RPM dengan kunci AI sendiri dalam jumlah terbatas per bulan, cetak dengan tanda air kecil | — | Pintu masuk; guru membawa Sigrak ke sekolahnya |
| Sigrak Guru | Satu guru | Semua fitur guru tanpa batas: RPM, Asesmen, Penilaian dan rapor, LKPD, Buka di Canva, tanpa tanda air | Guru sendiri | Untuk guru yang sekolahnya belum berlangganan |
| Sigrak Sekolah | Satu sekolah, semua guru | Sigrak Guru untuk semua akun + Portal Kepala Sekolah, validasi materi, pengaturan cetak sekolah, admin dan undangan guru | Kepala sekolah | Pendapatan utama |
| Sigrak Binaan | Satu pengawas atau gugus/KKG | Portal Pengawas untuk sekolah binaan + harga khusus Sigrak Sekolah bagi sekolah binaan | Pengawas, korwil, atau gugus | Satu keputusan, banyak sekolah |

Aturan paket: harga tahunan per tahun ajaran (Juli–Juni) agar cocok dengan siklus anggaran sekolah; akun guru yang sudah membayar Sigrak Guru dipindah otomatis saat sekolahnya berlangganan, dengan sisa masa aktif dikembalikan sebagai potongan. Kunci AI tetap milik tiap guru di semua paket, jadi biaya AI tidak menjadi biaya Sigrak.

Pertanyaan terbuka: angka harga per paket, dan apakah langganan aplikasi boleh dibayar dari dana BOS menurut petunjuk teknis tahun berjalan (perlu dicek ke dinas pendidikan Kudus sebelum dipakai sebagai bahan jualan).

## Arah visual

Tampilan Sigrak seperti buku kerja guru yang rapi: latar polos, satu warna merek, garis tipis, dan huruf yang mudah dibaca di HP. Tidak ada glassmorphism, glow, blur, atau gradien; bayangan hanya untuk menu dan dialog yang melayang.

**Warna.** Semua pasangan teks lolos WCAG AA (minimal 4,5:1); angka kontras dihitung terhadap latarnya.

| Token | Terang | Gelap | Dipakai untuk | Kontras |
| --- | --- | --- | --- | --- |
| `--brand` | #0F766E | #2DD4BF | Tombol utama, tautan, tab aktif | 5,47 di atas putih; teks #0B1F1C di atas #2DD4BF 9,19 |
| `--brand-strong` | #115E59 | #5EEAD4 | Hover, teks merek | 7,58 di atas putih; 11,8 di atas #151B19 |
| `--brand-tint` | #F0FDFA | #12302C | Latar baris terpilih, penanda halaman aktif | — |
| `--bg` | #F7F8F7 | #151B19 | Latar halaman | — |
| `--surface` | #FFFFFF | #1C2321 | Kartu, tabel, formulir | — |
| `--border` | #E3E6E4 | #2C3532 | Garis 1 px | — |
| `--text` | #1C2420 | #E6ECE9 | Teks utama | 14,91 / 14,58 |
| `--text-muted` | #5B6660 | #9AA7A1 | Keterangan | 5,98 / 6,42 |
| `--ok` / `--warn` / `--danger` / `--info` | #15803D / #B45309 / #B91C1C / #1D4ED8 | versi lebih terang dari set gelap | Status saja, tidak untuk hiasan | 5,02 / 5,02 / 6,47 / 6,7 |

Aturan warna: satu warna merek per layar; warna status hanya muncul bila ada status (terlambat, gagal, belum lengkap); ikon dan kartu menu tidak diberi warna berbeda-beda.

**Huruf.** Plus Jakarta Sans (dirancang Tokotype dari Jakarta, lisensi terbuka) untuk aplikasi dan logo; angka tabel memakai fitur tabular-nums. Skala 13 / 15 / 17 / 20 / 24 / 30 px; isi bacaan 16 px di HP; label minimal 13 px tebal 600, kalimat biasa (bukan huruf kapital). Dokumen cetak memakai huruf pilihan sekolah di pengaturan cetak (bawaan: Arial atau Times New Roman), karena format dinas sering mensyaratkannya.

**Ikon.** Lucide garis 1,5 px, ukuran 20 px (24 px di navigasi bawah), versi dikunci dan dibundel. Ikon selalu disertai teks, kecuali tutup, cari, dan menu. Tanpa emoji di tampilan maupun tutorial.

**Logo.** Simbol: dua goresan miring yang membentuk tanda centang sekaligus langkah maju, digambar seperti goresan kapur dengan ujung membulat; artinya "selesai, lalu lanjut". Wordmark "sigrak" huruf kecil, Plus Jakarta Sans 700 dengan jarak huruf sedikit dirapatkan. Simbol dipakai sendiri sebagai ikon aplikasi (putih di atas teal) dan favicon. Larangan: tanpa gradien, tanpa bayangan, tanpa maskot, tanpa motif batik atau menara yang membuatnya ramai. Desainer perlu menyiapkan versi warna, satu warna, dan negatif, serta ukuran minimal 16 px.

**Bentuk.** Kelipatan 4 px; radius 6 (input) dan 10 (kartu); target sentuh minimal 44 px; navigasi bawah lima item di HP; tabel menjadi daftar kartu di bawah 640 px; tombol aksi utama menempel di bawah layar.

## Struktur menu baru

Menu disusun dari pertanyaan guru, bukan dari nama modul: "apa yang harus saya isi hari ini", "apa yang saya catat di kelas", "apa yang saya siapkan", "bagaimana nilai murid", dan "data kelas saya".

&#91;embedded content: struktur menu baru · 5 menu guru, 3 portal\]

Di HP, lima menu guru menjadi navigasi bawah; di laptop menjadi menu samping dengan urutan yang sama. Kepala sekolah, pengawas, dan admin yang juga mengajar tetap memiliki lima menu guru, ditambah tombol pindah ke portalnya.

## Alur utama per peran

Tiap peran punya satu alur harian yang harus selesai paling banyak dalam empat langkah.

**Guru, satu hari mengajar (HP)**

1. Buka Hari Ini: terlihat jadwal dan daftar yang belum diisi.
2. Saat jam pelajaran, ketuk Di Kelas → Absensi: semua siswa tercatat hadir, ketuk yang tidak hadir.
3. Setelah pelajaran, Jurnal sudah terisi dari RPM; guru cukup menandai terlaksana dan menambah catatan bila perlu.
4. Catatan siswa (anekdot, BK, prestasi) ditulis saat kejadian; semuanya tersimpan meski tanpa sinyal dan terkirim saat tersambung.

**Guru, menyiapkan RPM (laptop atau HP)**

1. Kapan: pilih tanggal mulai; kelas dan mapel terisi dari jadwal.
2. Apa: pilih materi pokok dan fokus; JP dan jumlah pertemuan dihitung dari ATP.
3. Cara: pilih model pembelajaran per pertemuan atau biarkan otomatis.
4. Periksa: pratinjau, perbaiki bagian tertentu, lalu simpan, unduh PDF/Word, atau buka di Canva.

**Kepala sekolah, sekali seminggu**

1. Ringkasan: kelengkapan administrasi per guru minggu ini.
2. Pantau guru: buka guru yang tertinggal, kirim pengingat dari layar yang sama.
3. Validasi: periksa materi yang diajukan, setujui atau kembalikan dengan catatan.

**Pengawas, per kunjungan**

1. Sekolah binaan: pilih sekolah.
2. Pantau: lihat kelengkapan dan hasil supervisi kepala sekolah (hanya baca).
3. Validasi akhir materi, lalu unduh laporan kepengawasan.

&#91;embedded content: alur validasi materi · guru, kepala sekolah, pengawas\]

Materi bawaan yang dipakai apa adanya tidak masuk antrean; hanya materi yang disesuaikan atau ditambah sekolah yang melewati dua pemeriksa, dan setiap pengembalian membawa catatan untuk guru.

## Status tiap fitur

Dari 43 fitur yang ada, 10 dipertahankan, 12 digabung, 18 diganti, dan 3 dihapus; 10 fitur baru ditambah. Kolom Status bisa diubah langsung bila keputusan berubah.

| Area | Fitur sekarang | Status | Menjadi | Alasan |
| --- | --- | --- | --- | --- |
| Beranda | Beranda: progres administrasi | Ganti | Hari Ini: daftar yang harus diisi hari ini + progres minggu ini | Guru perlu tahu apa yang harus dikerjakan sekarang, bukan hanya persentase |
| Beranda | Papan informasi siaran | Gabung | Kabar (bersama Pemberitahuan) | Dua tempat pengumuman membingungkan |
| Beranda | Papan "Quote EDUNEXA hari ini" dan "Tips & motivasi" | Hapus | — | Hiasan yang memakan layar HP |
| Beranda | Pemberitahuan | Gabung | Kabar | Satu kotak masuk untuk semua kabar |
| Beranda | Mode Pemeriksaan / Data Demo | Ganti | Mode Contoh untuk presentasi ke sekolah | Namanya lebih jelas bagi calon pembeli |
| Beranda | Sesi diputus setelah 15 menit tidak aktif | Ganti | Sesi tetap; isian tersimpan otomatis | Guru sering berhenti di tengah isian; alasan biaya hilang setelah pindah ke SQL |
| Rencana | Kurikulum (CP, TP, ATP, Prota, Promes) | Gabung | Rencana > Kurikulum | Kurikulum dasar perencanaan, bukan administrasi kelas |
| Rencana | Generator RPM: modul terstruktur | Pertahankan | Rencana > RPM, alur 4 langkah: Kapan, Apa, Cara, Periksa | Fitur paling bernilai; tinggal dirapikan alurnya |
| Rencana | Modul mandiri (mode manual) | Gabung | Pilihan "RPM lepas jadwal" di langkah 1 | Satu pintu untuk semua RPM |
| Rencana | Modul kokurikuler (7 KAIH, projek, lintas disiplin) | Pertahankan | Rencana > Kokurikuler, membaca rencana sekolah |  |
| Rencana | Worksheet / LKPD | Gabung | Rencana > Bahan Ajar, juga bisa jadi lampiran RPM | LKPD bagian dari rencana pembelajaran |
| Rencana | Bank Modul / Penyimpanan RPM | Gabung | Dokumen Saya | Satu arsip untuk semua dokumen |
| Rencana | Download dan Impor Cadangan | Ganti | Simpan otomatis ke server; ekspor seluruh data di Akun | Cadangan manual membuat guru ragu datanya aman |
| Rencana | Cocokkan Tanggal dengan Isi (Kokurikuler Tahunan) | Ganti | Penyesuaian otomatis + kabar bila jadwal berubah | Guru tidak perlu tahu mekanismenya |
| Rencana | PDF hasil tangkapan layar (html2canvas) | Ganti | PDF teks dari server + pratinjau sebelum unduh | File ringan, teks bisa dicari, baris tidak terpotong |
| Asesmen | Bank Soal (PH, STS, SAS, US, kisi-kisi, kartu soal) | Gabung | Asesmen > Buat Soal | Soal dan nilai satu alur; soal terkait TP |
| Asesmen | Program penilaian | Gabung | Asesmen > Rencana Penilaian |  |
| Asesmen | Analisis nilai dan taraf serap | Pertahankan | Asesmen > Analisis, otomatis per TP |  |
| Asesmen | Leger dan "inject e-Rapor" | Ganti | Asesmen > Rapor: leger, deskripsi capaian, ekspor format e-Rapor | "Inject" istilah teknis |
| Asesmen | Pilihan jenjang SMP–SMK dan kelas 7–12 | Hapus | Muncul lagi saat data SMP/MTs siap | Pilihan tanpa data berujung galat |
| Di Kelas | Absensi | Ganti | Di Kelas > Absensi: bawaan "hadir semua", ketuk yang tidak hadir, jalan tanpa sinyal | Diisi saat jam pelajaran di HP |
| Di Kelas | Jurnal dan refleksi | Ganti | Di Kelas > Jurnal terisi dari RPM; refleksi disusun dari jurnal | Mengurangi ketik ulang |
| Di Kelas | Catatan anekdot, BK, prestasi | Gabung | Di Kelas > Catatan Siswa (satu formulir, jenis dipilih) | Tiga buku dengan isian yang sama |
| Di Kelas | Buku tamu, buku humas, notulen | Gabung | Portal Kepala Sekolah > Buku Sekolah | Milik sekolah, bukan milik kelas |
| Kelas Saya | Data siswa | Pertahankan | Kelas Saya > Siswa |  |
| Kelas Saya | Mutasi siswa | Gabung | Kelas Saya > Siswa > Pindah atau keluar | Bagian dari riwayat siswa |
| Kelas Saya | Kenaikan kelas | Gabung | Kelas Saya > Siswa > Kenaikan, aktif menjelang akhir tahun ajaran | Dipakai setahun sekali |
| Kelas Saya | Kaldik dan jadwal | Pertahankan | Kelas Saya > Jadwal; kaldik diisi sekali oleh sekolah | Kaldik sama untuk semua kelas |
| Kelas Saya | Piket | Pertahankan | Kelas Saya > Piket |  |
| Kelas Saya | Keuangan kelas (tabungan, pembayaran) | Pertahankan | Kelas Saya > Keuangan, bisa dimatikan oleh sekolah | Tidak semua sekolah mengizinkan wali kelas mengelola uang |
| Kelas Saya | Inventaris kelas | Pertahankan | Kelas Saya > Inventaris |  |
| Akun | Pengaturan (tema, kertas, profil, password) | Ganti | Akun (profil, password, tema) + Pengaturan Sekolah (kertas, kop) | Kertas dan kop milik sekolah, bukan per guru |
| Akun | Profil sekolah "first save wins" | Ganti | Dikelola admin/kepala sekolah; guru mengusulkan koreksi | Pemilik data jelas |
| Akun | API Key Gemini wajib saat aktivasi guru | Ganti | Kunci AI Saya, diminta saat pertama memakai fitur AI, dengan tombol uji | Daftar jadi cepat; AI tidak menghalangi fitur lain |
| Akun | API Key wajib di profil kepala sekolah | Hapus | — | Kepala sekolah tidak wajib memakai AI |
| Akun | Password bawaan sama untuk pengguna baru | Ganti | Undangan lewat tautan, guru membuat password sendiri | Keamanan akun |
| Akun | Tutorial | Ganti | Bantuan: petunjuk singkat di tiap halaman + pusat bantuan | Bantuan muncul saat dibutuhkan |
| Akun | Arsip | Ganti | Dokumen Saya: RPM, soal, LKPD, laporan, dengan status validasi |  |
| Akun | Hubungi Admin / Kotak Pesan | Pertahankan | Akun > Minta Bantuan |  |
| Akun | Ajukan pindah instansi | Pertahankan | Akun > Pindah Sekolah, disetujui admin sekolah tujuan |  |
| Portal | Portal Kepala Sekolah | Ganti | Ringkasan, Pantau Guru, Validasi, Supervisi, Program Sekolah, Agenda & Rapat, Data Sekolah, Pengaturan Sekolah | Disusun dari pekerjaan kepala sekolah |
| Portal | Portal Pengawas | Ganti | Sekolah Binaan, Pantau, Validasi, Supervisi, Laporan Kepengawasan |  |
| Portal | Super Admin / Control Center | Ganti | Konsol internal terpisah + kelola langganan | Bukan bagian aplikasi sekolah |
| Baru | Mode Di Kelas di HP tanpa sinyal | Tambah | Menu Di Kelas | Sudah disepakati |
| Baru | Validasi materi guru → kepala sekolah → pengawas | Tambah | Status di Dokumen Saya + antrean di portal | Sudah disepakati |
| Baru | Pengaturan cetak per sekolah (kop, logo, penandatangan, A4/F4, huruf) | Tambah | Pengaturan Sekolah > Cetak | Sudah disepakati |
| Baru | Buka di Canva | Tambah | Tombol di pratinjau RPM, LKPD, soal | Sudah disepakati |
| Baru | Riwayat pemakaian kunci AI | Tambah | Akun > Kunci AI Saya | Guru tahu kenapa AI berhenti |
| Baru | Perbaiki satu bagian RPM dengan AI | Tambah | Tombol di tiap bagian pratinjau RPM | Tidak perlu membuat ulang seluruh RPM |
| Baru | Draf deskripsi rapor dari nilai per TP | Tambah | Asesmen > Rapor |  |
| Baru | Langganan dan tagihan | Tambah | Pengaturan Sekolah > Langganan; Akun > Paket Saya | Model bisnis baru |
| Baru | Undang guru | Tambah | Pengaturan Sekolah > Guru | Ganti password bawaan |
| Baru | Panduan awal 3 langkah | Tambah | Muncul sekali setelah daftar: kelas, jadwal, siswa | Guru siap pakai di hari pertama |

## Langkah berikutnya

- [ ] Pilih nama (Sigrak atau cadangan), cek di PDKI kelas 9, 41, 42, lalu daftarkan domain .id dan .com
- [ ] Brief desainer logo: simbol goresan centang-langkah, wordmark huruf kecil, versi warna, satu warna, dan negatif
- [ ] Tetapkan harga empat paket dan cek apakah langganan aplikasi boleh dibiayai dana BOS di Kudus
- [ ] Uji menu lima tab dengan 5 guru kelas dan 2 guru mapel memakai purwarupa klik (tanpa data asli)
- [ ] Terapkan tabel parafrase teks (spreadsheet pendamping) bersamaan dengan design system baru
- [ ] Presentasikan versi singkat ke kepala sekolah dan pengawas, lalu catat keberatan mereka
- [ ] Putuskan nasib "Mode Ujian" di Bank Soal: fungsinya belum jelas dari halaman publik

## Sumber

- Halaman publik portal yang dibaca 29 Oktober 2026 (tanpa data pribadi): [index.html](https://portal-edunexa.web.app/index.html), [rpm-generator.html](https://portal-edunexa.web.app/rpm-generator.html), [bank-soal.html](https://portal-edunexa.web.app/bank-soal.html), [administrasi.html](https://portal-edunexa.web.app/administrasi.html), [worksheet-generator.html](https://portal-edunexa.web.app/worksheet-generator.html), [/kepala-sekolah/](https://portal-edunexa.web.app/kepala-sekolah/), [/pengawas/](https://portal-edunexa.web.app/pengawas/), [tutorial.html](https://portal-edunexa.web.app/tutorial.html)
- Status domain: RDAP [rdap.org](https://rdap.org) (diteruskan ke Verisign untuk .com dan PANDI untuk .id); isi situs yang aktif dicek langsung
- Konteks: [Catatan Pengembang](https://claude.ai/artifact/WqzXi3r4pN89ZEHQChVbm8), [Pemetaan Data Materi](https://claude.ai/artifact/Qtp98W2tt6s5pezxXnTHKW)
- Pendamping: spreadsheet Parafrase\_Teks\_UI\_Sigrak.xlsx dan presentasi "Sigrak untuk Kepala Sekolah dan Pengawas"
