# Religious Education Curriculum (SD and MI)

- **Status:** draft (owner review)
- **Task:** P1-07
- **Last updated:** 2026-10-30
- **Sources:** [`Salinan_Kepka_BKPDM_No._020_Tahun_2026_….pdf`](../sources/Salinan_Kepka_BKPDM_No._020_Tahun_2026_tentang_Perubahan_atas_Keputusan_Kepala_BSKAP_Kemendikdasmen_No._046_H_KR_2025.pdf), [`SK_Dirjen_Pendis_No_9941_Tahun_2025_….pdf`](../sources/SK_Dirjen_Pendis_No_9941_Tahun_2025_CP_PAI_dan_Bahasa_Arab_pada_Madrasah.pdf)

## Purpose

This document defines how SIGRAK covers religious education in SD (six religions) and in MI (the Ministry of Religious Affairs' PAI subjects and Bahasa Arab). It lists the official sources, what data exists, what the team must still build, and how the data model handles religion. Decisions: D-034, D-035, D-036, D-037.

## Official sources

| Source | Covers | Form | Used for |
|---|---|---|---|
| **Keputusan Kepala BKPDM No. 020 Tahun 2026** (11 June 2026), amending Kepka BSKAP 046/H/KR/2025 | Lampiran I: CP Pendidikan Agama dan Budi Pekerti for Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu, Fase A–F. Lampiran II: special education (SLB) versions | Scanned copy, no text layer | SD religion CP (D-034). Lampiran II is out of scope |
| **SK Dirjen Pendis No. 9941 Tahun 2025** (28 November 2025) | CP for Al-Qur'an Hadis, Akidah Akhlak, Fikih, Sejarah Kebudayaan Islam, Bahasa Arab at MI/MTs/MA, plus MAPK subjects and RA | PDF with text layer, plus the Dirjen's cover letter B-606 (1 December 2025, BSrE e-signed) | MI CP (D-035) |
| **KMA 450 Tahun 2024 as amended by KMA 1503 Tahun 2025** | Madrasah curriculum guideline, including the MI subject structure and JP (Lampiran II) | *Official copy not obtained yet* (the regional Kemenag link returned 404). The figures below come from a public summary of Lampiran II | MI allocation, to be verified |
| **KM-SD-2026-R2** (EDUNEXA curriculum data) | PAI SD with topics, TP, ATP, fokus, JP | JSON | Already references "CP PAI 020/2026", the same decree |

No newer amendment of SK 9941/2025 or KMA 1503/2025 was found on 2026-10-30.

**Provenance.** The BKPDM 020/2026 copy came from the owner. The SK 9941/2025 file was downloaded from a public mirror, because no direct kemenag.go.id link was found. It contains the official cover letter and decree text. Replace it with a copy from an official channel when one is available.

## SD: six religion subjects (D-034)

All six ship in v1. Each religion is a separate subject; students take the one matching their recorded religion.

| Religion | Subject | Elements (sub-elements) | SD CP pages | Data status |
|---|---|---|---|---|
| Islam | Pendidikan Agama Islam dan Budi Pekerti | Al-Qur'an Hadis, Akidah, Akhlak, Fikih, Sejarah Peradaban Islam | 6–8 | **Complete** in KM-SD-2026-R2 (topics, TP, fokus, JP). Cross-check its CP text against the decree |
| Kristen | Pendidikan Agama Kristen dan Budi Pekerti | Allah Berkarya (Pencipta, Pemelihara, Penyelamat, Pembaru); Manusia dan Nilai-nilai Kristiani (Hakikat Manusia, Nilai-nilai Kristiani); Gereja dan Masyarakat Majemuk (Tugas Panggilan Gereja, Masyarakat Majemuk); Alam dan Lingkungan Hidup (Alam Ciptaan Allah, Tanggung Jawab Manusia terhadap Alam) | 12–14 | CP only |
| Katolik | Pendidikan Agama Katolik dan Budi Pekerti | Pribadi murid, Yesus Kristus, Gereja, Masyarakat | 20–23 | CP only |
| Hindu | Pendidikan Agama Hindu dan Budi Pekerti | Kitab Suci Weda, Śraddhā dan Bhākti, Susila, Acara, Sejarah Agama Hindu | 29 | CP only |
| Buddha | Pendidikan Agama Buddha dan Budi Pekerti | Sejarah, Ritual, Etika | 34–36 | CP only |
| Khonghucu | Pendidikan Agama Khonghucu dan Budi Pekerti | Sejarah Suci, Kitab Suci, Keimanan, Tata Ibadah, Perilaku Jūnzǐ | 40–42 | CP only |

- **CP data:** [`../data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json`](../data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json) has 96 outcomes across Fase A–C. It was transcribed by reading the scanned pages, because no text version exists, and is marked `verified: false` until a second pass checks every line against the scan.
- **Where the CP is at element level:**
  - Islam, Hindu, and Khonghucu give one CP per element per phase.
  - Kristen gives one CP per sub-element, plus a phase summary. "Allah Penyelamat" and "Allah Pembaru" have no CP in Fase A.
  - Katolik gives one CP per element, plus a phase summary.
  - Buddha has three long CPs per phase.
- **JP:** each religion subject uses the same allocation as PAI in the national structure: 3 JP per week, 54/54 JP per semester for grades 1–5 and 54/42 for grade 6. This assumes the structure has a single row for "Pendidikan Agama dan Budi Pekerti" whatever the religion. Confirm it against the struktur kurikulum.
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

*JP figures are from a public summary of KMA 1503/2025 Lampiran II and must be verified against the official copy.*

- **36 weeks per year, 32 in grade 6. 1 JP = 35 minutes.** "Koku" JP are shifted to the cocurricular programme. RPM scheduling counts the intrakurikuler JP only, and the kokurikuler JP belong to the Kokurikuler module.
- **Other MI subjects** (Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS from grade 3, PJOK, Seni dan Budaya, Bahasa Inggris from grade 3, Koding dan KA as an option in grades 5–6, Muatan Lokal) use the national CP, but with MI's own allocation table from KMA 1503/2025.
- **CP data:** [`../data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json`](../data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json) holds Fase A–C text for the five subjects, extracted from the PDF's text layer. It is not yet split into element rows. **Arabic passages extract badly** and must be retyped from the PDF.
- **Teaching materials:** Kemenag publishes free Buku Teks Utama (BTU) for these subjects aligned with KMA 1503/2025. They are a natural reference source for RPM, like the Parijatha books for Bahasa Jawa.
- **Teachers:** the five subjects are taught by **guru mapel** (D-037). They default to `subject_teacher`, and the operator can still reassign them per class.
- **Religion at MI:** MI is an Islamic school, so only the Kemenag subjects are offered there, with no other religion subjects.

## How SIGRAK models it

- **School level decides the subject set.** `schools.level` = `sd` gives the six religion subjects. `mi` gives the Kemenag subjects plus MI's own allocation table.
- **Curriculum versions:** `KM-SD-2026-R2` (national SD subjects incl. PAI topics), `CP-PABP-BKPDM-020-2026` (six religions, CP), `CP-PAI-BA-MADRASAH-9941-2025` (MI PAI + Bahasa Arab, CP), and the MI allocation set from KMA 1503/2025. See [`data-model.md`](data-model.md#curriculum).
- **Student religion (D-036)** is recorded per student so religion classes can be grouped. For a religion subject, the journal, student notes, assessment, and report card list only that religion's students. Attendance stays daily and class-wide.
- **Privacy (D-036):** religion is sensitive personal data.
  - It is visible to the class's teachers, the operator, and the principal.
  - Supervisors never see it.
  - It never goes into an AI prompt, never appears on the exam site, and is never exported except in the report card where the law requires it.
- **AI generation** for religion subjects uses the same templates. The prompt carries the religion's CP/TP and element names as data. A template rule keeps religious content inside the selected religion's own sources and terms.

## Out of scope for v1

- **Pendidikan Kepercayaan terhadap Tuhan YME** (D-037). It is not in BKPDM 020/2026, and can be added later as its own subject with its own CP source.
- **Special-education CP** (Lampiran II of BKPDM 020/2026), unless a target school needs it (see Open questions).

## Data work (Phase 2)

1. **Verify the SD religion CP transcription** line by line against the scanned pages, then set `verified: true`.
2. **Cross-check PAI in KM-SD-2026-R2** against the decree's Islam CP.
3. **Build topics, TP, ATP, fokus, and JP** for Kristen, Katolik, Hindu, Buddha, and Khonghucu (grades 1–6, two semesters), in the same shape as KM-SD-2026-R2. AI may draft from the CP. **A guru agama of that same religion reviews and signs off each set** (D-037), and the dataset records the reviewer's name, school, and review date. The same rule applies to the MI subjects (a guru of that subject) and to the CP transcription check.
4. **Structure the MI CP:** split `raw_text` into element and sub-element rows, and retype the Arabic passages.
5. **Build topics, TP, ATP, fokus, and JP** for the five MI subjects, using the BTU books as references.
6. **Obtain the official KMA 1503/2025** and confirm the MI allocation table above.

## Open questions

- [ ] Official copy of KMA 1503/2025 (Lampiran II) and an official-channel copy of SK 9941/2025.
- [ ] Names of the reviewing guru agama for each religion (who, from which school).
- [ ] Inclusive schools: does any target school need the special-education CP (Lampiran II of BKPDM 020/2026)?
