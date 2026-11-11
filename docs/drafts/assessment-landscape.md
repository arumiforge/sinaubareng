# Assessment Landscape SD/MI → SIGRAK features

- **Status:** draft; owner decided 2026-11-09 (D-344…D-351); D-347 refined 2026-11-10 by D-369 (OMR in the teacher's browser) and D-370 (handwritten answers read on the phone). Accepted parts applied to `docs/final/` 2026-11-10 (P4-24); design detail continues in P4-19
- **Task:** P4-18
- **Last updated:** 2026-11-10
- **Input:** the owner's *Master Assessment Landscape SD/MI* (owner message, 2026-11-09): 10 need groups, purposes, methods, media, analysis, users, AN/TKA, 18 modules, a master taxonomy, MAP-01…MAP-08, five principles
- **Builds on:** P5-08 (Asesmen slice), D-213…D-215, D-286…D-295, D-172, D-174, D-026…D-030, P4-06 (assessment design, todo)

## 1. Summary

The owner's direction fits SIGRAK. Map every assessment need first, then decide the tools, and serve the teacher, class, and school as well as the student. SIGRAK already covers more of the map than it seems: Bank Soal, the per-TP analysis, KKTP, remedial, rapor, Pindai LJK, kokurikuler and 7 KAIH, Game kelas class results, and the TKA practice exam.

This draft translates the map into SIGRAK in three moves:

1. **Peta Asesmen as data, not as 18 engines.** The owner's taxonomy (domain, purpose, method, media, response, scoring, follow-up) becomes one curated dataset in `docs/data/assessment/`, reviewed by Tim Validasi. Every instrument SIGRAK makes or records carries these tags. A game can then be "diagnostic, digital literacy, scenario, interactive, process analysis" at the same time, which is exactly the owner's point.
2. **A few new instrument templates, one per content type (D-024).** Observation sheet, performance/project/product rubric, start-of-year readiness check, oral and reading record, and self and peer assessment cards. Most are paper-first, and the teacher records results on a phone in Di Kelas.
3. **A guided "five questions" wizard in Asesmen:**
   - *Apa yang diukur?*
   - *Untuk apa?*
   - *Bagaimana mengukurnya?*
   - *Bukti apa?*
   - *Tindak lanjutnya apa?*

   It picks from the taxonomy and suggests instruments for paper, digital, or both, so teachers never face 18 modules.

Some parts of the map are **not right for SIGRAK now**, mainly because of child data and the Gemini terms: IRT/Rasch per school, prediction models on children, AI on children's voice or handwriting, and diagnostic labels for special needs or talent. §6 lists them with safer alternatives.

## 2. The map against what SIGRAK has

Status: **Ada** = decided or in v1 · **Parsial** = partly covered · **Baru** = new feature proposed here · **Nanti** = needs the student session (D-331) or another decision · **Tidak** = not recommended.

### 2.1 Student assessment (owner groups 01–08, 2.10)

| Owner group | SIGRAK today | Status | Proposed |
|---|---|---|---|
| 01 Readiness and initial profile | A2 start-of-year diagnostic (D-214): short prerequisite items per TP + non-cognitive checklist, result as class groups | Parsial | **Cek Kesiapan Awal** template for grade 1 and new students: letter/sound recognition, writing readiness, number sense, following instructions, fine motor, done as an observation checklist + short oral tasks; result is a per-student profile, not a score |
| 02 Academic competence (SD and MI subjects) | Bank Soal, assessments per TP, KKTP, rapor; MI subjects from KMA 1503/2025 and SK 9941/2025 (`religious-education.md`) | Ada | Tag items with the taxonomy; MI subject areas (e.g. Al-Qur'an Hadis: membaca, menghafal, memahami) become subdomains so tahfiz and reading are assessed by performance, not only by written items |
| 03 Literacy and numeracy (cross-subject, 15 domains) | TKA practice for reading and math; LKPD patterns (P4-16) | Parsial | Cross-subject domains as taxonomy tags on any item or task; **Peta Literasi-Numerasi** per class from tagged items across subjects; financial literacy through numeracy tasks (Pasar Numerasi), not a new subject |
| 04 Character and graduate profile (8 DPL) | Kokurikuler assessment per DPL (A5), 7 KAIH tracker (A6), Catatan Siswa (anecdotal), catatan wali kelas (D-213) | Parsial | **Observasi DPL**: short behaviour indicators per DPL (jujur, disiplin, tanggung jawab, empati, peduli lingkungan, toleransi, tekun, mandiri, gotong royong, kepemimpinan), observed several times, summarised as trends; never a single multiple-choice "character score" |
| 05 Social-emotional and well-being | A2 non-cognitive checklist (well-being) | Parsial | **Cek Rasa** class check-in (emoji scale, anonymous class totals by default, like D-225 murid feedback); individual SEL notes stay in Catatan Siswa, visible to the class's teachers and principal only |
| 06 Skills and performance | Portfolio photos (A10), PJOK and Seni in rapor | Parsial | **Rubrik Kinerja** template (praktik, demonstrasi, proyek, produk) with a phone checklist per student; MI ibadah practice (wudu, salat, bacaan) as ready rubrics reviewed by Tim Validasi |
| 07 Metacognition and learning process | RPM *Merefleksi*, LKPD reflection | Parsial | Self-assessment cards and "Seberapa yakin?" marks on paper items (yakin / ragu / menebak); confidence analysis on Pindai LJK sheets later; error/misconception analysis (§4) |
| 08 Interest, talent, potential | Ekstrakurikuler records (D-216) | Parsial | **Eksplorasi Minat** short survey and teacher observation, shown as "sedang dijelajahi", never as a permanent talent label |
| 09-inclusion (owner 2.9) | — | Baru | **Akomodasi Murid**: per-student accommodation record (extra time, large print, read-aloud of instructions, oral answer allowed, breaks), used by printed tests (large-print layout) and later the student session; **Perlu dukungan** screening checklist with a fixed note that it is not a diagnosis |
| Kokurikuler and ekstrakurikuler (owner 2.10) | Kokurikuler assessment (A5), ekstrakurikuler with Pramuka (D-216), rapor rows | Ada | Same Rubrik Kinerja and Observasi DPL reused for activities |

### 2.2 Learning and programme evaluation (owner groups 09–10)

| Owner group | SIGRAK today | Status | Proposed |
|---|---|---|---|
| 09 Learning and class evaluation | Per-TP analysis, daya serap (D-295), item analysis (A7), teacher semester reflection (A4), journal completeness, Game kelas class results (D-172) | Ada | **Ringkasan Kelas** per period: TP mastered, groups, misconceptions, follow-ups done |
| 10 School quality and programme | Supervision instruments (P4-12), PKKS (D-308), Pusat Tindak Lanjut (D-310), completeness (D-263) | Parsial | School dashboard by subject and grade; import the school's **Rapor Pendidikan** indicators by hand for planning (PBD), so internal results sit beside AN results |

### 2.3 External and standardised (owner §8)

| Owner item | SIGRAK today | Status | Proposed |
|---|---|---|---|
| TKA SD/MI | Paket Soal TKA + CBT practice (D-026, D-027, D-030) | Ada | Keep |
| AN: AKM | — | Baru (small) | AKM-style literacy and numeracy items as a Bank Soal tag and LKPD stimuli, **not** a separate AKM simulator: AN is a system survey, not a student score |
| AN: Survei Karakter, Survei Lingkungan Belajar | — | Tidak | SIGRAK does not copy national surveys; it reads the school's Rapor Pendidikan results (above) |

### 2.4 The owner's 18 modules against SIGRAK

| Module | SIGRAK | Status |
|---|---|---|
| Assessment Planning | Rencana Penilaian, kisi-kisi from Bank Soal | Ada |
| Assessment Bank | Item library (D-294); rubric and observation library | Ada / Baru |
| Assessment Builder | Five-questions wizard + instrument templates | Baru |
| Test Engine | Paper + Pindai LJK (D-286); CBT for TKA only | Ada; general CBT Nanti (D-331) |
| Interactive Engine | Game kelas (IFP), LKPD interactive steps online | Ada / P4-16 |
| Performance Assessment | Rubrik Kinerja | Baru |
| Observation Module | Observasi DPL, Catatan Siswa | Parsial |
| Portfolio Module | Portofolio murid (A10) → evidence store for any instrument | Parsial |
| AI Assessment | Teacher-side only (§6) | Tidak for children's input; Parsial for teacher help |
| Adaptive Engine | — | Tidak now (§6) |
| Scoring Engine | Scores, rubric levels, partial credit (TKA) | Ada |
| Analytics Engine | Per-TP analysis, item analysis, growth per TP | Ada / Baru (growth) |
| Diagnostic Engine | Diagnostic A2 + misconception tags | Parsial |
| Intervention Engine | Remedial and enrichment (A1, D-291), Kelompok belajar (D-295), Perlu diulang | Ada |
| Reporting Engine | Rapor 12 components, e-Rapor/RDM export (D-218) | Ada |
| Inclusion Support | Akomodasi Murid | Baru |
| School Dashboard | Principal portal summaries | Parsial |
| External Assessment Support | TKA; Rapor Pendidikan import | Ada / Baru |

### 2.5 Operational needs (owner 9.1)

Covered or decided:

- multi-school tenancy
- curriculum versions (D-229, D-317)
- Fase A–C and grades 1–6
- Excel import and export
- answer-sheet scanning
- item shuffling (TKA)
- audit of score changes
- role-based access
- data protection rules

Gaps:

- **parallel forms** for Bank Soal (Paket A/B from the same blueprint, no AI needed: shuffle plus item swaps from the library)
- **makeup sessions** (*susulan*) for assessments
- **item sharing between teachers inside the school** (like the LKPD school presets)
- **parent-facing reports** (printed or WhatsApp text per D-215; a parent portal stays upcoming, D-220)

## 3. Peta Asesmen: the taxonomy as data

One dataset, `docs/data/assessment/`, in the same style as the curriculum and regulation data (`status: draft` until Tim Validasi review, D-113).

| File | Holds | From the owner's map |
|---|---|---|
| `domains.json` | Group → domain → subdomain → indicator, with codes (e.g. `LIT-BACA-EVAL`), phase and grade ranges, SD/MI flags, link to CP elements and DPL | MAP-02, §2 |
| `purposes.json` | Screening, placement, diagnostic, formative, summative, progress monitoring, dynamic, readiness, transition, follow-up, each mapped to the official formative/summative categories of Panduan Pembelajaran dan Asesmen 2025 | MAP-03, §3 |
| `methods.json` | Methods, instrument forms, media (paper, digital, IFP, mixed), response forms, with "works without a device" flag | MAP-04, §4–5 |
| `scoring.json` | Criterion, ipsative, rubric, CTT item analysis, error/misconception analysis, growth; each with the minimum data it needs | MAP-06, §6 |
| `uses.json` | Decisions a result can feed (remedial, enrichment, grouping, support, report, parent talk, programme review) and who sees it | MAP-01, §7 |

**Instrument tags.** Every Bank Soal item, LKPD, Game kelas activity, observation sheet, and rubric gets `domain_codes`, `purpose`, `method`, `media`, `response`, `scoring`. The AI may suggest tags at generation time (teacher side, no student data), and the teacher can change them. Reports group by any tag.

**The owner's eight documents.** MAP-01…MAP-08 need not be eight separate documents:

- MAP-01…04 and MAP-06 become this dataset plus this draft.
- MAP-05 already exists as the curriculum data.
- MAP-07 is §5 of this draft.
- MAP-08 is §2.4.

The MAP codes stay as section references, which saves writing and keeps one source.

## 4. How it looks for the teacher

**Asesmen › Buat instrumen**, the five questions:

| Step | Teacher sees | Behind it |
|---|---|---|
| 1 Apa yang diukur? | TP from the curriculum, or a domain from Peta Asesmen (literasi data, kolaborasi, kesiapan membaca…) | `domains.json`, CP, DPL |
| 2 Untuk apa? | Awal tahun / sebelum materi / selama belajar / akhir materi / setelah remedial | `purposes.json` |
| 3 Bagaimana? | 2–3 suggested methods with media badges: *Kertas*, *Tanpa perangkat*, *Papan interaktif*, *Perlu internet* | `methods.json` filtered by grade, domain, purpose, the class's devices |
| 4 Bukti apa? | The evidence to collect (answers, checklist, photo, product) and the rubric | Instrument template |
| 5 Tindak lanjut? | Shown after results: groups, remedial/enrichment ideas, next RPM *kondisi murid* (counts only, D-295) | `uses.json`, A1 |

**Recording on a phone (Di Kelas).** Observation and rubric results are entered like attendance: the roster, a tap per indicator level, and offline sync. That extends D-016/D-289 to observation and rubric entry.

**Misconception analysis.** When the AI writes a distractor in Bank Soal, it also tags the misconception behind it, such as "menjumlahkan pembilang dan penyebut". Pindai LJK then counts the chosen distractors per class and shows "miskonsepsi terbanyak". No student data goes to the AI; the tag is made before any student answers.

**Growth (ipsative).** Per student and per TP across assessments over the year: a simple line of levels, never a ranking.

## 5. Innovation matrix (MAP-07)

| Innovation | Value for SD/MI | SIGRAK fit | When |
|---|---|---|---|
| Game-based assessment | Engagement, process data | Class level now (Game kelas, D-172); per student with the student session | Now / Nanti |
| Scenario and simulation tasks | Real-life literacy and numeracy | LKPD patterns + PhET/GeoGebra embeds (P4-16) | Wave 2–3 |
| Confidence marks | Metacognition | Paper "yakin/ragu" column, read by Pindai LJK | Baru |
| Error / misconception analysis | Targeted remedial | Distractor tags (§4) | Baru |
| Multimodal evidence (photo, audio, video) | Skills, reading aloud, ibadah practice | Photos now (A10); audio and short video need disk limits (D-114) | Photo now, audio later |
| AI scoring help for essays | Saves teacher time | Only with anonymised text the teacher pastes or confirms; never children's handwriting images or voice to the free tier (§6) | Needs decision |
| Adaptive testing | Precise level | Needs a large calibrated item bank and the student session | Not now |
| IRT / Rasch | Item calibration | Classes of 10–30 are too small; possible only with a pooled, cross-school bank (new decision) | Not now |
| Predictive analytics | Early support | Use transparent rules (like the absence warning H2) instead of a model on children | Rules only |
| Digital trace / process analytics | How a student works | Only inside the student session, with clear limits | Nanti |

## 6. Cautions and clashes

| # | Idea from the map | Existing rule or law | Clash? | Proposed way |
|---|---|---|---|---|
| A1 | Parents, GPK, dinas/Kemenag as users | Students are records; no parent accounts (D-010, D-215); parent portal upcoming (D-220); owner keeps school roles simple | **Yes for new account types** | Parents get printed reports and WhatsApp text (D-215). A GPK is a teacher with a *pendamping* assignment on certain students, not a new role. Dinas/Kemenag only through aggregated pengawas reports (P5-09) |
| A2 | Student-facing CBT, adaptive, game assessment per student | Exam site only for TKA (D-026, D-027) | Planned | Builds on the student session (D-331) as a later task |
| A3 | AI on children's handwriting, voice, or essays | Gemini terms: users 18+, service not for under-18s, free-tier inputs may be reviewed by people; UU PDP Art. 25 (children's data); AI users are adults and no student data in prompts (D-023); PromptDataGuard (D-036) | **Yes** | Teacher-side AI only: generating instruments, tagging misconceptions, drafting feedback from anonymous class patterns. Pindai LJK reads bubbles on the device, not with AI |
| A4 | Character, SEL, talent, and special-needs results | Sensitive data rules (D-036 for religion, D-215 for health) | Risk | Same handling as health data: class teachers, operator, principal only; never to AI; no labels such as "berbakat" or diagnoses; trends from several observations |
| A5 | Screening versus diagnosis | — | Risk | Every screening screen shows "Ini bukan diagnosis" and suggests a referral; no medical or psychological terms as results |
| A6 | Norm-referenced comparison | — | Risk | Class and school aggregates only; no student rankings shown to students or parents |
| A7 | AN simulation | AN is a system survey, not a student score | Risk of misuse | AKM-style tags and stimuli only; Rapor Pendidikan import for planning |
| A8 | Formative scores in the rapor | Formative never becomes a rapor grade (D-205, D-290) | No | Taxonomy purposes map to formative or summative; only summative feeds grades |
| A9 | Many new instruments vs one template per type | D-024 | No | Each instrument kind is its own small template |
| A10 | Offline recording | Offline is Di Kelas only (D-016, D-289) | Extends | Observation and rubric entry join Di Kelas offline (decision needed) |

## 7. Suggested phasing

| Wave | Scope | Fits |
|---|---|---|
| **v1 (P5-08, unchanged)** | Rencana, scores, per-TP analysis, KKTP, remedial, rapor, Pindai LJK, item analysis, diagnostic A2, kokurikuler A5, 7 KAIH | Already decided |
| **P4-06 design (next)** | Use this draft as P4-06's input: taxonomy dataset shape, tag fields on items, instruments list, wizard | Design task |
| **Wave A** | Peta Asesmen data v1 (domains for literacy, numeracy, DPL, readiness; purposes; methods), tags on Bank Soal and LKPD, Rubrik Kinerja, Observasi DPL with phone entry, Cek Kesiapan Awal, misconception tags | Paper-first, low risk |
| **Wave B** | Akomodasi Murid and large-print tests, Ringkasan Kelas, growth per TP, confidence marks, parallel forms, makeup sessions, item sharing in the school, Rapor Pendidikan import | Inclusion and analysis |
| **Wave C** | Student session (D-331): digital tests, per-student game assessment, process data within limits; audio evidence | Device-based |
| **Not planned** | IRT/Rasch per school, adaptive engine, predictive models on children, AI on children's input, national survey copies | §5, §6 |

## 8. Owner questions

Answered on 2026-11-09:

- **AQ1** one dataset (D-344).
- **AQ2** a separate design task, P4-19 (D-345).
- **AQ3** all instrument templates (D-346).
- **AQ4** teacher-side AI now. Names are read on the device, multiple choice by OMR on SIGRAK's server, and essay text without student data may go to Google AI; reading handwriting and voice with AI comes later (D-347). *Amended 2026-11-10:* OMR runs in the teacher's browser and only results reach the server (D-369); handwritten Uraian/Isian are read into text on the phone, and only the text with a nomor absen + nomor soal code goes to the AI (D-370).
- **AQ5** GPK as a teacher assignment (D-348).
- **AQ6** offline entry (D-349).
- **AQ7** health-data rule (D-350).
- **AQ8** Wave A as listed (D-351).


| # | Question | Options |
|---|---|---|
| AQ1 | Make Peta Asesmen one curated dataset (`docs/data/assessment/`) instead of eight separate MAP documents? | Yes (recommended) / Eight documents |
| AQ2 | Use this draft as the input for P4-06 (assessment design)? | Yes / Separate task |
| AQ3 | Accept the new instrument templates: Rubrik Kinerja, Observasi DPL, Cek Kesiapan Awal, Akomodasi Murid, self/peer cards? | All / Pick |
| AQ4 | Keep AI away from children's handwriting, voice, and essays (teacher-side AI only)? | Yes (recommended) / Allow anonymised essay text with teacher confirmation |
| AQ5 | GPK as a teacher assignment rather than a new role? | Yes (recommended) / New role |
| AQ6 | Observation and rubric entry offline in Di Kelas (extends D-016)? | Yes / Online only |
| AQ7 | Sensitive results (SEL, special needs, talent) follow the health-data rule (D-215)? | Yes (recommended) / Other |
| AQ8 | Wave A scope as listed in §7? | Yes / Change |
