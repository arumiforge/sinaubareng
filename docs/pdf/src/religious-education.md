# Kurikulum Pendidikan Agama (SD dan MI)

<p class="source-note">Diterjemahkan dari <code>docs/final/religious-education.md</code> pada 9 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-07
- **Terakhir diperbarui:** 2026-11-09
- **Sumber:** `KMA_No_1503_Tahun_2025_….pdf`, `Salinan_Kepka_BKPDM_No._020_Tahun_2026_….pdf`, `SK_Dirjen_Pendis_No_9941_Tahun_2025_….pdf`, `SK_Dirjen_Pendis_No_6077_Tahun_2025_….pdf` (semuanya di `docs/sources/`)

## Tujuan

Dokumen ini menetapkan cara SIGRAK mencakup pendidikan agama di SD (enam agama) dan di MI (mata pelajaran PAI dan Bahasa Arab dari Kementerian Agama). Dokumen ini mencantumkan sumber resmi, data yang sudah ada, yang masih harus dibangun tim, dan cara model data menangani agama. Keputusan: D-034, D-035, D-036, D-037, D-038.

## Sumber resmi

| Sumber | Cakupan | Bentuk | Dipakai untuk |
|---|---|---|---|
| **Keputusan Kepala BKPDM No. 020 Tahun 2026** (11 Juni 2026), mengubah Kepka BSKAP 046/H/KR/2025 | Lampiran I: CP Pendidikan Agama dan Budi Pekerti untuk Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu, Fase A–F. Lampiran II: versi pendidikan khusus (SLB) | Salinan pindaian, tanpa lapisan teks | CP agama SD (D-034). Lampiran II: CP pendidikan khusus, masuk cakupan untuk sekolah inklusi (D-155), belum ditranskripsi |
| **SK Dirjen Pendis No. 9941 Tahun 2025** (28 November 2025) | CP Al-Qur'an Hadis, Akidah Akhlak, Fikih, Sejarah Kebudayaan Islam, Bahasa Arab di MI/MTs/MA, ditambah mata pelajaran MAPK dan RA | PDF dengan lapisan teks, ditambah surat pengantar Dirjen B-606 (1 Desember 2025, ditandatangani elektronik BSrE) dan surat pengantar Kanwil DKI Jakarta (16 Desember 2025) | CP MI (D-035) |
| **KMA 450 Tahun 2024 sebagaimana diubah KMA 1503 Tahun 2025** | Pedoman kurikulum madrasah, termasuk struktur mata pelajaran dan JP MI (Lampiran II, BAB III B, Tabel 1–5, hlm. 28–31) | Keputusan resmi (22 September 2025), salinan pindaian dari pemilik | Alokasi MI (D-038), data di `mi-allocation-kma-1503-2025.json` |
| **KMA No. 512 Tahun 2026** (31 Maret 2026, berlaku sejak tanggal ditetapkan) | Pedoman Kurikulum Berbasis Cinta: KBC sebagai pendidikan nilai untuk satuan pendidikan Kemenag *dan* guru pendidikan agama di sekolah umum, termasuk SD; Panca Cinta, enam nilai dasar, enam prinsip, 12 metode yang disarankan, 10 instrumen asesmen, tingkat praktik | Keputusan resmi, PDF hasil pindai dari owner (9 November 2026) | Sumber KBC sejak D-359, lihat bagian MI: Kurikulum Berbasis Cinta |
| **SK Dirjen Pendis No. 6077 Tahun 2025** (22 Juli 2025) | Panduan Kurikulum Berbasis Cinta (KBC) untuk semua madrasah: lima tema Panca Cinta, contoh pembelajaran, contoh pemetaan CP ke tema | PDF dengan lapisan teks, dari pemilik | Digantikan KMA 512/2026 sebagai sumber KBC (D-359); tetap menjadi acuan untuk contoh pembelajaran dan pemetaan CP ke tema |
| **KM-SD-2026-R2** (data kurikulum aplikasi lama) | PAI SD dengan topik, TP, ATP, fokus, JP | JSON | Sudah merujuk "CP PAI 020/2026", keputusan yang sama |

Tidak ditemukan perubahan yang lebih baru atas SK 9941/2025 maupun KMA 1503/2025 pada 2026-10-30.

**Asal-usul berkas.** Salinan BKPDM 020/2026 berasal dari pemilik. Berkas SK 9941/2025 diunduh dari mirror publik, karena tidak ditemukan tautan langsung di kemenag.go.id. Berkas itu memuat surat pengantar resmi dan teks keputusan. Salinan kedua dari sumber lain (situs sebuah madrasah, dari pemilik pada 2026-10-31) memiliki teks yang sama di seluruh 106 halaman, jadi teksnya hampir pasti asli; salinan itu hanya tidak memuat halaman sampul Kanwil DKI. Ganti dengan salinan dari kanal resmi (kemenag.go.id, pendis.kemenag.go.id, atau situs Kanwil/Kankemenag) bila tersedia. Prioritas rendah.

## SD: enam mata pelajaran agama (D-034)

Keenamnya masuk v1. Setiap agama adalah mata pelajaran tersendiri; siswa mengikuti yang sesuai dengan agama yang tercatat.

| Agama | Mata pelajaran | Elemen (subelemen) | Halaman CP SD | Status data |
|---|---|---|---|---|
| Islam | Pendidikan Agama Islam dan Budi Pekerti | Al-Qur'an Hadis, Akidah, Akhlak, Fikih, Sejarah Peradaban Islam | 6–8 | **Lengkap** di KM-SD-2026-R2 (topik, TP, fokus, JP). Cocokkan teks CP-nya dengan keputusan |
| Kristen | Pendidikan Agama Kristen dan Budi Pekerti | Allah Berkarya (Pencipta, Pemelihara, Penyelamat, Pembaru); Manusia dan Nilai-nilai Kristiani (Hakikat Manusia, Nilai-nilai Kristiani); Gereja dan Masyarakat Majemuk (Tugas Panggilan Gereja, Masyarakat Majemuk); Alam dan Lingkungan Hidup (Alam Ciptaan Allah, Tanggung Jawab Manusia terhadap Alam) | 12–14 | Hanya CP |
| Katolik | Pendidikan Agama Katolik dan Budi Pekerti | Pribadi murid, Yesus Kristus, Gereja, Masyarakat | 20–23 | Hanya CP |
| Hindu | Pendidikan Agama Hindu dan Budi Pekerti | Kitab Suci Weda, Śraddhā dan Bhākti, Susila, Acara, Sejarah Agama Hindu | 29 | Hanya CP |
| Buddha | Pendidikan Agama Buddha dan Budi Pekerti | Sejarah, Ritual, Etika | 34–36 | Hanya CP |
| Khonghucu | Pendidikan Agama Khonghucu dan Budi Pekerti | Sejarah Suci, Kitab Suci, Keimanan, Tata Ibadah, Perilaku Jūnzǐ | 40–42 | Hanya CP |

- **Data CP:** `cp-pendidikan-agama-sd-bkpdm-020-2026.json` berisi 96 capaian di Fase A–C. Data ini ditranskripsi dengan membaca halaman pindaian, karena tidak ada versi teksnya, dan sudah dicocokkan kata demi kata dengan pindaian di P2-01 (`verified: true`). Persetujuan guru agama masih diperlukan (D-037).
- **Letak CP di tingkat elemen:**
  - Islam, Hindu, dan Khonghucu memberi satu CP per elemen per fase.
  - Kristen memberi satu CP per subelemen, ditambah ringkasan fase. "Allah Penyelamat" dan "Allah Pembaru" tidak punya CP di Fase A.
  - Katolik memberi satu CP per elemen, ditambah ringkasan fase.
  - Buddha punya tiga CP panjang per fase.
- **JP:** setiap mata pelajaran agama memakai alokasi yang sama dengan PAI di struktur nasional: 3 JP per minggu, 54/54 JP per semester untuk kelas 1–5 dan 54/42 untuk kelas 6. Sudah dipastikan oleh Permendikdasmen 13/2025, Lampiran II B, Tabel 1–5: keenam agama berbagi satu baris (108 JP intrakurikuler per tahun, 96 di kelas 6, ditambah 36 atau 32 JP kokurikuler). Data: `curriculum-structure-permendikdasmen-13-2025.json`.
- **Guru:** secara bawaan setiap mata pelajaran agama diajar guru mapel (D-032), satu guru per agama. Sekolah kecil bisa punya satu guru yang mengajar beberapa kelas.

## MI: mata pelajaran Kemenag (D-035)

MI tidak mengajarkan satu mata pelajaran PAI. MI mengikuti struktur Kementerian Agama:

| Mata pelajaran | Kelas | JP per tahun (intrakurikuler + kokurikuler) | Elemen | Halaman CP (SK 9941, PDF) |
|---|---|---|---|---|
| Al-Qur'an Hadis | 1–6 | 72 (+36 koku di kelas 1–2); kelas 6: 64 | Pemahaman Konsep (Tajwid, Al-Qur'an, Hadis), Keterampilan Proses | 13–16 |
| Akidah Akhlak | 1–6 | 72 (+36 koku di kelas 5); kelas 6: 64 (+32 koku) | Pemahaman Konsep (Akidah, Akhlak, Adab, Kisah Keteladanan), Keterampilan Proses | 44–48 |
| Fikih | 1–6 | 72 (+36 koku di kelas 3–4); kelas 6: 64 | Pemahaman Konsep (Ibadah, Muamalah, Ushul Fikih; tidak semuanya ada di setiap fase), Keterampilan Proses | 70–73 |
| Sejarah Kebudayaan Islam | **3–6** (Fase B–C) | 72; kelas 6: 64 | Pemahaman Konsep, Keterampilan Proses | 90–92 |
| Bahasa Arab | 1–6 | 72; kelas 6: 64 | Komponen Bahasa, Keterampilan Bahasa | 99–101 |

*Angka JP sudah dicocokkan dengan KMA 1503/2025 Lampiran II, Tabel 1–5 (hlm. 28–31). Alokasi MI lengkap untuk setiap mata pelajaran: `mi-allocation-kma-1503-2025.json`.*

- **MI tetap memakai tabel KMA 1503/2025.** Permendikdasmen 13/2025 menyebut MI dalam tabel SD-nya, tetapi madrasah mengikuti KMA 1503/2025 (D-038). Di sana, JP kokurikuler diambil dari sebagian mata pelajaran per kelas, bukan dari setiap mata pelajaran seperti di SD.
- **36 minggu per tahun, 32 di kelas 6. 1 JP = 35 menit.** Madrasah boleh menambah sampai 6 JP per minggu (KMA 1503/2025, hlm. 32). JP "koku" dialihkan ke program kokurikuler. Penjadwalan RPM hanya menghitung JP intrakurikuler, dan JP kokurikuler milik modul Kokurikuler.
- **Mata pelajaran MI lainnya** (Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS mulai kelas 3, PJOK, Seni dan Budaya, Bahasa Inggris mulai kelas 3, Koding dan KA (mata pelajaran pilihan di kelas 5–6 menurut KMA 1503/2025; di SIGRAK berjalan lewat kokurikuler, D-208), Muatan Lokal) memakai CP nasional, tetapi dengan tabel alokasi MI sendiri dari KMA 1503/2025 (`mi-allocation-kma-1503-2025.json`). Muatan Lokal mengambil 72–216 JP per tahun (64–192 di kelas 6), dan siswa memilih satu seni (Seni Musik, Rupa, Teater, atau Tari).
- **Data CP:** `cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` berisi teks Fase A–C untuk kelima mata pelajaran, diambil dari lapisan teks PDF dan disusun menjadi baris elemen dan subelemen di P2-02 (D-039). Enam bacaan Bahasa Arab diketik ulang dari gambar halaman. Teks ditampilkan sesuai cetakan, dan sembilan baris yang mungkin memuat kesalahan sumber diberi `review_notes` (D-127). Tim Validasi SIGRAK masih harus meninjau dan menyetujui data sebelum diimpor (D-113).
- **Bahan ajar:** Kemenag menerbitkan Buku Teks Utama (BTU) gratis untuk mata pelajaran ini yang selaras dengan KMA 1503/2025. Buku ini sumber rujukan yang wajar untuk RPM, seperti buku Parijatha untuk Bahasa Jawa.
- **Guru:** kelima mata pelajaran diajar **guru mapel** (D-037). Secara bawaan statusnya `subject_teacher`, dan operator tetap bisa menugaskan ulang per kelas.
- **Agama di MI:** MI adalah sekolah Islam, jadi di sana hanya ada mata pelajaran Kemenag, tanpa mata pelajaran agama lain.

### MI: Kurikulum Berbasis Cinta (KBC)

**Sumber sejak 9 November 2026: KMA 512/2026 (D-359).** Keputusan Menteri Agama No. 512 Tahun 2026 menetapkan Pedoman Kurikulum Berbasis Cinta (ditetapkan 31 Maret 2026 dan berlaku sejak tanggal itu). Sumber: `KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf` (nomor halaman cetak). Tambahannya terhadap panduan 6077 di bawah:

- **Ruang lingkup** (Diktum KESATU; Lampiran §C, hlm. 6): semua satuan pendidikan Kemenag (RA, MI, MTs, MA, MAK, dan sekolah keagamaan lain) *serta* pendidikan agama di sekolah umum, termasuk TK, SD, dan SDLB.
- **Dua ranah** (Diktum KEDUA–KEEMPAT; §D–F, hlm. 12–15): program kurikuler (intra-, ko-, dan ekstrakurikuler, sebagai arus nilai lintas mata pelajaran) dan pembudayaan nilai (kebijakan satuan pendidikan, kepemimpinan berbasis keteladanan, lingkungan yang aman dan inklusif, tradisi satuan pendidikan).
- **Panca Cinta** (hlm. 9): cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air. **Enam nilai dasar** yang diturunkan darinya (hlm. 8–11): spiritual, personal, sosial, ekologis, kebangsaan, intelektual.
- **Enam prinsip** (hlm. 12): transformatif, integratif, kontekstual, reflektif, berkelanjutan, kolaboratif.
- **Pendekatan dan metode** (§G, hlm. 15–19): pembelajaran mendalam dengan pembelajaran berkesadaran dan menggembirakan; 12 metode yang disarankan (pembiasaan, pembelajaran berbasis pengalaman, dialog nilai, penceritaan, dilema moral, pembelajaran berbasis pelayanan sosial, pembelajaran sosial-emosional, refleksi naratif, proyek berbasis karakter, inkuiri nilai, pedagogi kontemplatif, refleksi terbimbing), masing-masing dengan jenjang yang cocok; pembiasaan dan penceritaan cocok untuk kelas awal.
- **Asesmen** (§I, hlm. 21–22): otentik dan berkelanjutan, dengan 10 instrumen (observasi, jurnal refleksi, portofolio, proyek sosial, catatan perkembangan, penilaian diri dan teman sebaya, wawancara nilai, refleksi naratif, skala sikap). Hasilnya terintegrasi, dilaporkan lewat Akhlak atau Budi Pekerti dan catatan sikap, dan tidak pernah dipakai untuk memberi label, menghakimi, atau memeringkat murid.
- **Masih akan terbit:** panduan teknis dari Dirjen berisi capaian KBC per fase, strategi, asesmen, dan pemantauan (§J, hlm. 22). Pemantauan memakai tiga tingkat praktik: merintis, berkembang, mekar (hlm. 23–24).

Panduan 6077 di bawah tetap berguna untuk contoh pembelajaran dan pemetaan CP ke tema.

SK Dirjen Pendis No. 6077 Tahun 2025 menetapkan Panduan Kurikulum Berbasis Cinta sebagai acuan pembelajaran di setiap madrasah, negeri maupun swasta (Diktum KEDUA). Sumber: `SK_Dirjen_Pendis_No_6077_Tahun_2025_Panduan_Kurikulum_Berbasis_Cinta.pdf` (nomor halaman di bawah adalah halaman PDF).

- **Tidak ada mata pelajaran baru dan tidak ada perubahan JP.** Nilai KBC disisipkan ke mata pelajaran yang sudah ada (intrakurikuler, kokurikuler, dan budaya sekolah). Alokasi MI dari KMA 1503/2025 tetap.
- **Panca Cinta, lima tema** (Tabel 1, hlm. 31–35), masing-masing dengan tujuan dan materi inti: Cinta Allah dan Rasul-Nya · Cinta Ilmu · Cinta Lingkungan · Cinta Diri dan Sesama Manusia · Cinta Tanah Air.
- **Kolom rencana pembelajaran.** Contoh rencana pembelajaran (Lampiran 2, hlm. 72–91) menambah dua baris di blok identitas: *Tema Kurikulum Berbasis Cinta* (satu atau lebih dari lima tema) dan *Materi Insersi* (nilai yang disisipkan). Contoh MI-nya adalah IPAS Fase C, Ekosistem, tema Cinta Allah dan Cinta Lingkungan, 2 × 35 menit (hlm. 75–77). TP, kegiatan, dan asesmen lalu membawa nilai-nilai itu.
- **Pemetaan CP.** Lampiran 1 (hlm. 59–71) memetakan materi CP ke lima tema untuk RA, untuk PAI dan Bahasa Arab, dan untuk mata pelajaran umum madrasah. Rumusan CP-nya tampaknya mengikuti CP yang lebih lama daripada SK 9941/2025, jadi tidak bisa diimpor apa adanya.
- **Semuanya contoh.** Panduan menyatakan semua aspek dan indikator adalah contoh, dan setiap madrasah boleh menyesuaikannya (hlm. 12).
- **Metode yang disarankan:** deep learning (mindful, meaningful, joyful), pembelajaran berbasis pengalaman, alur FIDS (Feel, Imagine, Do, Share), dan evaluasi berbasis proses.

**Di SIGRAK (D-126).** Untuk sekolah MI, wizard RPM v1 menambahkan *Tema KBC* (satu atau lebih tema Panca Cinta) dan *Materi Insersi*, dan AI merangkainya ke TP, kegiatan, dan asesmen. Kedua kolom bersifat opsional dan diisi awal oleh AI (D-142): guru boleh mengubah atau mengosongkannya. Ini berlaku untuk setiap mata pelajaran MI dan tidak berlaku untuk SD. KMA 512/2026 juga mencakup guru agama di SD; apakah SIGRAK menawarkan kolom KBC kepada mereka masih terbuka (D-359, `docs/drafts/open-questions.md`).

## Cara SIGRAK memodelkannya

- **Jenjang sekolah menentukan daftar mata pelajaran.** `schools.level` = `sd` memberi enam mata pelajaran agama. `mi` memberi mata pelajaran Kemenag ditambah tabel alokasi MI sendiri.
- **Versi kurikulum:** `KM-SD-2026-R2` (mata pelajaran SD nasional termasuk topik PAI), `CP-PABP-BKPDM-020-2026` (enam agama, CP), `CP-PAI-BA-MADRASAH-9941-2025` (PAI + Bahasa Arab MI, CP), dan `ALOKASI-MI-KMA-1503-2025` (alokasi MI). Lihat `data-model.md` (bagian Kurikulum).
- **Agama siswa (D-036)** dicatat per siswa agar kelas agama bisa dikelompokkan. Untuk mata pelajaran agama, jurnal, catatan siswa, asesmen, dan rapor hanya mencantumkan siswa beragama itu. Absensi tetap harian dan untuk seluruh kelas.
- **Privasi (D-036):** agama adalah data pribadi yang sensitif.
  - Agama terlihat oleh guru kelas itu, operator, dan kepala sekolah.
  - Pengawas tidak pernah melihatnya.
  - Agama tidak pernah masuk ke prompt AI, tidak pernah muncul di situs ujian, dan tidak pernah diekspor kecuali di rapor bila hukum mewajibkannya.
- **Pembuatan konten AI** untuk mata pelajaran agama memakai templat yang sama. Prompt membawa CP/TP dan nama elemen agama itu sebagai data. Aturan templat menjaga konten keagamaan tetap di dalam sumber dan istilah agama yang dipilih.

## Di luar cakupan v1

- **Pendidikan Kepercayaan terhadap Tuhan YME** (D-037). Tidak ada di BKPDM 020/2026, dan bisa ditambahkan nanti sebagai mata pelajaran tersendiri dengan sumber CP sendiri.

## Pekerjaan data (Fase 2)

1. ~~**Verifikasi transkripsi CP agama SD**~~ Selesai di P2-01 (`verified: true`).
2. **Cocokkan PAI di KM-SD-2026-R2** dengan CP Islam di keputusan.
3. **Susun topik, TP, ATP, fokus, dan JP** — draf selesai di P2-09: `tp-atp-pendidikan-agama-sd-2026.json` (D-139…D-141). Untuk Kristen, Katolik, Hindu, Buddha, dan Khonghucu (kelas 1–6, dua semester), dengan bentuk yang sama seperti KM-SD-2026-R2. AI boleh membuat draf dari CP. **Tim Validasi SIGRAK meninjau dan menyetujui setiap set** (D-113), dan dataset mencatat "Tim Validasi SIGRAK" serta tanggal tinjauan (D-112). Hal yang sama berlaku untuk mata pelajaran MI dan data CP.
4. ~~**Susun struktur CP MI**~~ Selesai di P2-02 (baris elemen, teks Arab diketik ulang).
5. **Susun topik, TP, ATP, fokus, dan JP** untuk lima mata pelajaran MI — draf selesai di P2-10: `tp-atp-pai-bahasa-arab-mi-2026.json` (D-145, D-146). Kedua set menunggu tinjauan Tim Validasi SIGRAK.
6. **Transkripsi CP pendidikan khusus** (Lampiran II BKPDM 020/2026) untuk enam agama, dengan bentuk yang sama seperti CP agama SD, untuk sekolah inklusi (D-155). Keputusan itu berupa salinan pindaian tanpa lapisan teks, jadi teksnya diketik lalu diperiksa Tim Validasi SIGRAK (D-113).

## Pertanyaan terbuka

- [ ] Salinan SK 9941/2025 dari kanal resmi (prioritas rendah: dua salinan dari sumber berbeda memiliki teks yang sama).
- [x] KBC di templat RPM untuk MI: di v1, untuk setiap mata pelajaran MI, tidak untuk SD (D-126); opsional, diisi awal oleh AI (D-142).
- [x] Nama peninjau: dicatat sebagai "Tim Validasi SIGRAK" (D-112).
- [x] Sekolah inklusi membutuhkan CP pendidikan khusus (Lampiran II BKPDM 020/2026); masuk cakupan dan menunggu transkripsi (D-155).
