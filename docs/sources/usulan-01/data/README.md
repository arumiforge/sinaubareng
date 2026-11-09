# Data pendukung Usulan 01

Data rujukan yang diambil dari aset statis publik portal EDUNEXA (`https://portal-edunexa.web.app`) pada 9 November 2026, lewat peramban pengguna. Tidak ada data pengguna, guru, murid, atau sekolah yang diambil: hanya daftar pilihan, katalog, aturan, dan data libur nasional yang tertanam di kode aplikasi.

Semua berkas berstatus **draft**. Tim Validasi SIGRAK memeriksa isinya sebelum diimpor (D-113). Kunci JSON berbahasa Inggris, nilai berbahasa Indonesia, seperti konvensi `docs/data/`.

| Berkas | Isi | Dipakai oleh | Catatan penting |
|---|---|---|---|
| `libur-nasional-cuti-bersama.json` | Libur nasional dan cuti bersama per tahun ajaran 2026/2027 (25 tanggal) dan 2027/2028 (5 tanggal, sampai 31 Des 2027), dengan dasar SKB 3 Menteri yang disebut sumber | U-05 | Wajib dicocokkan dengan salinan resmi SKB sebelum impor. Nomor SKB 2027 belum diperiksa |
| `katalog-kokurikuler.json` | 20 contoh kegiatan kokurikuler, 14 tema contoh lokal, bentuk, dimensi profil lulusan, mapel terkait, dan kebiasaan 7 KAIH hasil isi otomatis EDUNEXA | U-01, P5-04 (Buat > Kokurikuler) | "Projek" dipetakan ke "cara lainnya" sesuai Panduan Kokurikuler 2025. Tiga kegiatan memakai pola cadangan "semua mapel" dan ditandai `review_flags` |
| `katalog-ekstrakurikuler.json` | 6 kategori ekstrakurikuler (Pramuka, olahraga, seni, bela diri, kesehatan, teknologi) dengan tujuan dan format | H5 (P5-07) | Pramuka wajib tersedia (D-210) |
| `inventaris-kelas.json` | 30 barang standar kelas, usulan pilihan kondisi dan sumber dana | Inventaris kelas, O4 | Istilah disesuaikan (murid, IFP). Sumber dana tidak menyebut BOS |
| `komponen-administrasi-pembelajaran.json` | Komponen Telaah Administrasi "format Kudus" (24 komponen guru kelas, 18 komponen guru mapel), dipetakan ke label standar, sumber data SIGRAK, dan butir S-01/S-15 | U-01, U-03 | Pemetaan ke SIGRAK adalah usulan |
| `pilihan-isian.json` | Daftar pilihan formulir: asesmen diagnostik, prestasi, notulen, mutasi, jenis asesmen, bentuk soal, format nilai, kehadiran, sumbangan sukarela | U-08, U-09, U-11, A2 | Setiap daftar memuat label asli (`edunexa`) dan label standar (`sigrak`) |
| `aturan-cetak-edunexa.json` | Margin bawaan, orientasi otomatis, aturan "NIHIL", urutan sampul, aturan tahun dokumen | U-15 | Rujukan, bukan aturan SIGRAK |
| `aturan-progres-edunexa.json` | Rumus target progres, pengecualian guru mapel, definisi hari efektif, pemeriksa bentrok, pengesahan, OMR, bunyi selesai | U-01, U-02, U-04, U-05, U-08, U-14 | Ringkasan perilaku, bukan kode untuk disalin |

## Jejak sumber

Setiap berkas memuat blok `provenance` berisi nama berkas sumber, ukuran byte, dan SHA-256 dari salinan yang dibaca pada 9 November 2026. EDUNEXA terus diperbarui, sehingga ukuran dan hash berkas sumber bisa berbeda bila diunduh lagi. Bila perlu dibandingkan, ambil ulang lewat peramban dari portal yang sama.

## Cara impor yang diusulkan

1. Taruh folder ini apa adanya di `docs/sources/usulan-01/data/` (bahan dari pemilik, tidak diubah).
2. Tim Validasi memeriksa. Setelah disetujui, Claude Code menyalin ke `docs/data/` dengan nama dan struktur yang sesuai konvensi repo:
   - `docs/data/calendar/national-holidays-2026-2027.json`
   - `docs/data/catalogs/cocurricular-activities.json`, `extracurricular.json`, `class-inventory.json`
   - `docs/data/supervision/teaching-administration-components.json`
3. Seeder atau perintah artisan membaca dari `docs/data/`, bukan dari folder `sources`.
