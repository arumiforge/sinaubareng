# Pemetaan Data Materi EDUNEXA untuk Refactor RPM Generator

Oct 29, 2026 · @j

## Ringkasan

Seluruh data materi RPM Generator bersumber dari dua file statis versi **KM-SD-2026-R2**: `data_kurikulum_dengan_cp.json` (401 baris materi pokok, 1.900 fokus materi, 1.272 TP) dan `struktur_alokasi_sd.json` (52 baris alokasi JP + aturan generator). Cakupan: SD/MI kelas 1–6, semester Ganjil (1) dan Genap (2), 11 mata pelajaran.

- Rantai data inti: **Kelas → Mapel → Semester → Materi Pokok (ATP) → Fokus Materi (estimasi JP) → Pertemuan** (dipecah dari jadwal kelas).
- Data sangat konsisten: semua baris punya CP, TP, dan Fokus; jumlah JP fokus selalu sama dengan `alokasi_jp` ATP; tidak ada ID ganda.
- Satu selisih target: **Matematika kelas 4 semester 2 = 80 JP**, padahal target struktur 90 JP.
- Dua mapel ada di kurikulum tapi tidak punya target alokasi: **Keterampilan Tata Boga** (kelas 3–6) dan **Bahasa Inggris kelas 1–2** (kokurikuler).
- Data siswa dan identitas guru tidak dipetakan; bagian administrasi hanya memuat struktur, bukan isi.

Tabel lengkap per fokus materi ada di spreadsheet pendamping (`pemetaan_materi_edunexa.xlsx`).

## Skema data materi

Satu baris `data_kurikulum_dengan_cp.json` = satu **Materi Pokok** (setara satu bab/ATP) untuk satu kelas, mapel, dan semester. Beberapa kolom berisi JSON yang disimpan sebagai string, jadi harus di-`JSON.parse` dulu.

| Field | Tipe | Isi / contoh | Dipakai untuk |
| --- | --- | --- | --- |
| `ID_Kurikulum` | string | `KM-SD-2026-R2-PP-K1-01` (versi-mapel-kelas-urutan) | Kunci utama modul |
| `Legacy_ID`, `Legacy_ID_Aliases` | string, array | `CUR-256333696` | Kompatibilitas modul lama |
| `Versi_Kurikulum`, `Status_Versi` | string | `KM-SD-2026-R2`, `Aktif` | Filter versi |
| `Kelas`, `Fase` | number, string | 1–6, A/B/C | Dropdown Kelas |
| `Semester` | string | `Ganjil (1)` / `Genap (2)` | Filter semester |
| `Mata Pelajaran`, `Status_Mapel` | string | `Pendidikan Pancasila`, `Wajib` | Dropdown Mapel |
| `Jenis_Pembelajaran` | string, opsional | `Kokurikuler` (hanya B. Inggris kelas 1–2) | Pisah intra/kokurikuler |
| `Elemen` | string | dipisah `;` | Identifikasi RPM |
| `Materi Pokok` | string | `Aku dan Teman-Temanku` | Dropdown Materi / judul modul |
| `Capaian Pembelajaran` | string | teks CP fase | Bagian CP di RPM |
| `Data_Tujuan` | JSON string | `topik_materi`, `tujuan_pembelajaran[]`, `kode_tp[]` | Tujuan Pembelajaran |
| `Data_ATP` | JSON string | `kode_atp`, `urutan_semester`, `alokasi_jp`, `jp_per_minggu`, `minggu_mulai`–`minggu_selesai`, `distribusi_mingguan` | Alokasi & urutan |
| `Data_Fokus` | JSON string | `kode_fokus`, `urutan`, `fokus`, `estimasi_jp`, `status_alokasi` | Unit modul + jumlah JP |
| `Fokus_Materi` | JSON string | daftar nama fokus saja | Duplikat `Data_Fokus` (versi lama) |
| `Referensi` | JSON string | `sumber_cp`, `sumber_buku`, `halaman_buku`, `tanggal_penyesuaian` | Daftar pustaka RPM |
| `Audit_Data` | JSON string | status + catatan penyesuaian JP | Jejak audit, tidak tampil |
| `Cabang_Seni`, `Keterampilan_Proses` | opsional | khusus Seni dan IPAS | Konteks tambahan prompt |

Kunci kode: `TP-PP-K1-01-01` (TP), `ATP-PP-K1-BAB-01` (ATP), `FM-PP-K1-01-01` (fokus). Pola ini cukup untuk dijadikan foreign key saat refactor.

## Struktur alokasi JP

`struktur_alokasi_sd.json` menetapkan target JP: **1 JP = 35 menit**, 18 minggu efektif per semester untuk kelas 1–5, dan 18 + 14 minggu untuk kelas 6.

| Kunci | Isi |
| --- | --- |
| `alokasi[]` (52 baris) | `kelas`, `fase`, `mata_pelajaran`, `status`, `jenis_pembelajaran`, `jp_per_minggu`, `minggu_tahun`, `jp_per_tahun`, `semester_1{jp, minggu_efektif}`, `semester_2{...}`; khusus PAI ada `distribusi_topik.semester_n[]{urutan, elemen, materi_pokok, jp}` dan `sumber_kurikulum` |
| `kokurikuler_default[]` | Bahasa Inggris kelas 1–2: 2 JP/minggu, 36 JP per semester |
| `generator_rpm` | `mode_alokasi: berdasarkan_fokus_materi`, `jp_fokus_dapat_diubah_guru: false`, `jumlah_pertemuan_otomatis: true`, `durasi_otomatis: true`, `dimensi_profil_dipilih_ai: true` |
| `sinkronisasi` | Metode pembulatan sisa terbesar per ATP; validasi berbasis JP per minggu (2026-07-28) |
| `referensi_bahasa_jawa`, `referensi_pai` | Sumber buku Parijatha 2025/2026 dan CP PAI 020/2026 |
| `catatan[]` | 9 aturan kebijakan (muatan lokal, seni integratif, dsb.) |

JP per minggu: B. Indonesia 7/8/6/6/6/6, Matematika 4/5/5/5/5/5, IPAS 5 (kelas 3–6), Pancasila 4, PAI/PJOK/Seni 3, B. Jawa/B. Inggris/Koding 2.

## Rekap per kelas dan mapel

Total 203 materi pokok di semester 1 (3.628 JP) dan 198 di semester 2 (3.478 JP). Format sel: **materi S1+S2 · JP S1/S2**.

| Mapel | K1 | K2 | K3 | K4 | K5 | K6 |
| --- | --- | --- | --- | --- | --- | --- |
| PAI dan Budi Pekerti | 5+5 · 54/54 | 5+5 · 54/54 | 5+5 · 54/54 | 5+5 · 54/54 | 5+5 · 54/54 | 5+5 · 54/42 |
| Pendidikan Pancasila | 2+2 · 72/72 | 2+2 · 72/72 | 2+2 · 72/72 | 2+2 · 72/72 | 2+2 · 72/72 | 4+3 · 72/56 |
| Bahasa Indonesia | 4+4 · 126/126 | 4+4 · 144/144 | 2+2 · 108/108 | 4+4 · 108/108 | 3+3 · 108/108 | 2+2 · 108/84 |
| Matematika | 4+4 · 72/72 | 4+4 · 90/90 | 2+3 · 90/90 | 3+3 · 90/**80** | 4+5 · 90/90 | 2+2 · 90/70 |
| IPAS | — | — | 4+4 · 90/90 | 4+4 · 90/90 | 4+4 · 90/90 | 3+3 · 90/70 |
| PJOK | 3+3 · 54/54 | 2+3 · 54/54 | 4+4 · 54/54 | 3+2 · 54/54 | 2+3 · 54/54 | 3+2 · 54/42 |
| Seni dan Budaya | 4+4 · 54/54 | 7+6 · 54/54 | 5+4 · 54/54 | 5+5 · 54/54 | 6+6 · 54/54 | 5+4 · 54/42 |
| Bahasa Inggris | 5+5 · 36/36 (koku) | 5+5 · 36/36 (koku) | 3+3 · 36/36 | 4+2 · 36/36 | 3+3 · 36/36 | 3+3 · 36/28 |
| Bahasa Jawa | 3+3 · 36/36 | 4+3 · 36/36 | 4+4 · 36/36 | 4+4 · 36/36 | 4+4 · 36/36 | 4+4 · 36/28 |
| Koding dan KA | — | — | — | — | 2+2 · 36/36 | 5+5 · 36/28 |
| Keterampilan Tata Boga | — | — | 1+1 · 34/34 | 1+1 · 34/34 | 1+1 · 34/34 | 1+1 · 34/34 |

Semua sel sesuai target `struktur_alokasi_sd.json`, kecuali Matematika K4 S2 (80 dari 90 JP) dan Tata Boga (tanpa target). Dengan asumsi 2 JP per pertemuan, seluruh fokus menghasilkan 3.972 pertemuan; materi pokok terpanjang sampai Pertemuan 42.

## Alur dropdown RPM Generator

&#91;embedded content: alur formulir rpm-generator.html · 8 langkah\]

Guru hanya memilih tanggal, mapel, materi, fokus, dan pedagogi; JP, jumlah pertemuan, dan durasi dikunci oleh data. Modul "Mandiri" (mode manual) melewati penjadwal dan tidak mengubah progres ATP.

## Kerangka output RPM

RPM dihasilkan Gemini (`responseMimeType` JSON + `responseSchema`, model `gemini-3.6-flash` dengan cadangan sampai `gemini-2.5-flash-lite`). Field di bawah diambil dari skema di `rpm-generator.html`, dikelompokkan menurut empat komponen RPM.

| Komponen | Field JSON dari AI | Diisi dari data (bukan AI) |
| --- | --- | --- |
| Identifikasi | `identifikasi_kesiapan_kebutuhan`, `analisis_karakteristik_materi`, `dimensi_profil`, `asesmen_awal{keterangan_awal, prasyarat, soal[]{tipe, opsi, kunci}}` | Kelas, fase, mapel, elemen, CP |
| Desain pembelajaran | `metode`, `sintaks`, `mitra_pembelajaran`, `ruang_fisik`, `budaya_belajar`, `pemanfaatan_digital`, `media_sumber_belajar` | TP + kode TP, materi pokok, fokus |
| Pengalaman belajar | `pertemuan[]{materi_pertemuan, alokasi_menit, pendahuluan, inti_sintaks, penutup, ringkasan_kegiatan}` | Jumlah pertemuan dan durasi (otomatis dari jadwal) |
| Asesmen | `asesmen_formatif`, `prinsip_asesmen`, `teknik`, `indikator`, `rubrik[]{aspek, sangat_baik, baik, berkembang, perlu_bimbingan}`, `soal_evaluasi`, `tindak_lanjut` | — |
| Lampiran | `lkpd_instruksi`, `lkpd_tabel{kolom, baris, ukuran}`, `kartu`, `langkah` | Referensi buku |
| Kesinambungan | `ringkasan_materi_terakhir`, `rekomendasi_berikutnya` | Modul sebelumnya |

Modul kokurikuler memakai skema terpisah: `latar_belakang`, `tujuan_tahunan`, `indikator_keberhasilan`, `prosedur_pelaksanaan`, `fase_tahunan[]{fase, rentang, fokus, aktivitas}`, `peran_guru`, `peran_peserta_didik`, `pemantauan{frekuensi}`, `refleksi_berkala`. Jenisnya: Pembelajaran Kolaboratif Lintas Disiplin, Gerakan 7 KAIH, atau Projek.

Pilihan pedagogi per pertemuan: PjBL, Inkuiri & Discovery Learning, Problem Solving, Game Based Learning, Diskusi kelompok & eksplorasi.

## Bank soal: input awal sebelum generate

`bank-soal.html` memakai file kurikulum yang sama, jadi dropdown Materi di bank soal identik dengan RPM. Kerangkanya terdiri dari parameter paket, komposisi soal, dan pilihan ekspor.

| Kelompok | Field | Pilihan / isi |
| --- | --- | --- |
| Paket | `genJenisPenilaian` | Penilaian Harian, Sumatif Tengah Semester, Sumatif Akhir Semester, Ujian Sekolah/Madrasah |
| Paket | `genTanggal`, `genWaktu` | Tanggal pelaksanaan, durasi |
| Cakupan | `genJenjang`, `genKelas`, `genMapel`, `genMateri` | SD/MI–SMA/MA/SMK/MAK; kelas 1–9 tampil, data materi hanya ada untuk kelas 1–6 |
| Kesulitan | `genLevel` | Campuran (Mudah, Sedang, Sulit), Mudah, Sedang, Sulit |
| Komposisi | `jmlPG`, `jmlPGK`, `jmlBS`, `jmlIsian`, `jmlUraian` | Jumlah PG, PG Kompleks, Benar-Salah, Isian, Uraian |
| Ekspor | `exKisi`, `exSoal`, `exKunci`, `exBahas`, `exIndikator` | Kisi-kisi, naskah soal, kunci, pembahasan, indikator |
| Arsip | `bankSoalMapelFilter`, `bankSoalTanggalFilter`, `selectKartu` | Filter arsip dan pembuatan Kartu Soal |

Yang belum ada di kerangka: level kognitif (C1–C6 / LOTS–HOTS), kaitan soal ke `kode_tp`, dan tipe Menjodohkan. Untuk Penilaian Harian, cakupan cukup satu materi pokok; untuk STS/SAS perlu pilihan banyak materi per semester.

## Penilaian, administrasi, role, dan tanggal

Bagian ini hanya memetakan struktur penyimpanan dan alur. Koleksi berisi data siswa (`class_rosters`, `student_mutations`) dan profil pengguna sengaja tidak dibuka isinya.

### Penilaian

- Ada di dasbor utama (`index.html`), dengan aktivitas: Penilaian, Program Penilaian, Analisis Nilai, Taraf Serap. Kartu menu: observasi pengolahan nilai, cetak leger, inject e-Rapor.
- Konfigurasi satu penilaian: `jenisPenilaian`, `mapel`, `materi`, `tanggalValue`, `items`, `format`, `period`. Jenis dibagi Formatif dan Sumatif.
- Cadangan lokal di IndexedDB `edunexa_penilaian_backup_v1` (`nilai`, `pengaturan`).

### Administrasi kelas (`administrasi.html`)

| Menu | Sub-bagian / isi | Simpan di |
| --- | --- | --- |
| Kurikulum | Muatan Pelajaran, Struktur CP/TP, Daftar TP, ATP, Prota, Promes | `academic_curriculum_versions`, `academic_reference_*` |
| Kaldik & Jadwal | Kalender pendidikan, jadwal mapel, pengaturan waktu | `institution_kaldik`, `kaldik_calon`; jadwal dibaca RPM dari section `__learning`/`__learning2` |
| Jurnal & Refleksi | Jurnal per pertemuan, refleksi, status terlaksana | `kelas_admin_sections/{docId}__learning` + `__learning2` (data pembelajaran dipecah dua dokumen) |
| Absensi | Daftar hadir, rekap presensi | `kelas_admin_sections/{docId}__attendance` |
| Keuangan | Transaksi, buku kas besar, pembayaran | `kelas_admin_sections/{docId}__finance` |
| Inventaris, Kenaikan | Barang kelas; rencana naik/lulus | `class_inventory`, `promotion_plans` |
| Buku Catatan | Catatan anekdot, BK, buku tamu, humas, prestasi, notulen | `institution_notulen` (notulen); lokasi catatan lain belum terverifikasi |
| Kokurikuler | Rencana kegiatan sekolah | `school_cocurricular_plans` |

Pola `docId` kelas: `{sekolah}__{tahunAjaran}__kelas-{n}__{username}`. Target Progres Administrasi (Modul Ajar, Jurnal, Absensi) dihitung dari slot JP jadwal dalam rentang tanggal yang dipilih.

### Role

| Role | Nilai di kode | Cakupan |
| --- | --- | --- |
| Super Admin | `superadmin` (halaman `/superadmin.html`) | Semua institusi, pesan admin, statistik sistem |
| Admin / Operator sekolah | `Admin` | Kelas, anggota institusi, profil sekolah, tahun ajaran |
| Kepala Sekolah | portal `/kepala-sekolah/` | Supervisi, tugas, notifikasi, monitoring administrasi guru |
| Pengawas | `Pengawas`, portal `/pengawas/` | Pemantauan lintas sekolah |
| Guru Kelas (wali kelas) | `teacher` + `waliKelasUtama` | Semua mapel kelasnya, kecuali mapel yang diampu guru mapel |
| Guru Mapel | `teacher` + `guruMapel: true` | Mapel yang diampu di beberapa kelas |

### Tanggal

- Tahun ajaran mulai dari profil (bawaan 1 Juli); tanggal mulai semester genap wajib setelah mulai tahun ajaran.
- `rpmTanggal` = tanggal mulai modul; dropdown Mapel baru terisi setelah tanggal dipilih.
- Jadwal modul: `meetingSchedule[]{meeting, scheduledDate, plannedDate, status, journalId, minutes}`, riwayat maksimal 3 versi (`scheduleHistory`).
- Pertemuan bertanda tidak terlaksana di Jurnal membuat modul bergeser otomatis ke slot JP berikutnya; modul semester 2 menunggu tanggal mulai semester genap.

## Temuan dan rekomendasi refactor

Data materi sudah siap dipakai; pekerjaan refactor terutama soal bentuk data, bukan isinya.

1. **Pecah satu file besar jadi entitas berkode.** Parse kolom JSON-string saat build, lalu simpan sebagai `mapel`, `materi_pokok` (ATP), `fokus`, `tp`, dihubungkan lewat `ID_Kurikulum`, `kode_atp`, `kode_fokus`, `kode_tp`. Hapus `Fokus_Materi` karena duplikat `Data_Fokus`.
2. **Jadikan pertemuan entitas sendiri.** `pertemuan[] = {nomor, kode_fokus, jp, menit, tanggal}` dibangun dari `estimasi_jp` dibagi slot JP jadwal. Dropdown "Pertemuan 1..n" cukup membaca daftar ini.
3. **Kaitkan TP ke fokus.** Sekarang TP hanya menempel di materi pokok; tambahkan `kode_tp` di tiap fokus agar asesmen, bank soal, dan taraf serap bisa dihitung per TP.
4. **Perbaiki data alokasi.** Matematika K4 S2 kurang 10 JP; tambahkan target untuk Keterampilan Tata Boga; pastikan B. Inggris K1–2 tetap ditandai kokurikuler.
5. **Tetapkan satu sumber kebenaran.** Kurikulum ada di JSON statis dan juga di Firestore (`academic_curriculum_versions`); tulis urutan prioritas dan aturan versinya.
6. **Samakan kerangka bank soal dengan RPM.** Tambah `kode_tp`, level kognitif, tipe Menjodohkan, dan pilih banyak materi untuk STS/SAS. Sembunyikan kelas 7–9 sampai datanya ada.
7. **Hubungkan penilaian ke TP.** Item penilaian yang membawa `kode_tp` membuat Analisis Nilai dan Taraf Serap otomatis per TP tanpa input ulang.
