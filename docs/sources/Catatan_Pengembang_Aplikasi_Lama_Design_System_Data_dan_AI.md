# Catatan Pengembang EDUNEXA: Design System, Data, dan AI

Oct 29, 2026 · @j

## Ringkasan keputusan

Rekomendasi utama: pindah ke basis data relasional, tetapi bertahap. Data EDUNEXA (kurikulum, jadwal, modul, nilai, sekolah binaan) saling berelasi dan dipakai lintas sekolah, jadi SQL lebih cocok daripada dokumen NoSQL.

| Topik | Keputusan | Alasan singkat |
| --- | --- | --- |
| Basis data | MySQL 8 dengan CodeIgniter 4 sebagai API; Supabase (Postgres) sebagai alternatif tanpa server sendiri | Laporan kepala sekolah dan pengawas butuh query lintas kelas dan sekolah; NoSQL sekarang memaksa unduh dokumen besar |
| Frontend | Vanilla JS (ES modules) + design system sendiri, tanpa Tailwind CDN | File HTML 0,6–1,3 MB dengan script inline sulit dirawat dan lambat di HP |
| Kunci AI | Wajib per guru, dipanggil lewat server (proxy), tidak disimpan polos | Kuota Gemini dihitung per project, jadi 1 kunci sekolah = semua guru berebut kuota |
| Mobile | PWA untuk jurnal, absensi, dan catatan di kelas, bisa offline lalu sinkron | Pencatatan terjadi saat pelajaran, sering di sinyal lemah |
| Ekspor | PDF teks (bukan gambar) dibuat di server, DOCX dari template, tombol "Buka di Canva" | PDF sekarang hasil html2canvas: berat, teks tidak bisa dipilih |
| Kurikulum | Satu sumber berversi di SQL; sekolah bisa pakai bawaan atau menambah/menimpa per sekolah | Banyak sekolah, perubahan CP datang bertahap |

## Kondisi sekarang

&#91;embedded content: dibaca dari kode portal-edunexa.web.app, 29 Okt 2026\]

Tidak ada server aplikasi: setiap halaman membawa logikanya sendiri, sehingga aturan yang sama (filter mapel, hitung JP, ekspor) ditulis ulang di beberapa file. Kotak kuning menandai titik paling rawan, yaitu kunci AI yang keluar dari perangkat guru tanpa perantara.

## NoSQL ke SQL?

Ya, perlu SQL, karena masalah utama sekarang adalah relasi dan laporan, bukan skala. CI4 + MySQL + vanilla JS layak dan murah dirawat; yang wajib ditambah adalah lapisan offline untuk HP.

| Opsi | Kelebihan | Kekurangan | Cocok jika |
| --- | --- | --- | --- |
| Tetap Firebase, struktur dirapikan | Tidak ada migrasi; realtime dan offline bawaan | Query lintas sekolah tetap mahal; aturan keamanan rumit; biaya baca naik per sekolah | Hanya sedikit sekolah dan tanpa laporan pengawas |
| **CI4 + MySQL 8 + vanilla JS** | Relasi, transaksi, dan laporan mudah; hosting lokal murah; tim PHP cepat produktif | Harus membangun auth, sinkron offline, dan backup sendiri | Tim nyaman dengan PHP dan ingin kontrol penuh (rekomendasi) |
| Supabase (Postgres) + vanilla JS | SQL + auth + row level security + realtime siap pakai | Ketergantungan layanan; hosting di luar negeri; RLS perlu disiplin | Ingin SQL tanpa mengelola server |
| Firestore + ekspor BigQuery | Laporan jalan tanpa ubah aplikasi | Dua sistem; biaya dan kerumitan bertambah | Hanya butuh dasbor analitik |

Catatan untuk CI4:

- Pisah jadi REST API (`/api/v1/...`) dan halaman statis; jangan render seluruh aplikasi dari satu controller.
- Multi-sekolah: setiap tabel transaksi punya `school_id`, dan filter ini dipaksa di model dasar (bukan di tiap query).
- Auth: sesi berbasis cookie HttpOnly untuk web, token pendek untuk PWA; role diperiksa di filter CI4.
- Offline: PWA menyimpan antrean di IndexedDB, mengirim ke `/api/v1/sync` dengan `client_uuid` agar kiriman ulang tidak menggandakan data.
- Jangan ikut memindah semua sekaligus; mulai dari data yang paling relasional (kurikulum, modul, bank soal).

## Skema relasional usulan

Kurikulum dipisah menjadi data bawaan (global, berversi) dan penyesuaian sekolah, supaya sekolah bisa memakai data yang ada atau menambah sendiri tanpa merusak data bersama.

| Kelompok | Tabel | Kolom kunci |
| --- | --- | --- |
| Sekolah & akun | `schools`, `users`, `school_members`, `academic_years`, `semesters`, `classes`, `class_teachers` | `school_id`, `role` (super\_admin, admin, kepsek, pengawas, guru\_kelas, guru\_mapel), `start_date` semester genap |
| Kurikulum bawaan | `curriculum_versions`, `subjects`, `subject_allocations`, `topics` (materi pokok/ATP), `focus_items`, `learning_objectives`, `references` | `version_code` (KM-SD-2026-R2), `kode_atp`, `kode_fokus`, `kode_tp`, `estimasi_jp` |
| Penyesuaian sekolah | `school_topic_overrides`, `school_custom_topics`, `curriculum_validations` | `school_id`, `base_topic_id` (null = materi baru), status validasi, catatan pengawas |
| Jadwal & kalender | `calendars`, `holidays`, `timetable_slots` | `class_id`, hari, jam ke, `subject_id`, `jp` |
| Modul (RPM) | `modules`, `module_focus_items`, `meetings`, `meeting_contents` | `module_id`, nomor pertemuan, `planned_date`, `status` (rencana/terlaksana/tidak terlaksana) |
| Jurnal & kelas | `journals`, `attendance_records`, `class_notes` (anekdot, BK, prestasi) | `meeting_id`, `client_uuid` untuk sinkron offline |
| Bank soal | `question_banks`, `questions`, `question_options`, `question_objectives` | `type` (PG, PGK, BS, isian, uraian, menjodohkan), `cognitive_level`, `kode_tp` |
| Penilaian | `assessments`, `assessment_items`, `scores` | `assessment_type` (formatif/sumatif/STS/SAS), `kode_tp`, KKTP |
| AI | `ai_credentials`, `ai_usage_logs`, `prompt_templates` | kunci terenkripsi per `user_id`, token dan biaya per panggilan |
| Ekspor | `export_templates`, `school_branding` | kop, logo, tanda tangan, ukuran kertas, font |

Data siswa (`students`, `enrollments`) tetap terpisah dan hanya bisa dibaca oleh guru kelas terkait, admin sekolah, dan kepala sekolah.

## Design system baru

Tujuan: tampilan tenang seperti alat kerja, bukan poster. Ciri "AI slop" yang terlihat di versi sekarang: kartu kaca gelap dengan glow hijau, label huruf kapital 9–10 px, banyak badge berwarna, tombol hanya ikon, dan teks hijau pucat di atas putih pada mode terang (kontras rendah, terlihat di RPM Generator lebar 390 px).

| Hapus | Ganti dengan |
| --- | --- |
| Glassmorphism, gradien, glow, blur | Permukaan polos: latar abu sangat muda, kartu putih, garis 1 px |
| Banyak warna aksen per kartu/ikon | Satu warna merek + warna status (berhasil, peringatan, bahaya, info) |
| Label kapital kecil (`text-[9px] uppercase`) | Label kalimat biasa, minimal 13 px, tebal 600 |
| Tombol ikon tanpa teks | Ikon + teks; ikon saja hanya untuk aksi universal (tutup, cari) dengan `aria-label` |
| Badge "Kurang 232" di tiap baris | Progress bar tipis + angka; warna hanya bila terlambat |
| `user-scalable=no` | Izinkan zoom (aksesibilitas) |
| Tailwind CDN + `lucide@latest` | CSS token sendiri; ikon di-bundle dengan versi terkunci |

Token dasar (CSS custom properties):

- **Warna:** `--bg #F7F8F7`, `--surface #FFFFFF`, `--border #E3E6E4`, `--text #1C2420`, `--text-muted #5B6660`, `--brand #0F766E` (teal tua, lolos kontras 4,5:1 di atas putih), `--ok`, `--warn`, `--danger`, `--info`; mode gelap memakai set terpisah, bukan inversi.
- **Tipografi:** Plus Jakarta Sans atau Inter; skala 13 / 15 / 17 / 20 / 24 / 30; isi bacaan 16 px di HP.
- **Ruang & bentuk:** kelipatan 4 px; radius 6 (input), 10 (kartu); bayangan hanya untuk menu melayang.
- **Komponen inti:** Button (primer, sekunder, teks), Input, Select, Combobox, Checkbox, Tabs, Table + versi kartu untuk HP, Stepper (alur RPM), Toast, Dialog, Bottom sheet, Empty state, Skeleton.

Aturan mobile: target sentuh minimal 44 px; navigasi bawah 4–5 item di HP; tabel lebar berubah jadi daftar kartu di bawah 640 px; formulir satu kolom; tombol aksi utama menempel di bawah layar.

## AI: kunci per guru dan fitur yang fleksibel

Kunci per guru memang benar, tapi itu baru efektif jika tiap guru membuat kunci di **project Google miliknya sendiri**: Gemini menghitung batas "per project, not per API key" ([dokumentasi rate limits](https://ai.google.dev/gemini-api/docs/rate-limits)). Kunci sekolah yang dipakai bersama berarti semua guru berbagi satu kuota.

Kondisi sekarang (dari kode):

- Tiap akun guru sudah diminta kunci sendiri; tersimpan di `localStorage` (`edunexa_gemini_key_{username}`) dan disalin ke `users/{username}/profile.apiKey` di Realtime Database.
- Profil Kepala Sekolah juga mewajibkan "API Key Gemini".
- Panggilan ke Gemini langsung dari browser, jadi kunci terlihat di DevTools dan di jaringan.

Usulan:

1. Simpan kunci di tabel `ai_credentials`, terenkripsi (libsodium / `openssl_encrypt` dengan kunci di env), hanya bisa ditulis, tidak pernah dikirim balik ke browser.
2. Semua panggilan lewat endpoint server `/api/v1/ai/generate`; server menambahkan kunci milik guru yang login.
3. Catat `ai_usage_logs` (fitur, model, token, status, waktu) agar guru melihat pemakaiannya dan admin bisa melihat siapa yang sering terkena batas, tanpa melihat kuncinya.
4. Halaman "Kunci AI saya": tombol uji kunci, status terakhir, panduan langkah demi langkah membuat kunci dengan akun Gmail, atau belajar.id bila layanan AI Studio diizinkan admin domainnya.
5. Kunci sekolah hanya sebagai cadangan opsional yang diaktifkan admin, dengan batas harian per guru.

Fitur AI yang bisa dibuka untuk guru (memakai kunci mereka sendiri):

- **Template prompt yang bisa diubah sekolah** (`prompt_templates`): gaya bahasa, panjang, contoh lokal daerah.
- **Perbaiki bagian tertentu**: guru memilih satu bagian RPM (mis. kegiatan inti pertemuan 2) dan minta disederhanakan, dibuat diferensiasi, atau disesuaikan siswa berkebutuhan khusus.
- **Asisten jurnal**: ringkas catatan suara/teks singkat dari HP menjadi jurnal yang rapi.
- **Soal dari TP**: bank soal dibuat per `kode_tp` dengan level kognitif dan kunci pembahasan.
- **Deskripsi rapor**: draf deskripsi capaian dari nilai per TP, diedit guru sebelum dipakai.
- **Media ajar**: LKPD dan worksheet sesuai tema, lalu dibuka di Canva untuk dihias.

## Ekspor PDF/Excel dan Canva

Ekspor sekarang tersebar per halaman: RPM (`exportWord`, `exportPDF`, `cetakModul`), bank soal (modal ekspor + kartu soal PDF), worksheet (Word/PDF), dan administrasi (banyak `export*`/`cetak*` ke Excel via `xlsx-js-style`). PDF dibuat dengan html2canvas + jsPDF, jadi hasilnya gambar: file besar, teks tidak bisa dicari atau disalin, dan potongan halaman bisa memotong baris.

Usulan mesin ekspor tunggal:

- **Satu model dokumen** (JSON) untuk modul, bank soal, dan laporan; semua format dibuat dari model ini, bukan dari tangkapan layar.
- **PDF teks** di server: Dompdf/mPDF (PHP) atau Chromium headless; aturan `page-break-inside: avoid` per tabel/kegiatan.
- **DOCX** dari template Word milik sekolah (PhpWord TemplateProcessor), jadi kop, font, dan tanda tangan bisa diganti tanpa ubah kode.
- **Excel** dengan sheet terpisah dan kolom yang bisa difilter; nilai angka tetap angka, bukan teks.
- **Pengaturan cetak per sekolah** (`school_branding`): kop, logo, nama dan NIP penandatangan, ukuran kertas (A4/F4), margin, font, dan bagian mana yang ditampilkan.
- **Pratinjau sebelum unduh** dan pilihan "sertakan lampiran: LKPD, rubrik, kunci".

Canva: guru dengan akun pendidikan bisa memakai Canva for Education gratis ([Canva](https://www.canva.com/id_id/help/about-canva-for-education/)). Dua tingkat integrasi:

1. **Tanpa API (cepat):** tombol "Buka di Canva" yang mengunduh DOCX/PDF lalu membuka canva.com; guru mengimpor file sendiri.
2. **Dengan Canva Connect API:** OAuth per guru, lalu endpoint *design import* mengirim PDF/DOCX/PPTX langsung menjadi desain di akun guru ([Design import](https://canva.dev/docs/connect/api-reference/design-imports)); batas 20 permintaan per menit per pengguna. Autofill dari brand template butuh paket yang mendukung Autofill dan MFA aktif ([Autofill guide](https://www.canva.dev/docs/connect/autofill-guide/)), jadi jangan dijadikan syarat.

## Rencana bertahap dan risiko

&#91;embedded content: rencana usulan · 5 tahap, 4 gerbang\]

Kurikulum dipindah lebih dulu karena paling relasional dan tidak berisi data siswa, jadi risikonya paling kecil.

Risiko utama:

- **Data ganda selama transisi:** selama tahap 1–4, tiap modul hanya boleh ditulis di satu sistem; tandai modul hasil migrasi dengan `legacy_id`.
- **Jaringan sekolah lemah:** PWA wajib bisa menyimpan jurnal dan absensi offline sebelum menggantikan versi lama.
- **Kunci AI bocor:** cabut kebiasaan menyimpan kunci di profil yang bisa dibaca klien sebelum proxy server aktif.
- **Format ekspor berubah:** sekolah sudah terbiasa dengan tampilan lama; sediakan template "klasik" selama satu tahun ajaran.
