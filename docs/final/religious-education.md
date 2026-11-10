# Religious Education Curriculum (SD and MI)

- **Status:** final (approved 2026-10-31, D-123)
- **Task:** P1-07
- **Last updated:** 2026-11-09
- **Sources:** [`KMA_No_1503_Tahun_2025_….pdf`](../sources/KMA_No_1503_Tahun_2025_Pedoman_Implementasi_Kurikulum_Madrasah.pdf), [`Salinan_Kepka_BKPDM_No._020_Tahun_2026_….pdf`](../sources/Salinan_Kepka_BKPDM_No._020_Tahun_2026_tentang_Perubahan_atas_Keputusan_Kepala_BSKAP_Kemendikdasmen_No._046_H_KR_2025.pdf), [`SK_Dirjen_Pendis_No_9941_Tahun_2025_….pdf`](../sources/SK_Dirjen_Pendis_No_9941_Tahun_2025_CP_PAI_dan_Bahasa_Arab_pada_Madrasah.pdf), [`SK_Dirjen_Pendis_No_6077_Tahun_2025_….pdf`](../sources/SK_Dirjen_Pendis_No_6077_Tahun_2025_Panduan_Kurikulum_Berbasis_Cinta.pdf)

## Purpose

This document defines how SIGRAK covers religious education in SD (six religions) and in MI (the Ministry of Religious Affairs' PAI subjects and Bahasa Arab). It lists the official sources, what data exists, what the team must still build, and how the data model handles religion. Decisions: D-034, D-035, D-036, D-037, D-038.

## Official sources

| Source | Covers | Form | Used for |
|---|---|---|---|
| **Keputusan Kepala BKPDM No. 020 Tahun 2026** (11 June 2026), amending Kepka BSKAP 046/H/KR/2025 | Lampiran I: CP Pendidikan Agama dan Budi Pekerti for Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu, Fase A–F. Lampiran II: special education (SLB) versions | Scanned copy, no text layer | SD religion CP (D-034). Lampiran II: special-education CP, in scope for inclusive schools (D-155), not yet transcribed |
| **SK Dirjen Pendis No. 9941 Tahun 2025** (28 November 2025) | CP for Al-Qur'an Hadis, Akidah Akhlak, Fikih, Sejarah Kebudayaan Islam, Bahasa Arab at MI/MTs/MA, plus MAPK subjects and RA | PDF with text layer, plus the Dirjen's cover letter B-606 (1 December 2025, BSrE e-signed) and the Kanwil DKI Jakarta cover letter (16 December 2025) | MI CP (D-035) |
| **KMA 450 Tahun 2024 as amended by KMA 1503 Tahun 2025** | Madrasah curriculum guideline, including the MI subject structure and JP (Lampiran II, BAB III B, Tabel 1–5, pp. 28–31) | Official decree (22 September 2025), scanned copy supplied by the owner | MI allocation (D-038), data in [`mi-allocation-kma-1503-2025.json`](../data/curriculum/mi-allocation-kma-1503-2025.json) |
| **KMA No. 512 Tahun 2026** (31 March 2026, in force on that date) | Pedoman Kurikulum Berbasis Cinta: KBC as value education for Kemenag schools *and* religion teachers in general schools, SD included; Panca Cinta, six nilai dasar, six principles, 12 suggested methods, 10 assessment instruments, levels of practice | Official decree, scanned PDF supplied by the owner (2026-11-09) | KBC source since D-359, see [MI: Kurikulum Berbasis Cinta](#mi-kurikulum-berbasis-cinta-kbc) |
| **SK Dirjen Pendis No. 6077 Tahun 2025** (22 July 2025) | Panduan Kurikulum Berbasis Cinta (KBC) for all madrasah: five Panca Cinta themes, example lessons, example CP-to-theme mapping | PDF with text layer, supplied by the owner | Replaced as the KBC source by KMA 512/2026 (D-359); still the reference for its example lessons and CP-to-theme mapping |
| **SIGRAK-SD-2026-R1** (`sigrak-sd-2026-r1-umum.json`, converted from the legacy app curriculum data, D-363) | PAI SD with topics, TP, ATP, fokus, JP | JSON | Already references "CP PAI 020/2026", the same decree |

No newer amendment of SK 9941/2025 or KMA 1503/2025 was found on 2026-10-30.

**Provenance.** The BKPDM 020/2026 copy came from the owner. The SK 9941/2025 file was downloaded from a public mirror, because no direct kemenag.go.id link was found. It contains the official cover letters and decree text. A second copy from another source (a madrasah website, supplied by the owner on 2026-10-31) has the same text on all 106 pages, so the text is very likely authentic; it lacks only the Kanwil DKI cover page. Replace it with a copy from an official channel (kemenag.go.id, pendis.kemenag.go.id, or a Kanwil/Kankemenag site) when one is available. Low priority.

## SD: six religion subjects (D-034)

All six ship in v1. Each religion is a separate subject; students take the one matching their recorded religion.

| Religion | Subject | Elements (sub-elements) | SD CP pages | Data status |
|---|---|---|---|---|
| Islam | Pendidikan Agama Islam dan Budi Pekerti | Al-Qur'an Hadis, Akidah, Akhlak, Fikih, Sejarah Peradaban Islam | 6–8 | **Complete** in SIGRAK-SD-2026-R1 (topics, TP, fokus, JP; curation status `converted`, D-364). Cross-check its CP text against the decree |
| Kristen | Pendidikan Agama Kristen dan Budi Pekerti | Allah Berkarya (Pencipta, Pemelihara, Penyelamat, Pembaru); Manusia dan Nilai-nilai Kristiani (Hakikat Manusia, Nilai-nilai Kristiani); Gereja dan Masyarakat Majemuk (Tugas Panggilan Gereja, Masyarakat Majemuk); Alam dan Lingkungan Hidup (Alam Ciptaan Allah, Tanggung Jawab Manusia terhadap Alam) | 12–14 | CP only |
| Katolik | Pendidikan Agama Katolik dan Budi Pekerti | Pribadi murid, Yesus Kristus, Gereja, Masyarakat | 20–23 | CP only |
| Hindu | Pendidikan Agama Hindu dan Budi Pekerti | Kitab Suci Weda, Śraddhā dan Bhākti, Susila, Acara, Sejarah Agama Hindu | 29 | CP only |
| Buddha | Pendidikan Agama Buddha dan Budi Pekerti | Sejarah, Ritual, Etika | 34–36 | CP only |
| Khonghucu | Pendidikan Agama Khonghucu dan Budi Pekerti | Sejarah Suci, Kitab Suci, Keimanan, Tata Ibadah, Perilaku Jūnzǐ | 40–42 | CP only |

- **CP data:** [`../data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json`](../data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json) has 96 outcomes across Fase A–C. It was transcribed by reading the scanned pages, because no text version exists, and was verified word by word against the scan in P2-01 (`verified: true`). Guru agama sign-off is still needed (D-037).
- **Where the CP is at element level:**
  - Islam, Hindu, and Khonghucu give one CP per element per phase.
  - Kristen gives one CP per sub-element, plus a phase summary. "Allah Penyelamat" and "Allah Pembaru" have no CP in Fase A.
  - Katolik gives one CP per element, plus a phase summary.
  - Buddha has three long CPs per phase.
- **JP:** each religion subject uses the same allocation as PAI in the national structure: 3 JP per week, 54/54 JP per semester for grades 1–5 and 54/42 for grade 6. Confirmed by Permendikdasmen 13/2025, Lampiran II B, Tabel 1–5: the six religions share one row (108 intrakurikuler JP per year, 96 in grade 6, plus 36 or 32 kokurikuler JP). Data: [`curriculum-structure-permendikdasmen-13-2025.json`](../data/regulations/curriculum-structure-permendikdasmen-13-2025.json).
- **Teachers:** each religion subject is a guru mapel subject by default (D-032), one teacher per religion. A small school may have one teacher covering several classes.

## MI: Kemenag subjects (D-035)

MI does not teach a single PAI subject. It follows the Ministry of Religious Affairs' structure:

| Subject | Grades | JP per year (intrakurikuler + kokurikuler) | Elements | CP pages (SK 9941, PDF) |
|---|---|---|---|---|
| Al-Qur'an Hadis | 1–6 | 72 (+36 koku in grades 1–2); grade 6: 64 | Pemahaman Konsep (Tajwid, Al-Qur'an, Hadis), Keterampilan Proses | 13–16 |
| Akidah Akhlak | 1–6 | 72 (+36 koku in grade 5); grade 6: 64 (+32 koku) | Pemahaman Konsep (Akidah, Akhlak, Adab, Kisah Keteladanan), Keterampilan Proses | 44–48 |
| Fikih | 1–6 | 72 (+36 koku in grades 3–4); grade 6: 64 | Pemahaman Konsep (Ibadah, Muamalah, Ushul Fikih; not every one in every phase), Keterampilan Proses | 70–73 |
| Sejarah Kebudayaan Islam | **3–6** (Fase B–C) | 72; grade 6: 64 | Pemahaman Konsep, Keterampilan Proses | 90–92 |
| Bahasa Arab | 1–6 | 72; grade 6: 64 | Komponen Bahasa, Keterampilan Bahasa | 99–101 |

*JP figures verified against KMA 1503/2025 Lampiran II, Tabel 1–5 (pp. 28–31). Full MI allocation for every subject: [`mi-allocation-kma-1503-2025.json`](../data/curriculum/mi-allocation-kma-1503-2025.json).*

- **MI keeps the KMA 1503/2025 table.** Permendikdasmen 13/2025 names MI in its SD tables, but madrasah follow KMA 1503/2025 (D-038). There, kokurikuler JP are moved from some subjects per grade, not from every subject as in SD.
- **36 weeks per year, 32 in grade 6. 1 JP = 35 minutes.** A madrasah may add up to 6 JP per week (KMA 1503/2025, p. 32). "Koku" JP are shifted to the cocurricular programme. RPM scheduling counts the intrakurikuler JP only, and the kokurikuler JP belong to the Kokurikuler module.
- **Other MI subjects** (Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS from grade 3, PJOK, Seni dan Budaya, Bahasa Inggris from grade 3, Koding dan KA (an elective in grades 5–6 in KMA 1503/2025; in SIGRAK it runs through kokurikuler, D-208), Muatan Lokal) use the national CP, but with MI's own allocation table from KMA 1503/2025 ([`mi-allocation-kma-1503-2025.json`](../data/curriculum/mi-allocation-kma-1503-2025.json)). Muatan Lokal takes 72–216 JP per year (64–192 in grade 6), and students choose one art (Seni Musik, Rupa, Teater, or Tari).
- **CP data:** [`../data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json`](../data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json) holds Fase A–C text for the five subjects, extracted from the PDF's text layer and structured into element and sub-element rows in P2-02 (D-039). The six Bahasa Arab passages were retyped from the page images. The text is shown as printed, and the nine rows with possible source errors carry `review_notes` (D-127). Tim Validasi SIGRAK still has to review and sign off the data before import (D-113).
- **Teaching materials:** Kemenag publishes free Buku Teks Utama (BTU) for these subjects aligned with KMA 1503/2025. They are a natural reference source for RPM, like the Parijatha books for Bahasa Jawa.
- **Teachers:** the five subjects are taught by **guru mapel** (D-037). They default to `subject_teacher`, and the operator can still reassign them per class.
- **Religion at MI:** MI is an Islamic school, so only the Kemenag subjects are offered there, with no other religion subjects.

### MI: Kurikulum Berbasis Cinta (KBC)

**Source since 2026-11-09: KMA 512/2026 (D-359).** Keputusan Menteri Agama No. 512 Tahun 2026 sets the Pedoman Kurikulum Berbasis Cinta (set 31 March 2026, in force on that date). Source: [`KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf`](../sources/KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf) (printed page numbers). What it adds to the 6077 guide below:

- **Scope** (Diktum KESATU; Lampiran §C, p. 6): every Kemenag school (RA, MI, MTs, MA, MAK, and other religious schools) *and* religion education in general schools, TK, SD, and SDLB included.
- **Two domains** (Diktum KEDUA–KEEMPAT; §D–F, pp. 12–15): program kurikuler (intra-, ko-, and ekstrakurikuler, as a cross-subject value stream) and pembudayaan nilai (school policy, leadership by example, a safe and inclusive environment, school traditions).
- **Panca Cinta** (p. 9): cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air. **Six nilai dasar** derived from it (pp. 8–11): spiritual, personal, sosial, ekologis, kebangsaan, intelektual.
- **Six principles** (p. 12): transformatif, integratif, kontekstual, reflektif, berkelanjutan, kolaboratif.
- **Approach and methods** (§G, pp. 15–19): pembelajaran mendalam with mindful and joyful learning; 12 suggested methods (pembiasaan, experiential learning, dialog nilai, storytelling, dilema moral, service learning, SEL, refleksi naratif, proyek berbasis karakter, inkuiri nilai, pedagogi kontemplatif, refleksi terbimbing), each with the levels it suits; pembiasaan and storytelling suit the early grades.
- **Assessment** (§I, pp. 21–22): authentic and continuous, with 10 instruments (observation, reflection journal, portfolio, social action project, development notes, self and peer assessment, value conference, narrative reflection, attitude scale). Results are integrated, reported through Akhlak or Budi Pekerti and the attitude notes, and never used to label, judge, or rank students.
- **Still to come:** technical guides from the Dirjen with KBC learning outcomes per phase, strategies, assessment, and monitoring (§J, p. 22). Monitoring uses three levels of practice: merintis, berkembang, mekar (pp. 23–24).

The 6077 guide below stays useful for its example lessons and CP-to-theme mapping.


SK Dirjen Pendis No. 6077 Tahun 2025 sets the Panduan Kurikulum Berbasis Cinta as the reference for learning in every madrasah, public and private (Diktum KEDUA). Source: [`SK_Dirjen_Pendis_No_6077_Tahun_2025_Panduan_Kurikulum_Berbasis_Cinta.pdf`](../sources/SK_Dirjen_Pendis_No_6077_Tahun_2025_Panduan_Kurikulum_Berbasis_Cinta.pdf) (page numbers below are PDF pages).

- **No new subject and no JP change.** KBC values are inserted into existing subjects (intrakurikuler, kokurikuler, and school culture). The MI allocation from KMA 1503/2025 stays as it is.
- **Panca Cinta, five themes** (Tabel 1, pp. 31–35), each with goals and core material: Cinta Allah dan Rasul-Nya · Cinta Ilmu · Cinta Lingkungan · Cinta Diri dan Sesama Manusia · Cinta Tanah Air.
- **Lesson plan fields.** The example lesson plans (Lampiran 2, pp. 72–91) add two lines to the plan's identity block: *Tema Kurikulum Berbasis Cinta* (one or more of the five themes) and *Materi Insersi* (the values inserted). The MI example is IPAS Fase C, Ekosistem, themes Cinta Allah and Cinta Lingkungan, 2 × 35 minutes (pp. 75–77). TP, activities, and assessment then carry those values.
- **CP mapping.** Lampiran 1 (pp. 59–71) maps CP material to the five themes for RA, for PAI and Bahasa Arab, and for general madrasah subjects. Its CP wording appears to follow an older CP set than SK 9941/2025, so it cannot be imported as is.
- **Everything is an example.** The guide says all aspects and indicators are examples, and each madrasah may adapt them (p. 12).
- **Methods it recommends:** deep learning (mindful, meaningful, joyful), experience-based learning, the FIDS flow (Feel, Imagine, Do, Share), and process-based evaluation.

**In SIGRAK (D-126).** For MI schools, the v1 RPM wizard adds *Tema KBC* (one or more Panca Cinta themes) and *Materi Insersi*, and the AI weaves them into TP, activities, and assessment. Both fields are optional, AI pre-filled (D-142): the teacher may change or clear them. This applies to every MI subject and does not apply to SD. KMA 512/2026 also covers religion teachers in SD; whether SIGRAK offers the KBC fields to them is open (D-359, `docs/drafts/open-questions.md`).

## How SIGRAK models it

- **School level decides the subject set.** `schools.level` = `sd` gives the six religion subjects. `mi` gives the Kemenag subjects plus MI's own allocation table.
- **Curriculum versions:** `SIGRAK-SD-2026-R1` (SD topics: general subjects incl. PAI, and the five other religions), `SIGRAK-MI-2026-R1` (MI Kemenag subject topics), `CP-PABP-BKPDM-020-2026` (six religions, CP), `CP-PAI-BA-MADRASAH-9941-2025` (MI PAI + Bahasa Arab, CP), and `ALOKASI-MI-KMA-1503-2025` (MI allocation). See [`data-model.md`](data-model.md#curriculum).
- **Student religion (D-036)** is recorded per student so religion classes can be grouped. For a religion subject, the journal, student notes, assessment, and report card list only that religion's students. Attendance stays daily and class-wide.
- **Privacy (D-036):** religion is sensitive personal data.
  - It is visible to the class's teachers, the operator, and the principal.
  - Supervisors never see it.
  - It never goes into an AI prompt, never appears on the exam site, and is never exported except in the report card where the law requires it.
- **AI generation** for religion subjects uses the same templates. The prompt carries the religion's CP/TP and element names as data. A template rule keeps religious content inside the selected religion's own sources and terms.

## Out of scope for v1

- **Pendidikan Kepercayaan terhadap Tuhan YME** (D-037). It is not in BKPDM 020/2026, and can be added later as its own subject with its own CP source.

## Data work (Phase 2)

1. ~~**Verify the SD religion CP transcription**~~ Done in P2-01 (`verified: true`).
2. **Cross-check PAI in SIGRAK-SD-2026-R1** against the decree's Islam CP (curation task P4-31, D-364).
3. **Build topics, TP, ATP, fokus, and JP** — draft done in P2-09, now [`sigrak-sd-2026-r1-agama.json`](../data/curriculum/sigrak-sd-2026-r1-agama.json) (D-139…D-141, D-363). For Kristen, Katolik, Hindu, Buddha, and Khonghucu (grades 1–6, two semesters), in the same shape as the general SD subjects. AI may draft from the CP. **Tim Validasi SIGRAK reviews and signs off each set** (D-113), and the dataset records "Tim Validasi SIGRAK" and the review date (D-112). The same applies to the MI subjects and to the CP data.
4. ~~**Structure the MI CP**~~ Done in P2-02 (element rows, Arabic retyped).
5. **Build topics, TP, ATP, fokus, and JP** for the five MI subjects — draft done in P2-10, now [`sigrak-mi-2026-r1.json`](../data/curriculum/sigrak-mi-2026-r1.json) (D-145, D-146, D-363). Both sets have curation status `in_review` and wait for Tim Validasi SIGRAK review (D-364, task P4-31).
6. **Transcribe the special-education CP** (Lampiran II of BKPDM 020/2026) for the six religions, in the same shape as the SD religion CP, for inclusive schools (D-155). The decree is a scanned copy without a text layer, so the text is typed and then checked by Tim Validasi SIGRAK (D-113).

## Open questions

- [ ] An official-channel copy of SK 9941/2025 (low priority: two copies from different sources have identical text).
- [x] KBC in the RPM template for MI: in v1, for every MI subject, not for SD (D-126); optional, AI pre-filled (D-142).
- [x] Reviewer name: recorded as "Tim Validasi SIGRAK" (D-112).
- [x] Inclusive schools need the special-education CP (Lampiran II of BKPDM 020/2026); it is in scope and waits for transcription (D-155).
