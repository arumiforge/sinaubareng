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

To be filled from the literature search (§8.1 by theme, §8.2 caveats).

## 9. Third-party AR, VR, 3D, and simulations

To be filled from the tool scan.

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
| C14 | Supervision evidence | Supervision item 25 "LKPD, bahan bacaan, media" (`teacher-supervision-instruments.md`) | No | The LKPD pattern and its link to the RPM give supervision evidence for free |

## 11. Strengths, weaknesses, risks of the starter-then-adapt model

To be filled.

## 12. Suggested phasing

To be filled.

## 13. Owner questions

To be filled.
