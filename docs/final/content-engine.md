# Content Engine: Guided Generator and Template Engine

- **Status:** final (approved 2026-10-31, D-123)
- **Task:** P1-03
- **Last updated:** 2026-11-10 (P4-24: later decisions applied, D-210…D-371)
- **Sources:** [`Konsep_Awal_Produk_SIGRAK.md`](../sources/Konsep_Awal_Produk_SIGRAK.md), [`Pemetaan_Data_Materi_Aplikasi_Lama_untuk_Refactor_RPM_Generator.md`](../sources/Pemetaan_Data_Materi_Aplikasi_Lama_untuk_Refactor_RPM_Generator.md), [`Catatan_Pengembang_Aplikasi_Lama_Design_System_Data_dan_AI.md`](../sources/Catatan_Pengembang_Aplikasi_Lama_Design_System_Data_dan_AI.md)

## Purpose

This document explains how teachers' answers become finished teaching content: templates, wizards, the generation pipeline, editing, RPM scheduling, games, the TKA practice package and exam, the links into Asesmen, versioning, the policy layer, and export. It is for developers building the *Buat* menu. The layer that talks to Gemini is specified separately in [`ai-provider-engine.md`](ai-provider-engine.md). Product context is in [`product-concept.md`](product-concept.md).

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
├── Readiness/
│   └── RpmReadiness.php                # Siap / Segera habis / Perlu RPM per class and subject (D-270)
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
6. **One optional free-text note** ("Catatan untuk bahan ini", max 300 characters) for context in the teacher's own words, such as "banyak murid belum lancar membaca". It goes into the prompt as delimited *data*, never as instructions.
7. Answers autosave as a draft. A finished generation offers "Buat ulang dengan jawaban yang sama".
8. All wizard copy comes from `lang/id` and follows [`design-system.md`](design-system.md#ui-writing-rules). UI copy says "murid" for students (D-210).

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
                + rule-set codes (D-354)
```

- **Steps.** Large templates split generation into steps so each response stays within size limits and a failure only repeats one part. Example for RPM: `design` (identification + learning design) → `meetings` (one step per batch of meetings) → `assessment` (assessment + appendices). Later steps receive earlier outputs as context. All steps of one generation use the same AI key unless failover happens (sticky selection; see the AI engine).
- **Data fields are never generated.** Identity, CP, TP codes, meeting count, minutes, and references come from data and are merged into the document after generation. AI output for those fields is ignored.
- **Background work.** Each content type has its own queued job (`GenerateRpm`, `GenerateBankSoal`, …) with its own step plan, timeout, and progress labels. The page shows progress ("Langkah 2 dari 4"), and the teacher can leave and is notified when it is done.
- **Failure messages** say what happened and what to do, with technical detail behind "Lihat detail". For example: "RPM belum berhasil dibuat. Semua kunci AI Anda sedang mencapai batas; coba lagi pukul 15.00."

## Editing and partial revision

- The preview is a structured editor per section, never raw JSON. AI text is shown as plain text (Svelte escaping; never `{@html}` on AI output).
- **"Perbaiki bagian ini"**: the teacher picks a section and an action (*sederhanakan*, *buat diferensiasi*, *sesuaikan untuk murid berkebutuhan khusus*, *perpanjang*, *perpendek*). The template defines a section-level output schema for this. The result shows old and new side by side, and the teacher accepts or discards it.
- Each AI revision or manual save keeps the previous content as a revision (last 5, D-153), so the teacher can undo.

## v1 templates

### RPM

| | |
|---|---|
| Wizard | **Kapan:** start date, or "RPM lepas jadwal" (no timetable, no ATP progress). Class and subject from the timetable. **Apa:** materi pokok and fokus. JP and meetings are computed from the data and locked (`jp_fokus_dapat_diubah_guru: false`). **Cara:** learning model per meeting (PjBL, Inkuiri & Discovery Learning, Problem Solving, Game Based Learning, Diskusi kelompok & eksplorasi) or "Biarkan otomatis", plus the optional note. When the subject has *Kelompok belajar* from per-TP analysis, the wizard offers the group counts (perlu bimbingan, cukup, siap pengayaan) as *kondisi murid*: counts only, never names (D-295). **Periksa** |
| Filled from data | Class, phase, subject, elements, CP, TP + codes, materi pokok, fokus, number of meetings, minutes per meeting (JP × 35 for SD), book references, link to the previous RPM |
| Filled by AI | *Identifikasi:* readiness and needs, material characteristics, dimensi profil lulusan (chosen only from the eight below), initial assessment (prerequisites, items). *Desain:* optional *Lintas disiplin* (related subjects), method and syntax (*Praktik Pedagogis*), *Kemitraan Pembelajaran*, *Lingkungan Pembelajaran* (physical space, virtual space, learning culture), *Pemanfaatan Digital*, media and sources. *Pengalaman belajar:* per meeting — material, opening (*Awal*), core activities following the syntax and split into the three learning experiences *Memahami*, *Mengaplikasi*, *Merefleksi*, each tagged with the principles it uses (*berkesadaran*, *bermakna*, *menggembirakan*), closing (*Penutup*), summary. One meeting may cover only part of the three experiences. *Asesmen:* formative assessment (at the start, during, and at the end of learning), principles, techniques, indicators, 4-level rubric, evaluation items, follow-up. *Lampiran:* LKPD instructions and table, cards, steps. *Kesinambungan:* summary of the last material, recommendation for the next. Where the Profil Jenjang turns KBC on (MI; D-356, D-368): suggested Tema KBC + Materi Insersi (optional, D-142) |
| Dimensi profil lulusan | A fixed set of eight, with stable codes in this order: DPL1 keimanan dan ketakwaan terhadap Tuhan Yang Maha Esa, DPL2 kewargaan, DPL3 penalaran kritis, DPL4 kreativitas, DPL5 kolaborasi, DPL6 kemandirian, DPL7 kesehatan, DPL8 komunikasi (Permendikdasmen 13/2025 Pasal 17). The AI picks from this set only. Descriptions: [`docs/data/regulations/`](../data/regulations/README.md) |
| Official frame | The sections follow the lesson-plan frame for pembelajaran mendalam: Identifikasi, Desain Pembelajaran, Pengalaman Belajar, Asesmen (Panduan Pembelajaran dan Asesmen 2025, pp. 35–39). Section labels use the official terms in `lang/id`. The format is free, so SIGRAK's layout is allowed |
| KBC (MI; SD off by default) | For MI schools, the wizard adds *Tema KBC* (one or more Panca Cinta themes) and *Materi Insersi*, and the AI weaves them into TP, activities, and assessment. Both fields are optional: the AI pre-fills a suggestion from the CP and material, and the teacher may change or clear them, so an RPM can be generated without them (D-142). This applies to every MI subject (D-126). The fields are RPM extra fields of the level's Profil Jenjang (D-356), so code never checks the level. The source is KMA 512/2026, Pedoman Kurikulum Berbasis Cinta (D-359), which replaces SK Dirjen Pendis 6077/2025. SD religion teachers do not see the KBC fields by default; a SIGRAK admin can switch them on in the Profil Jenjang or the console (D-368) |
| Kokurikuler variant | Separate output schema, one plan per kokurikuler activity, following Panduan Kokurikuler 2025: header (school, class, theme, time allocation in JP, location), A *Dimensi Profil Lulusan*, B *Tujuan Pembelajaran* (competence and content), C *Praktik Pedagogis*, D *Lingkungan Pembelajaran*, E *Kemitraan Pembelajaran*, F *Pemanfaatan Digital*, G *Kegiatan* (Memahami, Mengaplikasi, Merefleksi), H *Asesmen* (formative, plus summative with a 4-level rubric). Forms, which may be combined: *Pembelajaran Kolaboratif Lintas Disiplin* (related subjects listed), *Gerakan 7 Kebiasaan Anak Indonesia Hebat* (7 KAIH), and *Cara Lainnya* (for example a project). The seventh habit is labelled "Tidur Cepat", with "tidur tepat waktu" as its explanation (D-207). Koding dan Kecerdasan Artifisial runs here as kokurikuler content (D-208). Reads the school's annual kokurikuler plan (dimensions, theme, form, related subjects, JP, yearly phases, monitoring), approved by the principal. The plan's annual JP target comes from `subject_allocations` (`cocurricular`): SD 216/216/252/252/252/224 for grades 1–6 (Permendikdasmen 13/2025), MI per KMA 1503/2025. The school may adjust it with a note; completeness counts held kokurikuler JP against it, and kokurikuler taught by a guru mapel becomes that guru mapel's target (D-277) |
| Rules | Meeting count and minutes equal the computed values. Every TP of the topic appears in at least one meeting or indicator. The rubric has exactly 4 levels (sangat baik, baik, berkembang, perlu bimbingan). No empty sections |
| Scheduling | Meetings are placed on the class timetable from the start date. Partial lessons, shifts and undo, the JP balance, readiness, early semester-2 drafts, placing a stand-alone RPM, and parallel copies are in [RPM scheduling](#rpm-scheduling) below (D-267…D-277) |
| Policy basis | The RPM stores the rule-set codes it was made under and is the first document with the Dasar Kebijakan panel and Cek Keselarasan (P5-04). See [Policy layer](#policy-layer) |

#### RPM scheduling

Built in P5-04; the journal side comes in P5-07 and the recaps in P5-09. The legacy app's scheduler behaviour is kept where these rows do not change it.

| Topic | Behaviour |
|---|---|
| Journal outcome (D-267) | The journal records each meeting's outcome: `journals.status` ∈ {`done`, `partial`, `not_done`}. `meetings.status` ∈ {`planned`, `done`, `partial`, `not_done`} is derived from the journal and stores no reason. `partial` stores `unfinished_experiences` (a subset of memahami, mengaplikasi, merefleksi) and does not shift: they become a "Lanjutan" block at the top of the next meeting and in Game kelas. Three partials in a row for one class and subject trigger the JP balance |
| Not-done reasons (D-268) | `not_done` needs a reason: Kegiatan sekolah (`school_event`), Guru bertugas di luar (`teacher_duty`), Murid tidak hadir (`students_absent`), Bencana/cuaca (`disaster_or_weather`), Lainnya (`other`, note required). Pantau Guru shows JP lost per reason |
| Shifts and undo (D-274) | A `not_done` meeting, a calendar change, or a timetable change shifts the later meetings to the next JP slots automatically. The teacher sees "2 pertemuan bergeser: Pertemuan 3 → Kamis 13/11, …" with *Batalkan*, which restores the previous `meeting_schedule_history` version. The Promes print gets a second view, *Realisasi*, built from journals: planned week against actual week per topic |
| Neraca JP (D-269) | One calculation per class and subject: remaining ATP JP against available slot JP up to the semester's SAS (the calendar's SAS exam event), recomputed after every shift and after 3 partials in a row. Result: Pas, Kurang n JP, or Ada n JP cadangan. When short, one warning with three actions: padatkan or merge meetings (AI section revision), move the topic to the next semester, or accept with a note for the teacher's semester reflection. With a surplus, the app suggests remedial or pengayaan |
| Readiness (D-270) | `RpmReadiness` per class and subject: Siap (every slot in the next 10 effective days has a meeting), Segera habis (meetings run out within 4–10), Perlu RPM (an empty slot within 3 effective days, or a past slot without an RPM, naming the first empty date), and grey Tidak ada jadwal / Belum ada ATP. Shown in Buat, Hari Ini (yellow and red only), and Pantau Guru. Tim SIGRAK sets the defaults 10 and 3 in the console; a school operator may override them for the school, not per teacher |
| Semester 2 (D-272) | Semester-2 RPMs may be drafted at any time. Their meetings are placed only from `academic_years.even_semester_starts_on`, and Buat shows "Dijadwalkan mulai [tanggal]". When the operator changes that date, the meetings are rescheduled with the shift preview, a Kabar, and a `meeting_schedule_history` version |
| Pasang ke jadwal (D-273) | An RPM lepas jadwal (`planned_on` NULL) can be placed on one class's timetable from a start date, with the same preview as a shift. From then on it counts in ATP progress, readiness, the JP balance, and completeness |
| Pakai untuk kelas lain (D-275) | Copies an RPM to a parallel class of the same grade and subject with no AI call; its meetings are placed on the target class's timetable. Each copy then lives on its own (edits, journals, shifts) |
| Pulihkan bawaan (D-276) | In Kurikulum, the principal or operator can restore one topic ("Pulihkan bawaan") or reset all school changes ("Reset semua penyesuaian"), with confirmation and an audit log. Old `school_topic_changes` stay as history, marked reverted. Existing RPMs do not change |
| Completeness (D-267) | `done` and `partial` count as RPM and journal present; `not_done` with a reason counts as a journal and removes the slot from the RPM target |

### Bank Soal

| | |
|---|---|
| Wizard | **Cakupan:** assessment type (Penilaian Harian = one materi pokok; STS/SAS = several materi in a semester; Ujian Sekolah/Madrasah). **Komposisi:** count per type — PG, PG Kompleks (MCMA), **PGK Kategori** (benar/salah per statement, as in TKA; new), Benar-Salah, **Menjodohkan** (new), Isian, Uraian. Difficulty mix (Campuran/Mudah/Sedang/Sulit). Cognitive level mix C1–C6 or "Biarkan otomatis". **Pelaksanaan:** date and duration. **Periksa**. For grades 1–2 (Fase A) the guide recommends observation and performance over written tests, so the wizard says so in a short note. Written items stay available as an option and are never blocked (D-206). **Ambil dari soal saya dulu** fills the composition from the teacher's matching items in the item library and asks the AI only for the gap (D-294) |
| Per item | Type, stem, options, key, explanation, indicator, `kode_tp`, cognitive level, difficulty. The AI fills only this per-item data (D-293). Later, in assessment Wave A (D-351): taxonomy tags from Peta Asesmen and a misconception tag on each distractor |
| Rules | Counts match the composition. PG has exactly one key, PG Kompleks two or more. PGK Kategori has 3–5 statements, each with a key. Menjodohkan pairs are one-to-one. Every `kode_tp` is inside the chosen scope |
| Derived prints (D-293) | Six prints rendered from the item data with no extra AI call: kisi-kisi, naskah soal, kunci, pembahasan, indikator, kartu soal (PDF, Word, Excel). They follow every item edit |
| Item library (D-294) | `question_items` indexes every Bank Soal item (document, position, type, `kode_tp`, cognitive level, difficulty, last use, stats) and stays in sync with the document content, which remains the source for rendering. Recently used items are not skipped but marked "pernah dipakai" with date and class. After an assessment, item analysis writes real difficulty and discrimination back to the item (P5-08) |
| Use in Asesmen | When an assessment uses a Bank Soal document, its items are copied into assessment items with `kode_tp`, so analysis per TP needs no retyping (see [`data-model.md`](data-model.md#content)). Its answer sheet is printed and scanned with Pindai LJK (see [Links into Asesmen](#links-into-asesmen)) |

### LKPD

| | |
|---|---|
| Wizard | Materi/TP (or prefilled from an RPM's appendix) → activity kind (observation, experiment, discussion, practice) and individual or group → **Periksa**. For grades 1–3 the wizard suggests blocks by phase, Fase A first (D-296) |
| Output | Title, goal, instructions, steps, table (columns, rows, size), questions, reflection, optional blocks, and a `pattern` field (D-329), so later pattern waves need no migration. v1 (P5-05) is otherwise unchanged |
| Blocks (D-296) | A fixed set drawn by the renderer, never AI images (D-030): `trace` (letters, numbers, Arabic letters for MI), `match_lines`, `cut_and_stick`, `colour_by_rule`, `count_pictures` (bundled pictograms), `fill_table`, `short_answer`. The AI picks blocks and fills text and data |
| Patterns (*Bentuk LKPD*) | Come in waves after v1. Wave 1 has nine: Detektif Teks, Peta Cerita, Data Kelasku, Etnomatematika, Misi Petualangan, Tiga Jalur, Tiket Keluar, Kotak Terkunci (its code is checked on the teacher's sheet, in the app, or on the IFP), and Pikir Komputasional Tanpa Komputer (D-332). Wave 3 bundles a self-hosted `h5p-standalone` player and Google `model-viewer` for 3D/AR, with a curated library on local disk (D-334). Pattern library and wave plan: [`docs/drafts/lkpd-exploration.md`](../drafts/lkpd-exploration.md) |
| Local context (D-333) | The school's *Konteks Daerah* profile: local language, foods, crafts, places, festivals, occupations, stories, and themes to avoid. Seed lists start with Kudus, Jepara, and Pati and are reviewed by Tim Validasi (D-113). The AI uses only items from the profile and seed list, and the teacher confirms them |
| Links and interactive steps | A `qr_link` block encodes the allowlisted third-party URL itself; there is no SIGRAK redirect route (D-330). A dead link is covered by the stored title and description, a printed no-device task, and an in-app link checker. Interactive steps (embeds, H5P, self-check) run online only, in the teacher's app and the IFP present session; a student session comes later with its own design (D-331). A third-party tool is embedded only when its licence clearly allows use in a paid product, recorded in the embed allowlist; otherwise link or QR only (D-337) |
| Presets and plan | A teacher can offer an adapted LKPD to the school library; other teachers of the same school start from it without AI. No sharing across schools (D-335). Every LKPD feature is core; Game kelas templates stay in the add-on (D-336) |
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
| Item forms | Only the three TKA forms: **PG** (one answer), **PGK MCMA** (several correct options), **PGK Kategori** (each statement answered with one label pair: Benar/Salah, Sesuai/Tidak Sesuai, or Setuju/Tidak Setuju; D-045). Items may be single (*soal tunggal*) or grouped on a shared stimulus (*soal grup*) |
| Filled by AI | Stimulus texts, item stems, options, keys, short explanations, and the competency/sub-competency tag for each item |
| Rules | Bahasa Indonesia stimulus texts are 150–200 words (poems excepted), with short simple sentences and mostly denotative vocabulary, as the framework describes. Every item is tagged with a framework competency and form. The package's form and competency mix stays within the configured bounds. Each PGK Kategori item has 3 statements (as in the official samples). There are no Isian or Uraian items |
| Stimulus visuals | Never AI images (D-030). Tables, pictograms, and bar charts are generated as **structured data** and drawn by our renderer. Plane shapes, nets, and measurement pictures come from a **bundled figure set** that the AI picks by id. **Cube stacks** (*susunan kubus*, front/top/side views) are generated as a height map and drawn with three.js, and the key for "what does the front view look like" is **computed by code**, not by the AI |
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
| Results | Score 0–100 per subject. Teachers and participants see four levels: Istimewa (≥ 95.00), Baik (76.67 to < 95), Memadai (50 to < 76.67), Kurang (< 50). Data and exports store the official category (Baik, Memadai, Kurang) and the Istimewa predicate separately in `exam_results`, so they match the SHTKA (Kepmendikdasmen 56/2026; D-041, D-120, D-371). The UI notes that these are practice results and that official TKA results (SHTKA) complement rapor scores in jalur prestasi and SPMB. Plus per-competency and per-item analysis, export to Excel and PDF, and an optional copy into Asesmen |
| Scoring (practice) | PG: 1 point for the key. PGK MCMA and PGK Kategori: **partial credit** per correct choice or statement (D-030). Totals are normalised to the TKA SD range **0–100**. The official TKA item-scoring model is not published, so the practice score is an *estimate* and the UI says so |

The general Bank Soal "Mode Ujian" stays inactive (D-026). The exam client is built for TKA packages only.

## Links into Asesmen

Design detail stays in P4-06 and P5-08 ([`docs/drafts/assessment-landscape.md`](../drafts/assessment-landscape.md)). This section lists what touches content.

- **Pindai LJK (D-286, P5-08).** Built in the Asesmen slice as its own task; Kartu Jawab (D-174) later reuses its camera and roster code. The printed LJK follows the sheet schools already use (D-325): a header with Mata Pelajaran, Kelas, Nama, and Nomor; PG letter boxes in two columns; numbered lines for Isian and Uraian; plus four corner markers and a QR that holds only the sheet's random `public_id` (D-287). Scanned forms: PG, PG Kompleks, Benar-Salah and PGK Kategori, and Menjodohkan. Boxes are read in the teacher's browser with a JavaScript OMR library; only the answers and their confidence go to the server, photos are never uploaded, and the offline queue stays (D-369, D-289).
- **Handwritten answers (D-370).** A follow-up task after Pindai LJK, once text recognition is tested on SD handwriting. The phone reads Uraian and Isian into text and the teacher corrects it; the photo never leaves the phone. Only the answer text, tagged `nomor absen-nomor soal` (e.g. `12-3`), goes to the AI with the key or rubric. The AI suggests a score and feedback; the teacher decides.
- **Assessment AI without student data (D-347, amended by D-369 and D-370).** Teacher-side AI makes instruments, tags misconceptions, and drafts feedback from class patterns. Names on paper are read on the teacher's device and matched to the roster there. No student personal data goes to an AI provider (D-023).
- **Later instrument templates (D-346).** One template per kind, each with its own wizard (D-024): Rubrik Kinerja, Observasi DPL, Cek Kesiapan Awal (the result is a profile, not a score), Akomodasi Murid, and self and peer assessment cards. Wave A (D-351) brings Peta Asesmen data v1, tags on Bank Soal and LKPD, Rubrik Kinerja, Observasi DPL with phone entry, Cek Kesiapan Awal, and misconception tags on distractors. P5-08 itself is unchanged.
- **From P4-04 (one line each):**
  - *Catatan wali kelas:* the AI drafts the rapor note from attendance counts, note types, and per-TP results, with no student name or personal data in the prompt (`PromptDataGuard`, D-036); the wali kelas edits and approves it (D-213).
  - *KKTP per TP* (D-214, A3) is the criterion that per-TP analysis reads.
  - *Diagnostic assessment* at the start of the year (D-214, A2).
  - *Remedial and pengayaan* from per-TP analysis (D-214, A1); the JP balance suggests them when there is a surplus (D-269).
  - *Kelompok belajar* per TP feeds the group counts to the next RPM wizard as kondisi murid, never names (D-295).

## Games: hybrid approach, presenting, downloading

**Hybrid by design.** The vanilla JS runtime owns all structure, which is roughly 70–90% of a game (a design target from the owner's concept, not a benchmark). AI supplies content JSON and picks config values from the allowed options. Every game made from one template version therefore behaves the same.

**No AI-written code in v1 (D-015).** If a teacher wants something the template lacks, "Usulkan fitur" records a request (`template_feature_requests`) for the team. A sandboxed AI-code option may come later with its own design.

**More game templates (D-176).** After Quiz Battle, new game templates follow the learning-function roadmap of Game kelas, the IFP learning mode (D-171): Lomba Tim, Pilah & Kelompokkan, and Pasangkan first. Design: [`ifp-classroom.md`](ifp-classroom.md).

**Tayangkan (present mode).** This is a fullscreen route inside the teacher's session. It uses large type for projectors, works with keyboard or clicker, and keeps scores on the teacher's device only. It stores no student data.

**Unduh game (standalone HTML, D-011).** This is a single `.html` file:

- The runtime JS and CSS are inlined. Content is embedded as `<script type="application/json" id="content">` with `<` escaped. The runtime renders every content string with `textContent`, never `innerHTML`.
- A Content-Security-Policy meta tag blocks all network access: `default-src 'none'; script-src 'unsafe-inline'; style-src 'unsafe-inline'; img-src data:`. The file plays offline and cannot phone home.
- Images and sounds are embedded as `data:` URIs (v1 uses only bundled assets).
- It contains teaching content only, plus the game title. It holds no student data and no teacher identity beyond what the teacher typed into the title.
- The runtime version is embedded. Re-downloading uses the runtime of the document's template version.

The downloaded file lives outside the login wall by design. That is acceptable because it holds nothing but teaching content.

## Versioning

- `template_versions` rows are **immutable once a generation uses them**. A prompt change made in Filament creates a new version, with no deploy needed.
- A document stores `template_key` and `template_version`. Renderers stay backward-compatible within a template. An incompatible output-schema change needs a new version plus either an `upgrade(content, fromVersion)` step or a kept old renderer.
- A section revision uses the document's own template version, not the newest one.
- A document also stores the rule-set codes it was made under (D-354), so a rule change never silently changes it. See [Policy layer](#policy-layer).

## Policy layer

SIGRAK's position is *Berpijak pada kebijakan, leluasa berinovasi*: national rules fix the what and the limits, and the school and teacher choose the how (D-352; [`docs/drafts/policy-innovation-layers.md`](../drafts/policy-innovation-layers.md)). The RPM is the first document under this layer.

- **Rule-set codes on the document (D-354, D-355).** Every RPM stores the rule-set codes it was made under, from the first RPM made in P5-04. Before P5-04, `curriculum_versions` gains `effective_on`, `applies_from_school_year`, and `supersedes_id`, and a policy-profile resolver (`PolicyProfile`, resolved from school year, level, and region) replaces the `config/curriculum.php` `allocation_versions` pointer, with D-230 behaviour unchanged. The full `regulations`, `regulation_relations`, `rule_sets`, and `rules` tables, the console for every domain, and Kabar Regulasi are a target for later (P5-09 or their own slice).
- **Profil Jenjang (D-356).** RPM extra fields per level, such as the MI KBC fields (D-368 for SD), are read from the level's Profil Jenjang record. Code never branches on `level == mi`.
- **Dasar Kebijakan panel and Cek Keselarasan (D-358).** Both start with the RPM in P5-04 and grow with each slice. The panel shows the CP, TP, DPL, principles, and the regulation and rule-set version, read from the document's taxonomy tags. Cek Keselarasan runs on save and in principal and supervisor review, and never rewrites the teacher's work.
- **Rule strength decides the reaction (D-353).** Strengths: `mandatory`, `minimum`, `maximum`, `recommended`, `not_recommended`, `example`, `school_discretion`, `teacher_discretion`, `free`. Clear L1/L2 `mandatory` rules block; below a `minimum` or above a `maximum` warns; `recommended` and `not_recommended` give a hint; the rest is free. Defaults that are only examples are labelled "Contoh".
- **Templates tag content with taxonomy codes (D-357).** Codes come from the master taxonomy in `docs/data/taxonomy/` (draft until Tim Validasi review). An RPM gets curriculum codes (CP, TP), profile codes (DPL), and learning codes (principles, practice); a LKPD gets its pattern and assessment purpose; Bank Soal items get assessment tags (D-351).

## Export

This is the single export engine from the developer notes. The engines are decided in D-028:

- **One document model** (the content JSON). Every format is rendered from it, never from a screenshot.
- **PDF: Gotenberg 8** (headless Chromium in a Docker container), called through `spatie/laravel-pdf` with its Gotenberg driver:
  - Text-based PDFs from Blade/HTML views, with full CSS: tables with `page-break-inside: avoid`, F4 and A4, repeating header and footer with page numbers, and the letterhead image.
  - JavaScript runs before capture (`waitUntilReady`), so KaTeX formulas, charts, and three.js figures render in print exactly as on screen.
  - It shapes Arabic script correctly, which PAI material needs.
  - **One PDF per download.** A package (RPM + LKPD + rubric + key, or all exam cards) is one HTML view with page breaks, so no PDF merging is needed. Word-to-PDF conversion is not used either (D-031). The one exception is Berkas Administrasi: its parts are rendered as separate PDFs and joined with Gotenberg's merge route (D-280).
  - Why a separate service:
    - Chromium is isolated in a container. Ubuntu 22.04 only ships Chromium as a snap, which is awkward to run from PHP workers.
    - Its memory use stays out of PHP-FPM.
    - Export jobs on the `exports` queue call it over HTTP. It is bound to localhost with basic auth, and the image version is pinned.
  - Rejected alternatives: Dompdf/mPDF (weak CSS and pagination, no JavaScript), Browsershot and the chrome-php driver (Node and/or Chromium on the host itself), and WeasyPrint (no JavaScript, an extra Python dependency).
- **DOCX:** PhpWord, built from SIGRAK's own Word layouts per template, with the letterhead image placed in the header and the signers as text. Schools do not supply Word templates.
- **Excel: Laravel Excel** (`maatwebsite/excel`, current major version, on PhpSpreadsheet). It handles styled multi-sheet exports (leger, kisi-kisi, analysis, TKA results), formulas, queued exports for large files, and chunked imports with row validation (student import, scores). Numbers are stored as numbers and columns are filterable. Confirm Laravel 13 support when installing.
- **Letterhead (kop) as an image (D-031).**
  - The school uploads a default letterhead image, and a teacher may upload their own, which replaces the school's on that teacher's documents.
  - The layout, logo, and divider line are all inside the image, so the teacher controls them. The system only places the image across the content width at the top of the first page.
  - Image rules: PNG or JPG, white or transparent background, at least 2,000 px wide, width-to-height ratio between 4:1 and 8:1, at most 2 MB. Printed height is capped at 4 cm.
  - The upload screen previews the image on A4 and F4 and warns when the resolution is too low for print.
- **Other print settings (per school):** signer names and NIP (text), A4/F4, margins, font (Arial or Times New Roman, D-125), and which sections to include.
- **Labels in Indonesian** through translation keys (repo convention).
- **Buka di Canva (level 1):** downloads DOCX/PDF and opens canva.com for the teacher to import. Direct import through the Canva Connect API is *Later*.

## Rendering libraries

These are added only where a template needs them (D-033). Every library is pinned, bundled by Vite, and never loaded from a CDN. The game runtime and the exam client each bundle only what they use.

| Library | Used for | Where |
|---|---|---|
| **three.js** (with its SVG renderer for print) | Cube-stack and solid figures from data (front/top/side views); room for future 3D games | TKA package, Bank Soal, LKPD previews, exam client, PDF |
| **KaTeX** | Fractions, operations, and units in Matematika items | Editor previews, exam client, games, PDF |
| **ApexCharts** (TailAdmin's chart library) | Bar charts in item stimuli; score analysis and portal summaries | App, exam client (stimuli only), PDF |
| **Noto Naskh Arabic** (or Amiri) font | Arabic text in PAI material | App, PDF, DOCX |
| Bundled figure and pictogram set (SVG) | Plane shapes, nets, measurement pictures, pictogram icons | All templates (D-030) |
| JavaScript OMR library (chosen in P5-08) | Reading LJK boxes in the teacher's browser (D-369) | App (Pindai LJK) |
| **h5p-standalone** (MIT) and Google **model-viewer** (Apache-2.0), later | H5P packages and 3D/AR models from a curated local library (LKPD Wave 3, D-334) | App, IFP present session |

There is no presentation library: a slide feature is not planned (D-033). AI-generated images are not used.

## Open questions

- [ ] Exact step split for the RPM template, to be tuned against real output sizes.
