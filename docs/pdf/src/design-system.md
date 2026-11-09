# Sistem Desain dan Penulisan UI

<p class="source-note">Diterjemahkan dari <code>docs/final/design-system.md</code> pada 1 November 2026. Teks UI berbahasa Indonesia dikutip apa adanya.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-06
- **Terakhir diperbarui:** 2026-11-01
- **Sumber:** `Rebranding_dan_Perombakan_Fitur_Aplikasi_Lama.md`, `Catatan_Pengembang_Aplikasi_Lama_Design_System_Data_dan_AI.md`, `Parafrase_Teks_UI_Sigrak.xlsx` (semuanya di `docs/sources/`)

## Tujuan

Dokumen ini menetapkan tampilan dan gaya bahasa SIGRAK: token, komponen, aturan ponsel, nada, dan istilah. Token dan aturannya berasal dari catatan aplikasi lama, disesuaikan dengan stack repo (Tailwind + TailAdmin, diporting ke Svelte). Dokumen ini untuk siapa pun yang membangun layar atau menulis teks UI. Teks UI sendiri berbahasa Indonesia dan disimpan di `lang/id`. Dokumen sumbernya berbahasa Inggris, dengan teks UI Indonesia dikutip sebagai data.

## Arah

Aplikasi harus terlihat seperti buku kerja guru yang rapi: latar polos, satu warna brand, garis tipis, dan huruf yang mudah dibaca di ponsel. Ini alat kerja yang tenang, bukan poster.

| Dihapus (terlihat di aplikasi lama) | Gantinya |
|---|---|
| Glassmorphism, gradien, glow, blur | Permukaan polos: halaman abu-abu sangat muda, kartu putih, garis 1 px |
| Warna aksen berbeda untuk tiap kartu atau ikon | Satu warna brand + warna status saja |
| Label huruf kapital yang sangat kecil (`text-[9px] uppercase`) | Label huruf kalimat, minimal 13 px, ketebalan 600 |
| Tombol hanya ikon | Ikon + teks. Ikon saja hanya untuk aksi universal (tutup, cari, menu) dengan `aria-label` |
| Lencana "Kurang 232" di setiap baris | Bilah kemajuan tipis + angka. Berwarna hanya bila terlambat |
| `user-scalable=no` | Zoom diizinkan (aksesibilitas) |
| Tailwind CDN, `lucide@latest` | Tailwind lewat build Vite. Ikon dikunci versinya dan dibundel |

## Pemetaan ke stack

- **Token berupa CSS custom property**, ditetapkan sekali di `resources/css/tokens.css` untuk terang dan gelap, lalu dipetakan ke tema Tailwind (`@theme` di Tailwind v4) sehingga kelas seperti `bg-surface`, `text-muted`, dan `border-line` memakainya.
- **TailAdmin adalah rujukan komponen, bukan rujukan tampilan.** Pola tata letaknya (sidebar, header, tabel, formulir, modal) diporting ke komponen Svelte. Gaya dekoratifnya (gradien, kartu statistik warna-warni, bayangan tebal) dibuang dan diganti token di bawah.
- **Filament (konsol internal)** memakai temanya sendiri dengan warna brand diatur ke `--brand`. Konsol ini khusus staf, jadi tidak harus sama persis dengan aplikasi sekolah.
- **Ikon:** Lucide untuk Svelte, versi dikunci di `package.json`, garis 1,5 px, 20 px (24 px di navigasi bawah).
- **Font:** Plus Jakarta Sans, di-host sendiri lewat build (tanpa CDN saat berjalan), dengan `font-variant-numeric: tabular-nums` di tabel.

## Token warna

Setiap pasangan teks memenuhi WCAG AA (≥ 4,5:1). Kontras diukur terhadap latar pasangannya sendiri.

| Token | Terang | Gelap | Kegunaan | Kontras |
|---|---|---|---|---|
| `--brand` | #0F766E | #2DD4BF | Tombol utama, tautan, tab aktif | 5,47 di atas putih; teks #0B1F1C di atas #2DD4BF 9,19 |
| `--brand-strong` | #115E59 | #5EEAD4 | Hover, teks brand | 7,58 di atas putih; 11,8 di atas #151B19 |
| `--brand-tint` | #F0FDFA | #12302C | Baris terpilih, penanda halaman aktif | — |
| `--bg` | #F7F8F7 | #151B19 | Latar halaman | — |
| `--surface` | #FFFFFF | #1C2321 | Kartu, tabel, formulir | — |
| `--border` | #E3E6E4 | #2C3532 | Garis 1 px | — |
| `--text` | #1C2420 | #E6ECE9 | Teks utama | 14,91 / 14,58 |
| `--text-muted` | #5B6660 | #9AA7A1 | Teks sekunder | 5,98 / 6,42 |
| `--ok` / `--warn` / `--danger` / `--info` | #15803D / #B45309 / #B91C1C / #1D4ED8 | Versi lebih terang dari set gelap | Hanya untuk status, tidak pernah untuk hiasan | 5,02 / 5,02 / 6,47 / 6,7 |

Aturan:

- Satu warna brand per layar.
- Warna status hanya muncul bila memang ada status (terlambat, gagal, belum lengkap).
- Kartu menu dan ikon tidak pernah diberi warna berbeda-beda.
- Mode gelap adalah set tersendiri, bukan hasil pembalikan warna.

## Huruf, jarak, bentuk

- **Skala huruf:** 13 / 15 / 17 / 20 / 24 / 30 px. Teks bacaan 16 px di ponsel. Label minimal 13 px, ketebalan 600, huruf kalimat.
- **Dokumen cetak** memakai font cetak sekolah, Arial (bawaan) atau Times New Roman (D-125). Kop berupa gambar yang diunggah (D-031), jadi font dan logonya mengikuti rancangan sekolah atau guru.
- **Matematika dan Arab:** KaTeX untuk rumus dan pecahan; Noto Naskh Arabic (atau Amiri) untuk teks Arab di materi PAI.
- **Jarak:** kelipatan 4 px. **Radius:** 6 (input), 10 (kartu). **Bayangan:** hanya untuk menu melayang dan dialog.

## Komponen

| Kelompok | Komponen |
|---|---|
| Aksi | Tombol (utama, sekunder, teks), tombol ikon (hanya tutup/cari/menu) |
| Formulir | Input, Select, Combobox (pemilih kurikulum), Checkbox, kartu Radio (pilihan wizard), Textarea dengan penghitung (catatan opsional untuk AI) |
| Struktur | Tab, Tabel + daftar kartu untuk ponsel, Stepper (wizard), Bottom sheet, Dialog |
| Umpan balik | Toast (satu baris, hilang sendiri), galat di tempat, Keadaan kosong, Skeleton, Kemajuan ("Langkah 2 dari 4") |
| Mesin konten | Kartu templat (galeri), Penyunting bagian dengan "Perbaiki bagian ini", Pembanding revisi (lama/baru), Panel status pembuatan |
| Kunci AI | Baris kunci (label, project, 4 karakter terakhir, chip status, waktu cooldown), sheet Tambah kunci dengan hasil uji dan kotak persetujuan multi-kunci |
| Ujian TKA | Kartu sesi, lembar kartu ujian (cetak), panel proktor (token aktif dengan hitung mundur, daftar peserta dengan status), tabel hasil |
| Cetak | Unggah kop dengan pratinjau A4/F4 dan peringatan resolusi, formulir pengaturan cetak |

## Aturan ponsel

- Area sentuh minimal 44 px.
- Di ponsel, lima menu guru berupa navigasi bawah. Di laptop, urutan yang sama berupa sidebar.
- Tabel berubah menjadi daftar kartu di bawah 640 px.
- Formulir satu kolom, dan tombol aksi utama menempel di bagian bawah layar.
- Wizard menampilkan satu langkah per layar, dengan "Kembali" dan aksi berikutnya selalu terlihat.
- Layar Di Kelas harus bisa dipakai dengan satu tangan: baris besar dan bawaan "semua hadir".

## Gaya runtime game

Game (Quiz Battle dan game berikutnya) dimainkan anak-anak di proyektor atau ponsel. Game mendapat **gayanya sendiri**, terpisah dari kerangka aplikasi yang tenang:

- Warna lebih cerah, huruf lebih besar, dan animasi diperbolehkan. Paletnya dibuat desainer, dengan brief yang digabung bersama logo (D-154).
- Tetap aksesibel: kontras AA untuk semua teks, tanpa kedipan di atas 3 Hz, penghitung waktu yang tidak hanya ditandai warna, dan tombol suara.
- Bundel runtime membawa CSS-nya sendiri, sehingga game yang diunduh tampil sama saat offline.

## Gaya situs ujian (latihan TKA)

Situs ujian adalah front end terpisah untuk siswa (D-027). Tampilannya harus terasa seperti klien ujian TKA nasional, agar pengalaman latihannya terbawa:

- Polos dan bebas gangguan: soal di kiri, teks atau gambar stimulus bila soalnya membutuhkan, pilihan jawaban sebagai area sentuh yang besar.
- Kisi nomor untuk berpindah antarbutir. Butir diberi warna *sudah dijawab*, *ragu-ragu* (ditandai), atau *belum dijawab*, juga dengan label teks, tidak pernah hanya warna.
- Hitung mundur yang terlihat, dengan peringatan 5 menit sebelum waktu habis.
- Pengatur ukuran huruf (A−, A, A+) dan bisa dipakai penuh dengan keyboard.
- Tanpa promosi brand, tanpa tautan ke aplikasi guru, tanpa kata-kata tentang AI.
- Teks singkat dan ramah anak: "Masukkan token dari pengawas", "Kirim jawaban", "Waktu tersisa 5 menit".

## Nada dan gaya bicara

Sigrak berbicara seperti rekan guru yang sigap dan sopan: langsung ke inti, santun, tidak pernah memamerkan teknologi.

| Sigrak itu | Bukan |
|---|---|
| Sigap: kalimat pendek, diawali kata kerja | Terburu-buru: memotong informasi penting |
| Ramah: menyapa "Anda", memakai "silakan" bila perlu | Sok akrab: "Yuk, Bapak/Ibu kece!" |
| Jelas: istilah sekolah yang dipakai guru | Teknis: Cloud, Firestore, sinkronisasi, inject, generate, prompt |
| Tenang: galat dijelaskan beserta jalan keluarnya | Menyalahkan: "Input tidak valid!" |
| Membantu: menawarkan langkah berikutnya | Menggurui: "Anda harus…" tanpa alasan |

## Aturan penulisan UI

1. Judul dan tombol memakai kata kerja: "Buat RPM", "Simpan jurnal", "Unduh PDF", "Tayangkan".
2. Huruf kapital hanya di awal kalimat dan pada nama diri. Tidak ada tombol berhuruf kapital semua.
3. Satu istilah untuk satu hal (daftar di bawah).
4. Galat = apa yang terjadi + apa yang harus dilakukan. Kode teknis disimpan di balik "Lihat detail".
5. Konfirmasi hapus menyebut nama item dan akibatnya, dan tombolnya menyebut aksinya ("Hapus jurnal"), tidak pernah "Ya".
6. Pesan berhasil berupa satu baris yang hilang sendiri, tidak pernah dialog yang harus ditutup.
7. Tanpa emoji, tanpa tanda seru bertumpuk, tanpa "cerdas", "canggih", atau "AI" sebagai hiasan. Bila fitur AI perlu nama, pakai "Kunci AI" dan kata kerja sederhana ("Buat", "Perbaiki").
8. "Anda" untuk setiap peran. "Bapak/Ibu" hanya di pengumuman dari kepala sekolah.
9. **Jangan pernah meminta guru menulis prompt.** Pertanyaan wizard bersifat pedagogis dan disertai nilai bawaan (lihat `content-engine.md`, bagian Aturan wizard).

| Situasi | Sebelum | Sesudah |
|---|---|---|
| Tombol utama | GENERATE MODUL | Buat RPM |
| Galat | Gagal memproses AI: {pesan} | RPM belum berhasil dibuat. Periksa kunci AI Anda, lalu coba lagi. |
| Semua kunci mencapai batas | — | Semua kunci AI Anda sedang mencapai batas. Coba lagi pukul {jam}. |
| Konfirmasi | Hapus jurnal? / Ya, Hapus | Hapus jurnal 12 Okt? Jurnal ini tidak bisa dikembalikan. / Hapus jurnal |
| Kosong | Data belum tersedia | Belum ada siswa. Tambahkan satu per satu atau impor dari Excel. |

## Istilah

Ini istilah baku dari lembar *Istilah* di spreadsheet (37 entri; daftar lengkapnya tetap di spreadsheet), diperbarui untuk konsep baru. Entri baru ditandai **baru**.

| Lama / hindari | Pakai | Catatan |
|---|---|---|
| Download | Unduh | |
| Generate / GENERATE MODUL | Buat (Buat RPM, Buat soal, Buat LKPD) | |
| Modul Ajar / Modul | RPM | Satu nama untuk dokumen perencanaan |
| Modul mandiri / Mode Manual | RPM lepas jadwal | |
| Bank Modul / Arsip / Penyimpanan Lokal | Dokumen Saya | |
| Worksheet | LKPD | Istilah resmi kurikulum |
| Bank Soal (menu) | Templat Bank Soal di bawah Buat; pintasan "Buat soal" di Asesmen | **Diperbarui**: soal kini ada di galeri templat |
| Rencana (menu) | Buat | **Baru** (D-021) |
| Template | Templat | **Baru**. Ejaan KBBI, dipakai di semua teks UI (D-151) |
| Game mode / Play | Tayangkan (presentasi), Unduh game (berkas) | **Baru** (D-011) |
| CBT / ujian online TKA | Simulasi TKA | **Baru** (D-026). Jangan pernah hanya "TKA", agar tidak tertukar dengan tes resmi |
| Username/password ujian | Kartu login | **Baru**. Nama yang sama dengan di TKA |
| Token ujian | Token | **Baru**. "Token dari pengawas" di teks untuk siswa |
| Proktor | Pengawas (untuk siswa), Proktor (untuk guru) | **Baru** |
| Kop surat / letterhead | Kop | **Baru**. "Unggah kop", "Kop sekolah", "Kop saya" |
| Ragu-ragu | Ragu-ragu | **Baru**. Sama dengan TKA |
| Prompt, generate, AI generator | (tidak pernah ditampilkan) | **Baru**. Pakai "Buat" dan pertanyaan pedagogis |
| API Key / API Key Gemini | Kunci AI | Jamak "kunci AI", satu per project Google |
| Google Cloud Project | Project Google | **Baru**. Label yang diketik guru untuk setiap kunci |
| Penilaian (menu) | Asesmen | |
| Administrasi Kelas | Kelas Saya (+ Di Kelas untuk absensi, jurnal, catatan) | |
| Pemberitahuan / papan pengumuman | Kabar | Dari sekolah ke guru |
| Pemberitahuan (kepala sekolah ke guru) | Pengingat | |
| Tutorial | Bantuan | |
| Pengaturan / Pengaturan Portal | Akun (pribadi) / Pengaturan Sekolah (sekolah) | |
| Dashboard | Ringkasan (portal) / Hari Ini (guru) | |
| Password / Username / Login | Kata sandi / Nama pengguna / Masuk | |
| Instansi | Sekolah | |
| Super Admin (untuk guru) | Tim Sigrak / admin sekolah | Siapa pun yang benar-benar memutuskan |
| Role / Hak Akses | Peran | |
| Cloud / Firestore / Firebase / sinkronisasi | tersimpan di akun | Nama teknologi tidak pernah ditampilkan |
| Edit / Reset / Timpa | Ubah / Kembalikan, Atur ulang / Ganti | |
| Presensi / Kehadiran / Daftar Hadir | Absensi | |
| Mutasi siswa | Pindah dan keluar | |
| Master + nama aplikasi lama | Kurikulum bawaan | |
| Gagal … | … belum berhasil | Lebih tenang, tetap jelas |
| `...` / `&` | `…` (satu karakter) / "dan" | |

## Sumber teks dan lokalisasi

- `Parafrase_Teks_UI_Sigrak.xlsx` berisi 1.499 teks lama → baru dari 10 halaman aplikasi lama (Administrasi Kelas 608, Beranda & akun 272, Portal Kepala Sekolah 199, Generator RPM 112, bersama 70, Super Admin 65, Tutorial 62, Bank Soal 58, Worksheet/LKPD 27, Portal Pengawas 26).
- Saat sebuah layar dibangun, teksnya dipindahkan ke `lang/id/*.php` dengan key bahasa Inggris (konvensi repo). Kolom "Teks baru" di spreadsheet menjadi teks awal, dicocokkan dengan istilah di atas.
- Placeholder `{…}` di spreadsheet menjadi parameter Laravel `:placeholder`.
- Teks untuk halaman yang sudah tidak ada (Super Admin → konsol internal, Tutorial → Bantuan) hanya dipakai bila layar baru masih membutuhkannya.

## Pertanyaan terbuka

- [x] Teks UI memakai "Templat" (KBBI), bukan "template" (D-151).
- [x] Palet runtime game: tugas desainer, dengan brief yang digabung bersama logo (D-154).
