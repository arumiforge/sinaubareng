# Official documents 2025: what changes for SIGRAK (SD/MI)

- **Status:** draft (P4-03). The owner answered the open questions on 2026-11-08 (D-204…D-209, §12). The `docs/final/` updates in §10 were applied in P4-03b.
- **Data:** [`docs/data/regulations/`](../data/regulations/README.md), eight JSON files, all `status: draft` (D-113, D-203).
- **Sources:** the eight PDFs, received in `docs/dokumen-resmi/` and moved unchanged to `docs/sources/` under descriptive names (D-203, §11).

Page references are PDF pages of each source file. "13/2025" = Permendikdasmen 13/2025; "PA" = Panduan Pembelajaran dan Asesmen; "PK" = Panduan Kokurikuler; "KSP" = Panduan Pengembangan KSP; "126" and "127" = the two Kepmendikdasmen; "slides" = Paparan Pembelajaran Mendalam.

## 1. Summary

1. **SD JP: the intrakurikuler numbers in KM-SD-2026-R2 match 13/2025 exactly** (Tabel 1–5, pp. 15–21). Three things differ: Bahasa Inggris in grades 1–2, Tata Boga, and a missing kokurikuler allocation (§2).
2. **Kokurikuler is a fixed slice of each SD subject:** 36 JP per subject per year (32 in grade 6). That gives 216 / 216 / 252 / 252 / 252 / 224 JP per year for grades 1–6. SIGRAK's SD data holds no kokurikuler allocation (§2.3).
3. **Eight dimensi profil lulusan** are now in the regulation (13/2025 Pasal 17). The RPM and kokurikuler templates should use this list as a fixed set (§3).
4. **The official lesson-plan frame** (PA pp. 35–39, slides pp. 37–39) is close to SIGRAK's RPM. It differs in *Pengalaman Belajar*, which is organised as Memahami → Mengaplikasi → Merefleksi with principle tags, and in a few fields (§4).
5. **Kokurikuler:** the official forms are lintas disiplin, Gerakan 7 KAIH, and *cara lainnya*; "Projek" is not one of them. The official plan template has fields that SIGRAK's kokurikuler schema lacks (§5).
6. **Assessment and rapor rules** affect Asesmen > Rapor and Bank Soal (§6). Formative results stay out of report grades. Fase A has no written tests. The rapor must carry a kokurikuler description and ekstrakurikuler, and the competency description names the highest and lowest achievement.
7. **KKA (Koding dan Kecerdasan Artifisial):** an elective in grades 5–6 only, 2 JP per week. It has content for Fase C only, and that content is a reference, not a formal CP (§7). SIGRAK runs it through kokurikuler (D-208).
8. **KSP:** the principal approves it and the format is free. SIGRAK offers a starter template that schools adapt (D-209, §8).

## 2. Curriculum structure and JP (13/2025, Lampiran II B)

### 2.1 Matches

Each figure is intrakurikuler JP per year from 13/2025, compared with KM-SD-2026-R2 S1+S2 (Pemetaan doc, `docs/sources/`). Weeks: 36 per year in grades 1–5, 32 in grade 6. 1 JP = 35 minutes.

| Subject | 13/2025 (grades 1 / 2 / 3–5 / 6) | KM-SD-2026-R2 | |
|---|---|---|---|
| Pendidikan Agama dan Budi Pekerti | 108 / 108 / 108 / 96 | 54+54, grade 6 54+42 | match |
| Pendidikan Pancasila | 144 / 144 / 144 / 128 | 72+72, grade 6 72+56 | match |
| Bahasa Indonesia | 252 / 288 / 216 / 192 | 126+126, 144+144, 108+108, 108+84 | match |
| Matematika | 144 / 180 / 180 / 160 | 72+72, 90+90 …, 90+70 | match, except the known grade 4 S2 gap (80 of 90) |
| IPAS (from grade 3) | — / — / 180 / 160 | 90+90, 90+70 | match |
| PJOK, Seni dan Budaya | 108 / 108 / 108 / 96 | 54+54, 54+42 | match |
| Bahasa Inggris (from grade 3) | — / — / 72 / 64 | 36+36, 36+28 | match for grades 3–6 |
| Muatan lokal (max) | 72 / 72 / 72 / 64 | Bahasa Jawa 36+36, 36+28 | match (at the cap) |
| Koding dan KA (elective, grades 5–6) | 72 / 64 | 36+36, 36+28 | match |

This confirms the D-141 assumption (`religious-education.md`): one religion row per structure, 3 JP per week, whatever the religion. The six religions share one merged cell in Tabel 1–5.

### 2.2 Conflicts

| # | Finding | Source | Affects | Proposal |
|---|---|---|---|---|
| C1 | **Bahasa Inggris grades 1–2 is not in the structure.** 13/2025 has Bahasa Inggris only from grade 3, with no kokurikuler. KM-SD-2026-R2 has 36+36 JP of "kokurikuler" Bahasa Inggris in grades 1–2, and `data-model.md` (Known data issues) says it "must stay cocurricular". | 13/2025 pp. 15–18 | `data-model.md`, KM-SD import | Under 13/2025, kokurikuler is a school-wide theme, not a subject. **Decided (D-204):** keep the grade 1–2 topics as content a school can switch on, off by default. The same applies to IPAS in grades 1–2 (not in the structure either; no KM-SD topics yet). Neither is a required rapor subject |
| C2 | **Muatan lokal cap is 72 JP per year (64 in grade 6)** for SD. Bahasa Jawa already uses all 72. Keterampilan Tata Boga (34+34 in KM-SD) has no room as separate muatan lokal. MI is different: 72–216 JP (KMA 1503/2025). | 13/2025 p. 16 (note c), p. 21 | `data-model.md` (Tata Boga issue), `region_subjects` | Answers the open "Tata Boga target" issue. Under 13/2025 Tata Boga can only be integrated into another subject or a kokurikuler theme, or replace Bahasa Jawa. **Decided (D-204):** Tata Boga is a school-optional muatan lokal. The SD cap of 72 JP (64 in grade 6) is enforced per school |
| C3 | **Kokurikuler allocation missing for SD.** SD gets 36 JP per subject per year (32 in grade 6), taken out of each compulsory subject except Bahasa Inggris. Totals: 216 / 216 / 252 / 252 / 252 / 224. | 13/2025 pp. 15–21; PK p. 35 | `subject_allocations` (`learning_type = cocurricular`), the kokurikuler module, the scheduler | Import the 13/2025 table as a new allocation version, e.g. `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, holding intra and kokurikuler JP per subject per grade, like `ALOKASI-MI-KMA-1503-2025`. RPM scheduling keeps counting intrakurikuler JP only. MI already works this way (D-146) |
| C4 | **MI kokurikuler differs from SD.** KMA 1503/2025 moves JP to kokurikuler only for some subjects per grade. 13/2025 puts SD and MI in one table title, but MI follows KMA 1503 (D-038). | 13/2025 p. 15; `mi-allocation-kma-1503-2025.json` | `religious-education.md` | No change. Note in the final doc that MI keeps the KMA table even though 13/2025's tables mention MI |
| C5 | **Minor:** the decree sets no general rule for SD to add JP (MI may add up to 6 JP per week). Tabel 1's last total row is labelled "Umum + Muatan Lokal"; the other tables say "Wajib + Muatan Lokal". | 13/2025 pp. 15–22 | — | None. The SD scheduler should not offer extra JP |

### 2.3 Other structure rules that bind SD

- In force on 15 July 2025. SD/MI may phase in from grades 1 and 4, or switch every grade at once (Pasal 32, p. 5).
- Seni: the school offers at least one of musik, rupa, teater, tari, and each student picks one (p. 16). This matches MI.
- Ekstrakurikuler is required, with kepramukaan at minimum. Its assessment is qualitative and goes in the rapor (Pasal 22; Lampiran III p. 74).
- Inclusive SD adds Program Kebutuhan Khusus. Gifted students get acceleration or enrichment (pp. 21–22).
- SDLB/MILB tables (1 JP = 30 minutes) are in the data file for completeness. SIGRAK targets regular SD/MI. Tabel 20 has a likely misprint (54 vs 64), recorded in `open_points`.

## 3. Dimensi profil lulusan

13/2025 Pasal 17, 126, PK, PA, and KSP all use the same eight dimensions: **keimanan dan ketakwaan terhadap Tuhan Yang Maha Esa, kewargaan, penalaran kritis, kreativitas, kolaborasi, kemandirian, kesehatan, komunikasi.** The descriptions are on slides pp. 14–15.

- `content-engine.md` (RPM, *Identifikasi*: "profile dimensions") does not name the set. **Update:** list the eight dimensions as an enum in the RPM and kokurikuler schemas, keep the codes stable (DPL1–DPL8 in PA's order, p. 13), and let AI pick from the set only.
- The final docs never mention the old Profil Pelajar Pancasila or P5, so nothing needs removing. KSP p. 122 and slides still use some old wording. Do not copy it into UI text.

## 4. RPM (lesson plan) vs the official frame

The official frame (PA pp. 35, 38–39; slides pp. 37–39; SD example PA pp. 112–114) has four components: **Identifikasi** (asesmen awal, optional; peserta didik and materi, optional; Dimensi Profil Lulusan, checkboxes), **Desain Pembelajaran** (CP, lintas disiplin, and topik, all optional; Tujuan Pembelajaran; Praktik Pedagogis; Kemitraan, optional; Lingkungan Pembelajaran; Pemanfaatan Digital, optional), **Pengalaman Belajar** (AWAL, optional; INTI: Memahami, Mengaplikasi, Merefleksi; PENUTUP, optional; each step tagged berkesadaran / bermakna / menggembirakan; INTI may span several meetings), and **Asesmen** (asesmen awal, proses, and akhir: as, for, and of learning).

Every teacher must have a lesson plan (RPP or modul ajar) that shows the framework explicitly or implicitly (PA p. 40). The format is free.

| # | SIGRAK today (`content-engine.md` RPM) | Official | Proposal |
|---|---|---|---|
| R1 | Per meeting: opening, core activities following the model's syntax, closing | Memahami → Mengaplikasi → Merefleksi, each tagged with principles | Keep the per-meeting layout, but split the core into the three experiences and add principle tags. A single meeting may cover only part of the INTI. The rule "every TP appears in a meeting or an indicator" stays |
| R2 | *Desain:* method, syntax, learning partners, physical space, learning culture, digital use, media | Praktik pedagogis, kemitraan, lingkungan (physical, virtual, culture), pemanfaatan digital | Mostly matches. Rename the labels to the official terms in `lang/id` (Praktik Pedagogis, Kemitraan Pembelajaran, Lingkungan Pembelajaran, Pemanfaatan Digital) |
| R3 | Identity: class, phase, subject, elements, CP, TP | Adds optional *lintas disiplin* and *topik* | Optional fields: "Lintas disiplin" (related subjects), filled by AI or left empty |
| R4 | *Asesmen:* formative assessment, 4-level rubric | Assessment at the start, during, and at the end; formative must stay out of rapor grades (PA p. 44) | Label the RPM's assessment as formative. Asesmen > Nilai must not mix it into report grades (§6) |
| R5 | Knowledge types are not modelled | Memahami covers pengetahuan esensial, aplikatif, nilai dan karakter (slides pp. 18–20) | Optional prompt hint only. No schema change needed |

## 5. Kokurikuler module vs Panduan Kokurikuler

| # | Finding | Source | Proposal |
|---|---|---|---|
| K1 | Forms are **pembelajaran kolaboratif lintas disiplin, Gerakan 7 KAIH, cara lainnya**, and they can be combined. SIGRAK lists "Projek" as a third type (`content-engine.md`, `product-concept.md`). | PK pp. 11, 28–34; 13/2025 p. 4 | Rename the type "Projek" to "Cara lainnya", with projects as one example; allow combinations |
| K2 | The official plan has a header (school, class, theme, time allocation, location) and sections A Dimensi, B Tujuan, C Praktik Pedagogis, D Lingkungan, E Kemitraan, F Pemanfaatan Digital, G Kegiatan (memahami, mengaplikasi, merefleksi), and H Asesmen (formative, plus summative with SB/B/C/K rubric). | PK pp. 38–41, 45–52 | Rework the kokurikuler output schema around these sections. Keep "yearly phases" and "monitoring" only in the school's annual plan (`school_cocurricular_plans`), not in each activity plan |
| K3 | Planning order: the principal forms a team → school analysis → dimension → theme → form → objectives → JP → activities → assessment. The principal approves an annual plan, then teachers write one plan per activity. | PK pp. 22–46 | Matches "reads the school's kokurikuler plan". The school plan needs: dimensions, theme, form, related subjects, JP, and approval by the principal |
| K4 | Rapor has a **kokurikuler column with a description only**, one per semester, naming the dimensions reached and those still developing. | PK pp. 40–41, 62; PA pp. 70–71 | Add a per-student per-semester kokurikuler description to Asesmen > Rapor, with AI draft and teacher edit, like `report_descriptions` |
| K5 | The 7th habit is "Tidur Cepat" in PK p. 30 but "tidur tepat waktu" in Tabel 11 (p. 49). | PK | **Decided (D-207):** label "Tidur Cepat"; "tidur tepat waktu" is only its explanation |
| K6 | A kokurikuler coordinator gets 2 extra hours per class group, up to 3 groups. | PK p. 37 | Informational only (staff workload is outside v1) |

## 6. Assessment and rapor (Panduan Pembelajaran dan Asesmen)

| # | Rule | Source | Affects | Proposal |
|---|---|---|---|---|
| A1 | **Formative results never go into rapor grades, promotion, or graduation**, and are not mixed with summative results. The end-of-semester summative is optional. | PA pp. 44, 46, 54, 60 | `assessments.kind`, Asesmen > Nilai, leger | Report grades come from summative kinds only (`summative`, `sts`, `sas`). Formative scores feed the per-TP analysis and the description draft (D-205) |
| A2 | **Fase A: no written tests** for formative assessment; tests are "not recommended" at all. SPMB may not use a calistung test. | PA pp. 21, 45, 56 | Bank Soal wizard for grades 1–2 | **Decided (D-206):** written items stay available for grades 1–2 as an option and are never blocked. The wizard may suggest observation/performance instruments |
| A3 | **KKTP: four approaches** (deskripsi kriteria, rubrik, interval nilai, persentase). Criteria must be sentences, not only a number. Pass marks in the examples vary (56, 75), so they are not fixed. | PA pp. 48–53, 63–64 | `assessments.kktp` | Store the approach and the criteria text, not only a number. Thresholds are configurable per school |
| A4 | Achievement is judged per TP (unmet TPs marked *). Score options: weighting or percentage for TPs that build on each other, plain average for independent TPs. | PA pp. 63–66 | Analisis per TP | Matches the per-TP design. Add the choice of processing method |
| A5 | **Rapor minimum, 12 items:** identity, school, class, semester, subject, grade, competency description, kokurikuler description, ekstrakurikuler, attendance (sakit/izin/tanpa keterangan), homeroom note, parent response. The description names the highest and lowest achievement. | PA pp. 70–73 | `report_descriptions`, Rapor export | Add the homeroom note, the parent response field (printed blank), attendance totals from `attendance_records`, and the kokurikuler description. **Decided (D-205):** the description is drafted from the highest and lowest TP achievement in the formative results. Formative scores never appear as rapor grades |
| A6 | **Ekstrakurikuler belongs in the rapor**, and kepramukaan is mandatory. | 13/2025 Pasal 22; PA p. 73 | D-161 (ekstrakurikuler records not in v1) | **Decided (D-205, narrows D-161):** the v1 rapor shows ekstrakurikuler per student (activity + qualitative description). Fuller records and the Dapodik sync stay out of v1 |
| A7 | **Fase A rapor may have no numeric grades** (school choice). | PA pp. 74, 142–145 | Rapor | A per-school setting for grades 1–2: description only |
| A8 | The school sets promotion. Holding a pupil back needs a teachers' meeting. Graduation uses grades 5–6 achievement, a school exam, and attendance. | PA pp. 74–78 | Kenaikan, Bank Soal "Ujian Sekolah" | Matches. TKA is not mentioned in the guide |
| A9 | Report at least at each semester end, and also through e-rapor/Dapodik. | PA pp. 73–74 | D-144 | No change. The e-Rapor format stays deferred |

The RPM rubric labels (sangat baik, baik, berkembang, perlu bimbingan) are compatible with the guide's four-level rubrics. PK uses SB/B/C/K.

## 7. Koding dan Kecerdasan Artifisial (KKA)

- **Status:** elective intrakurikuler. It can also be integrated into other subjects, kokurikuler, or ekstrakurikuler (127 pp. 6, 15). Applies from school year 2025/2026.
- **SD:** the regulation allows it in grades 5–6 only, 2 JP per week, 72/64 JP per year (13/2025 Pasal 32A; BSKAP KKA pp. 12, 65–69).
- **Decided (D-208):** in SIGRAK, KKA runs through the **kokurikuler** programme, one of the options in 127/P/2025, not as an intrakurikuler subject with its own allocation. The Koding dan KA rows in KM-SD-2026-R2 (grades 5–6) and the "option in grades 5–6" line in `religious-education.md` need an update when the final docs change. The Fase C content below then feeds kokurikuler plans.
- **Content:** Fase C has four elements: Berpikir Komputasional, Literasi Digital, Literasi dan Etika KA, Pemanfaatan dan Pengembangan KA (BSKAP KKA p. 70). Algoritma Pemrograman and Analisis Data are for later phases. **There is no CP decree yet.** Tabel 6 is a minimum reference for a CP that BSKAP will still issue. Cross-check the Koding topics in KM-SD-2026-R2 against Tabel 6 before import, and expect a CP update later.
- **Teaching:** game-based and unplugged approaches are recommended for SD. A homeroom teacher with interest may teach it (BSKAP KKA pp. 80, 87). `default_teacher` stays `class_teacher` unless the owner wants KKA as a guru mapel subject (D-032).
- **Internal inconsistencies** (in `open_points`): Tabel 4 vs Tabel 6, and p. 44 (AI from SMP) vs the Fase C AI outcomes; the integration subjects differ between 127 and the BSKAP paper.

## 8. Kurikulum Satuan Pendidikan (KSP)

- The **principal approves** the KSP. The pengawas reviews it against the principles and minimum components and mentors the school. There is no dinas approval (KSP pp. 13, 16). This fits the guru → kepala sekolah → pengawas flow (D-019).
- **Free format.** The guide does not ask for uniform administration (KSP pp. 7, 10). **Decided (D-209):** SIGRAK provides a starter KSP template that follows the guide's components and five-step cycle, and each school adapts it. Nothing in it is mandatory.
- Five-step cycle: karakteristik → visi-misi-tujuan → pengorganisasian pembelajaran → rencana pembelajaran → evaluasi/pengembangan profesional/pendampingan. Steps 1–2 every 4–5 years, steps 3–5 every semester or year (KSP pp. 13–19).
- The school-level plan needs ATP, an assessment overview, and kokurikuler themes. Class level needs RPP/modul ajar, with only a few samples attached (KSP pp. 19, 47).
- SD organises learning by subject or thematically. PAI, Bahasa Indonesia, and Pendidikan Pancasila may not be merged under another name (KSP pp. 34, 41).
- The Lampiran 10 readiness self-check (pp. 101–124) and the Rapor Pendidikan "benahi" example (pp. 64–66) could become a later feature for principals. Not proposed for v1.

## 9. Deep learning (126 and slides)

- The principles (berkesadaran, bermakna, menggembirakan; *memuliakan* above them), experiences, and framework are covered in §4. `religious-education.md` already mentions "deep learning (mindful, meaningful, joyful)" for KBC, which is consistent.
- 126 explicitly endorses "integrasi kecerdasan artifisial" (p. 14). The slides list AI use for planning, delivery, and assessment (p. 31). This supports the product's AI positioning.
- Neither 126 nor 127 mentions madrasah or Kemenag. MI keeps following KMA 1503/2025 and KBC. Deep learning is consistent with KBC.

## 10. Final-doc updates

**Applied in P4-03b (2026-11-08)** to the four final docs below and their Indonesian PDFs. The KSP starter template is placed in Kepala Sekolah portal > Program Sekolah.

| Final doc | Update |
|---|---|
| `data-model.md` | New allocation version from 13/2025 with SD kokurikuler JP (C3). Bahasa Inggris and IPAS grades 1–2 as school-switchable content, not required in the rapor, and Tata Boga as school-optional muatan lokal (D-204). Enforce the SD muatan lokal cap. Koding dan KA out of the intrakurikuler allocation (D-208). `assessments`: store the KKTP approach and criteria text (A3). Rapor: kokurikuler description, homeroom note, ekstrakurikuler row, Fase A description-only setting (A5–A7, K4) |
| `content-engine.md` | RPM: eight dimensions as an enum, INTI split into Memahami/Mengaplikasi/Merefleksi with principle tags, official labels, optional Lintas disiplin (R1–R4). Kokurikuler: type "Cara lainnya" instead of "Projek", plan schema per PK (K1–K3). Bank Soal: written items optional for grades 1–2 (D-206). 7 KAIH label "Tidur Cepat" (D-207). KSP starter template (D-209) |
| `product-concept.md` | Kokurikuler types (K1), KKA via kokurikuler (D-208); Rapor content incl. ekstrakurikuler (A5, A6, D-205); KSP starter template (D-209) |
| `religious-education.md` | D-141 JP assumption confirmed by 13/2025. MI keeps KMA 1503 even though 13/2025 tables name MI (C4). Koding dan KA line follows D-208 |

## 11. Where the PDFs live

**Done (D-203).** The eight PDFs were moved unchanged from `docs/dokumen-resmi/` to `docs/sources/` (SHA-256 values unchanged) and listed in `docs/sources/README.md`:

| Received as | Now |
|---|---|
| `1752637369_manage_file.pdf` | `Permendikdasmen_No_13_Tahun_2025_Perubahan_Permendikbudristek_12_2024_Kurikulum.pdf` |
| `1758017373_manage_file.pdf` | `Kepmendikdasmen_No_126_P_2025_Pedoman_Implementasi_Pembelajaran_Mendalam.pdf` |
| `1758017435_manage_file.pdf` | `Kepmendikdasmen_No_127_P_2025_Pedoman_Implementasi_Koding_dan_Kecerdasan_Artifisial.pdf` |
| `kokurikuler.pdf` | `Panduan_Kokurikuler_BSKAP_2025.pdf` |
| `kurikulum-satuan-pendidikan.pdf` | `Panduan_Pengembangan_Kurikulum_Satuan_Pendidikan_Revisi_2025.pdf` |
| `paparan-pembelajaran-mendalam.pdf` | `Paparan_Pembelajaran_Mendalam_Kemendikdasmen.pdf` |
| `pembelajaran-dan-asesmen.pdf` | `Panduan_Pembelajaran_dan_Asesmen_Revisi_2025.pdf` |
| `pembelajaran-kka.pdf` | `Pembelajaran_Koding_dan_Kecerdasan_Artifisial_BSKAP_2025.pdf` |

## 12. Owner answers (2026-11-08)

1. Bahasa Inggris grades 1–2: kept as content a school can switch on. IPAS grades 1–2 is optional in the same way, and neither has to be graded in the rapor (D-204).
2. Tata Boga: school-optional muatan lokal (D-204).
3. Ekstrakurikuler: shown in the v1 rapor (D-205).
4. Written tests in grades 1–2: optional, not removed (D-206).
5. 7 KAIH label: "Tidur Cepat"; "tidur tepat waktu" explains it (D-207).
6. PDFs: moved to `docs/sources/` with descriptive names (D-203, §11).
7. KKA runs through kokurikuler, including the grade 5–6 Koding dan KA rows in KM-SD-2026-R2 (confirmed). Formative scores are not shown in the rapor, but the rapor description uses the highest and lowest formative achievement (D-208, D-205).
8. KSP: free format, with a starter template each school adapts (D-209).

The `docs/final/` updates in §10 were applied in P4-03b.
