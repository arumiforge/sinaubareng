# Ringkasan Riset

<p class="source-note">Diterjemahkan dari <code>docs/final/research-digest.md</code> pada 9 November 2026.</p>

- **Status:** final (permintaan pemilik 2026-11-09, D-362)
- **Terakhir diperbarui:** 2026-11-09
- **Tujuan:** satu tempat yang merangkum semua riset yang sudah dilakukan untuk SIGRAK sejauh ini: regulasi resmi, studi jurnal dan lapangan, pembanding aplikasi, dan riset teknis. Dokumen ini berisi ringkasan dan penunjuk, bukan salinan. Rinciannya tetap ada di dokumen repo yang ditautkan.
- **PDF Indonesia:** `docs/pdf/research-digest.pdf`

## 1. Cara memakai ringkasan ini

**Sebelum meriset apa pun, periksa di sini dulu.** Kalau sebuah temuan sudah tercantum, pakai temuan itu dan ikuti tautannya untuk rinciannya. Periksa ulang hanya kalau temuannya sudah lama, ditandai *sekunder* atau *belum terverifikasi*, atau menyangkut uang, hukum, atau ketentuan vendor. **Setelah meriset, tambahkan temuan Anda di sini** dengan bentuk yang sama (sumber, tanggal dicek, tingkat verifikasi, temuan, dampak, keputusan, dokumen repo), dan taruh rincian lengkapnya di draf tugas tersebut (D-362). Temuan yang hanya berasal dari transkrip sesi sebelumnya dan belum pernah masuk dokumen repo lain ditandai **(sesi)**.

Tingkat verifikasi yang dipakai di bawah:

| Tingkat | Arti |
|---|---|
| **resmi** | Dibaca dari teks resmi: PDF di `docs/sources/`, peraturan.go.id, situs kementerian, atau ketentuan atau dokumentasi vendor sendiri |
| **mirror** | Dibaca teks lengkapnya di situs cermin (pasal.id, yang di sana bertanda "belum ditinjau", atau salinan PDF di situs sekolah atau blog). Cocokkan nomor pasal dengan PDF resmi sebelum dikutip |
| **sekunder** | Hanya dari berita, blog, atau cuplikan hasil pencarian. Jangan pernah dipakai di teks produk tanpa dicek |
| **belum terverifikasi** | Disebut atau diklaim, tetapi tidak ditemukan sumber yang bisa dipakai |

Teks produk dan teks aturan di dalam aplikasi wajib mengecek ulang setiap nomor regulasi di jdih.kemendikdasmen.go.id atau peraturan.go.id sebelum dipakai.

## 2. Regulasi dan standar nasional (Kemendikdasmen)

Dicek 2026-10-30 sampai 2026-11-09. Pada 2026-11-08 dan 2026-11-09 situs JDIH resmi sering menjawab 403, 429, atau 503, sehingga beberapa baris bersandar pada mirror pasal.id.

| Instrumen | Penerbit, tanggal | Yang diatur (sudut pandang SD/MI) | Tingkat | Rincian di repo |
|---|---|---|---|---|
| Permendikdasmen 13/2025 (mengubah Permendikbudristek 12/2024) | Kemendikdasmen, berlaku 15 Jul 2025 | Kurikulum: pembelajaran mendalam di kerangka dasar, 8 dimensi profil lulusan, intra/ko/ekstrakurikuler, tabel JP SD/MI (Lampiran II B, Tabel 1–5), KKA pilihan, Pramuka kembali wajib (Pasal 22) | resmi | `docs/data/regulations/`, `docs/drafts/dokumen-resmi-sd-mi.md` |
| Permendikbudristek 12/2024 (teks dasar) | 2024 | Dasar kurikulum: CP ditetapkan pusat (Pasal 11), muatan SD "dalam bentuk mata pelajaran" (Pasal 13), diversifikasi KSP (Pasal 29(2)), Bahasa Inggris pilihan sampai wajib pada 2027/2028 (Pasal 33) | mirror | `docs/drafts/policy-innovation-layers.md` §3 |
| Kepmendikdasmen 126/P/2025 | 4 Sep 2025 | Pedoman Pembelajaran Mendalam: prinsip, pengalaman belajar, kerangka, peran | resmi | `deep-learning-kepmendikdasmen-126-p-2025.json` |
| Kepmendikdasmen 127/P/2025 | 4 Sep 2025 | Pedoman Koding dan Kecerdasan Artifisial: status, cara pelaksanaan, risiko; tanpa kelas atau JP SD | resmi | `coding-ai-kepmendikdasmen-127-p-2025.json` |
| Permendikdasmen 1/2026 (Standar Proses) | ditetapkan 2 Jan, berlaku 5 Jan 2026; mencabut Permendikbudristek 16/2022 | Perencanaan oleh guru (Pasal 4(2)); rencana "paling sedikit memuat" tujuan, langkah, asesmen (Pasal 5); penilaian proses oleh diri sendiri, sejawat, kepala sekolah, dan murid minimal sekali per semester (Pasal 15–18); istilah *murid* | mirror (Pasal 9 tidak terbaca) | `docs/drafts/administration-features.md` §1, `docs/drafts/teacher-supervision-instruments.md` §1 |
| Permendikdasmen 10/2025 (SKL) | Jun 2025 (tanggal pasti belum terverifikasi) | 8 dimensi profil lulusan sebagai standar lulusan | sekunder | `policy-innovation-layers.md` §3 |
| Permendikdasmen 12/2025 (Standar Isi) | 2025 | "Kriteria minimal" ruang lingkup materi | sekunder | sama |
| Permendikbudristek 21/2022 (Standar Penilaian) | masih berlaku | Hasil dilaporkan dalam rapor atau laporan lain | mirror | sama |
| Permendikdasmen 26/2025 (Standar Pengelolaan) | 2025 | KSP wajib dan ditetapkan kepala sekolah; RKJM (4 tahun) dan RKT bersama komite; rombel SD paling banyak 28; pengawasan lewat pemantauan, supervisi, evaluasi (Pasal 22–23); kelas rangkap dibolehkan (Pasal 11(4)) | mirror | `administration-features.md` §1 |
| Permendikdasmen 11/2025 + Kepmendikdasmen 221/P/2025 | 2025 (221/P sekitar 12 Nov 2025, sekunder) | Beban kerja guru 24–40 JTM; wali kelas 2 JTM di SD; tugas tambahan lain paling banyak 6 JTM; hanya dihitung dengan SK dan tercatat di Dapodik; mencabut Kepmen 495/M/2024 | sekunder | `administration-features.md` §1 |
| Permendikdasmen 6/2026 (Budaya Sekolah Aman dan Nyaman) | ditetapkan 8 Jan 2026; mencabut 46/2023 | Tata tertib, SOP, kanal pengaduan rahasia, kesepakatan kelas, monev tahunan; TPPK berakhir, kasus beralih ke Pokja daerah dalam 6 bulan; guru kelas memimpin kondisi kelas di SD (Pasal 13); pelaksananya Kepmendikdasmen 17/2026 (sekunder) | PDF resmi ditautkan; tugas tingkat kelas belum dibaca lengkap | `administration-features.md`, `teacher-supervision-instruments.md` |
| Permendikdasmen 3/2025 (SPMB) | 2025 | Pindah sekolah boleh kecuali di semester terakhir kelas 6; sekolah penerima memperbarui Dapodik dalam 14 hari kerja | mirror | `administration-features.md` §1 |
| Permendikbudristek 58/2024 (ijazah) | sekitar Okt 2024 (tanggal belum terverifikasi) | Ijazah ditandatangani kepala sekolah; e-ijazah dicetak sekolah sejak 2025 (sekunder) | PDF resmi ditautkan | sama |
| Permendikdasmen 9/2025 (TKA) dan 9/2026 (AN) | 2025, 2026 | TKA pilihan untuk kelas 6; AN di kelas akhir | PDF resmi ditautkan (9/2026), mirror (9/2025) | sama, §6 di bawah |
| Permendikdasmen 8/2026 (BOSP) | ditetapkan 5 Feb 2026; mencabut 8/2025 | RKAS di sistem kementerian (dalam praktik ARKAS); batas laporan 31 Jul (Tahap I), 31 Jan (tahunan), 25 Jun (tahun sebelumnya); honor non-ASN paling banyak 20% (negeri) atau 40% (swasta) | PDF resmi ditautkan | `administration-features.md`; batas waktu **(sesi)** |
| Permendikdasmen 14/2026 (BAN-PDM) | sekitar 17 Jun 2026 (sekunder); mengganti Permendikbudristek 38/2023 | Badan akreditasi; instrumen IA 2024 (Kepmendikbudristek 246/O/2024), panduan versi 2025 | sekunder | `administration-features.md` §1; 246/O **(sesi)** |
| Permendikbudristek 22/2023 (sarpras) | berlaku 10 Mar 2023 | Ruang wajib termasuk perpustakaan dan UKS | PDF resmi ditautkan | `administration-features.md` |
| Perpusnas 4/2024 | 2024 | Standar Nasional Perpustakaan Sekolah/Madrasah (Lampiran I untuk SD/MI) | PDF resmi ditautkan | sama |
| Permendikbud 75/2016 (Komite) | 30 Des 2016 | Komite boleh menggalang sumbangan, bukan pungutan; tidak ditemukan penggantinya | resmi (setkab) | sama |
| Permendagri 7/2024 | ditetapkan 20 Jun 2024 | Mengubah Permendagri 19/2016 tentang barang milik daerah (inventaris BMD sekolah negeri) | resmi (BPK) | sama |
| PermenPAN-RB 21/2024 | 23 Des 2024 | Jabatan Fungsional Guru: siklus kinerja; statusnya dipersoalkan setelah uji materi MA 26 P/HUM/2025 | resmi (BPK), status belum terverifikasi | sama; uji materi MA **(sesi)** |
| Kepmendikdasmen 271/O/2025 | 2025 | Pedoman Pengelolaan Kinerja PTK: kepala sekolah menjadi PPK guru; tim kinerja boleh dibentuk bila guru lebih dari 10 | sekunder (salinan dibaca di blog) | `teacher-supervision-instruments.md` §1 |
| Permendikdasmen 11/2026 (PIP) | 2026, tanggal belum terverifikasi | Sekolah mengusulkan penerima PIP di SIPINTAR dari data Dapodik; madrasah memakai SIPMA | sekunder | **(sesi)** laporan aturan P4-04 |
| Permendikdasmen 2/2026 (Tata Naskah Dinas) | 2026 | Hanya mengikat kementerian, tidak mengikat sekolah; sekolah negeri mengikuti aturan Pemda | sekunder | **(sesi)** sama |
| SE Sesjen Kemendikdasmen 14/2025 | 2025 | Optimalisasi Gerakan 7 KAIH di sekolah (salinan yang ditemukan berlabel "DRAFT") | sekunder | `administration-features.md` |
| Permendikdasmen 23/2026 | 2026 | Tercantum sebagai sumber; isinya belum dirangkum di repo | sekunder | bagian Sources di `administration-features.md` |
| SE BGN 5/2025 (MBG) | 2025 | Sekolah menunjuk 1–3 guru PIC makan bergizi gratis, insentif yang diberitakan Rp100 ribu per hari; tidak dihitung JTM (belum terverifikasi) | sekunder | `administration-features.md` |

Panduan dari BSKAP (semuanya **resmi**, PDF-nya di `docs/sources/`, diekstrak ke `docs/data/regulations/`, D-203):

| Panduan | Temuan yang penting bagi SIGRAK | Keputusan |
|---|---|---|
| Panduan Pembelajaran dan Asesmen, Edisi Revisi Ke-3, Jul 2025 | Rapor punya **12 komponen minimum** (termasuk deskripsi kokurikuler, ekstrakurikuler, ketidakhadiran, catatan wali kelas, tanggapan orang tua). Asesmen formatif tidak pernah dipakai untuk rapor, kenaikan kelas, atau kelulusan. Fase A boleh dilaporkan tanpa angka. Ada 4 pendekatan KKTP; format ATP bebas ("Tidak ada format komponen yang ditetapkan oleh Pemerintah"). Kerangka rencana pembelajaran mirip RPM, dengan Memahami → Mengaplikasi → Merefleksi | D-205, D-206, D-208 |
| Panduan Kokurikuler 2025 | Bentuknya lintas disiplin, Gerakan 7 KAIH, cara lainnya ("Projek" tidak termasuk). Kokurikuler SD = 36 JP per mata pelajaran per tahun (32 di kelas 6) | D-207, D-208 |
| Panduan Pengembangan KSP, Edisi Revisi Ke-3, Jul 2025 | Sekolah memilih "bentuk dan sistematika"; administrasinya tidak diseragamkan | D-209 |
| Pembelajaran KKA (BSKAP, Feb 2025) | KKA untuk SD: kelas 5–6, 2 JP per minggu, pilihan, isi hanya Fase C, sebagai rujukan, bukan CP resmi | D-208 |
| Paparan Pembelajaran Mendalam (slide tanpa tanggal) | Kolom templat rencana pembelajaran, wajib dan opsional | — |

**Dampak.** Standar-standar ini mengunci capaian, struktur, isi minimum dokumen, prinsip, dan perlindungan, tetapi **tidak mengunci metode mengajar** (Standar Proses Pasal 4(2); format ATP dan KSP bebas; indikator KBC hanya contoh). SIGRAK memberi label "Wajib", "Disarankan", "Contoh", atau "Bebas" pada setiap bawaan dan menyimpan aturan sebagai data berversi (D-352…D-358, `docs/drafts/policy-innovation-layers.md`). Pernyataan kementerian menyebut 13/2025 bukan kurikulum baru ([metrotvnews](https://www.metrotvnews.com/read/NLMCJ6Vj-mendikdasmen-tegaskan-tidak-ada-perubahan-kurikulum), sekunder).

## 3. Kurikulum dan pembelajaran mendalam

| Temuan | Sumber, tingkat | Dampak | Keputusan, repo |
|---|---|---|---|
| JP intrakurikuler SD dari aplikasi lama (kini target semester SIGRAK-SD-2026-R1) sama persis dengan Tabel 1–5 Permendikdasmen 13/2025 | 13/2025 hlm. 15–21, resmi | Diimpor apa adanya | D-204, `dokumen-resmi-sd-mi.md` §2 |
| Bahasa Inggris dan IPAS tidak ada di struktur kelas 1–2 | 13/2025 hlm. 15–18, resmi | Konten opsional sekolah, mati secara bawaan, bukan mata pelajaran rapor wajib | D-204 |
| Batas muatan lokal SD: 72 JP per tahun (64 di kelas 6); Bahasa Jawa memakai seluruhnya; MI boleh 72–216 | 13/2025 hlm. 16, hlm. 21; KMA 1503/2025 | Tata Boga opsional sekolah; batas ditegakkan per sekolah | D-204, `data-model.md` |
| Total kokurikuler SD: 216 / 216 / 252 / 252 / 252 / 224 JP per tahun (kelas 1–6) | 13/2025, Panduan Kokurikuler hlm. 35, resmi | Versi alokasi baru `STRUKTUR-SD-PERMENDIKDASMEN-13-2025` | `dokumen-resmi-sd-mi.md` C3 |
| 8 dimensi profil lulusan: keimanan dan ketakwaan, kewargaan, penalaran kritis, kreativitas, kolaborasi, kemandirian, kesehatan, komunikasi | 13/2025 Pasal 17, resmi | Daftar tetap DPL1–DPL8 di RPM dan kokurikuler | `content-engine.md` |
| Pembelajaran mendalam: prinsip berkesadaran, bermakna, menggembirakan; pengalaman memahami, mengaplikasi, merefleksi; empat bagian kerangka | 126/P/2025, resmi | Langkah inti RPM mengikuti tiga pengalaman dengan tanda prinsip | `content-engine.md` |
| "Deep learning" di artikel Indonesia berarti pendekatan 2025, bukan AI | Penelusuran literatur LKPD, 2026-11-09 | Perhatikan istilah di UI dan kutipan | `lkpd-exploration.md` §8.3 |
| Berkas kurikulum aplikasi lama (diunduh 2026-11-08): 432 topik, 2.021 fokus, 1.372 TP, 58 baris alokasi, 12 mata pelajaran (baru: Seni Suara Daerah) | Portal publik aplikasi lama, dikelola pemilik; SHA-256 dicatat | Dikonversi ke dataset SIGRAK R1 pada 2026-11-09 lalu dihapus; kode lama di crosswalk (D-363, D-365); impor menerapkan D-204 dan D-208 | `docs/drafts/curriculum-source-check-2026.md`, `docs/data/curriculum/README.md` |
| KSP: disetujui kepala sekolah, format bebas | Panduan KSP hlm. 7, hlm. 10, resmi | Templat awal yang disesuaikan sekolah | D-209 |

## 4. Madrasah dan Kemenag

| Instrumen atau fakta | Tanggal | Temuan | Tingkat | Keputusan, repo |
|---|---|---|---|---|
| **KMA 512/2026, Pedoman Kurikulum Berbasis Cinta** | ditetapkan 31 Mar 2026, berlaku sejak ditetapkan | Menggantikan SK Dirjen Pendis 6077/2025 sebagai sumber KBC. Tetap memakai Panca Cinta (cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air); menambah enam nilai dasar, enam prinsip, 12 metode yang disarankan, 10 instrumen asesmen, tiga tingkat praktik (merintis, berkembang, mekar). KBC bukan mata pelajaran; hasilnya dilaporkan lewat Akhlak atau Budi Pekerti dan catatan sikap. Berlaku untuk sekolah di bawah Kemenag **dan untuk guru agama di sekolah umum, termasuk SD** | resmi (PDF pindaian dari pemilik, 2026-11-09; sebelumnya hanya sekunder, dengan satu laman menulis "KMA 521") | D-359, D-126; `docs/sources/KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf`; cakupan SD menjadi pertanyaan terbuka di `open-questions.md` |
| SK Dirjen Pendis 6077/2025 (Panduan KBC) | 22 Jul 2025 | Lima tema, contoh pembelajaran dengan kolom "Tema Kurikulum Berbasis Cinta" dan "Materi Insersi"; semuanya ditandai sebagai contoh | resmi | D-126, D-142, `docs/final/religious-education.md` |
| KMA 1503/2025 (mengubah KMA 450/2024) | 22 Sep 2025 | Kurikulum madrasah dengan pembelajaran mendalam dan KBC; struktur dan JP MI (Lampiran II, BAB III B, Tabel 1–5); MI boleh menambah sampai 6 JP per minggu | resmi | D-038, `mi-allocation-kma-1503-2025.json` |
| SK Dirjen Pendis 9941/2025 | 28 Nov 2025 | CP Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab | mirror (dua salinan dengan teks sama; tautan kemenag.go.id tidak ditemukan) | D-035 |
| Panduan Pembelajaran dan Asesmen madrasah dari Kemenag (surat B-572/Dt.I.I/PP.00/11/2025) | 19 Nov 2025 | Rencana pembelajaran versi minimum dan lengkap; Identifikasi minimum memuat DPL, Topik Panca Cinta, materi integrasi KBC; refleksi diri dibingkai sebagai *muhasabah* | mirror ([salinan](https://bloggeografi.id/wp-content/uploads/2025/11/panduan-pembelajaran-dan-asesmen.pdf)) | `teacher-supervision-instruments.md` §1, §8 |
| KMA 736/2026 (beban kerja guru madrasah) | 2026; mencabut KMA 890/2019 | 37,5 jam per minggu, 24–40 JTM; wali kelas **6 JTM** (SD: 2); kepala madrasah 24 JTM; guru kelas MI = 24 JTM; SKMT tiap semester; KMA 943/2026 mengubah lampirannya | sekunder | D-212; KMA 943 **(sesi)** |
| Kepdirjen Pendis 944/2026 (BOS madrasah), diubah dengan 2100/2026 | 2026 | Perencanaan dan pelaporan lewat e-RKAM | sekunder | D-219 (di luar cakupan); 2100/2026 **(sesi)** |
| Nomor Kepdirjen Pendis lain | 2025–2026 | PMB 10041/2025; ujian madrasah 751/2026; ijazah 3576/2026; kaldik madrasah 2026/27 4860/2026 (semester ganjil mulai 13 Jul 2026) | sekunder | **(sesi)** laporan aturan P4-04 |
| EMIS 4.0 / EMIS GTK | 2024–2026 | Menggantikan SIMPATIKA sejak semester genap 2024/25; NRG, SKMT, SKBK, dan TPG guru bergantung padanya | sekunder | `administration-features.md` §1 |
| RDM (Rapor Digital Madrasah) v3.1 | 2025 | Sekitar 25.139 MI (93,8%) memakainya di semester ganjil 2024/25; memuat deskripsi kokurikuler; pengguna melaporkan kesulitan input nilai; **tidak ditemukan dasar kewajiban formal** | sekunder (makalah konferensi, tirto) | D-218 (ekspor setelah Asesmen) |
| PKKM (penilaian kinerja kepala) madrasah | — | Masih mengikuti Kepdirjen Pendis 1111/2019; tidak ditemukan instrumen observasi guru Kemenag 2025–2026 | sekunder / belum terverifikasi | **(sesi)**, D-324 |
| Bahasa Inggris di MI | — | Pilihan sampai 2026/27, wajib mulai 2027/28 | sekunder | **(sesi)** |

## 5. Pendidikan agama di SD

| Sumber | Temuan | Tingkat | Keputusan, repo |
|---|---|---|---|
| Kepka BKPDM 020/2026 (11 Jun 2026), mengubah Kepka BSKAP 046/H/KR/2025 | CP Pendidikan Agama dan Budi Pekerti untuk enam agama, Fase A–F (Lampiran I); CP pendidikan khusus (Lampiran II, belum ditranskripsi) | resmi (pindaian, tanpa lapisan teks) | D-034, `cp-pendidikan-agama-sd-bkpdm-020-2026.json` |
| Struktur 13/2025 | Satu baris "Pendidikan Agama dan Budi Pekerti", 3 JP per minggu, untuk setiap agama | resmi | `religious-education.md` |
| Kepka BSKAP 046/H/KR/2025 dasar (CP mata pelajaran umum) | Belum ada di repo | — | Celah, §16 |

Pada 2026-10-30 tidak ditemukan perubahan yang lebih baru atas SK 9941/2025 atau KMA 1503/2025.

## 6. Asesmen: AN, TKA, AKMI, rapor, Peta Asesmen

| Temuan | Sumber, dicek | Tingkat | Dampak, keputusan |
|---|---|---|---|
| TKA SD/MI: Bahasa Indonesia dan Matematika, keduanya wajib; tipe soal tunggal dan grup; bentuk PG, PGK MCMA, PGK Kategori | Perka BSKAP 047/H/AN/2025 (24 Jul 2025), 2026-10-30 | resmi | Templat Paket Soal TKA dan situs ujian, D-026, D-027, D-030; `docs/data/tka/kerangka-tka-sd.json` |
| Durasi 2026: 75 menit per mata uji SD (2025: 60); nilai 0–100 dengan 2 desimal; kategori Baik, Memadai, Kurang; predikat Istimewa pada nilai 95 atau lebih | Kepmendikdasmen 56/2026 (6 Mar 2026), BAB X | resmi | Penilaian di latihan ujian |
| Batas kategori 50 dan 76,67 | Paparan hasil 2026 (26 Mei 2026) lewat detik, beritasatu, kumparan | sekunder | Dipakai sampai ada teks resmi yang menyebutkannya |
| 30 soal per mata uji | Set contoh soal Pusmendik; contoh di paparan | sekunder (tidak ada teks regulasi) | Bawaan, bukan aturan |
| Mulai 2026 sekitar 70% soal pusat dan 30% soal daerah | Kepmendikdasmen 56/2026 BAB IV–V; persentase dari paparan | resmi / sekunder | Catatan asal soal saja |
| Juknis TKA-AN 017/F/AN/2026 dan Tanya Jawab TKA 2025 tidak menyebut jumlah soal, batas kategori, atau model penilaian | Dibaca dengan OCR 2026-10-31 | resmi | Menegaskan celahnya |
| Kepmendikdasmen 95/M/2025 mengatur TKA 2025; tidak berlaku lagi untuk 2026 | Laman unduhan Pusmendik | resmi | Pakai nilai 2026 |
| AN adalah survei sistem, bukan nilai murid | Tinjauan peta asesmen, 2026-11-09 | — | Hanya label dan stimulus gaya AKM, tanpa simulator AN; baca Rapor Pendidikan sebagai gantinya (D-344…D-351, `docs/drafts/assessment-landscape.md`) |
| Isi minimum rapor dan aturan formatif | Panduan Pembelajaran dan Asesmen 2025 | resmi | D-205, D-290 |
| e-Rapor SD 2025.1 menambah asesmen kokurikuler dengan rubrik dimensi; ada laporan galat kokurikuler tertahan di "belum dikerjakan"; nilai diketik ulang dari alat lain | blog (kiakrikil, imrantululi), 2026-11-07 | sekunder | Ekspor e-Rapor setelah Asesmen (D-218); spesifikasi resmi 2026 masih dibutuhkan |
| Status AKMI (asesmen kompetensi madrasah) untuk MI tahun 2026 | — | belum terverifikasi | Celah, §16 |
| IRT/Rasch per sekolah tidak layak: satu kelas hanya 10–30 anak | Tinjauan peta asesmen | analisis | Hanya agregat kelas dan sekolah |

## 7. LKPD dan studi kelas

Penelusuran literatur pada 2026-11-09 (tugas P4-16): artikel jurnal Indonesia 2025–2026 tentang LKPD atau e-LKPD untuk SD/MI yang diuji coba. Ditemukan sekitar **60 studi primer dan 6 kajian**. Abstraknya dibaca di situs jurnal; bagian metode lengkapnya tidak. Terbitan Basicedu, JISD, dan Mimbar PGSD tahun 2025 tidak muncul; peringkat SINTA tidak dicek. Sekitar 35 studi tercantum lengkap dengan tautannya di `docs/drafts/lkpd-exploration.md` §8.1. Keputusan: D-329…D-337.

### 7.1 Apa yang didukung bukti

| Pola atau fitur | Kekuatan | Contoh studi (rincian di §8.1 draf) |
|---|---|---|
| Konteks lokal (cerita daerah, etnomatematika, etnosains) | **Paling kuat**, banyak daerah, dua dengan kelas kontrol | Purwanti dkk. 2026 (NG 0,68 lawan 0,52 kontrol); Wiratama dkk. 2025 MI (NG 0,79, p = 0,001); Handayani dkk. 2026 motif batik (NG 0,80) |
| Berpikir komputasional tanpa komputer (unplugged) | **Kuat**: satu studi terkontrol ditambah satu kajian | Fahrudin dkk. 2026 (NG 0,63 lawan 0,34); kajian Meyrena dkk. 2025 (13 studi) |
| Preset model pembelajaran (PBL, PjBL, RADEC, TaRL, STEM, CTL) | Baik, kebanyakan satu kelompok | Nuwair dkk. 2026 RADEC (NG 0,64 lawan 0,25) |
| Langkah pembelajaran mendalam | Sedang, NG 0,41–0,61 | Rosyada & Rezania 2026; Isnaeni & Indrawati 2026 |
| Umpan balik langsung (gaya Liveworksheets) | Sedang; mungkin efek kebaruan | Inanta dkk. 2026 (NG 0,78) |
| Cetak hybrid dengan QR | Sedang | Nugroho dkk. 2025 (NG 0,76); kajian Afifah & Hasyim 2026 |
| Jalur berdiferensiasi | Sedang, sampel kecil | Agustina dkk. 2026 TaRL (n = 16) |
| AR | Lemah: hanya validitas atau post-test | Nanda dkk. 2026 (Assemblr EDU) |
| Escape room, perburuan harta karun, permainan papan, lapbook, LKPD kokurikuler/P5, LKPD ABK | **Tidak ditemukan studi teruji untuk SD** | — |

Data kebutuhan: 70% murid kelas V bosan dengan LKPD yang monoton dan 65% merasa LKPD yang tidak kontekstual sulit (Sutriono dkk. 2026). Guru meminta gambar, petunjuk sederhana, video pendek, umpan balik langsung, pengerjaan bertahap, dan akses lewat HP (Putri, Wiguna & Suardipa 2026). Guru sudah paling banyak memakai AI untuk merencanakan modul ajar dan LKPD, dengan risiko ketergantungan dan informasi keliru (Putri, Dwiani dkk. 2026).

### 7.2 Studi tambahan dari penelusuran, belum masuk tabel draf (sesi)

Dari laporan riset P4-16 (2026-11-09). Semuanya **terverifikasi dari abstrak** kecuali diberi tanda.

| Studi | Bentuk, kelas, n | Hasil | Catatan |
|---|---|---|---|
| Ningsih & Rigianti 2026, *CJPE* 9(2), [tautan](https://e-journal.my.id/cjpe/article/download/8367/5356/41407) | Numerasi, pembelajaran mendalam | Hanya validitas dan kepraktisan | — |
| Istianah, Muktadir dkk. 2026, *Pendas* 11(3), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/download/59273/34938) | Think Talk Write bergamifikasi, narasi sejarah, V, n = 40 | 100% tuntas, tanpa kontrol | Menulis |
| Malasari dkk. 2025, *Pendas* 10(3), [tautan](https://www.journal.unpas.ac.id/index.php/pendas/article/download/28327/17049/123537) | LKPD Wordwall, IV, n = 27 | Ketuntasan 80–88% terhadap KKM 75 | Game |
| Sari & Setya 2025, *Pendas* 10(4), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/view/38767) | Liveworksheets matematika, V | NG 0,49 | Digital |
| Zajirah dkk. 2026, *PENDIKDAS* 7(1), [tautan](https://jurnal.habi.ac.id/index.php/Pendikdas/article/download/1078/606) | Liveworksheets IPAS, V, n = 24 | NG 0,70 | Digital |
| Aulia dkk. 2026, JPPSD 6(2), [tautan](https://ojs.unm.ac.id/jppsd/article/download/90238/37979) | Wizer.me, Pendidikan Pancasila, V | NG 0,75 | Digital |
| Dhiba dkk. 2026, JPPSD 6(1), [tautan](https://ojs.unm.ac.id/jppsd/article/download/87289/37291) | e-LKPD "Buria", IPAS, V | NG 0,76 | Digital |
| Liana dkk. 2026, Unimed JGK 10(4), [doi](https://doi.org/10.24114/jgk.v10i4.76570) | PBL (Canva + Liveworksheets), V, n = 11 | NG 0,585, p = 0,000 | Penulis meminta kelas kontrol |
| Amisa dkk. 2026, *Pendas* 11(3), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/download/58809/34481) | Suku Insana (NTT), IPAS, IV | NG 0,72 | Konteks lokal |
| Nurcahyani & Puspita 2025, JPPGSD 13(7), [doi](https://doi.org/10.26740/jppgsd.v13i7.70521) | "Jelajah Surabaya", keragaman budaya, V | NG 0,58–0,63 | Konteks lokal |
| Siska & Julianto 2026, JPPGSD 14(3), [doi](https://doi.org/10.26740/jppgsd.v14i3.77223) | Etnosains, wujud zat, n = 25 | NG 0,71, p = 0,000 | Konteks lokal |
| Musli'ah dkk. 2025, *Pendas* 10(3), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/view/31593) | Etnomatematika makanan Cilacap, IV, dua sekolah | Hanya respons | Konteks lokal |
| Haryanti dkk. 2026, *Pendas* 11(3), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/view/62933) | PBL ekosistem, V, n = 21 | NG 0,75 | Model |
| Valensia dkk. 2026, *Pendas* 11(3), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/view/58215) | PBL sistem pencernaan, V | NG 0,38; validitas bahasa hanya 0,50–0,67 | Contoh validitas lemah |
| Nugrahadiarti dkk. 2026, JPPSD 6(1), [tautan](https://ojs.unm.ac.id/jppsd/article/view/87746) | CTL bangun datar, III, n = 20 | NG 0,77 | Model |
| Wardani & Abtokhi 2025, *Ibtidaiyyah* 4(2), [tautan](https://urj.uin-malang.ac.id/index.php/ijpgmi/article/view/11816) | Discovery IPAS, IV MI | NG 0,55 | MI |
| Putra dkk. 2026, *Pendas* 11(3), [tautan](https://journal.unpas.ac.id/index.php/pendas/article/view/64298) | LKPD digital IPAS dengan koding/AI (BK), V | Hanya desain, tanpa angka | Celah KKA |
| Fauziyah dkk. 2026, *GERVASI* 10(2), [tautan](https://journal.upgripnk.ac.id/index.php/gervasi/article/view/11171) | Lokakarya LKPD koding unplugged, 17 guru | Tes guru 62,9 → 94,1; 5 guru masih perlu pendampingan | Pengabdian masyarakat |
| Sari, Junaidah & Erfayliana 2026, *Ibtida'iy* 11(2), [tautan](https://journal.ummat.ac.id/index.php/ibtidaiy/article/download/43558/pdf) | Pembelajaran luar kelas, V, n = 56, ada kontrol | NG 0,63 lawan 0,57 | Selisih kecil dari kontrol |
| *JPPIPA* Jul 2025, [tautan](https://jppipa.unram.ac.id/index.php/jppipa/article/view/11419) | LKPD IPAS ber-QR dengan discovery, V | — | hanya dari hasil pencarian (situs diblokir) |

### 7.3 Catatan kehati-hatian

Sebagian besar studi berupa R&D (ADDIE atau 4D) di satu kelas berisi 10–30 murid tanpa kelas kontrol. Validitas berasal dari 2–3 validator, sering kolega sendiri; kepraktisan berupa angket kepuasan (85–100%). "Efektif" bisa berarti N-gain, ketuntasan di atas KKTP, atau persentase post-test. Tidak ditemukan studi retensi, replikasi, atau meta-analisis gabungan; cakupan wilayahnya condong ke Jawa, Sumatra, dan NTT, tanpa studi untuk Bugis, Dayak, atau Papua. **Pakai studi-studi ini sebagai arah, bukan bukti**, dan ukur pemakaian SIGRAK sendiri.

## 8. Supervisi dan kinerja guru

Dicek 2026-11-08 (P4-12). Keputusan D-222…D-225, D-324, D-327, D-328.

| Sumber | Temuan | Tingkat |
|---|---|---|
| Perdirjen GTK 7607/B.B1/HK.03/2023 dan Panduan Substansi Pengelolaan Kinerja (Des 2023) | Observasi pada **8 fokus** (sub-indikator Rapor Pendidikan D1); siklus A–E dengan Form A–D; observasi adalah **titik awal (baseline), bukan penilaian**; skala Form B Belum dilakukan / Dilakukan tetapi belum efektif / Dilakukan dan efektif | resmi (di `docs/sources/`) |
| Ruang GTK 2026 | Observasi berjalan di Ruang GTK "berbasis indikator perilaku"; rumusan 8 fokus versi 2026 tidak ditemukan | sekunder / belum terverifikasi |
| Panduan BAN-PDM IA 2024 versi 2025, Komponen 1 | Empat butir, 4 tingkat, ditriangulasi lewat observasi, wawancara, dokumen; menilai **mutu layanan, bukan kelengkapan dokumen** | resmi ([PDF](https://ban-pdm.id/documents/download/1756890580_7aed8e14ada8b94fca13.pdf)) |
| Kepdirjen GTK 5539/B.B1/HK.03.01/2024 | Dasar proses pengelolaan kinerja di Ruang GTK; tersinkron ke e-Kinerja BKN; diperluas ke kepala sekolah dan pengawas pada 2026 | sekunder **(sesi)** |
| Contoh dari pemilik: instrumen supervisi SD 2026, set Kudus (pra, observasi, pasca), SK pembagian tugas | Memakai istilah sebelum 2025 (Profil Pelajar Pancasila, HOTS/4C, RPP/MA, P5); skor 0/1/2 atau 1–5 | contoh resmi di `docs/sources/` |

**Dampak.** Daftar kelengkapan dokumen dibuat pendek dan siklus observasi menjadi intinya, selaras dengan Form A–D Ruang GTK, sehingga hasilnya sekaligus menjadi bukti kinerja (D-217, D-223).

## 9. Administrasi dan pengelolaan sekolah

Dicek 2026-11-07 (P4-04). Keputusan D-210…D-221. Rincian: `docs/drafts/administration-features.md`.

| Topik | Temuan | Tingkat |
|---|---|---|
| Buku induk siswa | Tidak ada regulasi nasional yang berlaku; Dapodik adalah sistem data yang sah | belum terverifikasi |
| Ekspor Dapodik | Data murid dan rombel saat ini berasal dari Peserta Didik → Unduh → Daftar PD (Excel); riwayat KGB diisi lewat login tiap PTK; tata letak kolom 2026 tidak dipublikasikan; PDF panduan resmi 2026 terlalu besar untuk dibaca daring | sekunder (`docs/drafts/buku-induk-dapodik.md`, D-161) |
| Guru BK di SD | Tidak ada aturan yang mewajibkannya; dalam praktik dikerjakan guru kelas | sekunder |
| UKS | Tidak ditemukan pengganti SKB 4 Menteri 2014 untuk 2025–2026 | belum terverifikasi |
| BOSP dan MBG | Larangan memakai BOSP untuk makanan MBG diberitakan tetapi tidak ada di teks; larangan guru ASN menjadi bendahara belum terverifikasi | belum terverifikasi **(sesi)** |
| Kalender pendidikan | Ditetapkan per daerah (contoh: Jateng 400.3/23862/2026, Kota Semarang 29 Jun 2026, SE Jabar 30234/PK.02.01.05/PSMA 18 Jun 2026) | sekunder **(sesi)** |
| Beban minimum guru | Satu blog mengklaim turun menjadi 16 JTM: kemungkinan besar keliru | sekunder |
| Akreditasi | Berorientasi kinerja, 14 butir untuk kinerja guru, kepemimpinan kepala sekolah, dan iklim sekolah | panduan resmi |

**Dampak.** Fitur standar pasar adalah presensi dengan ekspor, buku nilai sampai rapor, pengumuman, dan data murid. Belum ada yang menangani rantai CP → TP → Prota/Promes → RPM → jurnal → asesmen → remedial → rapor dan deskripsi kokurikuler; rantai inilah nilai jual SIGRAK. BOS, ARKAS, e-RKAM, dan SPMB tetap di luar cakupan (D-025, D-219).

## 10. IFP dan game kelas

Ditinjau 2026-11-04 (P4-02). Rincian: `docs/final/ifp-classroom.md`. Keputusan D-171…D-181, D-233.

| Temuan | Sumber | Tingkat |
|---|---|---|
| Program IFP kementerian menargetkan 288.000+ sekolah paling lambat Des 2025; panel Android all-in-one 75 inci, 4K, multi-sentuh, dengan laptop dan hard disk konten; satu unit per sekolah pada 2025 (149.268 dari sasaran adalah SD), tiga per sekolah ditargetkan pada 2026; sering dipakai sebagai TV besar | [ANTARA 2025-10-07](https://www.antaranews.com/berita/5159673/mendikdasmen-pengiriman-papan-interaktif-ditargetkan-selesai-desember), [BBPMP Jatim](https://bbpmpjatim.kemendikdasmen.go.id/main/syarat-syarat-sekolah-penerima-ifp-dan-tipe-tipe-wilayahnya), [detikEdu 2025-10-23](https://www.detik.com/edu/sekolah/d-8174445/program-digitalisasi-pendidikan-berlanjut-dalam-5-tahun-sekolah-bakal-punya-6-ifp) | resmi + sekunder |
| Versi Android dan WebView panel tidak dipublikasikan | — | belum terverifikasi; uji di unit nyata (D-179) |
| Papan Interaktif (MANDAI): gratis, tanpa login, 42+ media, mengklaim 20.000+ sekolah; dibingkai sebagai ice breaking; tanpa TP, tanpa hasil | [papaninteraktif.com](https://papaninteraktif.com/) | resmi (situsnya) |
| Game DigiEd: sekitar 70 judul gratis per jenjang dan mata pelajaran; penjelasan setelah tiap jawaban; Kuis Kelas dari templat Excel dimainkan sebagai tiga game; hanya daring; hasil tidak disimpan | [game.digitaleducation.id](https://game.digitaleducation.id/) | resmi (situsnya) |

**Celah yang diisi SIGRAK:** keterkaitan dengan TP dan langkah RPM, hasil kelas per butir dan per TP, paket MI dan enam agama, Bahasa Jawa, luring, bentuk soal TKA, dan lembar panduan menjalankan aktivitas.

## 11. Pembanding aplikasi

Pembanding per 2026-11-07 (P4-04). Kebanyakan vendor tidak memublikasikan daftar fitur atau harga. **(sesi)** menandai rincian yang hanya ada di laporan riset.

### 11.1 Platform pemerintah

| Platform | Temuan | Tingkat |
|---|---|---|
| Rumah Pendidikan (diluncurkan 21 Jan 2025) | Super-app dengan 8 "ruang"; laporan Sep 2026 menyebut 950+ aplikasi sudah terintegrasi; berupa agregator, tanpa alur kerja kelas. Rumah Belajar menjadi Rumah Pendidikan / Ruang Murid (tanpa API embed) | resmi + sekunder **(sesi)** |
| Ruang GTK (dulu PMM) | Pelatihan, Karir & Kinerja, Bukti Karya; sejak 9 Jul 2026 ada pelatihan PM-KKA (pembelajaran mendalam serta koding dan AI) untuk 300 ribu+ guru | resmi **(sesi)** |
| "Platform AI Guru" di PMM/Ruang GTK | Satu artikel Feb 2026 menyebut menteri meluncurkan asisten AI generatif yang menyusun RPP dan modul ajar; nama produk, URL, atau siaran pers tidak ditemukan. Kalau benar ada, ini pesaing langsung | belum terverifikasi **(sesi)** ([artikel](https://bicarainfo.com/pendidikan/peluncuran-platform-ai-guru-asisten-virtual/)) |
| e-Rapor SD, Dapodik, ARKAS, Rapor Pendidikan, SIPLah | Data resmi, rapor, keuangan, indikator mutu, pengadaan; masing-masing berdiri sendiri; guru mengetik ulang nilai | sekunder / pengetahuan umum |
| Kemenag: EMIS 4.0, RDM, e-RKAM | Lihat §4. Tidak ditemukan asisten AI guru resmi dari Kemenag; Kemenag mengumumkan AI untuk layanan publik dan penyatuan sekitar 2.800 aplikasi (Mei 2026); mantan Sekjen Kemenag menekankan dukungan untuk perangkat sederhana (Okt 2026) | sekunder **(sesi)** |

### 11.2 Alat komersial dan AI

| Produk | Temuan | Tingkat |
|---|---|---|
| Kelas Pintar | Berfokus pada belajar; pembaruan Mar 2026 menambah asesmen otomatis untuk guru; Best Edutech App with AI (Selular Award, Jun 2026); mengklaim 1 juta+ murid | sekunder |
| Gredu | Paket sekolah dengan aplikasi guru, orang tua, murid; status 2026 tidak jelas (CB Insights mencantumkan "Acquired", belum terverifikasi) | sekunder |
| SmartSchool / MySCH.id | LMS, e-Rapor, CBT, PPDB; rencana 24 modul "belum final" pada 2021; kabarnya mulai Rp 2.000 per murid per bulan | sekunder (usang) |
| Sigma Guru (iOS) | Kelas, nilai, presensi, pesan, kuis, rapor daring, SPP; tanpa AI atau perencanaan kurikulum | resmi (halaman toko) |
| AIO Class | Presensi dengan Excel, jurnal dengan foto, CBT, poin perilaku, tabungan; freemium sekitar US$1,99 per bulan atau US$6,99–24,99 per tahun per guru; tanpa AI. Paling mirip dengan aplikasi administrasi pribadi guru | resmi (halaman toko), harga **(sesi)** |
| InfraDigital | Penagihan SPP, termasuk pembayaran di warung mitra Tokopedia | sekunder **(sesi)** |
| Edura School | Mengklaim "Automated Curriculum Generator" untuk ATP, Modul Ajar, Prota, Promes; halamannya diblokir | belum terverifikasi |
| GuruInovatif Modul Generator | Modul ajar AI gratis untuk 1–4 JP dengan LKPD, rubrik, remedial; ada batas harian; tidak terhubung ke nilai atau jurnal | resmi (situsnya) |
| "Asisten Guru AI Kontekstual" BRIN (Des 2025) | Pustaka prompt, termasuk "Kurikulum Cinta" dan pembelajaran mendalam; prototipe riset | sekunder (detik) |
| AI umum (Canva, Genspark, MagicSchool, Teachy, dan lainnya) | Pembuat rencana pembelajaran sekali jadi; guru menempelkan CP/TP sendiri | sekunder |

Tidak ditemukan atau tidak bisa diverifikasi: Sekolah.mu, Jurnal Sekolah, Educa Studio, Kipin, Schoolmedia, Mapel.id, dan aplikasi Android "Administrasi Guru SD". Paket administrasi Excel gratis banyak dibagikan di blog dan bisa dipakai luring.

### 11.3 Celah dan kisaran harga

Celah yang belum ditangani siapa pun: perencanaan yang terhubung ke administrasi; pembelajaran mendalam dan kokurikuler dalam narasi rapor; bentuk kerja guru kelas SD; mata pelajaran MI, KBC, dan RDM; pemakaian HP secara luring; impor Dapodik/EMIS dan ekspor e-Rapor/RDM; administrasi milik guru dengan AI murah (guru membawa kunci Gemini sendiri); bukti untuk supervisi dan Ruang GTK. Kisaran harga **(sesi)**: alat pemerintah gratis; pembuat konten AI gratis dengan batas, paket berbayar sekitar US$4–5 per bulan; aplikasi guru sekitar US$7–25 per tahun; paket sekolah diperkirakan Rp 0,5–5 juta per sekolah per tahun (sekunder). Keputusan harga: P5-10, `docs/final/product-concept.md`.

## 12. Alat belajar pihak ketiga (AR, 3D, simulasi)

Halaman vendor dicek 2026-11-09 (P4-16). Rincian: `lkpd-exploration.md` §9. Keputusan D-330, D-334, D-337.

| Alat | Temuan | Tingkat |
|---|---|---|
| H5P lewat `h5p-standalone` (MIT) | Pemutar yang bisa di-host sendiri; pustaka PHP-nya GPL, jadi pakai pemutar standalone saja | resmi |
| Google `<model-viewer>` (Apache-2.0) | 3D dan AR di HP tanpa aplikasi; iOS butuh USDZ; Android butuh Google Play Services for AR | resmi |
| PhET | Gratis, ada terjemahan Indonesia; pemakaian komersial simulasi CC-BY butuh lisensi terpisah | resmi; lisensi perlu dipastikan |
| GeoGebra | Gratis hanya untuk pemakaian nonkomersial; produk berbayar butuh lisensi | resmi; lisensi perlu dipastikan |
| Assemblr EDU | Guru belajar.id mendapat kuota gratis (10 Edu Kit per hari, unggahan 2025-05-02); harga rupiah, paket embed, dan akses murid tanpa login belum terverifikasi | sekunder / belum terverifikasi |
| Wordwall, Wayground (dulu Quizizz) | Murid bergabung tanpa akun; paket gratisnya kecil (Wordwall Basic: 3 aktivitas) | resmi |
| 8th Wall | Platform hosting ditutup 2026-02-28; proyek dihapus setelah 2027-02-28 | resmi |
| Google Expeditions | Mati sejak 2021 | resmi |
| Web NFC; suara bicara `id-ID` | NFC hanya di Chrome Android dan masih eksperimen; suara bergantung pada perangkat | resmi |
| SIBI (Buku Kemendikdasmen) | Bisa ditautkan per buku dan halaman; lisensi SIBI menutup kemungkinan draf AI dari Buku Guru (buku hanya rujukan) | resmi; D-317 |

## 13. Penyedia AI: Gemini paket gratis, aturan data, data anak

| Temuan | Sumber, dicek | Tingkat | Keputusan, repo |
|---|---|---|---|
| Batas kuota berlaku **per project Google Cloud, per model** (RPM, TPM, RPD), bukan per kunci; kuota harian direset tengah malam waktu Pasifik (pukul 14.00 atau 15.00 WIB) | [rate limits](https://ai.google.dev/gemini-api/docs/rate-limits), 2026-10-30 | resmi | D-023, `docs/final/ai-provider-engine.md` |
| Model bawaan Gemini 3.8 Flash: paket gratis 5 RPM, 250 ribu TPM, 20 RPD per project | AI Studio, 2026-10-31 | resmi | D-117 (cek ulang saat build) |
| Pengguna "will not attempt to circumvent" batas: rotasi banyak project bisa dianggap mengakali batas | [Ketentuan Gemini API](https://ai.google.dev/gemini-api/terms), [Ketentuan Google APIs](https://developers.google.com/terms) | resmi | D-023: risiko ditanggung guru, persetujuan disimpan |
| Pengguna harus berusia 18+; dilarang dipakai di layanan yang "directed towards or likely to be accessed by" anak di bawah 18 tahun (ketentuan berlaku 2026-03-23) | Ketentuan Gemini API, 2026-11-09 | resmi | D-124: murid tidak pernah memicu AI; tidak ada chatbot murid |
| Paket tak berbayar: Google boleh memakai prompt dan hasilnya, dan peninjau manusia bisa membacanya; jangan kirim data sensitif atau pribadi | Ketentuan Gemini API | resmi | D-023, D-036, D-347 (nama dibaca di perangkat) |
| Model preview dan eksperimen punya batas lebih ketat dan tidak untuk produksi | Dokumentasi Gemini | resmi | `ai-provider-engine.md` |
| SDK AI resmi Laravel (`laravel/ai` v1.0, Sep 2026) dirancang untuk satu kunci per penyedia | [Dokumentasi Laravel](https://laravel.com/docs/ai-sdk) | resmi | D-116: klien tipis buatan sendiri |
| UU 27/2022 (PDP) Pasal 25: data anak butuh penanganan khusus dan persetujuan orang tua | undang-undang | resmi | Hanya tujuan pihak ketiga tanpa login; tanpa data murid ke AI |
| PP 17/2025 ("PP Tunas"): tingkatan usia dan pemeriksaan usia untuk platform digital, berlaku sejak Mar 2026 | berita | sekunder | Mendukung kebijakan tanpa akun murid (D-010) |
| Kolom rincian galat 429 yang membedakan batas per menit dan per hari | — | belum terverifikasi | Terbuka; v1 memakai penghitung harian (D-128) |

## 14. Infrastruktur dan hosting

Versi dicek 2026-10-30; alat pemantauan 2026-10-31. Rincian: `docs/final/server-setup.md`, `docs/final/server-monitoring.md`.

| Temuan | Tingkat | Keputusan |
|---|---|---|
| Dukungan standar Ubuntu 22.04 berakhir pertengahan 2027 (tepat setelah musim TKA pertama); 24.04 sampai pertengahan 2029 | resmi (ubuntu.com) | D-086 |
| Laravel 13 (13.0 pada 2026-03-17; keamanan sampai 2028-03-17) mendukung PHP 8.3–8.5; PHP 8.5 aktif sampai 2027-12-31, keamanan sampai 2029-12-31; 8.5.12 menutup CVE Okt 2026 | resmi | server-setup |
| MySQL 8.4 LTS (8.4.13 pada 2026-10-20), dukungan premier sampai Apr 2029; MySQL 8.0 dari Ubuntu sudah habis masa dukungannya | resmi | D-007 |
| Redis 8.2 rilis extended, perbaikan sampai 2030-09-01 | resmi | server-setup |
| `maatwebsite/excel` 3.1 menghalangi PHP 8.5 (PhpSpreadsheet 1.x); 4.x mendukung Laravel 12/13 | resmi (Packagist) | server-setup |
| Gotenberg `8.39.0-chromium` (2026-10-27), sekitar 30% lebih kecil tanpa LibreOffice | resmi | D-028 |
| Node 24 LTS sampai 2028-04-30; Node 26 menjadi LTS pada 2026-10-28 | resmi | server-setup |
| IDCloudHost memastikan NAT keluar termasuk TCP/UDP 7844 untuk VM tanpa IP publik | vendor (2026-10-31) | D-070, D-088 |
| Paket gratis Cloudflare: DNS, proxy, WAF, Zero Trust Access gratis sampai 50 pengguna | resmi | D-088, D-095 |
| Healthchecks.io Hobbyist: 20 cek, Telegram dan email, boleh untuk infrastruktur perusahaan; Business US$20 per bulan untuk 100 cek | resmi | D-143 |
| UptimeRobot gratis: 50 monitor, interval 5 menit, boleh untuk komersial (ketentuan 2026-05-26), hanya email (Telegram berbayar) | resmi | D-143 |
| Better Stack gratis: 10 monitor, cek 3 menit, tanpa Telegram, "Free for personal projects" | resmi | ditolak |
| Tidak ada API publik resmi untuk situs referensi sekolah Kemendikdasmen (pencarian 2026-11-03) | resmi | D-169 |
| Cek merek PDKI untuk "Sigrak" dan cadangan "Kapur" (kelas 9, 41, 42) | — | terbuka ([PDKI](https://pdki-indonesia.dgip.go.id)) |

## 15. Data kalender, wilayah, dan sekolah

| Data | Sumber, tanggal | Tingkat | Keputusan, repo |
|---|---|---|---|
| Direktori sekolah: Kudus 570, Jepara 772, Pati 825 SD/MI | referensi.data.kemendikdasmen.go.id, diambil 2026-11-03 | resmi | D-169, D-170, `docs/data/schools/` |
| Kaldik Jawa Tengah 2026/2027 (Perkadin 400.3/23862/2026, 29 Mei 2026): hari efektif SD/MI 207 (5 hari) dan 254 (6 hari; 134 + 120) | PDF dari pemilik | resmi | D-323 (hanya versi 6 hari; Maulid 25 Agu 2026) |
| Libur nasional dan cuti bersama 2026/2027 (25 tanggal) dan 2027/2028 (5 tanggal) | Aset publik aplikasi lama, 2026-11-09 | belum terverifikasi sampai dicocokkan dengan SKB 3 Menteri | D-253, D-254: dicek daring beserta URL sumbernya sebelum impor |
| Kalender wilayah ditetapkan dinas provinsi atau kabupaten; madrasah mengikuti Kepdirjen Pendis 4860/2026 | Laporan aturan P4-04 | sekunder | D-256, D-257 |
| Data katalog aplikasi lama (contoh kokurikuler, ekstrakurikuler, inventaris kelas, komponen administrasi, pilihan isian) | Aset publik aplikasi lama, 2026-11-09 | draf, ditinjau sesuai D-113; dikurasi ke `docs/data/catalogs/` (D-367) | `docs/sources/usulan-01/data/README.md`, `docs/data/catalogs/README.md`, `docs/drafts/legacy-upgrade-01.md` |

## 16. Celah riset yang masih terbuka

| Celah | Mengapa penting | Dilacak di |
|---|---|---|
| PDF resmi yang belum ada di repo: Permendikdasmen 1/2026, 10/2025, 12/2025, 26/2025, 9/2026, 6/2026 (+ Kepmendikdasmen 17/2026); dasar Permendikbudristek 12/2024 dan 21/2022; Kepka BSKAP 046/H/KR/2025 | Sebagian besar baris di §2 bersandar pada mirror | P4-23 |
| Batas kategori TKA, jumlah soal, dan nilai parsial PGK dalam teks resmi | Penilaian latihan ujian | `kerangka-tka-sd.json` |
| AKMI untuk MI tahun 2026; spesifikasi impor resmi e-Rapor SD dan RDM 2026 | Ekspor (D-218) | P4-10, `policy-innovation-layers.md` §11 |
| Siapa yang menetapkan KKTP menurut pasal | Aturan Asesmen | sama |
| Rumusan 8 fokus observasi dan rubrik perilaku Ruang GTK 2026; instrumen PKG Kemenag 2025–2026; tugas wali kelas di Permendikdasmen 6/2026 | Instrumen supervisi | P4-08 |
| Tata letak kolom ekspor Dapodik 2026 (contoh anonim dari pemilik) | Impor Buku Induk | `buku-induk-dapodik.md` |
| Tanggal libur SKB 3 Menteri dan nomor SKB 2027 | Hari efektif | D-254 |
| Versi Android dan WebView IFP Kemendikdasmen | Game kelas | D-179 |
| Keberadaan "Platform AI Guru" kementerian; klaim Edura School | Persaingan | §11 |
| Lisensi komersial PhET dan GeoGebra; harga dan akses tanpa login Assemblr EDU | Embed LKPD (D-337) | `lkpd-exploration.md` §9 |
| Kolom rincian galat 429 Gemini | Rotasi kunci | `ai-provider-engine.md` |
| Masa simpan wajib menurut hukum untuk rapor, data ijazah, hasil supervisi | Pengaturan retensi di konsol (D-318) | pertanyaan terbuka P4-13 S6 |
| Contoh PKKS dari pengawas Kudus | Pencocokan instrumen PKKS (D-324) | pemilik |
| Bukti lapangan: LKPD escape room, permainan papan, lapbook, kokurikuler, dan ABK; studi retensi atau replikasi | Pola LKPD bersandar pada praktik, bukan uji coba | `lkpd-exploration.md` §8.2 |
| Kolom KBC untuk guru agama SD (cakupan KMA 512/2026) | Keputusan pemilik, bukan riset | `open-questions.md` |
| Cek merek PDKI | Merek | `product-concept.md` |

## 17. Letak riset mentahnya

| Sesi (2026-11-07 sampai 2026-11-09) | Riset yang dimuat | Di repo |
|---|---|---|
| P4-03: SD/MI data from dokumen resmi | Ekstraksi delapan PDF resmi | Seluruhnya, di `docs/data/regulations/` dan `dokumen-resmi-sd-mi.md` |
| P4-04: Research class and school admin features | Laporan aturan (2025–2026, SD dan MI), pembanding aplikasi, laporan supervisi | Sebagian besar; sisanya bertanda **(sesi)** di atas |
| Eksplorasi konsep LKPD untuk Sigrak | Laporan jurnal (sekitar 60 studi) dan penelusuran vendor | 35 teratas di `lkpd-exploration.md`; sisanya di §7.2 di atas |
| P4-13 S1…S6, Review proposal coverage | Wawancara pemilik dan keputusan atas perbandingan dengan aplikasi lama (tanpa riset web) | D-252…D-328, `legacy-upgrade-01.md`, `docs/drafts/legacy-coordination.md`, `docs/drafts/legacy-overlap-adjustments.md` |
| Sigrak positioning dan kebijakan pendidikan | Pengecekan standar 2025–2026 | `policy-innovation-layers.md` |

Riset yang lebih awal (Okt 2026) ada di dokumen final itu sendiri: kerangka TKA (`content-engine.md`, `kerangka-tka-sd.json`), sumber agama (`religious-education.md`), ketentuan Gemini (`ai-provider-engine.md`), dan versi server (`server-setup.md`, `server-monitoring.md`).
