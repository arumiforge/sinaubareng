# Teacher Supervision Instruments for SD/MI

- **Status:** draft; owner decided 2026-11-08 (D-222…D-225), see [§7](#7-owner-decisions-2026-11-08). MI version is a first draft (§8)
- **Task:** P4-12
- **Last updated:** 2026-11-08
- **Scope:** the principal (kepala sekolah/madrasah) supervising SD/MI teachers: guru kelas, guru mapel, and the wali kelas role. The pengawas supervising the principal or the school is **out of scope**.
- **Feeds:** K2 Supervisi akademik and K3 Bukti dukung kinerja in [`administration-features.md`](administration-features.md) (D-217), and P4-08.

## Purpose

The owner supplied one SD's 2026 instrument set ([`Contoh_Instrumen_Supervisi_Guru_SD_2026.pdf`](../sources/Contoh_Instrumen_Supervisi_Guru_SD_2026.pdf)) as a starting point (D-221). It is a good base, but it predates the 2025–2026 rules: it still uses Profil Pelajar Pancasila, "modul ajar", and HOTS/4C, treats supervision mostly as a document check, and has no pre- and post-observation steps.

This draft maps **every instrument a principal uses for SD/MI teachers**, then gives a first version of each, updated to the current rules. The instruments are editable templates, not hard-coded (D-221). Instrument text that teachers will see is quoted in Indonesian, because it is UI and print copy; it moves to `lang/id` when built.

## 1. Rules behind teacher supervision (2025–2026)

| Source | What it says about supervising teachers | Status |
|---|---|---|
| Permendikdasmen 26/2025 (Standar Pengelolaan), Pasal 22–23 | Oversight is regular and continuous through **pemantauan, supervisi, evaluasi**. The principal supervises the curriculum and learning process (goals reached, murid-centred) and how staff work, grow, and **reflect**. Pasal 11(4) allows kelas rangkap in SD/MI | Regulation text (pasal.id mirror; check article numbers against the PDF) |
| Permendikdasmen 1/2026 (Standar Proses), Pasal 4–19 | A plan has at least **tujuan, langkah, asesmen**, format free. Delivery: memahami–mengaplikasi–merefleksi with the principles berkesadaran, bermakna, menggembirakan, and the frame praktik pedagogis, kemitraan, lingkungan, pemanfaatan digital. **Penilaian proses pembelajaran** has four forms, each **at least once a semester**: self-assessment, peers, the principal (**supervisi akademik, analisis hasil belajar murid, umpan balik**, Pasal 18), and murid | Regulation text |
| Panduan Pembelajaran dan Asesmen 2025 (BSKAP) | Planning steps: Identifikasi → Desain pembelajaran → Pengalaman belajar → Asesmen (awal, proses, akhir). Asesmen awal, kemitraan, and pemanfaatan digital are optional. Principal reflection is dialogic, with random class visits, and must not add heavy paperwork. Self-reflection questions. KKTP stays | Guide (in `docs/sources/` via P4-03) |
| Perdirjen GTK 7607/B.B1/HK.03/2023 and the Panduan Substansi Pengelolaan Kinerja (Ruang GTK, Dec 2023), both in `docs/sources/` | Classroom observation on **8 focuses**, which are the sub-indicators of Rapor Pendidikan D1 (kualitas pembelajaran); the teacher picks one per period with the principal. Cycle **A diskusi persiapan → B observasi kinerja → C diskusi tindak lanjut → D upaya tindak lanjut → E refleksi tindak lanjut** (Forms A, B, C, D). The observation sets a **baseline and is not an assessment**. Form B lists recommended and avoided behaviours, scores **Belum dilakukan / Dilakukan tetapi belum efektif / Dilakukan dan efektif**, with notes and a recommendation. Forms C and D record the principal's view of the teacher's awareness (*tidak sadar / sadar kesulitan / sadar dampak kesulitan*). Periods Jan–Jun and Jul–Dec. Teacher accountability documents: KOSP, SK tugas tambahan, kehadiran di kelas | Regulation and guide text, supplied by the owner |
| Kepmendikdasmen 271/O/2025 (Pedoman Pengelolaan Kinerja PTK) | Cycle: pra-perencanaan → perencanaan → pelaksanaan, pemantauan, pembinaan → penilaian → tindak lanjut. The principal is the teacher's PPK. Schools with more than 10 teachers may form a tim kinerja | Salinan read; rubric wording for 2026 not confirmed |
| BAN-PDM IA 2024 (guide version 2025), Komponen 1 Kinerja Pendidik | Four items: social-emotional support, safe and orderly class, effective and meaningful learning, faith/nationhood/reasoning/character. 4 levels, triangulated by observation, interview, and documents. Judges **quality of service, not document completeness** | Guide text |
| Kemenag Panduan Pembelajaran dan Asesmen madrasah (B-572/Dt.I.I/PP.00/11/2025), KMA 1503/2025, KBC (6077/2025) | Lesson plan has a **minimum** and a **complete** version. The minimum Identifikasi includes the DPL, **Topik Panca Cinta**, and KBC integration material. Principal reflection is part of the kepala madrasah's supervision duty; self-reflection is framed as *muhasabah* | Guide text |
| Permendikdasmen 6/2026 (Budaya Sekolah Aman dan Nyaman) | Class agreements, positive discipline, safe class (used in the wali kelas instrument) | Regulation; class-level duties not read in full |

Terms used throughout the new instruments: **murid** (not peserta didik/siswa, D-210), **Dimensi Profil Lulusan (DPL)** with its 8 dimensions (keimanan dan ketakwaan, kewargaan, penalaran kritis, kreativitas, kolaborasi, kemandirian, kesehatan, komunikasi) instead of Profil Pelajar Pancasila and HOTS/4C, and **perencanaan pembelajaran (RPM/RPP/modul ajar)**. The guide itself still says "RPP atau modul ajar", and SIGRAK's template is the RPM, so the instrument accepts any format.

## 2. What the owner's sample has, and what changes

| Sample instrument | Items, max | Keep | Change |
|---|---|---|---|
| Kelengkapan Administrasi Pembelajaran | 12, max 24 | The idea of a short checklist | Trim to what the rules need, add analysis and follow-up, fold "pemanfaatan hasil asesmen diagnostik" into the plan. SIGRAK fills most of it automatically |
| Penelaahan ATP | 12, max 24 | Most criteria | Replace Profil Pelajar Pancasila with DPL; add "TP can be assessed (KKTP)" and realistic JP |
| Telaah Modul Ajar | 28, max 62 | Remedial, enrichment, assessment checks | Rebuild on the 2025 structure: Identifikasi, Desain, Pengalaman belajar, Asesmen. Optional parts score N/A, not 0. MI adds Panca Cinta |
| Penilaian Pelaksanaan Pembelajaran | 45, max 90 | Positive discipline, affective support, cognitive activation, literacy-numeracy | Regroup into 8 domains that match Standar Proses, the Ruang GTK focuses, and BAN-PDM. Replace HOTS/4C with DPL. Add pre- and post-observation and follow-up, which the sample lacks |
| Scoring | 0/1/2, % bands 91–100 Sangat Baik, 81–90 Baik, 71–80 Cukup, < 71 Kurang | Bands, as a school setting | Scale chosen per instrument; evidence note per item; strengths + 1–2 priorities + RTL as the main result |

## 3. Instrument map

Who: **GK** guru kelas, **GM** guru mapel, **WK** wali kelas. "Auto" = SIGRAK pre-fills it from data it already has.

| Code | Instrument | Group | Who | When | Basis | Auto |
|---|---|---|---|---|---|---|
| S-00 | Program dan jadwal supervisi | Management | Principal | Start of year, refresh each semester | 26/2025 Pasal 22–23 (customary form) | Teacher list, timetable, last results |
| S-01 | Kelengkapan perangkat pembelajaran | Planning | GK, GM | Start of each semester | Customary; 1/2026 Pasal 4–8 | Mostly (Kaldik, Prota, Promes, ATP, RPM, jadwal, jurnal, rencana penilaian, nilai, KKTP, absensi) |
| S-02 | Telaah CP–TP–ATP | Planning | GK, GM | Start of year | Panduan 2025 | Curriculum data, school changes |
| S-03 | Telaah Prota dan Promes | Planning | GK, GM | Start of year | Customary | Effective weeks, JP, kokurikuler time |
| S-04 | Telaah perencanaan pembelajaran (RPM) | Planning | GK, GM (MI adds KBC) | Each semester, or before an observation | 1/2026 Pasal 5; Panduan 2025; madrasah Panduan | Opens the RPM beside the form |
| S-05 | Telaah asesmen | Assessment | GK, GM | Each semester | 21/2022; Panduan 2025 | Rencana penilaian, Bank Soal, KKTP |
| S-06 | Analisis hasil asesmen dan tindak lanjut | Assessment | GK, GM | Mid- and end of semester | 1/2026 Pasal 18 | Analisis per TP, remedial/pengayaan (A1) |
| S-07 | Pra-observasi | Observation cycle | GK, GM | Each visit | Perdirjen 7607 Form A | RPM of the observed lesson |
| S-08 | Observasi pelaksanaan pembelajaran | Observation cycle | GK, GM | At least once a semester | 1/2026 Pasal 9–12, 18; Perdirjen 7607 Form B; IA 2024 | — |
| S-08a | Variant: PJOK (field) | Observation cycle | GM PJOK | Each semester | Customary | — |
| S-08b | Variant: Pendidikan Agama and MI Kemenag subjects | Observation cycle | GM agama, MI subjects | Each semester | Customary; KBC for MI | — |
| S-08c | Variant: Bahasa Inggris | Observation cycle | GM | Each semester | Customary | — |
| S-08d | Variant: kelas rangkap | Observation cycle | GK | Each semester | 26/2025 Pasal 11(4) | — |
| S-09 | Pasca-observasi: refleksi, umpan balik, RTL | Observation cycle | GK, GM | 1–3 days after the visit | Perdirjen 7607 Form C | Scores and notes from S-08 |
| S-10 | Pemantauan RTL | Observation cycle | GK, GM | 4–8 weeks later | Perdirjen 7607 Form D | RTL from S-09 |
| S-11 | Refleksi diri guru | Reflection | All | At least once a semester | 1/2026 Pasal 16; Panduan 2025 ch. 6 | Same as A4 (journals, scores) |
| S-12 | Umpan balik murid | Reflection | All | At least once a semester per subject | 1/2026 Pasal 19 | Teacher enters the class summary (murid are not users, D-010) |
| S-13 | Refleksi sejawat (discussion record, no scores, D-224) | Reflection | All | At least once a semester | 1/2026 Pasal 17 | — |
| S-14 | Telaah kokurikuler (incl. 7 KAIH) | Kokurikuler | GK, WK, kokurikuler team | Each semester | Panduan Kokurikuler 2025 | Kokurikuler plan, A5, A6 |
| S-15 | Administrasi wali kelas | Homeroom | WK | Start and end of semester | Customary; Panduan Asesmen 2025 (rapor); 6/2026 | Mostly (Siswa, Absensi, Catatan, H2, H6, Rapor) |
| S-16 | Rekap dan laporan hasil supervisi | Management | Principal | End of semester/year | 26/2025 Pasal 22 (evaluasi) | All of the above |
| S-17 | Program tindak lanjut sekolah | Management | Principal + teachers | Ongoing | Customary; Panduan (komunitas belajar) | Needs from S-16 |

Accreditation and Ruang GTK both judge practice over paperwork, so S-01 stays short and the observation cycle (S-07 → S-10) is the centre. S-07 to S-10 also give each teacher the evidence for Ruang GTK (K3).

## 4. Scoring

Proposed defaults, all editable per school:

| Instrument type | Default scale | Why |
|---|---|---|
| Checklists (S-01, S-15) | **0 Tidak ada · 1 Ada, belum sesuai · 2 Ada, sesuai** | Same as the owner's sample |
| Reviews (S-02…S-06, S-14) | **1–4 with a short descriptor per item** (1 Belum ada, 2 Ada tetapi belum memenuhi, 3 Memenuhi, 4 Memenuhi dan kontekstual) | Descriptive rubric like BAN-PDM; less argument over "1 vs 2" |
| Observation (S-08 and variants) | **Belum dilakukan · Dilakukan, belum efektif · Dilakukan dan efektif** | The official Ruang GTK scale, so results carry over to the teacher's performance record |

Rules for every instrument:

1. **N/A is not 0.** Optional parts (asesmen awal, kemitraan, pemanfaatan digital, lampiran) and items not seen in the visit score *Tidak berlaku / tidak teramati* and leave the total.
2. **Evidence note per item.** Required when the score is the lowest level: what was seen or heard.
3. **Nilai akhir** = score ÷ maximum of the applicable items × 100%, with the sample's bands (91–100 Sangat Baik, 81–90 Baik, 71–80 Cukup, < 71 Kurang) as a school setting, shown as a summary only.
4. **The main result is feedback:** 2–3 strengths, 1–2 priorities, and an RTL with a date. Both sign: the teacher and the principal (supervisor).
5. Every instrument prints on the school letterhead (D-031) and downloads as PDF/Word.

## 5. First draft of each instrument

Indicator text is the proposed Indonesian print text. Tags: **[W]** wajib (in the rule or the guide's core list), **[O]** opsional (N/A allowed), **[MI]** madrasah only.

### S-00 Program dan jadwal supervisi

Fields: tahun ajaran and semester; tujuan supervisi (from last semester's S-16 and Rapor Pendidikan notes typed by the principal); per teacher: fokus (1–2 of the S-08 domains, agreed in S-07), instrumen dipakai, tanggal rencana, teknik (kunjungan kelas, telaah dokumen, sejawat, kelompok/KKG), status. Output: the programme document and a calendar of visits. Visits respect the timetable, so the principal only picks a teacher and a week.

### S-01 Kelengkapan perangkat pembelajaran (10 items, 0–2)

1. Kalender pendidikan dan rincian minggu efektif
2. Program tahunan dan program semester
3. Alur tujuan pembelajaran (TP dari analisis CP)
4. Perencanaan pembelajaran (RPM/RPP/modul ajar, format bebas)
5. Jadwal pelajaran
6. Jurnal mengajar
7. Program dan instrumen asesmen, termasuk KKTP
8. Daftar nilai dan analisis hasil asesmen
9. Program remedial dan pengayaan
10. Daftar hadir murid (wali kelas); untuk guru mapel: perencanaan kokurikuler bila terlibat

In SIGRAK, items 1–9 are ticked automatically when the data exists; the principal only confirms "sesuai" and adds notes. A school can add items.

### S-02 Telaah CP–TP–ATP (10 items, 1–4)

1. Identitas: satuan, mata pelajaran, fase/kelas, CP yang diacu [W]
2. TP mencakup seluruh elemen CP pada fase tersebut [W]
3. Setiap TP memuat kompetensi dan konten (kata kerja operasional dan materi) [W]
4. TP dapat diukur (KKTP dapat diturunkan) [W]
5. ATP tersusun runtut dari sederhana ke kompleks [W]
6. ATP menggambarkan perkembangan sepanjang fase [W]
7. Alokasi JP per TP realistis terhadap minggu efektif [W]
8. TP terkait dengan dimensi profil lulusan yang relevan [W]
9. TP kontekstual dengan karakteristik murid dan sekolah [O]
10. Perubahan dari kurikulum bawaan sudah divalidasi (alur validasi D-019) [O]

### S-03 Telaah Prota dan Promes (6 items, 1–4)

1. Sesuai kalender pendidikan dan minggu efektif [W]
2. JP per mata pelajaran sesuai struktur kurikulum (SD: Permendikdasmen 13/2025; MI: KMA 1503/2025) [W]
3. Urutan TP sesuai ATP [W]
4. Waktu asesmen sumatif tercantum [W]
5. Waktu kokurikuler tercantum [W]
6. Ada ruang untuk remedial, pengayaan, dan kegiatan sekolah [O]

### S-04 Telaah perencanaan pembelajaran (RPM) (1–4)

A. Identitas
1. Satuan, fase/kelas, mata pelajaran, alokasi waktu [W]

B. Identifikasi
2. Kesiapan murid (asesmen awal dan tindak lanjutnya) [O]
3. Karakteristik materi [W]
4. Dimensi profil lulusan yang dipilih sesuai TP [W]
5. Topik Panca Cinta dan materi integrasi KBC [MI][W]

C. Desain pembelajaran
6. CP yang diacu [W]
7. TP memuat kompetensi, konten, dan kata kerja operasional [W]
8. Praktik pedagogis (model/strategi) sesuai TP [W]
9. Lingkungan pembelajaran aman, inklusif, saling menghargai [W]
10. Lintas disiplin ilmu [O]
11. Kemitraan pembelajaran (orang tua, komunitas, sekolah lain) [O]
12. Pemanfaatan digital atau nondigital [O]

D. Pengalaman belajar
13. Kegiatan awal, inti, dan penutup jelas [W]
14. Ada pengalaman memahami [W]
15. Ada pengalaman mengaplikasi dalam konteks nyata [W]
16. Ada pengalaman merefleksi [W]
17. Tiap langkah menunjukkan prinsip berkesadaran, bermakna, atau menggembirakan [W]
18. Diferensiasi untuk murid yang beragam, termasuk yang perlu dukungan [W]
19. Alokasi waktu realistis [W]

E. Asesmen
20. Asesmen awal [O]
21. Asesmen proses (umpan balik, penilaian diri, penilaian sejawat) [W]
22. Asesmen akhir sesuai TP [W]
23. Instrumen dan rubrik terlampir, KKTP jelas [W]
24. Rencana remedial dan pengayaan [W]

F. Lampiran
25. LKPD, bahan bacaan, media [O]

When the plan is a SIGRAK RPM, items 1, 3, 4, 6, 7, 13 are checked from its fields and shown pre-filled; the principal judges quality.

### S-05 Telaah asesmen (8 items, 1–4)

1. Asesmen awal/diagnostik direncanakan dan hasilnya dipakai [O]
2. Asesmen formatif beragam teknik (observasi, unjuk kerja, tulis, lisan) [W]
3. Asesmen sumatif sesuai TP dan kisi-kisi [W]
4. Butir soal sesuai kisi-kisi dan level kognitif [W]
5. Rubrik untuk unjuk kerja, produk, atau projek [W]
6. KKTP ditetapkan per TP [W]
7. Fase A: penekanan observasi dan unjuk kerja; tes tulis opsional (D-206) [W]
8. Rencana penggunaan hasil asesmen [W]

### S-06 Analisis hasil asesmen dan tindak lanjut (6 items, 1–4)

1. Hasil dianalisis per TP [W]
2. Murid yang belum mencapai KKTP teridentifikasi [W]
3. Program remedial sesuai kebutuhan (pembelajaran ulang, bimbingan, kelompok, tutor sebaya) [W]
4. Program pengayaan untuk murid yang sudah mencapai [W]
5. Hasil tindak lanjut tercatat (asesmen ulang) [W]
6. Hasil dipakai untuk memperbaiki perencanaan berikutnya [W]

### S-07 Pra-observasi (form, no score; Ruang GTK Form A)

- Kelas, mata pelajaran, tanggal dan jam (from the timetable), the RPM of that lesson
- Fokus observasi yang disepakati (1–2 domains of S-08, or the 8 Ruang GTK focuses)
- Yang ingin guru mendapat masukan
- Tantangan kelas yang perlu diketahui pengamat
- Kesepakatan: posisi pengamat, durasi, apakah murid diberi tahu
- Ruang GTK fields: periode (Januari–Juni or Juli–Desember), rencana hasil kerja (one of the 8 focuses), fokus perilaku, upaya mempelajari, perangkat ajar

### S-08 Observasi pelaksanaan pembelajaran (24 items, 3 levels)

The domains bridge Standar Proses (1/2026 Pasal 9–12), the Ruang GTK focuses (in brackets), and BAN-PDM Komponen 1. For a Ruang GTK observation, only the agreed focus is scored (as a baseline, not an assessment); the full form is a formative snapshot. Each item shows the recommended and avoided behaviours as hints, so the principal records evidence rather than a feeling.

1. **Lingkungan dan manajemen kelas**
   1. Suasana kelas teratur, waktu belajar efektif (keteraturan suasana kelas)
   2. Disiplin positif: kesepakatan kelas ditegakkan tanpa mempermalukan (penerapan disiplin positif)
   3. Kelas aman dan inklusif bagi semua murid
2. **Dukungan psikologis dan sosial-emosional**
   4. Guru menunjukkan harapan tinggi bahwa semua murid mampu (ekspektasi)
   5. Guru memberi perhatian dan bantuan kepada murid yang membutuhkan (perhatian dan kepedulian)
   6. Umpan balik membangun atas hasil dan perilaku murid (umpan balik konstruktif)
   7. Bahasa guru menghargai, murid merasa didengar
3. **Aktivasi kognitif dan pengalaman belajar**
   8. Penjelasan dan instruksi jelas dan terstruktur (instruksi pembelajaran)
   9. Guru menyesuaikan pengajaran dengan respons murid (instruksi yang adaptif)
   10. Murid berinteraksi dan berdiskusi untuk memahami (aktivitas interaktif)
   11. Murid membangun pemahaman (memahami)
   12. Murid menerapkan dalam konteks nyata (mengaplikasi)
   13. Murid mengevaluasi proses dan hasil belajarnya (merefleksi)
4. **Prinsip pembelajaran mendalam**
   14. Berkesadaran: murid tahu tujuan belajar dan terlibat aktif
   15. Bermakna: materi terhubung dengan kehidupan murid
   16. Menggembirakan: menantang dan menyenangkan
5. **Penguatan dimensi profil lulusan**
   17. Dimensi profil lulusan yang direncanakan tampak dalam kegiatan
   18. Literasi dan numerasi terintegrasi
   19. [MI] Nilai Panca Cinta dan adab tampak dalam pembelajaran
6. **Kemitraan dan pemanfaatan sumber belajar**
   20. Sumber dan media belajar beragam dan dipakai murid [O]
7. **Asesmen dalam proses**
   21. Guru memeriksa pemahaman murid selama pembelajaran
   22. Murid menilai diri atau teman [O]
   23. Guru menyesuaikan langkah berdasarkan bukti asesmen
8. **Penutup**
   24. Murid merangkum dan merefleksi; guru menyampaikan tindak lanjut

Each item has an evidence note. The form ends with *Kekuatan*, *Prioritas perbaikan*, and *Rekomendasi*.

**Variant items** (added to S-08, the core stays):

| Variant | Extra items |
|---|---|
| S-08a PJOK | Pemanasan dan keselamatan; pengelolaan ruang dan alat; waktu aktif bergerak murid cukup; tugas gerak sesuai kemampuan; umpan balik gerak; kebiasaan sehat dan kebersihan |
| S-08b Agama / MI | Keteladanan dan adab; praktik ibadah atau tartil/makharij bila relevan (Al-Qur'an Hadis, Fikih); SKI dikaitkan dengan konteks; Bahasa Arab dipakai dalam interaksi; [MI] integrasi KBC/Panca Cinta. For SD religions other than Islam, only the first item and "nilai agama dalam kehidupan" apply |
| S-08c Bahasa Inggris | Bahasa Inggris dipakai dalam instruksi; murid berbicara (lagu, permainan, TPR); suasana tidak menekan |
| S-08d Kelas rangkap | Pengelompokan per kelas/fase; tugas mandiri jelas saat guru mendampingi kelompok lain; rotasi waktu; TP berbeda per kelompok |

### S-09 Pasca-observasi (form, no score; Ruang GTK Form C)

- Refleksi guru: apa yang berjalan baik, apa yang ingin diubah, dampak ke murid
- Umpan balik pengamat: kekuatan (from S-08), 1–2 prioritas
- Kesadaran guru atas tantangan: *belum menyadari / menyadari / menyadari dampaknya* (Form C)
- Rencana tindak lanjut (RTL): tujuan, langkah, dukungan yang dibutuhkan (coaching, KKG, contoh praktik, pelatihan Ruang GTK), batas waktu

### S-10 Pemantauan RTL (form; Ruang GTK Form D)

- Per RTL row from S-09: tujuan, upaya, kapan, dukungan (copied), then capaian, tantangan, upaya peningkatan; the principal's awareness rating again; status *selesai / berlanjut*. A continuing RTL becomes the focus of the next S-07.

### S-11 Refleksi diri guru

The Panduan questions (8 in the madrasah edition, 7 in the general one), pre-filled with facts from SIGRAK (journals done, TP reached, remedial run): tujuan dan target semester ini; yang disukai; yang berhasil; yang perlu ditingkatkan; rencana agar lebih baik; tantangan terbesar; cara mengatasinya. Plus a self-rating on the S-08 domains. MI labels it *Muhasabah*. Same feature as A4.

### S-12 Umpan balik murid

Murid are not users (D-010). The teacher runs a short class activity (emoji cards for grades 1–3, a 5-question sheet for 4–6; printable) and enters the class count per answer: saya paham tujuan belajar; saya berani bertanya; kegiatannya menyenangkan; guru membantu saat saya kesulitan; saya ingin belajar lagi. Only class totals are stored.

### S-13 Refleksi sejawat

A discussion record only (D-224): date, colleague, topic (a plan, a lesson video, or a shared practice), what was learned, what to try. No observation scores. Evidence for Pasal 17.

### S-14 Telaah kokurikuler (8 items, 1–4)

1. Bentuk kegiatan jelas: lintas disiplin, 7 KAIH, atau lainnya [W]
2. Dimensi profil lulusan sasaran ditetapkan [W]
3. Alur kegiatan dan alokasi waktu sesuai rencana sekolah [W]
4. Kolaborasi antarguru atau mata pelajaran [O]
5. Kemitraan (orang tua, masyarakat) [O]
6. Asesmen kokurikuler per dimensi direncanakan [W]
7. 7 KAIH: pembiasaan dipantau dan catatan harian diolah [O]
8. Deskripsi kokurikuler rapor disusun dari hasil asesmen [W]

### S-15 Administrasi wali kelas (12 items, 0–2)

1. Data murid kelas lengkap dan mutakhir
2. Daftar hadir harian terisi
3. Rekap kehadiran dan tindak lanjut ketidakhadiran (H2)
4. Kesepakatan kelas disusun bersama murid (A8)
5. Catatan perkembangan dan anekdot murid
6. Catatan disiplin positif dan penanganan kasus
7. Komunikasi dengan orang tua tercatat (H6)
8. Kunjungan rumah bila diperlukan (H7)
9. Jadwal piket dan pengelolaan kelas
10. Data kesehatan ringan (tinggi, berat) per semester (H8) [O]
11. Rapor lengkap: nilai, deskripsi, kokurikuler, ekstrakurikuler, ketidakhadiran, catatan wali kelas (H1)
12. Kenaikan kelas atau kelulusan disiapkan (H3, H4) [end of year]

Items 1–3, 5, 7–11 are ticked from SIGRAK data. Counselling notes stay hidden from the form (only a count is shown), as today.

### S-16 Rekap dan laporan hasil supervisi

Per teacher: instruments done, scores and bands, strengths, priorities, RTL status. School level: most common priorities per domain, teachers without a visit, RTL completion. Output: semester report (PDF) and an Excel table. It feeds the principal's evaluation and next year's programme (S-00), and K6 (evaluasi KSP) later.

### S-17 Program tindak lanjut sekolah

Rows: kebutuhan (from S-16), kegiatan (coaching, KKG/komunitas belajar, contoh praktik, in-house training, pelatihan Ruang GTK), penanggung jawab, jadwal, bukti, status.

## 6. How it fits SIGRAK

| Need | How |
|---|---|
| Templates, not hard-coded | Each instrument is a versioned template (items, tags, scale, bands) in the database. Tim SIGRAK ships the defaults above; a school can copy and edit (D-221). A visit stores the template version it used |
| Data already in SIGRAK | S-01, S-04, S-06, S-11, S-15 pre-fill from Kurikulum, RPM, Jurnal, Asesmen, Absensi, Catatan Siswa |
| Phone use | S-08 works on a phone during the lesson, one domain per screen. Offline is not needed in v1 (D-016) |
| Teacher view | The teacher sees every result about them, comments, and signs. Only the principal fills the forms (D-224) |
| Ruang GTK | K3 bundles S-07 to S-10 and S-11 for the teacher's own upload; no integration |
| AI | Optional: draft the feedback paragraph from scores and evidence notes. No teacher or murid names in the prompt (D-036) |
| Supervisor (pengawas) | Sees school-level S-16 aggregates only, never teacher-level scores (D-224) |

Data model additions (for P4-08): `supervision_templates`, `supervision_template_items`, `supervision_programs`, `supervision_visits` (teacher, template version, date, status), `supervision_scores` (item, level, evidence note), `supervision_followups` (RTL rows). All school-scoped (`BelongsToSchool`).

## 7. Owner decisions (2026-11-08)

| Question | Choice | Decision |
|---|---|---|
| Instrument set | All 18 at once (S-00…S-17 with S-08 variants) | D-222 |
| Observation scale | Ruang GTK 3 levels | D-223 |
| Review scale | 1–4 with a descriptor per item; checklists keep 0/1/2 | D-223 |
| Bands | As in the sample, changeable by the school | D-223 |
| Who observes | Only the principal; S-13 peer reflection without scores | D-224 |
| Pengawas view | School recap only | D-224 |
| Murid feedback | Class totals entered by the teacher | D-225 |

The SK pembagian tugas sample is kept in `docs/sources/` as received (owner's choice). The Ruang GTK documents the owner supplied are in `docs/sources/` too.

Still open: the per-item descriptors for the 1–4 scale (S-02…S-06, S-14) and the recommended/avoided behaviour hints for S-08 are written in P4-08, from the Ruang GTK rubric shown in the platform (not in the supplied PDFs).

## 8. MI version (first draft)

The same 18 instruments apply to MI, with these changes. A revised version follows once Kemenag publishes or the owner supplies a madrasah instrument (none found for 2025–2026).

| Area | MI change |
|---|---|
| Names | *Kepala madrasah*, *madrasah*, *guru kelas/guru mapel MI*. S-11 is titled *Refleksi Diri (Muhasabah) Guru* |
| Basis | KMA 1503/2025 (amends KMA 450/2024), Panduan Pembelajaran dan Asesmen madrasah (B-572/Dt.I.I/PP.00/11/2025), KBC (Kepdirjen Pendis 6077/2025), beban kerja KMA 736/2026 |
| S-01 | Add: perencanaan memuat integrasi KBC |
| S-03 | JP per subject from KMA 1503/2025 (Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab, muatan lokal 72–216 JP) |
| S-04 | The [MI] item (Topik Panca Cinta and KBC integration material) is wajib. The madrasah *minimum* plan (Identifikasi: DPL, Panca Cinta, KBC material; Desain: TP, praktik pedagogis, lingkungan; Pengalaman belajar; Asesmen) is enough for a full score; *complete* items score N/A when absent |
| S-08 | Item 19 (Panca Cinta and adab) applies; S-08b is the default variant for the five Kemenag subjects |
| S-14 | Kokurikuler may carry Panca Cinta themes |
| S-15 | Same items |
| Performance | Ruang GTK forms apply to madrasah teachers registered there; teacher data of record is EMIS GTK, not Dapodik |

## Sources

All accessed 2026-11-08.

- Owner's sample: [`Contoh_Instrumen_Supervisi_Guru_SD_2026.pdf`](../sources/Contoh_Instrumen_Supervisi_Guru_SD_2026.pdf)
- Owner-supplied Ruang GTK documents: [`Perdirjen_GTK_7607_B.B1_HK.03_2023_Juknis_Pengelolaan_Kinerja_Guru_dan_Kepala_Sekolah.pdf`](../sources/Perdirjen_GTK_7607_B.B1_HK.03_2023_Juknis_Pengelolaan_Kinerja_Guru_dan_Kepala_Sekolah.pdf), [`Panduan_Substansi_Pengelolaan_Kinerja_Guru_dan_Kepala_Sekolah_2023.pdf`](../sources/Panduan_Substansi_Pengelolaan_Kinerja_Guru_dan_Kepala_Sekolah_2023.pdf)
- Permendikdasmen 1/2026: https://pasal.id/peraturan/permen/permendikdasmen-no-1-tahun-2026 · https://www.peraturan.go.id/filespengundangan/Permendikdasmen-no-1-tahun-2026.pdf
- Permendikdasmen 26/2025: https://pasal.id/peraturan/permen/permendikdasmen-no-26-tahun-2025 · https://peraturan.go.id/files/Permendikdasmen-no-26-tahun-2025.pdf
- Panduan Pembelajaran dan Asesmen 2025 (BSKAP): copy supplied by the owner (moved to `docs/sources/` by P4-03); mirror https://kurikulummerdeka.com/panduan-pembelajaran-mendalam-dan-asesmen-edisi-revisi-tahun-2025/
- Perdirjen GTK 7607/B.B1/HK.03/2023 (Pengelolaan Kinerja, Forms A–D, 8 focuses): https://uploads.belajar.id/document/files/Salinan_Perdirjen_Pengelolaan_Kinerja_Guru_dan_Kepala_Sekolah_01hy1qv1x4c9q32nmr5vxp23eh.pdf
- Kepmendikdasmen 271/O/2025 (secondary page with the salinan): https://www.komunitasbelajar.id/2025/12/kepmendikdasmen-nomor-27102025.html
- BAN-PDM Panduan Penjelasan IA 2024 versi 2025: https://ban-pdm.id/documents/download/1756890580_7aed8e14ada8b94fca13.pdf
- Kemenag Panduan Pembelajaran dan Asesmen madrasah (B-572/Dt.I.I/PP.00/11/2025): https://bloggeografi.id/wp-content/uploads/2025/11/panduan-pembelajaran-dan-asesmen.pdf
- KMA 1503/2025 (secondary summary): https://yunandra.com/kma-1503-tahun-2025-pedoman-kurikulum-madrasah/
- PermenPAN-RB 21/2024: https://peraturan.bpk.go.id/Details/311201/permen-panrb-no-21-tahun-2024
- Permendikdasmen 6/2026: https://peraturan.go.id/files/Permendikdasmen-no-6-tahun-2026.pdf
- Ruang GTK 2026 observation (secondary): https://www.dealearningcenter.id/cara-mengisi-e-kinerja-guru-2026-ruang-gtk/

Not verified: the 2026 Ruang GTK wording of the 8 focuses (the 2023 wording is used), the full text of Kepmendikdasmen 126/P/2025 on supervision, class-level duties in Permendikdasmen 6/2026, and any 2025–2026 Kemenag teacher-observation instrument (none found).
