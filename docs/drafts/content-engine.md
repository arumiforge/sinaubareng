# Content Engine: Guided Generator and Template Engine

- **Status:** draft (owner review)
- **Task:** P1-03
- **Last updated:** 2026-10-30
- **Sources:** [`Konsep_Awal_Produk_SIGRAK.md`](../sources/Konsep_Awal_Produk_SIGRAK.md), [`Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md`](../sources/Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md), [`Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md`](../sources/Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md)

## Purpose

This document explains how teachers' answers become finished teaching content: templates, wizards, the generation pipeline, editing, games, the TKA practice package and exam, versioning, and export. It is for developers building the *Buat* menu. The layer that talks to Gemini is specified separately in [`ai-provider-engine.md`](ai-provider-engine.md). Product context is in [`product-concept.md`](product-concept.md).

## Terms

| Term | Meaning |
|---|---|
| **Template** | A developer-built definition of one content type (RPM, Bank Soal, LKPD, Quiz Battle, Paket Soal TKA) |
| **Wizard** | The questions a template asks the teacher. Pedagogical only |
| **Specification** | A typed, validated object built from wizard answers plus data the system already has. It is the only input to prompt building |
| **Generation** | One run of a template for one teacher. It has one or more steps and produces a document |
| **Document** | The saved result: structured JSON content that the teacher edits, exports, presents, or downloads |
| **Renderer** | Code that turns document content into a preview/editor, PDF, DOCX, Excel, or a playable game |

## One template per content type, no generic generator

The owner's concept names a *Guided Generator* (a multi-step wizard) and *Developer Templates*. In SIGRAK they are one thing: **every template has its own guided wizard**. There is no generic "make any material" generator (D-024). A teacher can already get generic content by chatting with Gemini, so SIGRAK only offers content types where a template adds value Gemini alone does not:

- curriculum data (CP, TP, ATP, JP) filled in automatically;
- a fixed, school-ready structure and print format;
- validation rules (counts, keys, TP coverage, TKA forms);
- classroom delivery (present mode, game download, CBT exam).

Each content type has its own template class, its own wizard, its own generation job, and its own renderers. They share only infrastructure: the pipeline classes, the AI gateway, the editor shell, and the export engine.

## Template anatomy

Each template has the nine parts from the owner's concept. Templates live in code, with a database registry on top (D-013).

| Part | Holds | Lives in | Changed by |
|---|---|---|---|
| Metadata | Key, name and description (translation keys), category (`document` / `game`), icon, supported grades and subjects | Code + `templates` registry row (active flag, sort order) | Developer; team toggles active |
| Wizard configuration | Steps, questions, options, defaults, prefill sources, show/hide conditions | Code | Developer |
| Input schema | JSON Schema of the specification | Code | Developer |
| Prompt instructions | System and task instructions, style rules, examples | Code (baseline) + optional override per version, edited in Filament | Developer; team tunes wording without a deploy |
| Output schema | JSON Schema of the AI output, also sent to Gemini as the structured-output schema | Code | Developer |
| Design | Print layout for documents; theme and assets for games | Code | Developer |
| Validation rules | Business rules beyond the schema (see each template below) | Code | Developer |
| Renderers | Svelte preview/editor, server PDF/DOCX/Excel views, vanilla JS game runtime | Code | Developer |
| Version | Integer per template. A `template_versions` row ties a code version to a prompt override | `template_versions` table | Created on deploy or on prompt override |

Proposed folder layout:

```
app/Content/
├── Templates/
│   ├── Contracts/Template.php          # interface every template implements
│   ├── TemplateResolver.php            # key + version → template instance
│   ├── TemplateVersionManager.php      # creates/pins versions, applies prompt overrides
│   ├── Rpm/
│   │   ├── RpmTemplate.php             # metadata, wizard, steps, rules
│   │   ├── schemas/specification.json
│   │   ├── schemas/output.json         # + output-kokurikuler.json
│   │   └── prompts/instructions.md
│   ├── BankSoal/  Lkpd/  QuizBattle/  TkaPackage/
├── Generation/
│   ├── SpecificationBuilder.php
│   ├── PromptBuilder.php
│   ├── OutputValidator.php
│   ├── OutputRepairer.php
│   └── GenerationRunner.php            # shared step runner used by every job below
└── Export/                             # PdfExporter, DocxExporter, ExcelExporter, GameExporter

app/Jobs/Generation/                     # one job per content type (D-024)
├── GenerateRpm.php
├── GenerateBankSoal.php
├── GenerateLkpd.php
├── GenerateQuizBattle.php
├── GenerateTkaPackage.php
└── ReviseSection.php                    # "Perbaiki bagian ini", any template

resources/js/Templates/<Key>/            # Svelte preview + section editors
resources/game-runtime/quiz-battle/      # vanilla JS + CSS, built by Vite as a standalone bundle
resources/exam-client/                   # TKA exam site front end (separate bundle, no AI)
resources/views/exports/<key>/           # Blade views for PDF/DOCX
```

This separates the two concerns the owner's concept asked to keep apart. `app/AI/` (see [`ai-provider-engine.md`](ai-provider-engine.md)) knows nothing about education. `app/Content/` knows nothing about Gemini.

## Wizard rules

1. **Ask only what a teacher knows pedagogically:** class, subject, topic and learning goal (picked, not typed), students' condition, duration, difficulty, number of items, learning model, and assessment type.
2. **Never ask what the system knows:** JP, number of meetings, minutes, CP text, phase, TP codes, school identity, and dates from the timetable.
3. **Prefill** class and subject from the timetable slot for the chosen date, and from the teacher's assignments.
4. **The curriculum chain is pick-only:** Kelas → Mata pelajaran → Semester → Materi pokok (ATP) → Fokus → TP, all read from the curriculum data. Grades 7–12 stay hidden.
5. **At most 4 steps**, one screen each on a phone. Every question has a default. Where AI can reasonably choose (for example profile dimensions, or the learning model per meeting), offer "Biarkan otomatis".
6. **One optional free-text note** ("Catatan untuk bahan ini", max 300 characters) for context in the teacher's own words, such as "banyak siswa belum lancar membaca". It goes into the prompt as delimited *data*, never as instructions.
7. Answers autosave as a draft. A finished generation offers "Buat ulang dengan jawaban yang sama".
8. All wizard copy comes from `lang/id` and follows [`design-system.md`](design-system.md#ui-writing-rules).

## Generation pipeline

```
Wizard answers
   │
   ▼
SpecificationBuilder ── validates against the input schema,
   │                    enriches from DB (curriculum, JP, timetable, school)
   ▼
Specification (stored on the generation, immutable)
   │
   ▼  for each template step:
PromptBuilder ── template version's instructions + specification as JSON data
   │              + earlier step outputs + output schema
   ▼
AI Provider Engine ── sticky key, structured JSON output (app/AI)
   │
   ▼
OutputValidator ── JSON Schema + template rules
   │  invalid?
   ├──► OutputRepairer ── 1) deterministic fixes (trim, coerce numbers, drop unknown keys)
   │                      2) at most one AI repair call that receives the validation errors
   │    still invalid → step fails with a teacher-friendly message
   ▼
Step output accepted (stored once per step)
   │
   ▼
Document saved: content JSON + template key + template version
```

- **Steps.** Large templates split generation into steps so each response stays within size limits and a failure only repeats one part. Example for RPM: `design` (identification + learning design) → `meetings` (one step per batch of meetings) → `assessment` (assessment + appendices). Later steps receive earlier outputs as context. All steps of one generation use the same AI key unless failover happens (sticky selection; see the AI engine).
- **Data fields are never generated.** Identity, CP, TP codes, meeting count, minutes, and references come from data and are merged into the document after generation. AI output for those fields is ignored.
- **Background work.** Each content type has its own queued job (`GenerateRpm`, `GenerateBankSoal`, …) with its own step plan, timeout, and progress labels. The page shows progress ("Langkah 2 dari 4"), and the teacher can leave and is notified when it is done.
- **Failure messages** say what happened and what to do, with technical detail behind "Lihat detail". For example: "RPM belum berhasil dibuat. Semua kunci AI Anda sedang mencapai batas; coba lagi pukul 15.00."

## Editing and partial revision

- The preview is a structured editor per section, never raw JSON. AI text is shown as plain text (Svelte escaping; never `{@html}` on AI output).
- **"Perbaiki bagian ini"**: the teacher picks a section and an action (*sederhanakan*, *buat diferensiasi*, *sesuaikan untuk siswa berkebutuhan khusus*, *perpanjang*, *perpendek*). The template defines a section-level output schema for this. The result shows old and new side by side, and the teacher accepts or discards it.
- Each AI revision or manual save keeps the previous content as a revision (last 10), so the teacher can undo.

## v1 templates

### RPM

| | |
|---|---|
| Wizard | **Kapan:** start date, or "RPM lepas jadwal" (no timetable, no ATP progress). Class and subject from the timetable. **Apa:** materi pokok and fokus. JP and meetings are computed from the data and locked (`jp_fokus_dapat_diubah_guru: false`). **Cara:** learning model per meeting (PjBL, Inkuiri & Discovery Learning, Problem Solving, Game Based Learning, Diskusi kelompok & eksplorasi) or "Biarkan otomatis", plus the optional note. **Periksa** |
| Filled from data | Class, phase, subject, elements, CP, TP + codes, materi pokok, fokus, number of meetings, minutes per meeting (JP × 35 for SD), book references, link to the previous RPM |
| Filled by AI | *Identifikasi:* readiness and needs, material characteristics, profile dimensions, initial assessment (prerequisites, items). *Desain:* method, syntax, learning partners, physical space, learning culture, digital use, media and sources. *Pengalaman belajar:* per meeting — material, opening, core activities following the syntax, closing, summary. *Asesmen:* formative assessment, principles, techniques, indicators, 4-level rubric, evaluation items, follow-up. *Lampiran:* LKPD instructions and table, cards, steps. *Kesinambungan:* summary of the last material, recommendation for the next |
| Kokurikuler variant | Separate output schema: background, yearly goal, success indicators, procedure, yearly phases (phase, range, focus, activities), teacher role, student role, monitoring frequency, periodic reflection. Types: Pembelajaran Kolaboratif Lintas Disiplin, Gerakan 7 KAIH, Projek. Reads the school's kokurikuler plan |
| Rules | Meeting count and minutes equal the computed values. Every TP of the topic appears in at least one meeting or indicator. The rubric has exactly 4 levels (sangat baik, baik, berkembang, perlu bimbingan). No empty sections |
| Scheduling | Meetings are placed on the class timetable. A meeting marked "tidak terlaksana" in the journal shifts the rest to the next JP slot. Semester 2 RPMs wait for the even-semester start date (behaviour carried over from EDUNEXA's scheduler) |

### Bank Soal

| | |
|---|---|
| Wizard | **Cakupan:** assessment type (Penilaian Harian = one materi pokok; STS/SAS = several materi in a semester; Ujian Sekolah/Madrasah). **Komposisi:** count per type — PG, PG Kompleks (MCMA), **PGK Kategori** (benar/salah per statement, as in TKA; new), Benar-Salah, **Menjodohkan** (new), Isian, Uraian. Difficulty mix (Campuran/Mudah/Sedang/Sulit). Cognitive level mix C1–C6 or "Biarkan otomatis". **Pelaksanaan:** date and duration. **Periksa** |
| Per item | Type, stem, options, key, explanation, indicator, `kode_tp`, cognitive level, difficulty |
| Rules | Counts match the composition. PG has exactly one key, PG Kompleks two or more. PGK Kategori has 3–5 statements, each with a key. Menjodohkan pairs are one-to-one. Every `kode_tp` is inside the chosen scope |
| Exports | Kisi-kisi, naskah soal, kunci, pembahasan, indikator, kartu soal (PDF, Word, Excel) |
| Use in Asesmen | When an assessment uses a Bank Soal document, its items are copied into assessment items with `kode_tp`, so analysis per TP needs no retyping (see [`data-model.md`](data-model.md#content)) |

### LKPD

| | |
|---|---|
| Wizard | Materi/TP (or prefilled from an RPM's appendix) → activity kind (observation, experiment, discussion, practice) and individual or group → **Periksa** |
| Output | Title, goal, instructions, steps, table (columns, rows, size), questions, reflection |
| Design | Print layout for A4 and F4, school letterhead optional |

### Quiz Battle (game)

| | |
|---|---|
| Wizard | **Soal:** use a saved Bank Soal set (auto-gradable types only: PG, Benar-Salah) or make new questions from materi/TP. **Permainan:** teams (2–4) or individual, seconds per question (10–60), points, show the explanation after each question, number of rounds. **Periksa** |
| Filled by AI | Questions, answers, explanations (when new), a short hint per question, team names themed to the topic, an intro line |
| Owned by the runtime | Layout, controls, timer, scoring, turn order, animations, sounds, keyboard and remote control, responsive sizes, accessibility |
| Rules | Every config value is inside the template's allowed ranges/enums. Every question has a valid key |

### Paket Soal TKA (`tka_package`)

This template produces a practice package that follows the official TKA framework for SD/MI. The framework is the Kerangka Asesmen TKA (Perka BSKAP 047/H/AN/2025), published on the [Pusmendik TKA site](https://pusmendik.kemendikdasmen.go.id/tka) and checked on 2026-10-30.

| | |
|---|---|
| Wizard | **Mata uji:** Bahasa Indonesia or Matematika. **Cakupan:** all framework content (default) or chosen elements/competencies for focused practice. **Tingkat:** mixed (default), easier, or harder. **Jumlah:** 30 items (TKA default) or a shorter practice set. **Periksa** |
| Official framework (data, not AI) | *Bahasa Indonesia* measures reading only. Text types are informational and fiction. Competencies: pemahaman tekstual, pemahaman inferensial, evaluasi dan apresiasi, with the sub-competencies of the framework matrix. *Matematika* covers the elements Bilangan, Geometri dan Pengukuran, and Data. Competencies are memahami, mengaplikasikan, and bernalar, with the sub-elements and limits of the framework matrix |
| Item forms | Only the three TKA forms: **PG** (one answer), **PGK MCMA** (several correct options), **PGK Kategori** (each statement answered benar/salah, sesuai/tidak sesuai, or mendukung/tidak mendukung). Items may be single (*soal tunggal*) or grouped on a shared stimulus (*soal grup*) |
| Filled by AI | Stimulus texts, item stems, options, keys, short explanations, and the competency/sub-competency tag for each item |
| Rules | Bahasa Indonesia stimulus texts are 150–200 words (poems excepted), with short simple sentences and mostly denotative vocabulary, as the framework describes. Every item is tagged with a framework competency and form. The package's form and competency mix stays within the configured bounds. Each PGK Kategori item has 3 statements (as in the official samples). There are no Isian or Uraian items |
| Stimulus visuals | Tables, pictograms, and bar charts are generated as **structured data** and drawn by our renderer, never as AI images. Geometry figures (nets, solid views) come from a bundled figure set or a teacher upload (see Open questions) |
| Output | The package document: items in order, keys, explanations, blueprint (*kisi-kisi*) per competency. Exports: naskah, kunci, pembahasan, kisi-kisi (PDF/Word/Excel). It is also the source for a **Simulasi TKA** session (below) |

## TKA practice exam (CBT)

The exam mimics how the national TKA runs: computer-based, the participant signs in with a login card, a proctor token is required, it is timed, and the result is reported as a score with a category. Students are not app users (D-010), so the exam runs on a **separate exam site** with session-scoped credentials (D-027).

| Part | Behaviour |
|---|---|
| Session | Created by a teacher or the operator from a *Paket Soal TKA*. It has a date, a time window, a duration (75 minutes by default, the TKA duration for each SD subject), the participating grade 6 classes, and a proctor (the teacher) |
| Exam cards | One *kartu login* per student: name, class, session, username, password. Printed as PDF (several cards per page). Credentials work only for that session and expire when it closes |
| Token | A 6-character token derived from the session secret and the current 15-minute window (TOTP-style), so nothing needs storing. The proctor screen shows the current token. Participants need it to start or resume |
| Exam client | A plain, TKA-like screen at its own subdomain: question area, number grid, *ragu-ragu* flag, timer, font size control. Works on desktop and tablet. **No AI calls, no links into the app, no data beyond the participant's own exam** |
| Integrity | Item and option order is shuffled per participant (stimulus groups stay together). Every answer autosaves with a sequence number (idempotent), so a reconnect resumes where it stopped. Time is enforced by the server. The proctor can reset a participant's login or extend time for one participant, and both actions are logged |
| Results | Score 0–100 per subject, mapped to the TKA categories (Kurang < 50; Memadai 50 to < 76.67; Baik 76.67 to < 95; Istimewa ≥ 95, as reported for TKA 2026). Plus per-competency and per-item analysis, export to Excel and PDF, and an optional copy into Asesmen |
| Scoring (practice) | PG: 1 point for the key. PGK MCMA and PGK Kategori: partial credit per correct choice or statement, normalised to 0–100. The official TKA scoring model is not published in this form, so the practice score is an *estimate* and the UI says so |

The general Bank Soal "Mode Ujian" stays inactive (D-026). The exam client is built for TKA packages only.

## Games: hybrid approach, presenting, downloading

**Hybrid by design.** The vanilla JS runtime owns all structure, which is roughly 70–90% of a game (a design target from the owner's concept, not a benchmark). AI supplies content JSON and picks config values from the allowed options. Every game made from one template version therefore behaves the same.

**No AI-written code in v1 (D-015).** If a teacher wants something the template lacks, "Usulkan fitur" records a request (`template_feature_requests`) for the team. A sandboxed AI-code option may come later with its own design.

**Tayangkan (present mode).** This is a fullscreen route inside the teacher's session. It uses large type for projectors, works with keyboard or clicker, and keeps scores on the teacher's device only. It stores no student data.

**Unduh game (standalone HTML, D-011).** This is a single `.html` file:

- The runtime JS and CSS are inlined. Content is embedded as `<script type="application/json" id="content">` with `<` escaped. The runtime renders every content string with `textContent`, never `innerHTML`.
- A Content-Security-Policy meta tag blocks all network access: `default-src 'none'; script-src 'unsafe-inline'; style-src 'unsafe-inline'; img-src data:`. The file plays offline and cannot phone home.
- Images and sounds are embedded as `data:` URIs (v1 uses only bundled assets).
- It contains teaching content only, plus the game title. It holds no student data and no teacher identity beyond what the teacher typed into the title. The Sigrak Dasar (free) plan adds a small "Dibuat dengan Sigrak" footer.
- The runtime version is embedded. Re-downloading uses the runtime of the document's template version.

The downloaded file lives outside the login wall by design. That is acceptable because it holds nothing but teaching content.

## Versioning

- `template_versions` rows are **immutable once a generation uses them**. A prompt change made in Filament creates a new version, with no deploy needed.
- A document stores `template_key` and `template_version`. Renderers stay backward-compatible within a template. An incompatible output-schema change needs a new version plus either an `upgrade(content, fromVersion)` step or a kept old renderer.
- A section revision uses the document's own template version, not the newest one.

## Export

This is the single export engine from the developer notes. The engines are decided in D-028:

- **One document model** (the content JSON). Every format is rendered from it, never from a screenshot.
- **PDF: Gotenberg 8** (headless Chromium + LibreOffice in one Docker container), called through `spatie/laravel-pdf` with its Gotenberg driver:
  - Text-based PDFs from Blade/HTML views, with full CSS: tables with `page-break-inside: avoid`, F4 and A4, repeating header and footer with page numbers, and the school letterhead.
  - It shapes Arabic script correctly, which PAI material needs.
  - LibreOffice converts a school's DOCX template to PDF, so the Word and PDF versions look the same.
  - It also merges PDFs (an RPM with LKPD, rubric, and key attachments as one file), stamps the Sigrak Dasar watermark, and can produce PDF/A for archiving.
  - It runs as a separate service on the internal network only (bound to localhost, with basic auth). Chromium's memory use stays out of PHP-FPM, and export jobs on the `exports` queue call it over HTTP.
  - Pin the image version.
  - Rejected alternatives: Dompdf/mPDF (weak CSS and pagination), Browsershot (needs Node, Puppeteer, and Chromium installed on the host itself), and WeasyPrint (excellent paged media, but no JavaScript, an extra Python dependency, and no Office conversion).
- **DOCX:** PhpWord TemplateProcessor from the school's Word template, so letterhead, font, and signatures change without code.
- **Excel: Laravel Excel** (`maatwebsite/excel`, current major version, on PhpSpreadsheet). It handles styled multi-sheet exports (leger, kisi-kisi, analysis, TKA results), formulas, queued exports for large files, and chunked imports with row validation (student import, scores). Numbers are stored as numbers and columns are filterable. Confirm Laravel 13 support when installing.
- **Per-school print settings:** letterhead, logo, signer name and NIP, A4/F4, margins, font (default Arial or Times New Roman), and which sections to include.
- **Labels in Indonesian** through translation keys (repo convention).
- **Buka di Canva (level 1):** downloads DOCX/PDF and opens canva.com for the teacher to import. Direct import through the Canva Connect API is *Later*.
- Sigrak Dasar (free plan): small watermark.

## Open questions

- [ ] Images in LKPD, games, and TKA geometry items: a bundled figure/icon set only in v1, or teacher uploads too? (AI-generated images: not in v1.)
- [ ] TKA practice scoring: keep partial credit for PGK, or score all-or-nothing per item?
- [ ] Exact step split for the RPM template, to be tuned against real output sizes.
