# Research Digest

- **Status:** final (owner request 2026-11-09, D-362)
- **Last updated:** 2026-11-11
- **Purpose:** one place that summarises every piece of research done for SIGRAK so far: official regulations, journal and field studies, app benchmarks, and technical research. It is a digest with pointers, not a copy. Details stay in the linked repo documents.
- **Indonesian PDF:** `docs/pdf/research-digest.pdf`

## 1. How to use this digest

**Before you research anything, check here first.** If a finding is listed, use it and follow the link to the detail. Re-check it only if it is old, marked *secondary* or *unverified*, or touches money, law, or a vendor's terms. **After you research, add your findings here** in the same shape (source, date checked, verification level, finding, implication, decisions, repo doc), and put the full detail in the task's draft (D-362). Findings that came only from earlier session transcripts and never reached another repo doc are marked **(session)**.

Verification levels used below:

| Level | Meaning |
|---|---|
| **official** | Read in the official text: a PDF in `docs/sources/`, peraturan.go.id, a ministry site, or the vendor's own terms or docs |
| **mirror** | Read in full text on a mirror (pasal.id, marked "belum ditinjau" there, or a school or blog copy of the PDF). Check article numbers against the official PDF before quoting |
| **secondary** | Only news, blogs, or search snippets. Never put it in product copy without checking |
| **unverified** | Mentioned or claimed, but no usable source was found |

Product copy and in-app rule text must re-check any regulation number on jdih.kemendikdasmen.go.id or peraturan.go.id before use.

## 2. Regulations and national standards (Kemendikdasmen)

Checked 2026-10-30 to 2026-11-11. On 2026-11-08 and 2026-11-09 the official JDIH sites often returned 403, 429, or 503, so several rows rest on the pasal.id mirror. In P4-23 (2026-11-10/11) jdih.kemendikdasmen.go.id, peraturan.go.id, and peraturan.bpk.go.id could not be reached from the work environment; six PDFs were taken from www.kemendikdasmen.go.id, a ministry unit, or regional education offices, each with its `verification` level in [`docs/sources/README.md`](../sources/README.md) and extracted to [`docs/data/regulations/`](../data/regulations/README.md). In P4-32 (2026-11-11) the owner supplied the last four from an Indonesian connection (Permendikdasmen 1/2026 and 9/2026, Kepmendikdasmen 17/2026, base Permendikbudristek 12/2024), extracted the same way.

| Instrument | Issuer, date | What it governs (SD/MI view) | Level | Detail in repo |
|---|---|---|---|---|
| Permendikdasmen 13/2025 (amends Permendikbudristek 12/2024) | Kemendikdasmen, in force 15 Jul 2025 | Curriculum: pembelajaran mendalam in the kerangka dasar, 8 dimensi profil lulusan, intra/ko/ekstrakurikuler, SD/MI JP tables (Lampiran II B, Tabel 1–5), KKA elective, Pramuka required again (Pasal 22) | official | [`docs/data/regulations/`](../data/regulations/README.md), [`dokumen-resmi-sd-mi.md`](../drafts/dokumen-resmi-sd-mi.md) |
| Permendikbudristek 12/2024 (base text) | set 25 Mar, in force 26 Mar 2024 (BN 2024/172); amended by 13/2025 | Still in force where 13/2025 did not amend: Fase A–C = kelas I–II, III–IV, V–VI (Pasal 9); CP set centrally (Pasal 11); SD content "dalam bentuk mata pelajaran" (Pasal 13); KSP "paling sedikit memuat" karakteristik, visi-misi-tujuan, pengorganisasian and perencanaan pembelajaran, diversification, set by the principal (Pasal 29–30); Kurikulum 2013 only until 2025/2026 (Pasal 31); **Bahasa Inggris in SD/MI elective until 2026/2027, compulsory from 2027/2028 (Pasal 33, not amended)**, while the 13/2025 tables already list it as required in kelas III–VI | official | `curriculum-base-permendikbudristek-12-2024.json` (P4-32), [`policy-innovation-layers.md`](../drafts/policy-innovation-layers.md) §3 |
| Kepmendikdasmen 126/P/2025 | 4 Sep 2025 | Pedoman Pembelajaran Mendalam: principles, learning experiences, framework, roles | official | `deep-learning-kepmendikdasmen-126-p-2025.json` |
| Kepmendikdasmen 127/P/2025 | 4 Sep 2025 | Pedoman Koding dan Kecerdasan Artifisial: status, delivery modes, risks; no SD grades or JP | official | `coding-ai-kepmendikdasmen-127-p-2025.json` |
| Permendikdasmen 1/2026 (Standar Proses) | set 2 Jan, in force 5 Jan 2026 (BN 2026/1); revokes Permendikbudristek 16/2022 and Permendiknas 3/2008 | Planning by the teacher (Pasal 4(2)); **plan "paling sedikit memuat" tujuan, langkah, asesmen (Pasal 5)**, no format set; atmosphere and the teacher roles keteladanan, pendampingan, fasilitasi (Pasal 9); memahami, mengaplikasi, merefleksi (Pasal 10); load in JP (Pasal 14); **teacher self-reflection required at least once a semester (Pasal 15); review by peers, the principal, and murid optional, each at least once a semester when done, murid per subject (Pasal 16–19)**; term *murid*; no guru kelas or wali kelas wording | official | `process-standard-permendikdasmen-1-2026.json` (P4-32), [`administration-features.md`](../drafts/administration-features.md) §1, [`teacher-supervision-instruments.md`](../drafts/teacher-supervision-instruments.md) §1 |
| Permendikdasmen 10/2025 (SKL) | set 10 Jun, in force 13 Jun 2025 (BN 2025/410); revokes Permendikbudristek 5/2022 | 8 dimensi profil lulusan with definitions (Pasal 4); SD/MI competency description per dimension (Pasal 7 a–h); SKL guides graduation (Pasal 3) and every other standard (Pasal 2(4)); dasar focus on faith, Pancasila character, literacy and numeracy (Pasal 6) | official | `graduate-competency-standard-permendikdasmen-10-2025.json` (P4-23) |
| Permendikdasmen 12/2025 (Standar Isi) | set 10 Jul, in force 15 Jul 2025 (BN 2025/502); revokes Permendikbudristek 8/2024 | "Kriteria minimal" for scope of material; 11 muatan wajib, English the compulsory foreign language (Pasal 3); religion content with Kemenag, muatan lokal by the Pemda (Pasal 7); SD scope per muatan in Lampiran II B, with **Teknologi Digital (computational thinking, digital literacy, basic AI) under Matematika** | official | `content-standard-permendikdasmen-12-2025.json` (P4-23) |
| Permendikbudristek 21/2022 (Standar Penilaian) | set 26 Apr, in force 28 Apr 2022 (BN 2022/460); no replacement found | Assessment berkeadilan, objektif, edukatif; five steps; formative at every level, summative from SD for promotion and graduation against kriteria ketercapaian tujuan pembelajaran (Pasal 9); report at least states achievement, in rapor or another form (Pasal 8); **SD graduation looks at kelas V and VI** (Pasal 10(2)); mechanism set by the school on the BSKAP guide (Pasal 11–12) | mirror (salinan from a Dinas site) | `assessment-standard-permendikbudristek-21-2022.json` (P4-23) |
| Permendikdasmen 26/2025 (Standar Pengelolaan) | set 16 Dec, in force 17 Dec 2025 (BN 2025/1079); revokes Permendikbudristek 47/2023 | Plan made with the komite and set by the principal (Pasal 4); RKJM (4 years) and RKT (Pasal 5); KSP, program pembelajaran, program penilaian as minimum outputs (Pasal 7); SD rombel at most 28, at most 24 rombel per SD (Pasal 8–9); kelas rangkap allowed (Pasal 11(4)); **one learning session per day, 3-year transition** (Pasal 17, 26); oversight through pemantauan, supervisi, evaluasi (Pasal 22–23) | mirror (JDIH salinan from a Dinas site) | `management-standard-permendikdasmen-26-2025.json` (P4-23), `administration-features.md` §1 |
| Permendikdasmen 11/2025 + Kepmendikdasmen 221/P/2025 | 2025 (221/P around 12 Nov 2025, secondary) | Teacher workload 24–40 JTM; wali kelas 2 JTM in SD; other extra duties up to 6 JTM; counts only with an SK and in Dapodik; revokes Kepmen 495/M/2024 | secondary | `administration-features.md` §1 |
| Permendikdasmen 6/2026 (Budaya Sekolah Aman dan Nyaman) | set 8 Jan, in force 9 Jan 2026 (BN 2026/11); revokes 46/2023 | Tata tertib, kode etik, SOP (Pasal 13); early detection with a confidential complaint channel (Pasal 12); kesepakatan kelas drafted with students (Pasal 19); yearly monev reported to the Pokja (Pasal 41); TPPK and Satgas end, cases go to a regional Pokja formed within 6 months (Pasal 43–44); **in SD the guru kelas leads class conditions; "wali kelas" as class manager is named only for SMP/SMA/SMK** (Pasal 18(3)); implementing guide Kepmendikdasmen 17/2026 in the next row | official | `safe-school-culture-permendikdasmen-6-2026.json` (P4-23), `administration-features.md`, `teacher-supervision-instruments.md` |
| Kepmendikdasmen 17/2026 (Pedoman Penyelenggaraan BSAN) | set and in force 6 Feb 2026; implements 6/2026 (Pasal 27) | **For PAUD and SD the class role is "Guru Kelas atau Wali Kelas"**: main responsibility for class conditions (Tabel 3.5); kesepakatan kelas "setidaknya memuat" nilai kebajikan, interaksi saling menghargai, tindakan pembinaan yang mendidik, signed by the class, reviewed, shared with parents; tata tertib and kode etik minimum content, reviewed at least yearly; POS minimum content; complaint channel with a first response within 24 hours (recommended); recording or sharing lesson photos or video without consent is a teacher code-of-ethics breach; first report to the Pokja leaves out murid names; the principal's BSAN monev is part of the yearly school report | mirror (JDIH salinan re-saved by a non-government site) | `safe-school-culture-guide-kepmendikdasmen-17-2026.json` (P4-32) |
| Permendikdasmen 3/2025 (SPMB) | 2025 | Transfers allowed except in the last semester of grade 6; receiving school updates Dapodik within 14 working days | mirror | `administration-features.md` §1 |
| Permendikbudristek 58/2024 (ijazah) | about Oct 2024 (date unverified) | Ijazah signed by the principal; e-ijazah printed by the school from 2025 (secondary) | official PDF linked | same |
| Permendikdasmen 9/2025 (TKA) and 9/2026 (AN) | 2025; 9/2026 set 11 Feb, in force 12 Feb 2026 (BN 2026/98), amends Permendikbudristek 17/2021 | TKA optional for grade 6. **AN participants: murid in the final grade (SD/MI kelas VI), every pendidik, and the principal** (9/2026 Pasal 5(3)); AKM, survei karakter on the 8 dimensi profil lulusan, survei lingkungan belajar (Pasal 3); registration through the ministry database (Pasal 6); literacy and numeracy may be folded into academic-achievement measurement (Pasal 9A); guide set by the Minister, now Kepmendikdasmen 56/2026 (Pasal 12) | official (9/2026), mirror (9/2025) | `national-assessment-permendikdasmen-9-2026.json` (P4-32), `administration-features.md` §1, §6 below |
| Permendikdasmen 8/2026 (BOSP) | set 5 Feb 2026; revokes 8/2025 | RKAS in the ministry system (ARKAS in practice); report deadlines 31 Jul (Tahap I), 31 Jan (annual), 25 Jun (previous year); honor for non-ASN at most 20% (negeri) or 40% (swasta) | official PDF linked | `administration-features.md`; deadlines **(session)** |
| Permendikdasmen 14/2026 (BAN-PDM) | around 17 Jun 2026 (secondary); replaces Permendikbudristek 38/2023 | Accreditation body; instrument IA 2024 (Kepmendikbudristek 246/O/2024), guide version 2025 | secondary | `administration-features.md` §1; 246/O **(session)** |
| Permendikbudristek 22/2023 (sarpras) | in force 10 Mar 2023 | Required rooms incl. library and UKS | official PDF linked | `administration-features.md` |
| Perpusnas 4/2024 | 2024 | Standar Nasional Perpustakaan Sekolah/Madrasah (Lampiran I for SD/MI) | official PDF linked | same |
| Permendikbud 75/2016 (Komite) | 30 Dec 2016 | Komite may raise donations, not levies; no replacement found | official (setkab) | same |
| Permendagri 7/2024 | set 20 Jun 2024 | Amends Permendagri 19/2016 on regional assets (inventaris BMD for negeri schools) | official (BPK) | same |
| PermenPAN-RB 21/2024 | 23 Dec 2024 | Jabatan Fungsional Guru: performance cycle; status disputed after Supreme Court review MA 26 P/HUM/2025 | official (BPK), status unverified | same; MA review **(session)** |
| Kepmendikdasmen 271/O/2025 | 2025 | Pedoman Pengelolaan Kinerja PTK: principal is the teacher's PPK; a tim kinerja is allowed above 10 teachers | secondary (salinan read on a blog) | `teacher-supervision-instruments.md` §1 |
| Permendikdasmen 11/2026 (PIP) | 2026, date unverified | Schools propose PIP recipients in SIPINTAR from Dapodik data; madrasah use SIPMA | secondary | **(session)** P4-04 rules report |
| Permendikdasmen 2/2026 (Tata Naskah Dinas) | 2026 | Binds the ministry only, not schools; negeri schools follow their Pemda rules | secondary | **(session)** same |
| SE Sesjen Kemendikdasmen 14/2025 | 2025 | Optimising Gerakan 7 KAIH in schools (copy found was labelled "DRAFT") | secondary | `administration-features.md` |
| Permendikdasmen 23/2026 | 2026 | Listed as a source; content not summarised in the repo | secondary | `administration-features.md` Sources |
| BGN SE 5/2025 (MBG) | 2025 | Schools name 1–3 PIC teachers for free meals, reported incentive Rp100k per day; not counted as JTM (unverified) | secondary | `administration-features.md` |

Guides from BSKAP (all **official**, PDFs in `docs/sources/`, extracted to `docs/data/regulations/`, D-203):

| Guide | Finding that matters for SIGRAK | Decisions |
|---|---|---|
| Panduan Pembelajaran dan Asesmen, Edisi Revisi Ke-3, Jul 2025 | Rapor has **12 minimum components** (incl. kokurikuler description, ekstrakurikuler, absences, catatan wali kelas, tanggapan orang tua). Formative never feeds rapor, promotion, or graduation. Fase A may report without numbers. 4 KKTP approaches; ATP format is free ("Tidak ada format komponen yang ditetapkan oleh Pemerintah"). Lesson-plan frame close to the RPM, with Memahami → Mengaplikasi → Merefleksi | D-205, D-206, D-208 |
| Panduan Kokurikuler 2025 | Forms: lintas disiplin, Gerakan 7 KAIH, cara lainnya ("Projek" is not one). SD kokurikuler = 36 JP per subject per year (32 in grade 6) | D-207, D-208 |
| Panduan Pengembangan KSP, Edisi Revisi Ke-3, Jul 2025 | Schools choose "bentuk dan sistematika"; not uniform administration | D-209 |
| Pembelajaran KKA (BSKAP, Feb 2025) | KKA for SD: grades 5–6, 2 JP per week, elective, Fase C content only, as a reference not a formal CP | D-208 |
| Paparan Pembelajaran Mendalam (undated slides) | Lesson-plan template fields, required vs optional | — |

**Implication.** The standards fix outcomes, structure, minimum document content, principles, and protection, but **not the teaching method** (Standar Proses Pasal 4(2); ATP and KSP formats free; KBC indicators are examples). SIGRAK labels each default as "Wajib", "Disarankan", "Contoh", or "Bebas" and keeps rules as versioned data (D-352…D-358, [`policy-innovation-layers.md`](../drafts/policy-innovation-layers.md)). A ministry statement says 13/2025 is not a new curriculum ([metrotvnews](https://www.metrotvnews.com/read/NLMCJ6Vj-mendikdasmen-tegaskan-tidak-ada-perubahan-kurikulum), secondary).

## 3. Curriculum and pembelajaran mendalam

| Finding | Source, level | Implication | Decisions, repo |
|---|---|---|---|
| The legacy app's SD intrakurikuler JP (now the semester targets of SIGRAK-SD-2026-R1) match 13/2025 Tabel 1–5 exactly | 13/2025 pp. 15–21, official | Import as is | D-204, `dokumen-resmi-sd-mi.md` §2 |
| Bahasa Inggris and IPAS are not in the grades 1–2 structure | 13/2025 pp. 15–18, official | School-optional content, off by default, not a required rapor subject | D-204 |
| SD muatan lokal cap: 72 JP per year (64 in grade 6); Bahasa Jawa uses all of it; MI may have 72–216 | 13/2025 p. 16, p. 21; KMA 1503/2025 | Tata Boga is school-optional; cap enforced per school | D-204, `data-model.md` |
| SD kokurikuler totals: 216 / 216 / 252 / 252 / 252 / 224 JP per year (grades 1–6) | 13/2025, Panduan Kokurikuler p. 35, official | New allocation version `STRUKTUR-SD-PERMENDIKDASMEN-13-2025` | `dokumen-resmi-sd-mi.md` C3 |
| 8 dimensi profil lulusan: keimanan dan ketakwaan, kewargaan, penalaran kritis, kreativitas, kolaborasi, kemandirian, kesehatan, komunikasi | 13/2025 Pasal 17, official | Fixed list DPL1–DPL8 in RPM and kokurikuler | `content-engine.md` |
| Pembelajaran mendalam: principles berkesadaran, bermakna, menggembirakan; experiences memahami, mengaplikasi, merefleksi; four framework parts | 126/P/2025, official | RPM core steps follow the three experiences with principle tags | `content-engine.md` |
| "Deep learning" in Indonesian papers means the 2025 approach, not AI | LKPD literature scan, 2026-11-09 | Watch wording in UI and citations | `lkpd-exploration.md` §8.3 |
| Legacy curriculum files (downloaded 2026-11-08): 432 topics, 2,021 fokus, 1,372 TP, 58 allocation rows, 12 subjects (new: Seni Suara Daerah) | Legacy app public portal, owner-run; SHA-256 recorded | Converted to the SIGRAK R1 dataset on 2026-11-09 and removed; old codes in the crosswalk (D-363, D-365); import applies D-204 and D-208 | [`curriculum-source-check-2026.md`](../drafts/curriculum-source-check-2026.md), `docs/data/curriculum/README.md` |
| KSP: principal approves, format free | KSP guide p. 7, p. 10, official | Starter template that schools adapt | D-209 |

## 4. Madrasah and Kemenag

| Instrument or fact | Date | Finding | Level | Decisions, repo |
|---|---|---|---|---|
| **KMA 512/2026, Pedoman Kurikulum Berbasis Cinta** | set 31 Mar 2026, in force on setting | Replaces SK Dirjen Pendis 6077/2025 as the KBC source. Keeps Panca Cinta (cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air); adds six nilai dasar, six principles, 12 suggested methods, 10 assessment instruments, three practice levels (merintis, berkembang, mekar). KBC is not a subject; results go through Akhlak or Budi Pekerti and attitude notes. Applies to Kemenag schools **and to religion teachers in general schools, SD included** | official (scanned PDF supplied by the owner on 2026-11-09; earlier only secondary, with one page writing "KMA 521") | D-359, D-126; `docs/sources/KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf`; SD scope decided in D-368 (KBC fields off for SD by default). P4-23 re-check: sha256 matches the README record; the decree page (PDF p. 3) confirms set 31 Mar 2026, in force on setting; no official-channel copy fetched (jdih.kemenag.go.id unreachable) |
| SK Dirjen Pendis 6077/2025 (Panduan KBC) | 22 Jul 2025 | Five themes, example lessons with fields "Tema Kurikulum Berbasis Cinta" and "Materi Insersi"; all marked as examples | official | D-126, D-142, [`religious-education.md`](religious-education.md) |
| KMA 1503/2025 (amends KMA 450/2024) | 22 Sep 2025 | Madrasah curriculum with pembelajaran mendalam and KBC; MI structure and JP (Lampiran II, BAB III B, Tabel 1–5); MI may add up to 6 JP per week | official | D-038, `mi-allocation-kma-1503-2025.json` |
| SK Dirjen Pendis 9941/2025 | 28 Nov 2025 | CP for Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab | mirror (two copies with identical text; no kemenag.go.id link found) | D-035 |
| Kemenag Panduan Pembelajaran dan Asesmen madrasah (letter B-572/Dt.I.I/PP.00/11/2025) | 19 Nov 2025 | Minimum and complete lesson-plan versions; minimum Identifikasi includes DPL, Topik Panca Cinta, KBC material; self-reflection framed as *muhasabah* | mirror ([copy](https://bloggeografi.id/wp-content/uploads/2025/11/panduan-pembelajaran-dan-asesmen.pdf)) | `teacher-supervision-instruments.md` §1, §8 |
| KMA 736/2026 (madrasah workload) | 2026; revokes KMA 890/2019 | 37.5 h per week, 24–40 JTM; wali kelas **6 JTM** (SD: 2); kepala madrasah 24 JTM; MI guru kelas = 24 JTM; SKMT each semester; KMA 943/2026 amends the annex | secondary | D-212; KMA 943 **(session)** |
| Kepdirjen Pendis 944/2026 (BOS madrasah), amended by 2100/2026 | 2026 | Planning and reporting through e-RKAM | secondary | D-219 (out of scope); 2100/2026 **(session)** |
| Other Kepdirjen Pendis numbers | 2025–2026 | PMB 10041/2025; ujian madrasah 751/2026; ijazah 3576/2026; kaldik madrasah 2026/27 4860/2026 (odd semester began 13 Jul 2026) | secondary | **(session)** P4-04 rules report |
| EMIS 4.0 / EMIS GTK | 2024–2026 | Replaced SIMPATIKA from the even semester of 2024/25; teacher NRG, SKMT, SKBK, and TPG depend on it | secondary | `administration-features.md` §1 |
| RDM (Rapor Digital Madrasah) v3.1 | 2025 | About 25,139 MI (93.8%) used it in the odd semester of 2024/25; includes kokurikuler descriptions; users report grade-entry friction; **no formal mandate found** | secondary (conference paper, tirto) | D-218 (export after Asesmen) |
| PKKM (principal appraisal) madrasah | — | Still follows Kepdirjen Pendis 1111/2019; no 2025–2026 Kemenag teacher-observation instrument found | secondary / unverified | **(session)**, D-324 |
| Bahasa Inggris in MI | — | Elective until 2026/27, compulsory from 2027/28 | secondary | **(session)** |

## 5. Religious education in SD

| Source | Finding | Level | Decisions, repo |
|---|---|---|---|
| Kepka BKPDM 020/2026 (11 Jun 2026), amends Kepka BSKAP 046/H/KR/2025 | CP Pendidikan Agama dan Budi Pekerti for six religions, Fase A–F (Lampiran I); special-education CP (Lampiran II, not yet transcribed) | official (scan, no text layer) | D-034, `cp-pendidikan-agama-sd-bkpdm-020-2026.json` |
| 13/2025 structure | One "Pendidikan Agama dan Budi Pekerti" row, 3 JP per week, for every religion | official | `religious-education.md` |
| Base Kepka BSKAP 046/H/KR/2025 (16 Jul 2025; revokes 32/H/KR/2024) | CP for all subjects (1,691 pages); SD/MI in Lampiran II (pp. 23–347); Fase A = kelas I–II, B = III–IV, C = V–VI; Bahasa Inggris compulsory from Fase B; IPAS from Fase B; KKA Fase C only. Religion CP superseded by BKPDM 020/2026 | official (ministry unit copy) | `cp-decree-sd-index-kepka-bskap-046-h-kr-2025.json` (page index, P4-23); CP curation P4-29/P4-30 |

No newer amendment of SK 9941/2025 or KMA 1503/2025 was found on 2026-10-30.

## 6. Assessment: AN, TKA, AKMI, rapor, Peta Asesmen

| Finding | Source, checked | Level | Implication, decisions |
|---|---|---|---|
| TKA SD/MI: Bahasa Indonesia and Matematika, both required; item types tunggal and grup; forms PG, PGK MCMA, PGK Kategori | Perka BSKAP 047/H/AN/2025 (24 Jul 2025), 2026-10-30 | official | Paket Soal TKA template and exam site, D-026, D-027, D-030; [`kerangka-tka-sd.json`](../data/tka/kerangka-tka-sd.json) |
| 2026 duration 75 minutes per SD subject (2025: 60); score 0–100 with 2 decimals; categories Baik, Memadai, Kurang; predikat Istimewa at 95 or more | Kepmendikdasmen 56/2026 (6 Mar 2026), BAB X | official | Scoring in the practice exam |
| Category limits 50 and 76.67 | 2026 results briefing (26 May 2026) via detik, beritasatu, kumparan | secondary | Used until an official text states them |
| 30 items per subject | Pusmendik sample sets; briefing example | secondary (no regulation text) | Default, not a rule |
| From 2026, about 70% central items and 30% regional items | Kepmendikdasmen 56/2026 BAB IV–V; split from the briefing | official / secondary | Item origin note only |
| Juknis TKA-AN 017/F/AN/2026 and Tanya Jawab TKA 2025 state no item count, limits, or scoring model | OCR read 2026-10-31 | official | Confirms the gap |
| Kepmendikdasmen 95/M/2025 governed TKA 2025; superseded for 2026 | Pusmendik download page | official | Keep 2026 values |
| AN 2026: murid in the final grade (SD/MI kelas VI), every teacher and the principal take part; literacy and numeracy may be measured inside the academic-achievement test of certain subjects (Pasal 9A), the basis for running TKA and AN together | Permendikdasmen 9/2026 (12 Feb 2026), 2026-11-11 | official | No AN simulator; Simulasi TKA already targets kelas 6 (D-026); [`national-assessment-permendikdasmen-9-2026.json`](../data/regulations/national-assessment-permendikdasmen-9-2026.json) |
| AN is a system survey, not a student score | Assessment landscape review, 2026-11-09 | — | AKM-style tags and stimuli only, no AN simulator; read Rapor Pendidikan instead (D-344…D-351, [`assessment-landscape.md`](../drafts/assessment-landscape.md)) |
| Rapor minimum content and formative rules | Panduan Pembelajaran dan Asesmen 2025 | official | D-205, D-290 |
| e-Rapor SD 2025.1 adds kokurikuler assessment with dimension rubrics; reported bug with kokurikuler stuck at "belum dikerjakan"; grades re-typed from other tools | blogs (kiakrikil, imrantululi), 2026-11-07 | secondary | e-Rapor export after Asesmen (D-218); official 2026 spec still needed |
| AKMI (madrasah competency assessment) status for MI in 2026 | — | unverified | Gap, §16 |
| IRT/Rasch per school is not sound: one class has 10–30 children | Assessment landscape review | analysis | Class and school aggregates only |

## 7. LKPD and classroom studies

Literature scan on 2026-11-09 (task P4-16): Indonesian journal articles from 2025–2026 on LKPD or e-LKPD for SD/MI with a trial. About **60 primary studies and 6 reviews** were found. Abstracts were read on the journal sites; full methods sections were not. Basicedu, JISD, and Mimbar PGSD 2025 issues did not surface; SINTA ranks were not checked. About 35 studies are listed with links in [`lkpd-exploration.md`](../drafts/lkpd-exploration.md) §8.1. Decisions: D-329…D-337.

### 7.1 What the evidence supports

| Pattern or feature | Strength | Example studies (detail in §8.1 of the draft) |
|---|---|---|
| Local context (cerita daerah, etnomatematika, etnosains) | **Strongest**, many regions, two with a control class | Purwanti et al. 2026 (NG 0.68 vs 0.52 control); Wiratama et al. 2025 MI (NG 0.79, p = 0.001); Handayani et al. 2026 batik motif (NG 0.80) |
| Unplugged computational thinking | **Strong**: one controlled study plus a review | Fahrudin et al. 2026 (NG 0.63 vs 0.34); Meyrena et al. 2025 review (13 studies) |
| Learning-model presets (PBL, PjBL, RADEC, TaRL, STEM, CTL) | Good, mostly one-group | Nuwair et al. 2026 RADEC (NG 0.64 vs 0.25) |
| Deep-learning steps | Moderate, NG 0.41–0.61 | Rosyada & Rezania 2026; Isnaeni & Indrawati 2026 |
| Instant feedback (Liveworksheets-style) | Moderate; may be novelty | Inanta et al. 2026 (NG 0.78) |
| QR hybrid print | Moderate | Nugroho et al. 2025 (NG 0.76); Afifah & Hasyim 2026 review |
| Differentiated paths | Moderate, small samples | Agustina et al. 2026 TaRL (n = 16) |
| AR | Weak: validity or post-test only | Nanda et al. 2026 (Assemblr EDU) |
| Escape room, treasure hunt, board game, lapbook, kokurikuler/P5 LKPD, special-needs LKPD | **No tested study for SD found** | — |

Needs data: 70% of grade V students were bored by monotonous LKPD and 65% found non-contextual LKPD hard (Sutriono et al. 2026). Teachers asked for pictures, simple instructions, short video, instant feedback, step-by-step work, and phone access (Putri, Wiguna & Suardipa 2026). Teachers already use AI most to plan modul ajar and LKPD, with dependency and misinformation risks (Putri, Dwiani et al. 2026).

### 7.2 Additional studies from the scan, not yet in the draft table (session)

From the P4-16 research report (2026-11-09). All are **abstract-verified** unless marked.

| Study | Form, grade, n | Result | Note |
|---|---|---|---|
| Ningsih & Rigianti 2026, *CJPE* 9(2), [link](https://e-journal.my.id/cjpe/article/download/8367/5356/41407) | Numeracy, deep learning | Validity and practicality only | — |
| Istianah, Muktadir et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/59273/34938) | Gamified Think Talk Write, history narrative, V, n = 40 | 100% mastery, no control | Writing |
| Malasari et al. 2025, *Pendas* 10(3), [link](https://www.journal.unpas.ac.id/index.php/pendas/article/download/28327/17049/123537) | Wordwall LKPD, IV, n = 27 | Mastery 80–88% vs KKM 75 | Game |
| Sari & Setya 2025, *Pendas* 10(4), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/38767) | Liveworksheets math, V | NG 0.49 | Digital |
| Zajirah et al. 2026, *PENDIKDAS* 7(1), [link](https://jurnal.habi.ac.id/index.php/Pendikdas/article/download/1078/606) | Liveworksheets IPAS, V, n = 24 | NG 0.70 | Digital |
| Aulia et al. 2026, JPPSD 6(2), [link](https://ojs.unm.ac.id/jppsd/article/download/90238/37979) | Wizer.me, Pendidikan Pancasila, V | NG 0.75 | Digital |
| Dhiba et al. 2026, JPPSD 6(1), [link](https://ojs.unm.ac.id/jppsd/article/download/87289/37291) | e-LKPD "Buria", IPAS, V | NG 0.76 | Digital |
| Liana et al. 2026, Unimed JGK 10(4), [doi](https://doi.org/10.24114/jgk.v10i4.76570) | PBL (Canva + Liveworksheets), V, n = 11 | NG 0.585, p = 0.000 | Authors ask for a control group |
| Amisa et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/58809/34481) | Suku Insana (NTT), IPAS, IV | NG 0.72 | Local context |
| Nurcahyani & Puspita 2025, JPPGSD 13(7), [doi](https://doi.org/10.26740/jppgsd.v13i7.70521) | "Jelajah Surabaya", cultural diversity, V | NG 0.58–0.63 | Local context |
| Siska & Julianto 2026, JPPGSD 14(3), [doi](https://doi.org/10.26740/jppgsd.v14i3.77223) | Ethnoscience, states of matter, n = 25 | NG 0.71, p = 0.000 | Local context |
| Musli'ah et al. 2025, *Pendas* 10(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/31593) | Cilacap food ethnomathematics, IV, two schools | Responses only | Local context |
| Haryanti et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/62933) | PBL ecosystems, V, n = 21 | NG 0.75 | Model |
| Valensia et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/58215) | PBL digestion, V | NG 0.38; language validity only 0.50–0.67 | Weak validity example |
| Nugrahadiarti et al. 2026, JPPSD 6(1), [link](https://ojs.unm.ac.id/jppsd/article/view/87746) | CTL plane shapes, III, n = 20 | NG 0.77 | Model |
| Wardani & Abtokhi 2025, *Ibtidaiyyah* 4(2), [link](https://urj.uin-malang.ac.id/index.php/ijpgmi/article/view/11816) | Discovery IPAS, IV MI | NG 0.55 | MI |
| Putra et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/64298) | Digital IPAS LKPD with coding/AI (CT), V | Design only, no numbers | KKA gap |
| Fauziyah et al. 2026, *GERVASI* 10(2), [link](https://journal.upgripnk.ac.id/index.php/gervasi/article/view/11171) | Unplugged-coding LKPD workshop, 17 teachers | Teacher test 62.9 → 94.1; 5 still needed mentoring | Community service |
| Sari, Junaidah & Erfayliana 2026, *Ibtida'iy* 11(2), [link](https://journal.ummat.ac.id/index.php/ibtidaiy/article/download/43558/pdf) | Outdoor learning, V, n = 56, control | NG 0.63 vs 0.57 | Small gap over control |
| *JPPIPA* Jul 2025, [link](https://jppipa.unram.ac.id/index.php/jppipa/article/view/11419) | QR-code IPAS LKPD with discovery, V | — | search-only (site blocked) |

### 7.3 Caveats

Most studies are R&D (ADDIE or 4D) in one class of 10–30 students with no control group. Validity comes from 2–3 validators, often colleagues; practicality is a satisfaction survey (85–100%). "Effective" can mean N-gain, mastery above KKTP, or a post-test percentage. No retention studies, replications, or pooled meta-analysis were found; coverage skews to Java, Sumatra, and NTT, with nothing for Bugis, Dayak, or Papua. **Use the studies as direction, not proof**, and measure SIGRAK's own use.

## 8. Teacher supervision and performance

Checked 2026-11-08 (P4-12). Decisions D-222…D-225, D-324, D-327, D-328.

| Source | Finding | Level |
|---|---|---|
| Perdirjen GTK 7607/B.B1/HK.03/2023 and Panduan Substansi Pengelolaan Kinerja (Dec 2023) | Observation on **8 focuses** (sub-indicators of Rapor Pendidikan D1); cycle A–E with Forms A–D; observation is a **baseline, not an assessment**; Form B scale Belum dilakukan / Dilakukan tetapi belum efektif / Dilakukan dan efektif | official (in `docs/sources/`) |
| Ruang GTK 2026 | Observation runs inside Ruang GTK "berbasis indikator perilaku"; 2026 wording of the 8 focuses not found | secondary / unverified |
| BAN-PDM IA 2024 guide version 2025, Komponen 1 | Four items, 4 levels, triangulated by observation, interview, documents; judges **quality of service, not document completeness** | official ([PDF](https://ban-pdm.id/documents/download/1756890580_7aed8e14ada8b94fca13.pdf)) |
| Kepdirjen GTK 5539/B.B1/HK.03.01/2024 | Process basis for Ruang GTK performance management; synced to BKN e-Kinerja; extended to principals and pengawas in 2026 | secondary **(session)** |
| Owner samples: SD supervision instruments 2026, Kudus set (pra, observasi, pasca), SK pembagian tugas | Use pre-2025 terms (Profil Pelajar Pancasila, HOTS/4C, RPP/MA, P5); scored 0/1/2 or 1–5 | official samples in `docs/sources/` |

**Implication.** Keep the document checklist short and make the observation cycle the core, aligned to Ruang GTK Forms A–D so results double as performance evidence (D-217, D-223).

## 9. Administration and school management

Checked 2026-11-07 (P4-04). Decisions D-210…D-221. Detail: [`administration-features.md`](../drafts/administration-features.md).

| Topic | Finding | Level |
|---|---|---|
| Buku induk siswa | No current national regulation; Dapodik is the legal data system | unverified |
| Dapodik export | Students and current rombel come from Peserta Didik → Unduh → Daftar PD (Excel); KGB history is entered per PTK login; the 2026 column layout is not published; the official 2026 guide PDF was too large to read online | secondary ([`buku-induk-dapodik.md`](../drafts/buku-induk-dapodik.md), D-161) |
| Guru BK in SD | No rule requires one; in practice the guru kelas does it | secondary |
| UKS | No 2025–2026 replacement of the 2014 SKB 4 Menteri found | unverified |
| BOSP and MBG | A ban on using BOSP for MBG food was reported but is not in the text; a ban on ASN teachers as bendahara is unverified | unverified **(session)** |
| Kalender pendidikan | Set regionally (examples: Jateng 400.3/23862/2026, Semarang City 29 Jun 2026, Jabar SE 30234/PK.02.01.05/PSMA 18 Jun 2026) | secondary **(session)** |
| Minimum teacher load | One blog claims it falls to 16 JTM: likely wrong | secondary |
| Accreditation | Performance-oriented, 14 items across teacher performance, principal leadership, and school climate | official guide |

**Implication.** Table stakes are attendance with export, grade book to rapor, announcements, and student data. Nobody covers the chain CP → TP → Prota/Promes → RPM → jurnal → asesmen → remedial → rapor and kokurikuler descriptions; that chain is SIGRAK's sales story. BOS, ARKAS, e-RKAM, and SPMB stay out of scope (D-025, D-219).

## 10. IFP and classroom games

Reviewed 2026-11-04 (P4-02). Detail: [`ifp-classroom.md`](ifp-classroom.md). Decisions D-171…D-181, D-233.

| Finding | Source | Level |
|---|---|---|
| Ministry IFP programme targeted 288,000+ schools by Dec 2025; 75-inch Android all-in-one panels, 4K, multi-touch, with a laptop and content drive; one unit per school in 2025 (149,268 of the targets are SD), three per school aimed for 2026; often used as a big TV | [ANTARA 2025-10-07](https://www.antaranews.com/berita/5159673/mendikdasmen-pengiriman-papan-interaktif-ditargetkan-selesai-desember), [BBPMP Jatim](https://bbpmpjatim.kemendikdasmen.go.id/main/syarat-syarat-sekolah-penerima-ifp-dan-tipe-tipe-wilayahnya), [detikEdu 2025-10-23](https://www.detik.com/edu/sekolah/d-8174445/program-digitalisasi-pendidikan-berlanjut-dalam-5-tahun-sekolah-bakal-punya-6-ifp) | official + secondary |
| The panel's Android and WebView versions are not published | — | unverified; test on a real unit (D-179) |
| Papan Interaktif (MANDAI): free, no login, 42+ media, claims 20,000+ schools; framed as ice breaking; no TP, no results | [papaninteraktif.com](https://papaninteraktif.com/) | official (site) |
| Game DigiEd: about 70 free titles by level and subject; explanation after each answer; Kuis Kelas from an Excel template plays as three games; online only; no results kept | [game.digitaleducation.id](https://game.digitaleducation.id/) | official (site) |

**Gap SIGRAK fills:** link to TP and the RPM step, class results per item and TP, MI and six-religion packs, Bahasa Jawa, offline, TKA item forms, and a run sheet.

## 11. App benchmarks

Benchmark of 2026-11-07 (P4-04). Most vendors publish no feature matrix or price. **(session)** marks detail that is only in the research report.

### 11.1 Government platforms

| Platform | Finding | Level |
|---|---|---|
| Rumah Pendidikan (launched 21 Jan 2025) | Super-app with 8 "ruang"; a Sep 2026 report says 950+ apps integrated; an aggregator, no class workflow. Rumah Belajar became Rumah Pendidikan / Ruang Murid (no embed API) | official + secondary **(session)** |
| Ruang GTK (ex-PMM) | Training, Karir & Kinerja, Bukti Karya; since 9 Jul 2026 the PM-KKA training (pembelajaran mendalam plus coding and AI) for 300k+ teachers | official **(session)** |
| "Platform AI Guru" in PMM/Ruang GTK | One Feb 2026 article says the minister launched a generative AI assistant that drafts RPP and modul ajar; no product name, URL, or press release found. If real, a direct competitor | unverified **(session)** ([article](https://bicarainfo.com/pendidikan/peluncuran-platform-ai-guru-asisten-virtual/)) |
| e-Rapor SD, Dapodik, ARKAS, Rapor Pendidikan, SIPLah | Data of record, rapor, finance, quality indicators, procurement; each stands alone; teachers re-type grades | secondary / background |
| Kemenag: EMIS 4.0, RDM, e-RKAM | See §4. No official Kemenag AI teacher assistant found; Kemenag announced AI for public services and consolidation of about 2,800 apps (May 2026); a former Kemenag secretary-general stressed basic-device support (Oct 2026) | secondary **(session)** |

### 11.2 Commercial and AI tools

| Product | Finding | Level |
|---|---|---|
| Kelas Pintar | Learning-first; Mar 2026 update added automated assessment for teachers; Best Edutech App with AI (Selular Award, Jun 2026); claims 1M+ students | secondary |
| Gredu | School suite with teacher, parent, student apps; 2026 status unclear (CB Insights lists "Acquired", unverified) | secondary |
| SmartSchool / MySCH.id | LMS, e-Rapor, CBT, PPDB; 24-module plan "not final" in 2021; reportedly from Rp 2,000 per student per month | secondary (stale) |
| Sigma Guru (iOS) | Classes, grades, attendance, messaging, quizzes, online rapor, SPP; no AI or curriculum planning | official (store page) |
| AIO Class | Attendance with Excel, journal with photos, CBT, conduct points, savings; freemium about US$1.99 per month or US$6.99–24.99 per year per teacher; no AI. Closest analogue to a teacher's own admin app | official (store page) **(session)** prices |
| InfraDigital | SPP collection, incl. payment at Tokopedia partner shops | secondary **(session)** |
| Edura School | Claims an "Automated Curriculum Generator" for ATP, Modul Ajar, Prota, Promes; page blocked | unverified |
| GuruInovatif Modul Generator | Free AI modul ajar for 1–4 JP with LKPD, rubric, remedial; daily limit; no link to grades or journal | official (site) |
| BRIN "Asisten Guru AI Kontekstual" (Dec 2025) | Prompt library, includes "Kurikulum Cinta" and pembelajaran mendalam; research prototype | secondary (detik) |
| Generic AI (Canva, Genspark, MagicSchool, Teachy, others) | One-shot lesson-plan generators; teachers paste CP/TP themselves | secondary |

Not found or not verifiable: Sekolah.mu, Jurnal Sekolah, Educa Studio, Kipin, Schoolmedia, Mapel.id, and "Administrasi Guru SD" Android apps. Free Excel admin packs are widely shared on blogs and work offline.

### 11.3 Gaps and price points

Gaps nobody covers: planning linked to administration; pembelajaran mendalam and kokurikuler in rapor narratives; the SD guru kelas shape; MI subjects, KBC, and RDM; offline phone use; Dapodik/EMIS import and e-Rapor/RDM export; teacher-owned admin with cheap AI (bring-your-own Gemini key); evidence for supervision and Ruang GTK. Price points **(session)**: government tools free; AI generators free with caps, paid tiers about US$4–5 per month; teacher apps about US$7–25 per year; school suites estimated Rp 0.5–5 million per school per year (secondary). Pricing decisions: P5-10, [`product-concept.md`](product-concept.md).

## 12. Third-party learning tools (AR, 3D, simulations)

Vendor pages checked 2026-11-09 (P4-16). Detail: `lkpd-exploration.md` §9. Decisions D-330, D-334, D-337.

| Tool | Finding | Level |
|---|---|---|
| H5P via `h5p-standalone` (MIT) | Self-hostable player; the PHP library is GPL, so use only the standalone player | official |
| Google `<model-viewer>` (Apache-2.0) | 3D and phone AR without an app; iOS needs USDZ; Android needs Play Services for AR | official |
| PhET | Free, Indonesian translations; commercial use of CC-BY sims needs a separate licence | official; licence to confirm |
| GeoGebra | Free only for non-commercial use; a paid product needs a licence | official; licence to confirm |
| Assemblr EDU | belajar.id teachers get a free quota (10 Edu Kits a day, post of 2025-05-02); rupiah prices, embed plan, and student no-login access unverified | secondary / unverified |
| Wordwall, Wayground (ex-Quizizz) | Students join without an account; free tiers small (Wordwall Basic: 3 activities) | official |
| 8th Wall | Hosted platform closed 2026-02-28; projects deleted after 2027-02-28 | official |
| Google Expeditions | Dead since 2021 | official |
| Web NFC; `id-ID` speech voice | NFC is Chrome-on-Android only and experimental; voice depends on the device | official |
| SIBI (Buku Kemendikdasmen) | Can be linked by book and page; the SIBI licence ruled out an AI draft from Buku Guru (books are references only) | official; D-317 |

## 13. AI provider: Gemini free tier, data rules, child data

| Finding | Source, checked | Level | Decisions, repo |
|---|---|---|---|
| Rate limits are **per Google Cloud project, per model** (RPM, TPM, RPD), not per key; daily quota resets at midnight Pacific (14:00 or 15:00 WIB) | [rate limits](https://ai.google.dev/gemini-api/docs/rate-limits), 2026-10-30 | official | D-023, [`ai-provider-engine.md`](ai-provider-engine.md) |
| Default model Gemini 3.8 Flash: free tier 5 RPM, 250k TPM, 20 RPD per project | AI Studio, 2026-10-31 | official | D-117 (re-check at build time) |
| Users "will not attempt to circumvent" limits: multi-project rotation may be read as circumvention | [Gemini API terms](https://ai.google.dev/gemini-api/terms), [Google APIs Terms](https://developers.google.com/terms) | official | D-023: risk carried by the teacher, consent stored |
| Users must be 18+; no service "directed towards or likely to be accessed by" under-18s (terms effective 2026-03-23) | Gemini API terms, 2026-11-09 | official | D-124: students never trigger AI; no student chatbot |
| Unpaid tier: Google may use prompts and outputs, and human reviewers may read them; do not send sensitive or personal data | Gemini API terms | official | D-023, D-036, D-347 (names read on the device) |
| Preview and experimental models have tighter limits and are not for production | Gemini docs | official | `ai-provider-engine.md` |
| Laravel first-party AI SDK (`laravel/ai` v1.0, Sep 2026) targets one key per provider | [Laravel docs](https://laravel.com/docs/ai-sdk) | official | D-116: own thin client |
| UU 27/2022 (PDP) Art. 25: children's data needs special handling and parental consent | statute | official | Only no-login third-party targets; no student data to AI |
| PP 17/2025 ("PP Tunas"): age tiers and age checks for digital platforms, in force since Mar 2026 | news | secondary | Supports no student accounts (D-010) |
| 429 detail fields that separate per-minute from per-day limits | — | unverified | Open; v1 uses a daily counter (D-128) |

## 14. Infrastructure and hosting

Versions checked 2026-10-30; monitoring tools 2026-10-31. Detail: [`server-setup.md`](server-setup.md), [`server-monitoring.md`](server-monitoring.md).

| Finding | Level | Decisions |
|---|---|---|
| Ubuntu 22.04 loses standard support mid-2027 (right after the first TKA season); 24.04 to mid-2029 | official (ubuntu.com) | D-086 |
| Laravel 13 (13.0 on 2026-03-17; security to 2028-03-17) supports PHP 8.3–8.5; PHP 8.5 active to 2027-12-31, security to 2029-12-31; 8.5.12 fixes the Oct 2026 CVEs | official | server-setup |
| MySQL 8.4 LTS (8.4.13 on 2026-10-20), premier support to Apr 2029; Ubuntu's 8.0 is end of life | official | D-007 |
| Redis 8.2 extended release, fixes to 2030-09-01 | official | server-setup |
| `maatwebsite/excel` 3.1 blocks PHP 8.5 (PhpSpreadsheet 1.x); 4.x supports Laravel 12/13 | official (Packagist) | server-setup |
| Gotenberg `8.39.0-chromium` (2026-10-27), about 30% smaller without LibreOffice | official | D-028 |
| Node 24 LTS to 2028-04-30; Node 26 became LTS on 2026-10-28 | official | server-setup |
| IDCloudHost confirmed outbound NAT incl. TCP/UDP 7844 for a VM without a public IP | vendor (2026-10-31) | D-070, D-088 |
| Cloudflare free plan: DNS, proxy, WAF, Zero Trust Access free up to 50 users | official | D-088, D-095 |
| Healthchecks.io Hobbyist: 20 checks, Telegram and email, allowed for company infrastructure; Business US$20 per month for 100 | official | D-143 |
| UptimeRobot free: 50 monitors, 5-minute interval, commercial use allowed (terms 2026-05-26), email only (Telegram paid) | official | D-143 |
| Better Stack free: 10 monitors, 3-minute checks, no Telegram, "Free for personal projects" | official | rejected |
| No official public API for the Kemendikdasmen school reference site (search 2026-11-03) | official | D-169 |
| PDKI trademark check for "Sigrak" and backup "Kapur" (classes 9, 41, 42) | — | open ([PDKI](https://pdki-indonesia.dgip.go.id)) |

## 15. Calendar, region, and school data

| Data | Source, date | Level | Decisions, repo |
|---|---|---|---|
| School directory: Kudus 570, Jepara 772, Pati 825 SD/MI | referensi.data.kemendikdasmen.go.id, fetched 2026-11-03 | official | D-169, D-170, `docs/data/schools/` |
| Jawa Tengah kaldik 2026/2027 (Perkadin 400.3/23862/2026, 29 May 2026): SD/MI effective days 207 (5-day) and 254 (6-day; 134 + 120) | PDF supplied by the owner | official | D-323 (6-day only; Maulid on 25 Aug 2026) |
| National holidays and cuti bersama 2026/2027 (25 dates) and 2027/2028 (5 dates) | Legacy app public assets, 2026-11-09 | unverified until checked against the SKB 3 Menteri | D-253, D-254: check online with source URLs before import |
| Region calendars are set by provincial or district dinas; madrasah follow Kepdirjen Pendis 4860/2026 | P4-04 rules report | secondary | D-256, D-257 |
| Legacy catalog data (kokurikuler examples, ekstrakurikuler, class inventory, administration components, form choices) | Legacy app public assets, 2026-11-09 | draft, D-113 review; curated into `docs/data/catalogs/` (D-367) | [`usulan-01/data`](../sources/usulan-01/data/README.md), [`docs/data/catalogs/`](../data/catalogs/README.md), [`legacy-upgrade-01.md`](../drafts/legacy-upgrade-01.md) |

## 16. Open research gaps

| Gap | Why it matters | Where tracked |
|---|---|---|
| Every regulation PDF listed in `policy-innovation-layers.md` §11 is now in the repo (P4-23, P4-32, 2026-11-11). Mirror copies to swap for JDIH files: Permendikdasmen 26/2025, Permendikbudristek 21/2022, Kepmendikdasmen 17/2026. The base Permendikbudristek 17/2021 (AN), amended by 9/2026, is not in the repo | Article numbers read on a mirror need a check against the JDIH file; 17/2021 matters only if SIGRAK quotes its unamended AN articles | P4-23, P4-32 follow-up |
| Bahasa Inggris in SD/MI for 2026/2027: elective under 12/2024 Pasal 33 (not amended), but listed as required in kelas III–VI in the 13/2025 tables | Labels in the 2026/2027 structure rule set | Tim Validasi, [`policy-innovation-layers.md`](../drafts/policy-innovation-layers.md) §6.3 |
| TKA category limits, item count, and PGK partial credit in an official text | Scoring of the practice exam | `kerangka-tka-sd.json` |
| AKMI for MI in 2026; official e-Rapor SD and RDM 2026 import specs | Exports (D-218) | P4-10, `policy-innovation-layers.md` §11 |
| Who sets the KKTP at article level (21/2022 only says the teacher picks or develops instruments, Pasal 5, and compares against kriteria ketercapaian tujuan pembelajaran, Pasal 9(8); the rest is in the BSKAP guide) | Asesmen rules | same |
| 2026 Ruang GTK wording of the 8 observation focuses and behaviour rubrics; any Kemenag 2025–2026 PKG instrument (wali kelas duties: answered in §2; in SD the guru kelas under 6/2026, "Guru Kelas atau Wali Kelas" in the guide 17/2026) | Supervision instruments | P4-08 |
| Dapodik 2026 export column layout (anonymised samples from the owner) | Buku Induk import | `buku-induk-dapodik.md` |
| SKB 3 Menteri holiday dates and the 2027 SKB number | Effective days | D-254 |
| Kemendikdasmen IFP Android and WebView versions | Game kelas | D-179 |
| "Platform AI Guru" (ministry) existence; Edura School claims | Competition | §11 |
| PhET and GeoGebra commercial licences; Assemblr EDU prices and no-login access | LKPD embeds (D-337) | `lkpd-exploration.md` §9 |
| Gemini 429 detail fields | Key rotation | `ai-provider-engine.md` |
| Legal retention periods for rapor, ijazah data, supervision results | Console retention settings (D-318) | P4-13 S6 open question |
| PKKS sample from Kudus pengawas | PKKS instrument match (D-324) | owner |
| Field evidence: escape-room, board-game, lapbook, kokurikuler, and special-needs LKPD; retention or replication studies | LKPD patterns rest on practice, not trials | `lkpd-exploration.md` §8.2 |
| PDKI trademark check | Brand | `product-concept.md` |

## 17. Where the raw research lives

| Session (2026-11-07 to 2026-11-09) | Research it holds | In the repo |
|---|---|---|
| P4-03: SD/MI data from dokumen resmi | Extraction of the eight official PDFs | Fully, in `docs/data/regulations/` and `dokumen-resmi-sd-mi.md` |
| P4-04: Research class and school admin features | Rules report (2025–2026, SD and MI), app benchmark, supervision report | Mostly; the rest is marked **(session)** above |
| Eksplorasi konsep LKPD untuk Sigrak | Journal report (about 60 studies) and vendor scan | Top 35 in `lkpd-exploration.md`; the rest in §7.2 above |
| P4-13 S1…S6, Review proposal coverage | Owner interviews and decisions on the legacy app comparison (no web research) | D-252…D-328, `legacy-upgrade-01.md`, [`legacy-coordination.md`](../drafts/legacy-coordination.md), [`legacy-overlap-adjustments.md`](../drafts/legacy-overlap-adjustments.md) |
| Sigrak positioning dan kebijakan pendidikan | 2025–2026 standards check | `policy-innovation-layers.md` |
| P4-23: Get and extract missing regulations (2026-11-10/11) | Download of six regulation PDFs, SD/MI extracts, KMA 512/2026 re-check | Fully: `docs/sources/`, `docs/data/regulations/`, `docs/agent/tasks/P4-23-missing-regulations.md` |
| P4-32: Add the four remaining regulations (2026-11-11) | Owner-supplied PDFs of Permendikdasmen 1/2026 and 9/2026, Kepmendikdasmen 17/2026, base Permendikbudristek 12/2024; SD/MI extracts | Fully: `docs/sources/`, `docs/data/regulations/`, `docs/agent/tasks/P4-32-remaining-regulations.md` |

Earlier research (Oct 2026) is in the final docs themselves: TKA framework (`content-engine.md`, `kerangka-tka-sd.json`), religion sources (`religious-education.md`), Gemini terms (`ai-provider-engine.md`), and server versions (`server-setup.md`, `server-monitoring.md`).
