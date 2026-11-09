# LKPD Exploration: from worksheet to learning experience

- **Status:** draft (exploration, needs owner review)
- **Task:** P4-16
- **Last updated:** 2026-11-09
- **Builds on:** [`content-engine.md`](../final/content-engine.md#lkpd) (LKPD template), D-296 (LKPD blocks for grades 1–3), [`ifp-classroom.md`](../final/ifp-classroom.md) (Game kelas)

## 1. Summary

The current LKPD template produces a good-looking sheet: title, goal, instructions, steps, table, questions, reflection (`content-engine.md`), plus the grade 1–3 blocks of D-296. This exploration asks a different question: **what does the student do differently** when they work on a SIGRAK LKPD?

Proposal in one paragraph: keep **one LKPD template** (D-024) and give it two new layers. (1) A **pattern library** (*Bentuk LKPD*): about 18 student-experience patterns such as Misi Petualangan, Kotak Terkunci, Detektif Teks, Pasar Numerasi, Lab Mini, Wawancara Keluarga, or Tiga Jalur. Each pattern has its own output schema, like the RPM kokurikuler variant. (2) A **block library** owned by the renderer: D-296 blocks for grades 1–3 plus new blocks for grades 4–6 (number line, fraction strip, bar model, graphic organisers, code wheel, QR/embed block). Each LKPD is a **starter**: the teacher picks a pattern, gets a draft filled from curriculum data and the school's **local context profile**, and then adapts it. A school can keep its adapted LKPDs as **school presets** for other teachers. AR, VR, 3D, and simulations come only from third-party services, as a QR code on paper or a vetted embed in the app. Every LKPD still works with paper only.

## 2. What changes for the student

| From (sheet) | To (experience) | How SIGRAK supports it |
|---|---|---|
| Copy answers from the book | Solve a problem or case they care about | Pattern stimuli from local context and daily life (pasar, sawah, banjir, jajanan) |
| Same task for everyone | Choose a path or level | *Tiga Jalur* pattern; block difficulty per path |
| Work alone in silence | Talk, divide roles, decide together | Role cards (ketua, pencatat, juru bicara, pengecek waktu) as a block |
| Answer and hand in | Check yourself, get a code, unlock the next step | Code-wheel / lock block; answer sums to a lock code |
| Paper only | Paper as the anchor, screens when useful | QR block to a simulation, 360° tour, AR model; IFP group check (Game kelas) |
| Reflection as one last question | Thinking about thinking at each step | Per-step *Merefleksi* prompts tied to the three learning experiences |
| Teacher grades later | Evidence the teacher can use the same day | Checklist per group in Di Kelas; Pindai LJK for closed items; Game kelas class results |

These map onto the deep-learning frame SIGRAK already uses in the RPM: principles *berkesadaran*, *bermakna*, *menggembirakan* and the experiences *Memahami*, *Mengaplikasi*, *Merefleksi* (Kepmendikdasmen 126/P/2025, `docs/data/regulations/`). Every pattern below says which experience it carries most.

## 3. Formats

The owner listed five formats. They are not separate products; one LKPD can be printed and also carry QR codes.

| Format | Meaning in SIGRAK | Output | Needs |
|---|---|---|---|
| **Cetak** | Printed LKPD, the default for every pattern | PDF A4/F4 (Gotenberg), DOCX, Buka di Canva | Printer; black-and-white safe |
| **Tanpa perangkat** | Cut, fold, move, play, go outside; no screen at any step | PDF with cut lines, cards, board, stations | Scissors, glue, dice, objects |
| **Digital interaktif** | Students or groups work on a screen | Offline HTML (like Unduh game, D-011) and IFP mode (Game kelas, D-171) | IFP, laptop, or teacher phone; no student login |
| **Gabungan** | Paper is the anchor; screen steps are optional | PDF with QR blocks; "Lanjut di Game kelas" step | One device per class is enough |
| **Teknologi baru** | AI for the teacher, third-party AR/VR/3D/simulation | Embed or QR block from an allowlist | Internet at the moment of use |

## 4. Pattern catalogue (*Bentuk LKPD*)

Evidence per pattern is in §8.2. Local-context, unplugged CT, model-based, and differentiated patterns have tested studies; escape room, treasure hunt, board game, and lapbook (G2–G4, S4) are good classroom practice but have no tested SD study from 2025–2026.

Prep: ● low (print and go) ●● medium (cutting, grouping) ●●● high (stations, materials, devices). Grades: A = Fase A (1–2), B = Fase B (3–4), C = Fase C (5–6).

### 4.1 Literacy

| # | Pattern | Student experience | Format | Grades | Prep | Main experience |
|---|---|---|---|---|---|---|
| L1 | **Detektif Teks** | Read a short case text, collect clues, decide "who/why", prove with sentences from the text | Cetak | B, C | ● | Memahami (inference, TKA *pemahaman inferensial*) |
| L2 | **Peta Cerita** | Retell a folk tale on a story map (tokoh, latar, masalah, penyelesaian), then change the ending | Cetak | A–C | ● | Mengaplikasi |
| L3 | **Kartu Kata Bergambar** | Match word cards to pictograms, sort by first sound, build sentences | Tanpa perangkat | A | ●● | Memahami |
| L4 | **Wartawan Cilik** | Interview a family or community member, write a short news piece | Cetak (take-home) | B, C | ● | Mengaplikasi, links to kokurikuler |
| L5 | **Teka-teki Baca Berjenjang** | One text in three levels; each level ends with a clue word | Cetak | A–C | ● | Memahami (differentiated) |

### 4.2 Numeracy

| # | Pattern | Student experience | Format | Grades | Prep | Main experience |
|---|---|---|---|---|---|---|
| N1 | **Pasar Numerasi** | Run a pretend stall with price cards and rupiah money; buy, pay, give change | Tanpa perangkat | A–C | ●● | Mengaplikasi |
| N2 | **Model Batang** | Solve word problems with bar models drawn by the renderer | Cetak | B, C | ● | Memahami |
| N3 | **Data Kelasku** | Collect real data in class (snacks, transport), make a table and pictogram/bar chart, write a conclusion | Cetak | A–C | ● | Mengaplikasi (TKA *Data*) |
| N4 | **Ukur Sekitar** | Measure objects and places at school; estimate, then check | Tanpa perangkat | A–C | ●● | Mengaplikasi |
| N5 | **Etnomatematika** | Find shapes, patterns, symmetry, and counting in local crafts and buildings (batik, ukir, anyaman, rumah adat) | Cetak + QR | B, C | ● | Bermakna (local context) |

### 4.3 Games and adventure

| # | Pattern | Student experience | Format | Grades | Prep | Main experience |
|---|---|---|---|---|---|---|
| G1 | **Misi Petualangan** | A story map with 4–6 missions; each finished mission gets a stamp; the last mission is the reflection | Cetak | A–C | ● | Menggembirakan |
| G2 | **Kotak Terkunci** (escape room) | Each correct answer gives a digit or letter; the group opens the "lock" (code checked on the teacher's sheet, the IFP, or the offline HTML) | Cetak + optional digital | B, C | ●● | Mengaplikasi; self-check |
| G3 | **Papan Petak** | A printable board game; landing on a square means answering a question card | Tanpa perangkat | A–C | ●● | Menggembirakan; matches the D-176 *Papan Petak* game template |
| G4 | **Jelajah Pos** | Station rotation or a treasure hunt around the school; a clue at each post | Tanpa perangkat | A–C | ●●● | Mengaplikasi |
| G5 | **Kartu Misi Acak** | Draw a card, do the mission; the teacher sets the deck size and time | Tanpa perangkat | A–C | ●● | Menggembirakan |

### 4.4 Science, project, and deep learning

| # | Pattern | Student experience | Format | Grades | Prep | Main experience |
|---|---|---|---|---|---|---|
| S1 | **Lab Mini (POE)** | Predict, observe, explain; record in a table drawn by the renderer | Cetak | B, C | ●● | Memahami, Mengaplikasi |
| S2 | **Jurnal Proyek** | A multi-meeting LKPD for PjBL or kokurikuler: plan, roles, progress log, product, presentation, reflection | Cetak | B, C | ●● | All three |
| S3 | **Masalah Nyata** (PBL) | A real local problem (sampah, banjir, air bersih); students propose and test a small solution | Cetak + QR | B, C | ●● | Bermakna |
| S4 | **Lapbook / Lipat** | Make a folded mini-book or lapbook as the product of the topic | Tanpa perangkat | A–C | ●● | Mengaplikasi |
| S5 | **Pikir Komputasional Tanpa Komputer** | Unplugged CT: algorithm cards, pixel art from code, sorting games, pattern rules (KKA via kokurikuler, D-208) | Tanpa perangkat | B, C | ●● | Mengaplikasi |

### 4.5 Differentiation, reflection, and character

| # | Pattern | Student experience | Format | Grades | Prep | Main experience |
|---|---|---|---|---|---|---|
| D1 | **Tiga Jalur** | Same TP, three paths: *Mulai*, *Tantangan*, *Bintang*; the student or teacher picks; matches TaRL grouping and Kelompok belajar (D-295) | Cetak | A–C | ● | Berkesadaran |
| D2 | **Tiket Keluar** | A small exit card per student; feeds "Perlu diulang" | Cetak | A–C | ● | Merefleksi; matches D-176 *Tiket Keluar* |
| D3 | **Jejak 7 KAIH** | A weekly habit tracker for home, signed by a parent | Cetak (take-home) | A–C | ● | Kokurikuler (D-207) |
| D4 | **Cermin Belajar** | Visual reflection: emoji scale, "what helped me", next goal | Cetak | A–C | ● | Merefleksi |

For MI, every pattern can carry the KBC theme and *Materi Insersi* the same way the RPM does (D-142). Arabic tracing stays in the D-296 `trace` block.

## 5. Block library

Blocks are drawn by the renderer from data the AI fills, never as AI images (D-030). They work in PDF, DOCX (as images where needed), and the offline HTML.

| Group | Blocks | Status |
|---|---|---|
| Grades 1–3 (D-296) | `trace`, `match_lines`, `cut_and_stick`, `colour_by_rule`, `count_pictures`, `fill_table`, `short_answer` | Decided, v1 (P5-05) |
| Numeracy | `number_line`, `fraction_strip`, `bar_model`, `place_value_chart`, `rupiah_money` (from the D-175 manipulative), `clock_face`, `grid_paper`, `pictogram_chart`, `bar_chart` | New |
| Literacy | `story_map`, `kwl_chart`, `frayer_model`, `venn`, `cause_effect`, `sequence_strip`, `word_cards` | New |
| Game and flow | `mission_map`, `stamp_card`, `code_lock` (answers → code), `board_game`, `card_deck`, `station_card`, `role_cards`, `path_choice` | New |
| Science and project | `poe_table`, `observation_log`, `project_timeline`, `checklist_rubric` | New |
| Local | `aksara_trace` (aksara Jawa first, D-177), `local_map` (from the D-175 map), `regional_word_bank` | New |
| Hybrid | `qr_link` (third-party URL from an allowlist, with a no-device fallback task), `ifp_step` ("Lanjut di Game kelas") | New |

## 6. Local context and kokurikuler

**Local context profile (*Konteks Daerah*).** A school-level setting: region (from the school profile), local language, a short list of local things the school wants students to meet (foods, crafts, places, festivals, occupations, stories), and themes to avoid. The wizard reads it so stimuli and examples are local without the teacher typing them. It holds no student data.

- **Seed content per region** starts with Kudus, Jepara, and Pati, where the pilot runs (D-177, D-257), and is reviewed by Tim Validasi SIGRAK like other curated data (D-113). Examples of what a seed list could hold: carving and furniture (Jepara), Menara Kudus and jenang (Kudus), salt farming and fishing (Pati coast). **The AI never invents local facts**; it only uses items from the profile and the seed list, and the teacher confirms them.
- **Child-appropriate filter.** Some well-known local products are not suitable for SD material (for example tobacco and kretek). The seed list marks them as excluded.

**Kokurikuler.** The Jurnal Proyek pattern (S2), Wawancara (L4), Masalah Nyata (S3), Jejak 7 KAIH (D3), and Pikir Komputasional (S5) can be attached to a kokurikuler plan from the RPM kokurikuler variant, so one activity yields both the plan and the student sheets. This fits the three kokurikuler forms (collaborative cross-subject learning, 7 KAIH, other ways) and KKA through kokurikuler (D-208).

## 7. Starter, then adapt

SIGRAK makes the first draft; the teacher and the school own the final LKPD.

1. **Pilih bentuk:** the wizard suggests 3 patterns from the TP, grade, and the RPM meeting's learning model (for example PjBL → Jurnal Proyek; Game Based Learning → Misi Petualangan or Kotak Terkunci).
2. **Jawab:** format (cetak / tanpa perangkat / gabungan / digital), individual or group, time, materials the class has, differentiation on/off.
3. **Buat:** the AI fills the pattern's schema; the renderer draws blocks.
4. **Sesuaikan:** the teacher edits text, swaps blocks, changes difficulty per path, or revises one section ("Perbaiki bagian ini"). Then prints, downloads, or opens in Canva.
5. **Simpan sebagai preset sekolah** (new): the teacher can offer the adapted LKPD to the school library. Another teacher starts from it for a parallel class, without AI (like D-275).

This keeps D-013: patterns and blocks live in code; a school preset is a saved document plus its wizard answers, not a new template.

## 8. Research basis

Literature scan on 2026-11-09 of Indonesian journal articles from 2025–2026 on LKPD/e-LKPD for SD/MI with a trial. About 60 primary studies and 6 reviews were found. The list below holds the most useful ones. Each abstract was read on the journal site; the full methods sections were not. Abbreviations: Val = validity, NG = N-gain (Hake), *Pendas* = *Pendas: Jurnal Ilmiah Pendidikan Dasar* (Unpas), JPPGSD = *Jurnal Penelitian Pendidikan Guru SD* (Unesa), JPPSD = *Jurnal Pendidikan & Pembelajaran SD* (UNM).

### 8.1 Studies by theme

| Theme | Study | Grade, form | Result | Supports |
|---|---|---|---|---|
| Literacy | Syahputri et al. 2026, *Pendas* 11(2), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/52308/30269) | IV, print LKPD on local Riau stories, reading comprehension | Val 77–98%, NG 0.74 | L1, L5, local context |
| Literacy | Lestari & Rigianti 2026, *CJPE* 9(1), [link](https://e-journal.my.id/cjpe/article/download/8015/5135/38784) | V, deep-learning LKPD, reading and writing | Pre 52 → post 72, one class | L1, L2 |
| Literacy | Zahranisa & Damayanti 2025, JPPGSD 13(3), [doi](https://doi.org/10.26740/jppgsd.v13i3.68449) | I, interactive early-reading module by level | NG 0.66 (still developing readers), 0.82 (proficient) | L3, L5, D1 |
| Numeracy | Siswardani et al. 2025, *Pendas* 10(4), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/37391/22839) | III, numeracy e-LKPD on multiplication | NG 0.69 | N1, N2 |
| Numeracy | Markhamah et al. 2026, *Pendas* 11(2), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/52097/30123) | III, numeracy with Islamic values | NG 0.54, t-test p = 0.000 | MI overlay (D-142) |
| Numeracy | Handayani et al. 2026, *Didaktika* 15(1), [doi](https://doi.org/10.58230/27454312.3431) | V, ethnomathematics with a batik motif, plane shapes | NG 0.80, Wilcoxon p < 0.05 | N5 |
| Game | Kalyana et al. 2026, JPPGSD 14(5), [doi](https://doi.org/10.26740/jppgsd.v14i5.77872) | V, game e-LKPD with levels, ecosystems, n = 24 | NG 0.65 | G1 |
| Game | Alawiah et al. 2026, *CJPE* 9(2), [link](https://e-journal.my.id/cjpe/article/view/8819) | IV, traditional game "Simar", mixed operations, two schools | Validity only | G3, G5, local context |
| Game (review) | Isti'ana et al. 2026, *Pendas* 11(2), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/52771/31855) | SLR, 23 studies on gamified e-LKPD | Moderate–high NG; points and instant feedback raise motivation | G1, G2 |
| Digital | Inanta et al. 2026, *Pendas* 11(2), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/52516/30338) | V, Liveworksheets guided inquiry | NG 0.78 | Offline HTML self-check |
| Digital | Noor et al. 2025, *J. Educ. Sciences* 9(4), [link](https://jes.ejournal.unri.ac.id/index.php/JES/article/download/581/424) | IV, Liveworksheets, Pendidikan Pancasila, three schools | Mann–Whitney p = 0.000 | Instant feedback |
| Digital | Rahma et al. 2025, *Paedagogi* 11(2), [link](https://jurnal.unimed.ac.id/2012/index.php/PAEDAGOGI/article/view/66767/28877) | V, IPAS "Daerahku Kebanggaanku" | NG 0.79 | Local context |
| Hybrid | Nugroho et al. 2025, *JPIM* 2(1), [link](https://ojs.ruangpublikasi.com/index.php/jpim/article/view/611) | IV, printed IPS LKPD with QR codes | NG 0.76 | `qr_link` |
| Hybrid (review) | Afifah & Hasyim 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/59844) | SLR, 20 studies on QR e-LKPD | Positive on motivation; narrative only | `qr_link` |
| Deep learning | Rosyada & Rezania 2026, *Elementary School* 13(1), [link](https://es.upy.ac.id/index.php/es/article/download/4684/2772/12935) | IV, IPS | NG 0.61, p < 0.001 | Three-experience steps |
| Deep learning | Isnaeni & Indrawati 2026, JPPGSD 14(9), [doi](https://doi.org/10.26740/jppgsd.v14i9.80935) | III, multiplication, n = 30 | NG 0.41 | Three-experience steps |
| Deep learning | Karnila et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/60615) | V, qualitative | Barriers: time, facilities, mixed ability | Low-prep patterns, D1 |
| Needs | Putri, Wiguna & Suardipa 2026, *Cetta* 9(2), [link](https://jayapanguspress.penerbit.org/index.php/cetta/article/download/5457/2611/23906) | II, needs analysis (4 teachers, 48 students) | Users asked for pictures, simple instructions, short video, instant feedback, step-by-step work, phone access | Wizard defaults, blocks |
| Local | Nomleni et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/57378/34736) | III, Timor (NTT) ceremony, Pendidikan Pancasila | NG 0.68 | Local context |
| Local | Wiratama et al. 2025, *Mitra PGMI* 11(1), [doi](https://doi.org/10.46963/mpgmi/v11i1.2380) | MI, ethnoscience e-LKPD, with a control class | NG 0.79, p = 0.001 | Local context, MI |
| Local | Purwanti et al. 2026, *Al-Madrasah* 10(2), [doi](https://doi.org/10.35931/am.v10i2.6448) | V, Lampung local ecosystem, 27 vs 27 students | NG 0.68 vs 0.52 (control) | Local context |
| Local | Sutriono et al. 2026, *Pendas* 11(2), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/47440/26618/173393) | V, Javanese culture e-LKPD, needs survey | 70% of students bored by monotonous LKPD; 65% found non-contextual LKPD hard | Why patterns matter |
| Model | Hafshah et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/63580) | IV, PjBL, number patterns, n = 28 | NG 0.76 | S2 |
| Model | Nuwair et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/59498/33316) | RADEC with LKPD, 24 vs 24 students | NG 0.64 vs 0.25, p = 0.000 | Model presets |
| Model | Agustina et al. 2026, *SSA* 4(1), [link](https://ejournal.insuriponorogo.ac.id/index.php/ssa/article/view/9546) | IV, TaRL LKPD, n = 16 | NG 0.62 | D1 |
| Model | Apriliana et al. 2025, *JPMS* 13, [doi](https://doi.org/10.21831/jpms.v13iSpecial_issue.89002) | V, STEM, quasi-experiment, n = 47 | Effect size 0.63 | S1, S3 |
| AR | Nanda et al. 2026, *Elementary* 6(2), [doi](https://doi.org/10.51878/elementary.v6i2.10013) | IV, LKPD with Assemblr EDU, plant parts, n = 37 | Val 88–100%, response 93% | §9 Assemblr |
| AR | Anggraini et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/57274/32244) | V, AR LKPD, volume of cubes and cuboids | Post-test effectiveness 90% | `model-viewer` |
| Unplugged CT | Fahrudin et al. 2026, *JUDIKDAS* 5(2), [doi](https://doi.org/10.51574/judikdas.v5i2.5688) | IPAS, grid maps and instruction cards, 60 students with a control class | NG 0.63 vs 0.34 | S5 (strongest design found) |
| Unplugged CT (review) | Meyrena et al. 2025, *Pendas* 10(4), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/36104/25242/164534) | SLR, 13 studies | Consistently effective; suits low-tech schools | S5, D-208 |
| CT | Saputra et al. 2025, *J. Review Pend. Dasar* 11(2), [doi](https://doi.org/10.26740/jrpd.v11n2.p184-207) | IV, problem-solving worksheets for CT, fractions, n = 23 | NG 0.42, Cohen's d 1.18 | S5 |
| AI use | Putri, Dwiani et al. 2026, *Pendas* 11(3), [link](https://journal.unpas.ac.id/index.php/pendas/article/download/62646/36846) | Teachers and students, qualitative | Teachers use AI most to plan modul ajar and LKPD; risks of dependency and misinformation | Teacher-side AI with review |
| Inclusive | Sa'diyah & Arifin 2025, *Educate* 10(1), [doi](https://doi.org/10.32832/educate.v10i1.18159) | Slow learners, case study | Same material, easier questions per student | D1 "Mulai" path |
| Science (review) | Fikria et al. 2026, *Pendas* 11(2), [link](https://journal.unpas.ac.id/index.php/pendas/article/view/45152) | SLR, 20 studies on LKPD in IPA SD | Moderate–high NG; PBL, discovery, STEM, and technology-based forms the most effective | Model presets |

### 8.2 What the evidence supports, and how strongly

| Pattern or feature | Evidence |
|---|---|
| Local context (§6) | **Strongest and most consistent:** many studies across regions, including two with a control class |
| Unplugged CT (S5) | **Strong:** a controlled study plus a review |
| Learning-model presets (PBL, PjBL, RADEC, TaRL, STEM) | Good: many one-group studies, a few with control classes |
| Deep-learning steps | Moderate: one-group studies, NG 0.41–0.61 |
| Digital self-check / instant feedback | Moderate: many Liveworksheets studies; the effect may partly be novelty |
| QR hybrid | Moderate: one trial plus a narrative review |
| Differentiated paths (D1) | Moderate: TaRL and slow-learner studies, small samples |
| AR | Weak: validity and practicality only, or post-test only |
| Game mechanics (G1) | Moderate for digital levels; **no tested escape-room, treasure-hunt, or board-game LKPD for SD found** (G2–G4 are practice-based) |
| Lapbook, foldable, pop-up (S4) | **Not tested as LKPD**; only as "media" |
| Kokurikuler / P5 LKPD | **No field trial found** |
| Special-needs LKPD | **No tested study found** beyond one case study |

### 8.3 Caveats

1. **Small designs.** Most studies are R&D (ADDIE or 4D) in one school, often one class of 10–30 students, with a one-group pre/post test and no control group. Only a handful had a comparison class.
2. **Weak validity.** Validity comes from 2–3 expert validators, often colleagues of the authors. Some report impossible numbers (for example "102%").
3. **Practicality ceiling.** Practicality is a satisfaction questionnaire, almost always 85–100%: a ceiling and novelty effect, not usage data.
4. **"Effective" means different things:** N-gain, mastery above KKTP, or a post-test percentage. N-gain usually sits between 0.4 and 0.8.
5. **No retention studies.** No delayed post-tests, no replications in other schools, and no use by teachers outside the research team.
6. **Positive bias.** Almost every study concludes "valid, practical, effective". Many come from student theses and fast journals.
7. **Terminology.** "Deep learning" in these papers means the 2025 pedagogical approach, not AI.
8. **Regional skew.** Regional coverage skews to Java, Sumatra, and NTT. The sample also leans on *Pendas* (Unpas) because its PDFs download freely.
9. **No pooled meta-analysis.** No true 2025–2026 meta-analysis that pools LKPD effects for SD was found.

So SIGRAK should present these studies as **inspiration and direction, not proof** (R10), and measure its own use.

### 8.4 Feature ideas that come straight from the literature

- **Learning-model presets** inside the LKPD wizard, reusing the RPM's model choice (PBL, PjBL, Inkuiri/Discovery, Problem Solving, GBL), and later RADEC, TaRL, CTL, and STEM.
- **Instant self-check** for closed items in the offline HTML (code lock, auto-check).
- **Uji LKPD helper** (optional, later): the dominant research method among teachers doing PTK or *skripsi* is a pre/post test with N-gain. A small helper could compute N-gain from Asesmen scores of a class before and after an LKPD, with no new student data. This is a candidate only; it is not in the current scope.

## 9. Third-party AR, VR, 3D, and simulations

Web scan of 2025–2026 vendor pages, checked 2026-11-09. Prices and plan limits change often, so re-check each one before building. Items marked *unverified* could not be opened.

### 9.1 Two findings that shape the design

1. **Gemini terms rule out AI that students use.** The Gemini API terms (effective 2026-03-23) require users to be 18 or older. They also bar use in a service "directed towards or likely to be accessed by individuals under the age of 18". On the unpaid tier, human reviewers may read inputs and outputs ([terms](https://ai.google.dev/gemini-api/terms)). SIGRAK's teacher-side generation is fine. A student chatbot, AI read-aloud feedback, or sending photos of student handwriting to Gemini is not (see C10).
2. **Indonesian child-data rules favour tools with no student accounts.**
   - UU 27/2022 (PDP) Art. 25 requires special handling and parental consent for children's data.
   - PP 17/2025 ("PP Tunas"), in force since March 2026 as reported in the news, sets age tiers and age checks for digital platforms.
   - Rule for SIGRAK: support only targets that a student opens **without logging in**, and send no student data.

### 9.2 Shortlist

| Priority | Tool | Use in LKPD | Why | Watch out |
|---|---|---|---|---|
| 1 | **H5P, self-hosted** through `h5p-standalone` (MIT) | Interactive drag, sort, fill, and interactive-book steps in the app, on the IFP, and offline | Content and data stay on our server, no student account, can be cached | Authoring through the free Lumi desktop app or our own blocks. The H5P PHP library is GPL, so use only the standalone player |
| 2 | **Google `<model-viewer>`** (Apache-2.0), self-hosted | 3D model in the app; "Lihat di ruangan" AR on phones without installing an app | The answer to "AR without our own engine". No third party at view time | We need a curated library of CC-BY GLB models (solids, organs, planets, rumah adat). iOS needs USDZ. Android needs Google Play Services for AR, otherwise it falls back to 3D |
| 3 | **PhET** | IPAS simulations as iframe or QR | Free, Indonesian translations, no student data, downloadable HTML | Commercial use of CC-BY sims needs a separate licence from PhET; confirm, since SIGRAK is paid |
| 4 | **GeoGebra** | Math applets as iframe or QR | Indonesian UI, no account to use | Free only for non-commercial use; a paid product needs a licence (office@geogebra.org) |
| 5 | **YouTube** (incl. 360°) | Short video or 360° tour, using the `youtube-nocookie.com` domain | Everyone has it; no account to watch | Ads and recommendations; heavy on bandwidth; 360° in mobile embeds is uneven |
| 6 | **Assemblr EDU** (Indonesian) | AR/3D as link or QR, embed for 3D view | Local company, Indonesian UI, used in many SD IPAS studies. belajar.id teachers get a free quota (10 Edu Kits a day, 1000+ objects; post dated 2025-05-02) | *Unverified:* rupiah prices, whether embeds need a paid plan, whether students can open a shared link without login. Test before promising |
| 7 | **Wordwall**, **Wayground** (ex-Quizizz) | Link or QR to a teacher's own activity | Popular with Indonesian teachers; students join without an account | Free tiers are small (Wordwall Basic: 3 activities) and change often |
| 8 | **Scratch**, **Code.org** | KKA links for kokurikuler (D-208) | Free, Indonesian UI, no sign-in needed to try | Progress tracking needs accounts; link only |

**Generic fallback:** a "tautan apa saja → QR + link" block for any https URL, with embeds limited to an allowlist of domains.

**Avoid:**
- **8th Wall:** the hosted platform closed on 2026-02-28, and hosted projects are deleted after 2027-02-28.
- Small free plans or plans that need accounts: MyWebAR free, ZapWorks, Blippar, JigSpace free, Merge Cube, ThingLink, Frame VR, Delightex/CoSpaces.
- For students: Liveworksheets free (ads), Padlet (user posts), Book Creator and Canva (student accounts).
- Google Expeditions has been dead since 2021.

**Government content:**
- Rumah Belajar became **Rumah Pendidikan / Ruang Murid** in January 2025. It has no embed API and its URLs may change, so use link or QR only.
- Buku Kemendikdasmen (SIBI) can be linked by book and page.
- Many Rumah Pendidikan pieces run on the IFP that almost every SD/MI now has (D-179), so the online view must work well on a large touch screen.

### 9.3 Other technology

| Idea | Verdict |
|---|---|
| QR codes on print | Yes. Generate them server-side. Better: point the QR at **our own short redirect** so a dead link can be fixed after printing. This needs a public route, which clashes with D-010 (C15) |
| Text-to-speech for early reading | Web Speech API in Chrome on Android, but an `id-ID` voice depends on the device. Fallback: pre-generated audio inside the offline HTML |
| Photo scan of printed answers | Closed items only, through Pindai LJK (D-286), on the device. No handwriting sent to Gemini |
| NFC cards | Web NFC is Chrome on Android only and experimental. Skip |
| VR headsets | Skip. 360° on the IFP or a phone covers the SD use cases |

### 9.4 Main risks

- **Link rot:** Expeditions, 8th Wall, the move from Sketchfab to Fab, and ministry rebrands. Mitigations: our own redirect, a scheduled link checker, a stored title and description, and a printed fallback task.
- **Accounts and child privacy:** support no-login targets only.
- **Ads**, for example on YouTube and the Liveworksheets free plan.
- **Licence or price changes:** GeoGebra, PhET, Wordwall, Assemblr.
- **Bandwidth:** 360° video and large 3D files. Show the size, add a "perlu internet" label, and prefer content that can be cached (H5P, model-viewer, PhET HTML).

## 10. Clash check against existing decisions

| # | Idea | Existing rule | Clash? | Proposed way |
|---|---|---|---|---|
| C1 | Students do an LKPD on their own device and the answers come back | Students are records, never users; no public pages or share links (D-010) | **Yes** | v1.x: offline HTML self-check with no data back (as D-011); IFP group mode (D-171). Answers back only through paper scanning (D-286, D-174) or a teacher checklist. A student session like the exam site (D-027) would need a new decision |
| C2 | Illustrated, colourful LKPD | No AI images (D-030) | Partial | Bundled pictogram and illustration set chosen by id; the teacher can upload a photo; Buka di Canva for decoration |
| C3 | Interactive LKPD with logic (code lock, drag, sort) | No AI-written code (D-015) | No | Fixed runtime blocks; the AI fills data only |
| C4 | Many LKPD kinds | One template per content type, no generic generator (D-024) | No, if kept inside the LKPD template | Patterns are variants with their own output schema, like the RPM kokurikuler variant |
| C5 | Embeds (AR/VR/simulations) inside the downloaded HTML | The downloaded HTML blocks all network access (D-011, CSP) | **Yes** | Embeds only as QR on paper and as iframes in the logged-in app/IFP (with a CSP `frame-src` allowlist); the offline HTML shows the QR and the fallback task |
| C6 | Rendering libraries for new blocks | Pinned, bundled, no CDN (D-033) | No | Draw blocks with SVG; no new library unless needed |
| C7 | Interactive LKPD offline | Offline is Di Kelas only, plus D-181 bundle and D-289 | No | Offline HTML file (same rules as Unduh game) |
| C8 | LKPD game patterns vs game templates | Game roadmap by learning function (D-176) | Overlap | LKPD = paper and group work, Game kelas = panel play. Papan Petak, Tiket Keluar, Kotak Kejutan share content: one LKPD can "continue in Game kelas" (`ifp_step`) |
| C9 | School presets shared across teachers | Templates in code (D-013); no public share links (D-010) | No inside one school | School library inside the school scope. Sharing across schools (marketplace) needs a decision |
| C10 | Student-facing AI (chatbot, AI feedback on student answers) | No student personal data in prompts; students are not users; teacher-owned free-tier keys (D-012) | **Yes** | AI stays teacher-side: drafting, levelling texts, rubric feedback suggestions from anonymous class patterns (as D-172). No student chat |
| C11 | Local content | Curated data reviewed by Tim Validasi (D-113) | No | Seed lists reviewed before use; the AI uses only listed items |
| C12 | Camera AR / movement activities | Camera and movement only when tied to a TP (D-180) | No | AR only when the 3D view is the learning (anatomy, bangun ruang, planets) |
| C13 | LKPD blocks for grades 1–3 | Decided for v1 (D-296) | No | This exploration extends it to grades 4–6 |
| C15 | QR on paper points to our own short redirect, so dead links can be fixed after printing | Login-only; no public pages or share links (D-010) | **Yes** | Option A: QR goes straight to the third-party URL (no clash, but dead links stay dead on paper). Option B: a public redirect route that only 302s to allowlisted URLs and shows nothing of SIGRAK (needs a decision) |
| C16 | Self-hosted H5P player and 3D models | Bundled libraries, no CDN (D-033); local disk storage (D-114) | No | Bundle `h5p-standalone`/`model-viewer` with Vite; models and H5P packages as curated assets on local disk |
| C14 | Supervision evidence | Supervision item 25 "LKPD, bahan bacaan, media" (`teacher-supervision-instruments.md`) | No | The LKPD pattern and its link to the RPM give supervision evidence for free |

## 11. Strengths, weaknesses, risks of the starter-then-adapt model

### 11.1 Strengths

| Strength | Why it matters |
|---|---|
| Fast first draft tied to curriculum data | TP, CP, fokus, and the RPM meeting are already in SIGRAK; the teacher starts at 70% instead of 0% |
| Teacher keeps control | The final LKPD reflects the class, which supervision and *pembelajaran mendalam* both expect |
| Flexible across schools | A city school with devices and a village school with only paper use the same pattern in different formats |
| Local relevance at scale | One school profile makes every LKPD local without the teacher typing context |
| Consistent quality floor | Patterns and blocks are designed and tested once; AI fills text, not layout or logic (D-015, D-030) |
| Reuse inside the school | School presets save parallel-class teachers from redoing work (as D-275) |
| Evidence loop | LKPD → Game kelas / Pindai LJK / checklist → "Perlu diulang" → next LKPD |

### 11.2 Weaknesses

| Weakness | Effect |
|---|---|
| Choice overload | Around 18 patterns × 5 formats × blocks can confuse a busy teacher |
| "Starter" used as final | Many teachers will print the draft unchanged, so generic content reaches the class |
| Paper experience is hard to measure | Most pattern value (talk, movement, collaboration) leaves no data in SIGRAK |
| Bigger build | Each pattern needs an output schema, rules, a PDF layout, and maybe an offline HTML runtime |
| Print cost | Colour, many pages, cards, and boards cost schools money; black-and-white layouts must still work |
| No student devices in most classes | "Digital interaktif" mostly means the IFP or the teacher's phone |

### 11.3 Risks and mitigations

| # | Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|---|
| R1 | AI invents wrong local facts or stereotypes a region | Medium | High | AI uses only items from the reviewed seed list and school profile (C11); a "Periksa fakta lokal" checkbox before printing |
| R2 | Content unsuitable for children (tobacco, violence, myths presented as fact) | Low–medium | High | Exclusion list in the seed data; prompt rules; Tim Validasi review |
| R3 | Pedagogical quality drops after teacher edits | Medium | Medium | Rules re-check on save (TP still covered, instructions not empty); principal validation flow already exists |
| R4 | Gemini free-tier limits with multi-step, richer LKPDs | High | Medium | Keep each LKPD to one or two calls; blocks drawn by code, not AI; reuse presets without AI |
| R5 | Third-party link rot, ads, price or licence changes | High | Medium | Allowlist, link checker, stored title, fallback task, prefer self-hosted (H5P, model-viewer) |
| R6 | Child privacy (UU PDP, PP Tunas) and Gemini terms | Medium | High | No student accounts, no student data in prompts, no student-facing AI (C1, C10) |
| R7 | Feature sprawl delays v1 (P5-05) | High | High | v1 keeps the decided LKPD + D-296 blocks; patterns come in waves (§12) |
| R8 | Overlap with Game kelas causes two places for the same activity | Medium | Low | LKPD = paper and group, Game kelas = panel; one content source (C8) |
| R9 | Copyright of embedded or adapted content (book pages, videos, school presets copied from elsewhere) | Medium | Medium | Link instead of copy; licence field on uploaded media; presets stay inside the school |
| R10 | Research evidence is thinner than it looks (small samples, local trials) | High | Low | Present studies as inspiration, not proof; measure our own use (pattern chosen, printed, edited, repeated) |
| R11 | Maintenance of many patterns across template versions | Medium | Medium | Patterns are versioned with the template (D-013); retire unused patterns based on usage |

## 12. Suggested phasing

| Wave | Scope | Fits |
|---|---|---|
| **v1 (P5-05, unchanged)** | LKPD as decided + D-296 blocks for grades 1–3. Add only the `pattern` field to the output schema so later waves need no migration | Current plan |
| **Wave 1 (v1.x)** | Decided (D-332): the 7 print-first patterns below plus Kotak Terkunci (G2) and Pikir Komputasional (S5). Originally proposed: 7 print-first patterns with low prep and the best evidence (§8.2): Detektif Teks (L1), Peta Cerita (L2), Data Kelasku (N3), Etnomatematika (N5), Misi Petualangan (G1), Tiga Jalur (D1), Tiket Keluar (D2). Learning-model presets reuse the RPM model. Numeracy and literacy blocks. Local context profile + Kudus/Jepara/Pati seed lists. Wizard suggests patterns from the RPM model | Literacy and numeracy first; cheapest to build |
| **Wave 2** | Kotak Terkunci (G2) with the offline HTML self-check, Pasar Numerasi (N1), Lab Mini (S1), Jurnal Proyek (S2) linked to the kokurikuler variant, Pikir Komputasional (S5, strong evidence, D-208). `qr_link` block with an allowlist. `ifp_step` to Game kelas. School presets | Hybrid and kokurikuler |
| **Wave 3** | Self-hosted H5P player and `model-viewer` 3D/AR with a curated model library; embeds of PhET, GeoGebra, YouTube nocookie, and Assemblr after its terms are checked; remaining patterns; more regions' seed lists | New technology |
| **Later / needs decision** | Student answers from a device, a public QR redirect, a cross-school preset marketplace, TTS audio | C1, C9, C15 |

## 13. Owner questions

Answered on 2026-11-09: **Q1** yes, v1 unchanged (D-329). **Q4** straight to the third-party URL (D-330), which settles C15 as option A. **Q5** interactive LKPD runs online, and a student session comes later (D-331), which settles C1. Later the same day: **Q2** nine patterns in Wave 1, adding Kotak Terkunci and Pikir Komputasional (D-332); **Q3** local context profile, Kudus/Jepara/Pati seed lists first (D-333); **Q6** self-host H5P and `model-viewer` (D-334); **Q7** presets inside one school (D-335); **Q8** all LKPD features core (D-336); **Q9** embed only where the licence clearly allows it, otherwise link or QR (D-337). All questions are answered.

| # | Question | Options |
|---|---|---|
| Q1 | Keep v1 LKPD as decided and start patterns in v1.x? | Yes (recommended) / Add Wave 1 to P5-05 |
| Q2 | Which patterns go first? | Wave 1 list in §12 / Owner picks from §4 |
| Q3 | Local context profile per school, with seed lists reviewed by Tim Validasi? | Yes, Kudus/Jepara/Pati first / Later |
| Q4 | QR on paper: straight to third-party URLs or through a public SIGRAK redirect (C15)? | Straight (no new public route) / Redirect (fixable links, new exception to D-010) |
| Q5 | Interactive LKPD for students: offline HTML self-check (no data back) only, or also a student session like the exam site (C1)? | Offline HTML + IFP only / Plan a student session later |
| Q6 | Self-host H5P and `model-viewer` with our own model library (Wave 3)? | Yes / Third-party links only |
| Q7 | School presets: inside one school only, or shared across schools later? | One school / Across schools later (moderation needed) |
| Q8 | Is LKPD part of Sigrak Guru core, with the IFP and embed parts in the Game kelas add-on (D-158, D-175)? | Core + add-on split / All core |
| Q9 | Check PhET and GeoGebra commercial licences, and Assemblr EDU terms, before Wave 3? | Yes / Link-only use, no embed |
