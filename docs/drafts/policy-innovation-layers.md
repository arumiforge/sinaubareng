# Policy Foundation and Innovation Layers

- **Status:** draft; owner decided 2026-11-09 (D-352…D-359, see §12). Accepted parts applied to `docs/final/` 2026-11-10 (P4-24: product-concept, data-model, architecture, content-engine, religious-education); this draft stays the design reference for the later waves of §13
- **Task:** P4-21
- **Last updated:** 2026-11-10
- **Input:** owner idea of 2026-11-09: "build on national curriculum and policy first, then give the widest room for innovation on top of that foundation"
- **Builds on:** `docs/drafts/dokumen-resmi-sd-mi.md` (P4-03), `docs/drafts/administration-features.md` §1 (P4-04), `docs/data/regulations/`, `docs/data/curriculum/`, `docs/final/data-model.md`, `docs/final/religious-education.md`, `docs/drafts/assessment-landscape.md` (P4-18), D-113, D-203…D-210, D-226…D-234, D-317, D-339, D-344

## 1. Summary and position

**Proposed position for SIGRAK:** *Berpijak pada kebijakan, leluasa berinovasi.*

- **National standards fix the what and the limits.** They set graduate outcomes (SKL), learning outcomes (CP), the learning load (JP), the minimum content of a lesson plan and a report card, and the protection of children.
- **The school and the teacher choose the how.** The KSP, TP/ATP, KKTP, methods, media, instruments, themes, and schedule are theirs.
- **SIGRAK provides the tools** that make new ways of learning possible, practical, and accountable. Every innovation in SIGRAK can show which national element it serves.

The research for this draft confirms the owner's idea. The 2025–2026 rules lock outcomes, structure, and reporting. They do not lock methods. Permendikdasmen 1/2026 (Standar Proses) sets only the *minimum content* of a planning document. ATP and lesson-plan templates in the official guides are explicitly examples. So aligning with central policy does not narrow innovation. It tells SIGRAK *where* the walls are, so everything inside them can be free.

Three moves turn the idea into the product:

1. **A layer model with a strength for every rule** (§2). Each rule in SIGRAK says who set it (nation, region, school, teacher, SIGRAK) and how strong it is (mandatory, minimum, maximum, recommended, example, free). The app blocks only the few hard national rules, warns at caps and floors, suggests on recommendations, and leaves the rest free.
2. **Policy as versioned data** (§6). Regulations, their amendments, and the rule sets derived from them are stored as data with effective dates and school years, not hard-coded. A document made in SIGRAK remembers which rule set it was made under.
3. **One master taxonomy** (§9) that names every element (DPL, CP elements, principles, methods, assessment purposes, LKPD patterns, …) once, with a stable code, a source, a strength, and an SD/MI scope. Features tag their content with it, and the tags are what make "this innovation serves that standard" visible.

SD and MI share one core and differ through a **Profil Jenjang** (§8), not through two apps.

## 2. The layer model

| Layer | Who sets it | Examples | In SIGRAK |
|---|---|---|---|
| **L0 Law** | DPR, President | UU 20/2003 Sisdiknas, PP 57/2021 as amended by PP 4/2022 (Standar Nasional Pendidikan) | Reference only |
| **L1 Binding national standards** | Minister (Kemendikdasmen); Minister of Religious Affairs for madrasah | Permendikdasmen 10/2025 (SKL), 12/2025 (Standar Isi), 1/2026 (Standar Proses), Permendikbudristek 21/2022 (Standar Penilaian), Permendikbudristek 12/2024 as amended by Permendikdasmen 13/2025 (Kurikulum), Permendikdasmen 26/2025 (Pengelolaan), 6/2026 (Budaya Sekolah), 9/2026 (AN), KMA 1503/2025 (madrasah curriculum) | Locked, versioned, edited only by SIGRAK staff after Tim Validasi review |
| **L2 National references** | Kepmen, Kepala BSKAP/BKPDM, Dirjen Pendis, official guides | CP (Kepka BSKAP 046/H/KR/2025 as amended by BKPDM 020/2026; SK Dirjen Pendis 9941/2025), Kepmendikdasmen 126/P/2025 (Pembelajaran Mendalam), 127/P/2025 (KKA), Panduan Pembelajaran dan Asesmen 2025, Panduan KSP 2025, Panduan Kokurikuler 2025, SK Dirjen Pendis 6077/2025 (KBC), Perka BSKAP 047/H/AN/2025 (TKA) | CP text is binding content; guide rules carry their own strength (many are "contoh"). Shown as defaults; schools may change what the guide leaves open, with a trace |
| **L3 Region** | Pemda, Dinas, Kanwil/Kemenag kab | Muatan lokal (Permendikbudristek 12/2024 Pasal 26), kalender pendidikan provinsi, regional supervision and PKKS forms | `region_subjects`, region calendars (D-256), regional variants (D-324, D-327) |
| **L4 School** | Principal (KSP is "ditetapkan oleh kepala Satuan Pendidikan", Pasal 30) | KSP, TP/ATP choice, KKTP approach, promotion criteria, kokurikuler themes, schedule, enabled subjects, presets | `school_subjects`, school curriculum changes (D-019), presets (D-335), overrides (D-212, D-277) |
| **L5 Teacher and class** | Teacher (Standar Proses Pasal 4(2): planning "dilakukan oleh Pendidik") | RPM, methods, media, LKPD, instruments, games, class agreement | Every content template |
| **L6 SIGRAK innovation** | SIGRAK | Game kelas, Pindai LJK, AI-assisted templates, Peta Asesmen, Konteks Daerah, analytics | Must name the L1/L2 element it serves and may never contradict an L1 mandatory rule |

A higher layer bounds a lower one, never the other way. A lower layer may always go *beyond* a floor (more JP inside the MI +6 room, more assessment than the minimum, more report content than the 12 components) unless a cap says otherwise.

### 2.1 Rule strength

The learning and assessment guide data already tags each rule with `force` (`mandatory` 22, `recommended` 23, `optional` 16, `school_discretion` 8, `not_recommended` 1; `docs/data/regulations/learning-assessment-guide-bskap.json`). This draft extends that vocabulary to every rule and gives each value one app behaviour:

| Strength (`force`) | Meaning | App behaviour | Existing example |
|---|---|---|---|
| `mandatory` | Must be done exactly | **Block** (cannot save) | A compulsory subject cannot be switched off (D-230); formative results never feed the rapor; PAI, Bahasa Indonesia and Pendidikan Pancasila may not be merged (KSP guide pp. 34, 41) |
| `minimum` | Floor; more is allowed | **Warn** below the floor | Rapor 12 minimum components; teacher reflection at least once a semester |
| `maximum` | Cap; less is allowed | **Warn** above the cap | Muatan lokal 72 JP/year in SD (D-232) |
| `recommended` | The guide advises it | **Suggest** (shown as a hint, default on) | Two-component TP wording |
| `not_recommended` | The guide advises against it | **Suggest** (hint, default off, still allowed) | Written tests in Fase A (D-206) |
| `example` | Given as a model only | **Default** that can be replaced | Government ATP, RPM layout, KBC methods and instruments (KMA 512/2026 pp. 16–21) |
| `school_discretion` | The school decides | Free, set by the principal or operator | KKTP approach, promotion criteria, kokurikuler themes |
| `teacher_discretion` | The teacher decides | Free, set by the teacher | Methods, media, instruments |
| `free` | No rule at all | Free | SIGRAK's own features |

**Block only what is clearly mandatory in an L1 or L2 text.** Everything else is a warning or a hint. This keeps SIGRAK a *guardrail, not a gate*: a school that innovates is never stopped, it only sees how its choice relates to the rule.

## 3. What the government actually fixes

Sources: repo extracts (`docs/drafts/dokumen-resmi-sd-mi.md`, `docs/data/regulations/*.json`) and web checks on 2026-11-09. Official JDIH sites returned 503/403 on that day, so several items come from the pasal.id mirror ("belum ditinjau") or secondary sites and are marked.

| Standard | Fixed (L1/L2) | Left to the school or teacher | Check |
|---|---|---|---|
| **SKL** (Permendikdasmen 10/2025) | Graduate profile as 8 dimensi profil lulusan: keimanan dan ketakwaan, kewargaan, penalaran kritis, kreativitas, kolaborasi, kemandirian, kesehatan, komunikasi | How each dimension is developed and observed | secondary |
| **Standar Isi** (Permendikdasmen 12/2025) | "Kriteria minimal" for the scope of material | Depth beyond the minimum, local context | secondary |
| **Standar Proses** (Permendikdasmen 1/2026, set 2 Jan, in force 5 Jan 2026, revokes Permendikbudristek 16/2022) | Planning is done by the teacher (Pasal 4(2)); the planning document "paling sedikit memuat" objectives, steps, and assessment (Pasal 5); learning "dengan saling memuliakan melalui olah pikir, olah hati, olah rasa, dan olah raga" (Pasal 3(1)); a learning framework (Pasal 12); process review by the teacher, and optionally by peers, the principal, and murid (Pasal 15–16) | **Method, model, template, and format.** The rule sets content minimums, not a method | pasal.id; Pasal 9 text not readable |
| **Standar Penilaian** (Permendikbudristek 21/2022, still in force) | Results reported "dalam rapor atau bentuk laporan … lainnya"; procedure guide set by the curriculum and assessment unit | School sets the mechanism for promotion and graduation | pasal.id |
| **Kurikulum** (Permendikbudristek 12/2024 as amended by 13/2025) | CP set by the senior official for curriculum (Pasal 11); SD/MI content "dalam bentuk mata pelajaran" (Pasal 13); load as a yearly allocation (Pasal 15); JP tables, kokurikuler load, 8 DPL, ekstrakurikuler with Pramuka; Bahasa Inggris an elective until it becomes compulsory in 2027/2028 (Pasal 33); KKA an elective from 2025/2026 | KSP "dengan prinsip diversifikasi sesuai dengan Satuan Pendidikan, potensi daerah, dan Peserta Didik" (Pasal 29(2)); thematic or block organisation (guide level); weekly distribution of yearly JP; kokurikuler themes and timing; which seni branch; muatan lokal delivery | pasal.id (original text; 13/2025 changes from the repo extract) |
| **Pembelajaran Mendalam** (13/2025 Pasal 3; Kepmendikdasmen 126/P/2025) | The approach in the kerangka dasar; principles berkesadaran, bermakna, menggembirakan; experiences memahami, mengaplikasi, merefleksi; four framework parts (praktik pedagogis, kemitraan, lingkungan, pemanfaatan digital) | Which pedagogical practice, model, or medium fulfils them | repo extract |
| **Asesmen** (Panduan Pembelajaran dan Asesmen 2025) | Formative is never used for rapor, promotion, or graduation; rapor 12 minimum components; competency description names highest and lowest achievement; report at least each semester | ATP format ("Tidak ada format komponen yang ditetapkan oleh Pemerintah", pp. 31–32); KKTP approach and threshold (p. 53, "keleluasaan"); description wording; Fase A without numeric grades; promotion criteria | repo extract with `force` tags |
| **Kokurikuler** (Panduan Kokurikuler 2025) | Yearly JP taken from compulsory subjects; strengthens one or more of the 8 DPL | Themes "dikembangkan oleh Satuan Pendidikan"; form (lintas disiplin, 7 KAIH, other) and timing | repo extract |
| **KSP** (Panduan KSP 2025, Pengelolaan 26/2025) | KSP exists and is set by the principal | "kewenangan untuk menentukan bentuk dan sistematika" (p. 7); not uniform administration (p. 10) | repo extract |
| **Madrasah** (KMA 1503/2025, SK Dirjen 9941/2025, KMA 512/2026) | MI structure and JP; CP for Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab; KBC as value education across intra-, ko-, and ekstrakurikuler and school culture (KMA 512 Diktum KEDUA–KEEMPAT) | Up to 6 extra JP per week; KBC methods (12 suggested) and instruments (10 listed) are chosen to fit the context (KMA 512 pp. 16–21) | repo extract; KMA 512 official PDF |
| **Child protection and data** (6/2026, UU PDP) | Safe school culture, confidential complaints, personal data protection | — (SIGRAK adds its own stricter rules: D-023, D-347, D-350) | secondary |

### 3.1 Do national standards limit method innovation?

**No.** What they limit falls in five groups, and none of them is a method:

1. **Outcomes:** SKL (8 DPL) and CP.
2. **Structure:** subjects, yearly JP, kokurikuler load, ekstrakurikuler with Pramuka, the muatan lokal cap.
3. **Minimum content of documents:** the planning document (objectives, steps, assessment), the rapor (12 components).
4. **Principles:** pembelajaran mendalam principles and experiences, and formative kept apart from summative.
5. **Protection:** a safe school and child data.

The texts point the other way. Planning is the teacher's (Standar Proses Pasal 4(2)). The ATP is an example. The KSP form is the school's. KBC indicators are examples. Organisation by theme or block is allowed at guide level. The real risk is not that rules stop innovation. It is that **old habits read guide examples as rules**. SIGRAK can correct that by labelling every default as "contoh" when it is one.

## 4. How the app accommodates central policy

1. **Policy as data.** Every rule, table, and list from L1–L3 lives in versioned data (§6), never in code constants. Today one rule is hard-coded: `config/curriculum.php` `allocation_versions`. It moves to data.
2. **Dasar Kebijakan panel.** Every document (RPM, LKPD, Bank Soal, asesmen plan, kokurikuler plan, rapor) shows a short panel: the CP and TP it serves, the DPL and principles it touches, and the regulation and version it follows. The data is the taxonomy tags (§9) the template already fills. This is what makes an innovation *accountable* in front of a principal or supervisor.
3. **Cek Keselarasan.** One checker reads the rule strengths (§2.1) and returns block / warn / hint lines. It runs when a document is saved and in the principal's and supervisor's review. It never rewrites the teacher's work.
4. **Labels "Wajib" / "Disarankan" / "Contoh" / "Bebas"** on form fields and defaults, so teachers see which parts come from a rule and which are theirs to change.
5. **Kabar Regulasi.** When a new rule set is published (§6.4), schools get a short Kabar: what changed, from which school year, and which of their documents are affected (impact report, D-317). Teachers decide; SIGRAK does not silently rewrite existing documents.
6. **No double data entry.** Rule data feeds the templates, the rapor, the JP checks, and the exports (e-Rapor, RDM; D-218) from one place.

## 5. Classification of SIGRAK components

Layer = where the rule that bounds the component comes from. Strength = the strongest rule on it. "Versioned" = it must follow §6.

| Component | Layer | Strength | Who may change | Versioned | SD/MI differs | Decisions |
|---|---|---|---|---|---|---|
| Subject list per grade | L1 | mandatory | Staff | Yes | Yes | D-034, D-035, D-230 |
| Intrakurikuler JP per subject | L1 | mandatory (SD); minimum + up to 6 extra JP (MI) | Staff; MI school adds | Yes | Yes | D-038, D-204 |
| Kokurikuler JP | L1 | mandatory yearly total | Staff; school spreads it (D-277) | Yes | Yes | D-277 |
| Muatan lokal | L3 | maximum (SD 72 JP) | Region, then school | Yes | Yes | D-231, D-232 |
| Optional subjects (Bahasa Inggris 1–2, KKA, Seni Suara Daerah, Tata Boga) | L1/L3 | school_discretion; Bahasa Inggris becomes mandatory 2027/2028 | School | Yes | Yes | D-204, D-208 |
| CP | L2 | mandatory content | Staff | Yes | Yes | D-226, D-139, D-145 |
| TP / ATP | L2 example → L4 | example | School, teacher | Yes (defaults) | Yes | D-019, D-276 |
| Topics, fokus (KM-SD data) | L2-derived (legacy app data) | example | Staff, school | Yes | SD only | D-020 |
| 8 DPL | L1 | mandatory list | Nobody | Yes | Same | D-210 |
| Pembelajaran mendalam principles and experiences | L1/L2 | mandatory list, free practice | Nobody (list); teacher (practice) | Yes | Same | — |
| RPM | L5 inside L1 minimum | minimum content (TP, steps, assessment) | Teacher | Template versions | MI adds KBC fields | D-024, D-126 |
| LKPD, Bank Soal, Quiz Battle, Game kelas | L5/L6 | teacher_discretion / free | Teacher | Template versions | Small | D-332, D-172 |
| Formative assessment | L2 | mandatory separation from rapor | Teacher | Yes (rules) | Same | — |
| Fase A written tests | L2 | not_recommended | Teacher | — | Same | D-206 |
| KKTP | L2 → L4 | school_discretion | School, teacher | Yes (approaches) | Same | — |
| Rapor | L1/L2 | minimum (12 components) | School adds; description wording free | Yes | Yes (RDM vs e-Rapor) | D-205, D-210, D-218 |
| Promotion and graduation | L1 → L4 | school_discretion within Standar Penilaian | School | Yes | Same | — |
| Kokurikuler themes, 7 KAIH | L2 → L4 | school_discretion | School | Yes (forms) | MI adds KBC | D-207 |
| Ekstrakurikuler, Pramuka | L1 | minimum (Pramuka offered) | School | Yes | Same | D-216 |
| KBC / Panca Cinta, six nilai dasar | L2 (KMA 512/2026) | example (methods), mandatory to implement in madrasah | Madrasah | Yes | MI; SD religion teachers open (D-359) | D-126, D-359 |
| KSP | L1 → L4 | mandatory to exist; free form | Principal | Starter template only | Small | D-209 |
| Kalender pendidikan | L3 | mandatory dates; school adds events | Region, school | Yearly | Yes (Kemenag) | D-253, D-256, D-323 |
| Timetable | L4 | school_discretion within JP | Operator | No | Same | D-247…D-251 |
| JTM, wali kelas load | L1 | mandatory defaults, school override | School | Yes | Yes (2 vs 6) | D-212 |
| Journal, attendance | L4/L5 | free form | Teacher | No | Same | — |
| Supervision, PKKS, teacher performance | L1/L3 | mandatory frame, regional variants | Staff, region | Yes | Yes | D-222…D-225, D-324, D-327 |
| School safety (class agreement, complaints) | L1 | mandatory to exist | School | Yes | Same | D-210 |
| TKA practice | L2 | follows the official framework | Staff | Yes | Same (TKA for both) | D-026, D-030 |
| AN / Rapor Pendidikan | L1 | read only (SIGRAK does not simulate AN) | — | — | MI also AKMI (unverified) | D-351 |
| Peta Asesmen taxonomy | L6 on L2 | free, tagged | Staff | Yes | MI subdomains | D-344 |
| AI assistance | L6 | free within D-023, D-347 | — | — | Same | D-023, D-347 |
| Student data | L0 (UU PDP) + SIGRAK | mandatory protection | — | — | Same | D-023, D-350 |

## 6. Regulation versioning

### 6.1 What exists today

- `curriculum_versions` holds `code`, `name`, `status` (draft/active/retired), `meta`, `imported_at`. There is no effective date, school year, issuing body, or link to the regulation it implements (`database/migrations/2026_11_08_000001_create_curriculum_versions_table.php`).
- The version in force is a config pointer (`config/curriculum.php` `allocation_versions`), not data.
- `subjects` is global and not versioned, so a subject that is renamed or dropped cannot be expressed.
- Other rules (rapor, assessment, kokurikuler, TKA, JTM) exist only as draft JSON in `docs/data/regulations/` and as prose.
- Good patterns to keep: draft-first import with Tim Validasi review (D-113, D-229), provenance with sha256 and pages, `SourceOverrides` for documented corrections (D-234), and immutable `template_versions`.

### 6.2 Proposed model (concept, not yet a migration)

**`regulations`**: one row per legal document.
- `kind` (uu, pp, permen, kepmen, kepka, kma, sk_dirjen, panduan, surat_edaran, perda, other), `number`, `title`, `issuer`, `ministry` (kemendikdasmen, kemenag, other), `issued_on`, `promulgated_on`, `effective_on`, `status` (draft, in_force, amended, revoked), `source_file` + `sha256`, `source_url`, `verification` (official_copy, mirror, secondary), `notes`.

**`regulation_relations`**: `from_id`, `to_id`, `type` (amends, revokes, implements, references), `articles` (e.g. "3, 6, 16–19, 22, 32, 33"). Examples:
- Permendikdasmen 13/2025 *amends* Permendikbudristek 12/2024
- Permendikdasmen 1/2026 *revokes* Permendikbudristek 16/2022
- KMA 1503/2025 *amends* KMA 450/2024
- BKPDM 020/2026 *amends* BSKAP 046/H/KR/2025
- Kepmendikdasmen 126/P/2025 *implements* Permendikdasmen 13/2025

**`rule_sets`**: a versioned package of rules for one domain, derived from one or more regulations.
- `domain`: structure_jp, cp, tp_atp_defaults, assessment_rapor, cocurricular, extracurricular, calendar, workload_jtm, supervision, tka, taxonomy
- `code` (same style as today, e.g. `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`), `status` (draft, active, retired), `level_scope` (sd, mi, both), `region_scope` (null = national), `applies_from_school_year`, `applies_to_school_year` (null = open), `transition` (JSON: phase-in by grade, e.g. "grades 1 and 4 first"), `regulation_ids`, `supersedes_id`, `reviewed_by`, `reviewed_at`.
- `curriculum_versions` becomes the CP / structure kind of rule set. **Add columns, do not rebuild**: the existing tables keep working.

**`rules`**: the individual rules inside a rule set, each with `key`, `force` (§2.1), `value` (JSON), `source_ref` (regulation + page/article), `level_scope`.

**`subjects`**: add `since_rule_set` and `retired_in_rule_set`, so a subject can appear or disappear between versions.

### 6.3 Resolving the policy in force

A **policy profile** is resolved from (school year, level, region): for each domain, the active rule set whose school-year range covers that year. It replaces the config pointer and is cached per school year.

- **Documents remember their rule set.** An RPM, asesmen plan, or rapor stores the rule-set codes it was made under, like `template_versions`. A rule change never silently changes an existing document.
- **Rules follow the school year.** A rule published mid-year applies from the next school year unless the regulation itself sets an earlier date. Example: Bahasa Inggris becomes compulsory in SD/MI in 2027/2028 (Permendikbudristek 12/2024 Pasal 33), so the 2027/2028 structure rule set lists it as mandatory, and 2026/2027 still lists it as optional.
- **Transitions are data.** Phase-in by grade is stored in `transition`, so one rule set can say "grades 1 and 4 in year one".

### 6.4 Workflow

1. **Watch** the official channels: jdih.kemendikdasmen.go.id, peraturan.go.id, peraturan.bpk.go.id, kurikulum.kemendikdasmen.go.id, guru.kemendikdasmen.go.id, jdih.kemenag.go.id, pendis.kemenag.go.id, and Dinas/Kanwil sites for L3. Mirrors (pasal.id) are leads, not sources.
2. **Capture** the PDF in `docs/sources/` with sha256 and a `verification` level (official copy beats mirror beats secondary).
3. **Draft** the rule set (Kurator Kurikulum, D-339), with page references.
4. **Review** by Tim Validasi (D-113).
5. **Publish** through the curriculum console with compare, impact, and roll back (D-317, extended to every domain).
6. **Tell** schools through Kabar Regulasi (§4.5).

## 7. Where innovation is wide open

Inside the walls of §3, these are free (strength `school_discretion`, `teacher_discretion`, or `free`):

| Area | Room for innovation | SIGRAK tools |
|---|---|---|
| Method and model | PjBL, PBL, inquiry, differentiation, game-based, outdoor, peer teaching | RPM template choices, Game kelas, LKPD patterns |
| Organisation | Thematic, block, integrated, mixed (KSP level) | KSP starter (D-209), RPM across subjects |
| Media and mode | Paper, IFP, phone-free class, hybrid, AR/VR embeds with a licence | LKPD forms (D-332…D-337), Game kelas |
| Assessment beyond the minimum | Rubrics, observation, portfolio, self and peer, readiness checks | Peta Asesmen templates (D-346) |
| Kokurikuler | Any theme, any form, any timing | Kokurikuler plan, 7 KAIH tracker |
| Local context | Local stories, products, places, language | Konteks Daerah (D-333) |
| Time | Weekly spread of yearly JP, blocks of days | Timetable, JP checks |
| School partnership | Parents, community, local experts | Parent messages (D-215) |
| Data for teaching | Class patterns, misconceptions, follow-up | Per-TP analysis, Ringkasan Kelas |
| AI help | Drafting, differentiation, feedback ideas | Templates, never free prompts (D-024) |

**The only fixed edges of innovation in SIGRAK:** L1 `mandatory` rules, child data rules (D-023, D-347, D-350), no permanent labels on children, licences (D-337), and the teacher deciding (AI suggests, the teacher chooses).

## 8. SD and MI configuration: Profil Jenjang

SD and MI follow the same national standards (PP 57/2021; SKL, Isi, Proses, Penilaian apply to both, and KMA rules for madrasah say non-PAI subjects follow the Kemendikdasmen rules). They differ in governance and in a few domains. **Recommendation: one codebase, a shared core, and a level overlay stored as data.**

| Setting | SD | MI | Today |
|---|---|---|---|
| Ministry | Kemendikdasmen | Kemenag (Ditjen Pendis) | Only in source titles |
| Structure and JP | Permendikdasmen 13/2025 tables | KMA 1503/2025 tables; up to 6 extra JP/week | `config/curriculum.php` pointer |
| Religion subjects | Six Pendidikan Agama subjects | Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, plus Bahasa Arab | `subjects.school_level` |
| CP source | BSKAP 046/H/KR/2025 + BKPDM 020/2026 | Same for general subjects; SK Dirjen 9941/2025 for PAI and Bahasa Arab | `curriculum_versions` |
| Character frameworks | 8 DPL, 7 KAIH | 8 DPL, 7 KAIH, KBC / Panca Cinta | Docs only |
| Kokurikuler JP source | Every compulsory subject except Bahasa Inggris | Only some subjects (C4) | Docs only |
| Wali kelas JTM | 2 | 6 | D-212 (prose) |
| RPM extra fields | — | Tema KBC, Materi Insersi | D-126 |
| Rapor export | e-Rapor SD | RDM | D-218 |
| External assessment | AN, TKA | TKA; AKMI (Kemenag) to be verified | — |
| Calendar | Provincial kaldik | Kemenag/Kanwil calendar, often with provincial dates | D-256 |
| Data of record | Dapodik | EMIS | D-161 |

**Profil Jenjang** is a small data record per level (`sd`, `mi`), versioned through the rule sets of §6, that names: the ministry, the rule-set codes per domain, the character frameworks on, the RPM extra fields, the rapor export target, the JTM defaults, and the external assessments. Code reads the profile; it never checks `if level == mi`. New levels (SDLB, or later SMP/MTs) are then a new profile, not a new branch of code.

## 9. Master taxonomy

### 9.1 Principles

- **One code per concept, stable forever.** Labels may change; codes do not. A retired item keeps its code with `retired_in`.
- **Every node has a source and a strength.** A node from a regulation cites it; a SIGRAK node says `source: sigrak`.
- **Closed vocabularies only where a rule fixes the list** (8 DPL, 3 principles, phases). **Open vocabularies** (methods, media, local context) let a school add local items, marked `scope: school`.
- **Crosswalks between versions**, so old documents stay readable: Profil Pelajar Pancasila (6 dimensions) → 8 DPL; P5 / P5RA → kokurikuler; RPP / Modul Ajar → dokumen perencanaan / RPM; peserta didik → murid.
- **Data first:** `docs/data/taxonomy/` as reviewed JSON (`status: draft` until Tim Validasi, D-113), imported later into tables. It joins `docs/data/assessment/` (D-344) and `docs/data/catalogs/` (P4-15) instead of duplicating them.

### 9.2 Node shape

```json
{
  "code": "DPL.03",
  "facet": "C",
  "label_id": "Penalaran Kritis",
  "parent": "DPL",
  "level_scope": "both",
  "force": "mandatory",
  "source_ref": {"regulation": "PERMENDIKDASMEN-10-2025", "article": "…"},
  "status": "draft",
  "since": "TAX-2026-R1",
  "retired_in": null,
  "crosswalk": [{"from": "P3.BERNALAR-KRITIS", "type": "renamed"}]
}
```

### 9.3 Facets

| Facet | Contents | Closed or open | Source |
|---|---|---|---|
| **A Policy** | Regulation kinds, standards (SKL, Isi, Proses, Penilaian, Pengelolaan, …), rule strengths (§2.1), layers (§2) | Closed | L0–L2 |
| **B Curriculum structure** | Level (SD, MI), phase (A, B, C), grade, semester, learning type (intra, ko, ekstra), subject kind (compulsory, religion, local, optional, cocurricular), subject, CP element and sub-element, CP, TP, ATP, topic, fokus | Closed (lists), data per version (CP/TP) | L1/L2, KM-SD |
| **C Profile and character** | 8 DPL (with sub-dimensions where the guide gives them), 7 KAIH, Panca Cinta (MI), safe school culture | Closed | 10/2025, 13/2025, Kokurikuler guide, 6077/2025, 6/2026 |
| **D Learning** | 3 principles, 3 experiences, 4 framework parts, pedagogical practices and models, modes (face to face, outdoor, IFP, no device), media | Principles closed; practices, modes, media open | 126/P/2025, 1/2026, SIGRAK |
| **E Assessment** | Peta Asesmen: domains, purposes (diagnostic, formative, summative), methods, media, response, scoring, follow-up | Closed core, open extensions | D-344, assessment guide |
| **F Content** | Content types (RPM, Bank Soal, LKPD, Quiz Battle, TKA package, …), LKPD patterns, game templates, question types | Closed (product), grows by release | SIGRAK |
| **G Context** | Province, regency, regional context items, local languages, muatan lokal | Open | L3, D-333 |
| **H Administration** | School roles, staff roles, documents (KSP, SK, jurnal, rapor), teacher performance indicators, supervision instruments | Closed (roles), open (documents) | L1, D-339, D-222 |

### 9.4 How features use the taxonomy

- Templates tag what they make: an RPM gets B (CP, TP), C (DPL), D (principles, practice); a LKPD gets F (pattern) and E (purpose).
- The Dasar Kebijakan panel (§4.2) and Cek Keselarasan (§4.3) read those tags.
- Analytics count by tag: which DPL a class worked on this semester; which TP have no assessment yet.
- Search and the shared library filter by tag.

## 10. Clash check with earlier decisions

| Decision | Relation | Recommendation |
|---|---|---|
| D-210 (follow the 2025–2026 rules) | Same direction; this draft gives it a structure | Keep |
| D-206 (Fase A written tests optional) | Fits `not_recommended`: allowed, with a hint | Keep; show the hint |
| D-232 (muatan lokal cap warns) | Fits `maximum`: warn | Keep |
| D-230 + `config/curriculum.php` pointer | The pointer is a hard-coded policy | Replace with policy-profile resolution (§6.3); behaviour unchanged |
| D-139 / D-145 (one version per CP set) | Fits rule sets of domain `cp` | Keep |
| D-229 (import never changes status) | Fits draft-first | Keep; apply to every rule set |
| D-317 (curriculum console) | Covers only curriculum | Extend to every rule-set domain |
| D-020 (legacy app curriculum import) | KM-SD data is L2-derived, not a regulation | Label it `example` / derived; CP text from official decrees wins where they differ |
| D-113 (Tim Validasi) | Same gate | Keep for taxonomy and rule sets |
| D-344 (Peta Asesmen dataset) | Becomes facet E | Keep the dataset; register its codes in the taxonomy |
| D-126 (KBC in the MI RPM) | Becomes a Profil Jenjang setting | Keep; source is now KMA 512/2026 (D-359) |
| D-212 (JTM 2 vs 6) | Becomes a Profil Jenjang setting | Keep |
| Old terms in official guides (P5, Profil Pelajar Pancasila, Kurikulum Merdeka) | Guides lag behind the Permen | Use the Permen term in the UI; keep crosswalks |

No decision is overturned. The only behaviour change proposed is moving one config pointer into data.

## 11. Sources still needed

**Got in P4-23 (2026-11-11), now in `docs/sources/`** with sha256, extracts in `docs/data/regulations/`, and entries in `docs/data/taxonomy/regulations.json`:

- Permendikdasmen 10/2025 (SKL), 12/2025 (Standar Isi), 6/2026 (Budaya Sekolah Aman dan Nyaman): official copies.
- Kepka BSKAP 046/H/KR/2025 (general-subject CP, 1,691 pages, kept whole): copy from a ministry unit.
- Permendikdasmen 26/2025 (Pengelolaan) and Permendikbudristek 21/2022 (Penilaian): **mirror** copies (JDIH salinan re-hosted by regional education offices). Kept until a JDIH copy can be fetched, then replaced and re-hashed (owner, 2026-11-11).

**Still not in `docs/sources/`** (JDIH, peraturan.go.id, and BPK were unreachable from the build environment; task P4-32, the owner fetches them):

- Permendikdasmen 1/2026 (Standar Proses). Pasal 9 could not be read from the mirror.
- Permendikdasmen 9/2026 (AN) and Kepmendikdasmen 17/2026 (pelaksanaan Budaya Sekolah Aman dan Nyaman).
- The base Permendikbudristek 12/2024 (only the 13/2025 changes are in the repo).
- **KMA 512/2026, resolved.** The owner supplied the official PDF on 2026-11-09 (`docs/sources/KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf`). Set 31 March 2026 and in force on that date, it replaces SK Dirjen Pendis 6077/2025 as the KBC source (D-359). Its scope covers Kemenag schools *and* religion teachers in general schools, SD included (Lampiran §C, p. 6); see the open question in `docs/drafts/open-questions.md`.
- Open facts: who sets the KKTP at article level; AKMI for MI in 2026; official e-Rapor SD and RDM 2026 specs.

## 12. Owner questions

**Answered 2026-11-09, all as recommended:** PQ1 → D-352, PQ2 → D-353, PQ3 → D-354, PQ4 → D-355, PQ5 → D-356, PQ6 → D-357, PQ7 → D-358, PQ8 → D-359.

**PQ1. Position statement.** Adopt *"Berpijak pada kebijakan, leluasa berinovasi"* as SIGRAK's position, with the meaning in §1? *Recommended: yes.*

**PQ2. Rule strength and app behaviour.** Use the strengths and behaviours of §2.1: block only clear L1/L2 mandatory rules, warn on minimum and maximum, hint on recommended and not recommended, free otherwise? *Recommended: yes.*

**PQ3. Regulation versioning model.** Adopt `regulations`, `regulation_relations`, `rule_sets` (extending `curriculum_versions`), and `rules`, resolved per school year, level, and region (§6)? *Recommended: yes, as the target model.*

**PQ4. Timing.** When does the model enter the build? *Recommended:* the minimum before P5-04 (content core), because an RPM must store the rule set it was made under: `effective_on`, `applies_from_school_year`, `supersedes_id` on `curriculum_versions`, and the config pointer replaced by data. The full `regulations` and `rules` tables and the extended console come with P5-09 (portals) or as their own slice.

**PQ5. Profil Jenjang.** SD and MI as data profiles over one shared core (§8), no `if level == mi` in code? *Recommended: yes.*

**PQ6. Master taxonomy.** Build `docs/data/taxonomy/` with facets A–H, stable codes, strengths, and crosswalks (§9), reviewed by Tim Validasi, joining Peta Asesmen and the legacy app catalogs? *Recommended: yes, as task P4-22.*

**PQ7. Dasar Kebijakan panel and Cek Keselarasan.** Show the policy panel on every document and run one checker for blocks, warnings, and hints (§4)? *Recommended: yes; panel in P5-04 for RPM first, checker grows with each slice.*

**PQ8. KMA 512/2026.** Can the owner get the official PDF? *Recommended: get the PDF; keep 6077 meanwhile.* Answer: PDF supplied; KMA 512/2026 is the KBC source (D-359).

## 13. Phasing and follow-up tasks

| Wave | What | Task |
|---|---|---|
| 0 (docs) | Owner answers PQ1–PQ8; decisions recorded | P4-21 |
| 0 (docs) | Get and extract the missing regulations (§11) into `docs/sources/` and `docs/data/regulations/` | P4-23 |
| 0 (data) | `docs/data/taxonomy/` seed (facets A–H) and a regulation registry JSON with relations | P4-22 |
| 1 (build, before P5-04) | Version columns on `curriculum_versions`; policy-profile resolver replaces `allocation_versions`; Profil Jenjang data | Part of P5-04 prep |
| 2 (build) | Documents store rule-set codes; Dasar Kebijakan panel on RPM; first Cek Keselarasan rules | P5-04 |
| 3 (build) | `regulations` / `rules` tables, console for every domain, Kabar Regulasi | P5-09 or own slice |
| Docs | Apply the accepted parts to `docs/final/` (product-concept, data-model, architecture, content-engine) + Indonesian PDFs | P4-24 (applied 2026-11-10) |

## Sources

Repo: `docs/drafts/dokumen-resmi-sd-mi.md`, `docs/drafts/administration-features.md` §1 and Sources, `docs/data/regulations/README.md` and files, `docs/data/curriculum/README.md`, `docs/final/data-model.md`, `docs/final/religious-education.md`, `config/curriculum.php`, `app/Curriculum/Import/`.

Web (checked 2026-11-09; official JDIH sites returned 503/403 that day):
- Permendikdasmen 1/2026, pasal.id mirror: https://pasal.id/peraturan/permen/permendikdasmen-no-1-tahun-2026 ; official PDF path: https://www.peraturan.go.id/files/Permendikdasmen-no-1-tahun-2026.pdf ; BPK: https://peraturan.bpk.go.id/Details/344196
- Permendikbudristek 12/2024, pasal.id mirror: https://pasal.id/peraturan/permen/permendikbudristek-no-12-tahun-2024
- Permendikdasmen 13/2025, pasal.id mirror: https://pasal.id/peraturan/permen/permendikdasmen-no-13-tahun-2025
- Permendikdasmen 10/2025, pasal.id mirror: https://pasal.id/en/peraturan/permen/permendikdasmen-no-10-tahun-2025
- Permendikdasmen 12/2025, BPK: https://peraturan.bpk.go.id/Details/322494
- Permendikdasmen 9/2026, BPK: https://peraturan.bpk.go.id/Details/345735
- Permendikbudristek 21/2022, pasal.id mirror: https://pasal.id/peraturan/permen/permendikbudriset-no-21-tahun-2022
- KMA 512/2026: official PDF in `docs/sources/` (owner, 2026-11-09). Earlier secondary summaries: https://bukuyunandra.com/?p=9845 ; https://yunandra.com/pembudayaan-nilai-kbc-kma-512-tahun-2026/
- Ministry statement that 13/2025 is not a new curriculum: https://www.metrotvnews.com/read/NLMCJ6Vj-mendikdasmen-tegaskan-tidak-ada-perubahan-kurikulum
