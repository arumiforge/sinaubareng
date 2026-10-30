# Content Engine: Guided Generator and Template Engine

- **Status:** draft (owner review)
- **Task:** P1-03
- **Last updated:** 2026-10-30
- **Sources:** [`Konsep_Awal_Produk_SIGRAK.md`](../sources/Konsep_Awal_Produk_SIGRAK.md), [`Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md`](../sources/Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md), [`Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md`](../sources/Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md)

## Purpose

This document explains how teachers' answers become finished teaching content: templates, wizards, the generation pipeline, editing, games, versioning, and export. It is for developers building the *Buat* menu. The layer that talks to Gemini is specified separately in [`ai-provider-engine.md`](ai-provider-engine.md). Product context is in [`product-concept.md`](product-concept.md).

## Terms

| Term | Meaning |
|---|---|
| **Template** | A developer-built definition of one content type (RPM, Bank Soal, LKPD, Quiz Battle, Buat bebas) |
| **Wizard** | The questions a template asks the teacher. Pedagogical only |
| **Specification** | A typed, validated object built from wizard answers plus data the system already has. It is the only input to prompt building |
| **Generation** | One run of a template for one teacher. It has one or more steps and produces a document |
| **Document** | The saved result: structured JSON content that the teacher edits, exports, presents, or downloads |
| **Renderer** | Code that turns document content into a preview/editor, PDF, DOCX, Excel, or a playable game |

## Two generators, one model

The owner's concept names two generator types. Both are built on the same template model, so they share the wizard, pipeline, editor, and export code.

| | Developer templates | Buat bebas (Guided Generator) |
|---|---|---|
| Examples | RPM, Bank Soal, LKPD, Quiz Battle | A reading passage, a material summary, a rubric, a reflection sheet, … |
| Structure | Fixed by the template: sections, fields, layout, game mechanics | A generic document: headings, paragraphs, lists, tables |
| Wizard | Template-specific questions | Broader questions: for whom (class, subject, topic/TP), kind of material (from a list the SIGRAK team maintains), form and length, language style, optional note |
| Teacher writes a prompt? | No | No |

"Buat bebas" is therefore just another template (key `guided`) with a wider wizard and a generic output schema. It is **not** a chat box.

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
│   ├── BankSoal/  Lkpd/  QuizBattle/  Guided/
├── Generation/
│   ├── SpecificationBuilder.php
│   ├── PromptBuilder.php
│   ├── OutputValidator.php
│   ├── OutputRepairer.php
│   └── GenerationRunner.php            # runs steps, calls app/AI, stores results
└── Export/                             # PdfExporter, DocxExporter, ExcelExporter, GameExporter

resources/js/Templates/<Key>/            # Svelte preview + section editors
resources/game-runtime/quiz-battle/      # vanilla JS + CSS, built by Vite as a standalone bundle
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
- **Background work.** Generation runs on the queue. The page shows progress ("Langkah 2 dari 4"), and the teacher can leave and is notified when it is done.
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
| Wizard | **Cakupan:** assessment type (Penilaian Harian = one materi pokok; STS/SAS = several materi in a semester; Ujian Sekolah/Madrasah). **Komposisi:** count per type — PG, PG Kompleks, Benar-Salah, **Menjodohkan** (new), Isian, Uraian. Difficulty mix (Campuran/Mudah/Sedang/Sulit). Cognitive level mix C1–C6 or "Biarkan otomatis". **Pelaksanaan:** date and duration. **Periksa** |
| Per item | Type, stem, options, key, explanation, indicator, `kode_tp`, cognitive level, difficulty |
| Rules | Counts match the composition. PG has exactly one key, PG Kompleks two or more. Menjodohkan pairs are one-to-one. Every `kode_tp` is inside the chosen scope |
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

### Buat bebas (`guided`)

| | |
|---|---|
| Wizard | **Untuk siapa:** class, subject, topic/TP. **Jenis bahan:** from the team's list. **Bentuk:** length, form, language style. **Catatan** (optional) → **Periksa** |
| Output | Generic document: title, sections of headings, paragraphs, lists, tables |
| Note | The list of material kinds lives in the template registry, so the team can extend it without code changes to the wizard |

## Games: hybrid approach, presenting, downloading

**Hybrid by design.** The vanilla JS runtime owns all structure, which is roughly 70–90% of a game (a design target from the owner's concept, not a benchmark). AI supplies content JSON and picks config values from the allowed options. Every game made from one template version therefore behaves the same.

**No AI-written code in v1 (D-015).** If a teacher wants something the template lacks, "Usulkan fitur" records a request (`template_feature_requests`) for the team. A sandboxed AI-code option may come later with its own design.

**Tayangkan (present mode).** This is a fullscreen route inside the teacher's session. It uses large type for projectors, works with keyboard or clicker, and keeps scores on the teacher's device only. It stores no student data.

**Unduh game (standalone HTML, D-011).** This is a single `.html` file:

- The runtime JS and CSS are inlined. Content is embedded as `<script type="application/json" id="content">` with `<` escaped. The runtime renders every content string with `textContent`, never `innerHTML`.
- A Content-Security-Policy meta tag blocks all network access: `default-src 'none'; script-src 'unsafe-inline'; style-src 'unsafe-inline'; img-src data:`. The file plays offline and cannot phone home.
- Images and sounds are embedded as `data:` URIs (v1 uses only bundled assets).
- It contains teaching content only, plus the game title. It holds no student data and no teacher identity beyond what the teacher typed into the title. The free tier adds a small "Dibuat dengan Sigrak" footer.
- The runtime version is embedded. Re-downloading uses the runtime of the document's template version.

The downloaded file lives outside the login wall by design. That is acceptable because it holds nothing but teaching content.

## Versioning

- `template_versions` rows are **immutable once a generation uses them**. A prompt change made in Filament creates a new version, with no deploy needed.
- A document stores `template_key` and `template_version`. Renderers stay backward-compatible within a template. An incompatible output-schema change needs a new version plus either an `upgrade(content, fromVersion)` step or a kept old renderer.
- A section revision uses the document's own template version, not the newest one.

## Export

This is the single export engine from the developer notes:

- **One document model** (the content JSON). Every format is rendered from it, never from a screenshot.
- **PDF:** text-based, rendered on the server, with `page-break-inside: avoid` per table and activity, and a preview before download. *Engine choice is open:* headless Chromium (for example Browsershot) gives the best HTML/CSS fidelity but needs Chromium on the server; mPDF is pure PHP but supports less CSS. To be decided in the export task.
- **DOCX:** from the school's Word template (PhpWord TemplateProcessor), so letterhead, font, and signatures change without code.
- **Excel:** separate sheets, filterable columns, numbers stored as numbers (Bank Soal kisi-kisi, Asesmen).
- **Per-school print settings:** letterhead, logo, signer name and NIP, A4/F4, margins, font (default Arial or Times New Roman), and which sections to include.
- **Labels in Indonesian** through translation keys (repo convention).
- **Buka di Canva (level 1):** downloads DOCX/PDF and opens canva.com for the teacher to import. Direct import through the Canva Connect API is *Later*.
- Free tier: small watermark.

## Open questions

- [ ] PDF engine: Chromium (Browsershot) or mPDF.
- [ ] First list of material kinds for *Buat bebas*.
- [ ] Images in LKPD and games: bundled icon set only in v1, or teacher uploads? (AI-generated images: not in v1.)
- [ ] Exact step split for the RPM template, to be tuned against real output sizes.
