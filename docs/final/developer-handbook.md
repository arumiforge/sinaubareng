# SIGRAK Developer Handbook

- **Status:** final (owner request 2026-11-09, D-361)
- **Last updated:** 2026-11-09
- **Audience:** every new developer or agent who joins SIGRAK, before they read any other document. The Indonesian PDF of this handbook, `docs/pdf/developer-handbook.pdf`, is the one complete reading for a new developer (D-361).
- **Indonesian PDF:** [`docs/pdf/developer-handbook.pdf`](../pdf/developer-handbook.pdf), translated in [`docs/pdf/src/developer-handbook.md`](../pdf/src/developer-handbook.md)

## How to read this handbook

This handbook tells the whole SIGRAK story in one place: what the product is, who uses it, which national rules it stands on, how a school year flows through the app from the first invitation to the last report card, how the code is put together, how AI is kept safe, how exports and the server work, and how the team and its agents work. It is a narrative. It does **not** replace the detailed documents. Each chapter ends with, or points to, the detailed doc and the decision IDs (`D-NNN` in [`docs/agent/DECISIONS.md`](../agent/DECISIONS.md)) that hold the exact rules.

Three build-status labels appear throughout:

| Label | Meaning |
|---|---|
| **Built** (*Dibangun*) | Code exists on `main`, with tests, as of 2026-11-09 |
| **Designed** (*Didesain*) | Agreed in a final doc, a draft, or a decision, but not coded yet |
| **Later** (*Nanti*) | Accepted as a future feature or shown as "upcoming"; not part of v1 |

Some rules to keep in mind while reading:

- **Later decisions win.** The final docs in `docs/final/` were approved on 2026-10-31 (D-123) and updated in places since. Many decisions after that date (D-203 onward, and especially D-252…D-362) are not yet applied to every final doc. Where a final doc and a later decision differ, this handbook follows the later decision and says so. The list of known differences is in [chapter 12](#12-decision-map).
- **Nothing here is new.** This handbook only retells what the final docs, the accepted drafts, the decisions, and the code already say. If something is not in those sources, it is not in SIGRAK.
- **Names.** The owner's previous product is always called "the legacy app" in English and "aplikasi lama" in Indonesian (D-360).
- **Research.** Before you research a regulation, a study, or a vendor, read [`docs/final/research-digest.md`](research-digest.md) first, and add what you find to it afterwards (D-362).

Contents:

1. What SIGRAK is and its position
2. Users, roles, and access
3. The policy foundation
4. The end-to-end flow
5. Architecture
6. Data model tour
7. AI layer and child data protection
8. Exports, localization, and UI writing
9. Infrastructure and operations
10. How we work
11. Roadmap
12. Decision map
13. Glossary
14. Where to read more

## 1. What SIGRAK is and its position

### 1.1 The product in one paragraph

SIGRAK (a working name until a PDKI brand check is done; D-017, D-069) is a web app for teachers and school leaders in Indonesian primary schools: **SD** (Sekolah Dasar, under the Ministry of Primary and Secondary Education, Kemendikdasmen) and **MI** (Madrasah Ibtidaiyah, under the Ministry of Religious Affairs, Kemenag), grades 1–6. It does three jobs, and all three are part of v1 (D-009, D-026):

1. **AI-assisted teaching content.** Teachers make lesson plans (RPM), question banks (Bank Soal), worksheets (LKPD), classroom games (Quiz Battle, growing into Game kelas), and TKA practice packages. They choose a template and answer pedagogical questions. They never write a prompt (D-024).
2. **School administration.** Attendance, the teaching journal, student notes, assessment and report cards, class data, and portals for the principal, the school operator, and the supervisor (pengawas). Most of these features come from the legacy app, rebuilt on a new stack with a new data model (D-020).
3. **A TKA practice exam (CBT).** A computer-based practice exam for grade 6 that works like the national Tes Kemampuan Akademik. Students take it on a separate exam site with a one-session exam card and a proctor token (D-026, D-027, D-089).

### 1.2 Who pays

Only teachers and schools pay. Students and parents never pay and never see a price (D-157). Prices are per academic year (July–June):

| Plan | For | Price per year |
|---|---|---|
| Uji coba (trial) | One teacher, every feature for 7 days (D-160) | Free |
| Sigrak Guru | One teacher, all teacher features | Rp60.000, or Rp50.000 when the school has Sigrak Sekolah |
| Sigrak Sekolah | Two accounts (principal, operator) with their portals | Rp100.000 |
| Add-on | One extra feature for a school with Sigrak Sekolah | Rp50.000 each |
| Sigrak Binaan | Supervisor portal; not sold, opens free once 5 or more supervised schools have Sigrak Sekolah | Free |

After the trial, an unpaid account can only view and download its data. There is no permanent free plan and no watermark (D-157, D-160, XP-13). The starting add-ons are Simulasi TKA, Buku Induk and Dapodik sync (held as upcoming, D-161), Keuangan and Inventaris kelas, Buku Sekolah, Game kelas, and Rapor (D-158). A teacher may try Game kelas for 7 days, once, even without the school add-on (D-159). Which features are core and which are add-ons is **not fixed in code**: Tim SIGRAK sets the mapping per feature in the internal console, and the D-158 list is only the starting configuration (D-211).

Three business rules matter to developers:

- **AI is never a SIGRAK cost.** Each teacher brings their own Gemini free-tier keys (D-012, D-023).
- **School subscriptions are paid from funds other than BOS** (the national school operating grant). Sales material never presents BOS as a payment source (D-025).
- **Payment is manual.** A school or teacher pays outside the app, for example by bank transfer. Penjualan (sales) records the claim and Keuangan (finance), or the owner while nobody holds that role, confirms it in the console. There is no payment gateway in v1 (D-091, D-340).

### 1.3 Principles

The product concept lists seven principles in priority order ([`product-concept.md`](product-concept.md#positioning-and-principles)):

1. Teachers answer questions they understand pedagogically, never prompts.
2. Developers build the structure; AI fills the content.
3. The teacher stays the author. Every AI result is editable, and any one section can be revised alone.
4. Data the system already has is never asked for again (class and subject from the timetable, JP and dates from the curriculum and calendar).
5. It works on a cheap phone with weak signal where it matters (Di Kelas offline, offline game files).
6. School data belongs to the school; AI keys and AI usage belong to the teacher. Student personal data never goes to the AI.
7. Only build what Gemini alone cannot give: structure, curriculum data, school formats, or classroom delivery.

The brand line is "Kerja guru, lebih sigrak." and the promise is "Bahan ajar siap pakai dan administrasi beres, tanpa menulis prompt." (D-017). The values are Cekatan, Jelas, Bisa diandalkan, Membumi, and Menghormati guru.

### 1.4 The position: *Berpijak pada kebijakan, leluasa berinovasi*

On 2026-11-09 the owner fixed SIGRAK's position as *Berpijak pada kebijakan, leluasa berinovasi* ("grounded in policy, free to innovate", D-352):

- **National standards fix the *what* and the limits:** graduate outcomes, learning outcomes (CP), the learning load (JP), the minimum content of a lesson plan and a report card, and child protection.
- **The school and the teacher choose the *how*:** the school curriculum (KSP), TP and ATP, KKTP, methods, media, instruments, themes, and schedule.
- **SIGRAK gives them tools** for new ways of learning, and every innovation can show which national element it serves.

The research behind this (P4-21) found that the 2025–2026 rules lock outcomes, structure, and reporting, but not methods. Standar Proses (Permendikdasmen 1/2026) only sets the *minimum content* of a planning document, and the ATP and lesson-plan templates in the official guides are explicitly examples. So following national policy closely does not narrow innovation; it shows where the walls are. [Chapter 3](#3-the-policy-foundation) explains how this becomes code.

### 1.5 What SIGRAK is not

- **Not a general AI chat or "make anything" generator.** There is no free generator and no "Buat bebas" (D-024). A teacher can already get that from Gemini.
- **Not a student app.** Students are records, never users. There are no student accounts, no parent accounts, and no public pages or share links (D-010). The only student-facing surface is the TKA exam site, with session-scoped cards (D-027).
- **Not an AI that reads children.** No student personal data goes to an AI provider, and the AI never reads children's handwriting or voice in v1 (D-023, D-347).
- **Not a finance or BOS system.** BOS and RKAS reports belong to the government apps (ARKAS, e-RKAM) and are out of scope (D-219, D-328).
- **Not a presentation tool.** There is no slide feature ("Bahan Tayang" was dropped, D-033).
- **Not an official exam.** Simulasi TKA is a practice simulation; its scores are estimates (D-089).
- **Not a copy of the legacy app.** Several legacy features are deliberately not carried over: the free generator and Bahan Tayang, SMP–SMA levels, a mandatory AI key at activation, default passwords, browser-screenshot PDFs, the 15-minute idle logout, exam mode, public verification pages, and the old terms *siswa*, *P5*, *taraf serap*, and *teguran* (D-322).
- **Not SMP/MTs yet.** Grades 7–12 stay hidden until their curriculum data exists (D-018).

Market: SD/MI anywhere in Indonesia (D-018). Kudus is the pilot and supplies the defaults; the first region data covers Kudus, Jepara, and Pati in Jawa Tengah (D-170, D-257, D-323).

Read more: [`product-concept.md`](product-concept.md), [`docs/drafts/policy-innovation-layers.md`](../drafts/policy-innovation-layers.md) §1, D-009, D-017, D-024, D-025, D-157…D-160, D-211, D-322, D-352.

## 2. Users, roles, and access

### 2.1 School roles

School users are people in a school. They sign in to the school app at `app.sigrak.com`. A user has **no role column**: roles come from memberships (`school_memberships`), so one person can be teacher and operator at the same time, or principal at one school and teacher at another (D-010, data model).

| Role | Who | Main jobs |
|---|---|---|
| Guru kelas (wali kelas) | Homeroom teacher of one class | Class subjects, content, daily attendance, journal, assessment, rapor, class data |
| Guru mapel | Teacher of one special subject across classes | Content reused across classes, journal, notes, and assessment for their subject |
| Kepala sekolah | Principal | Completeness, validation, supervision, school programmes, print settings, subscription |
| Operator sekolah | School administrative staff | Academic year, classes, inviting teachers, timetable, calendar, school profile |
| Pengawas / korwil | Supervisor of several schools | Read-only view of supervised schools, final curriculum validation, supervision reports |

Teaching defaults (D-032, D-228): in SD, PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, and the Pendidikan Agama subjects go to a guru mapel; every other subject goes to the homeroom teacher. In MI, the Kemenag subjects (Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI) and Bahasa Arab also default to a guru mapel. The operator can reassign any subject of any class. A guru pendamping khusus (GPK) is **not** a role; it is a teacher assigned to certain students (D-348). A principal, operator, or supervisor who also teaches keeps the teacher menus and switches to their portal with the role switch.

The supervisor is a special case. A pengawas has **no school membership**. Their reach comes from `supervisor_assignments`, read by `user_id` across schools and not scoped to a "current school" (D-122, D-168). They see school-level totals and never student-level rows (D-224, D-350). Supervisor accounts are created with the supervisor portal in P5-09 (D-184); in the designed flow a pengawas requests a school as *sekolah binaan*, Tim SIGRAK approves in the console, and the principal is notified (D-309).

### 2.2 Students, exam participants, and parents

- **Students (murid) are records.** A student has a name, NISN, gender, birth date, religion, enrollments, attendance, notes, and scores, all managed by teachers. Students never log in to the app (D-010). The UI calls them *murid*, following Permendikdasmen 1/2026 (D-210).
- **Exam participants are not a role.** For a TKA practice session, each student gets a session-scoped exam card (username and password) and needs the proctor's rotating token. The credentials stop working when the session closes, and the participant lives in `exam_participants`, not in `users` (D-027).
- **Parents have no account.** Teachers send prepared messages through their own WhatsApp with a plain `wa.me` link (D-215). A parent portal is shown only as "upcoming" (D-220).
- A **student session for interactive LKPD** is planned later, session-scoped like the exam site (D-331). It is not in v1.

### 2.3 Internal staff (Tim SIGRAK)

Staff use a separate Filament console at `admin.sigrak.com`, with their own table (`staff_users`), their own guard (`staff`), and Cloudflare Access in front. A school account can never become staff (D-010, D-055, D-088).

- **Built:** three staff roles, `admin`, `support`, and `content` (D-167). `php artisan staff:create <email>` makes the first admin on a server.
- **Designed:** eleven roles, all active from the start (D-339): Pemilik (`owner`), Admin (`admin`), Validator (`validator`, Tim Validasi SIGRAK), Penyusun Konten (`content`), Kurator Kurikulum (`curriculum`), Support (`support`), Pemasaran (`marketing`), Penjualan (`sales`), Koordinator Wilayah (`regional_lead`), Keuangan (`finance`), and Teknis (`engineer`). A person may hold several roles, each with its own scope, so `staff_role_assignments` replaces `staff_users.role`. Prices and AI models move from admin to owner.
- **Powers stay in code, scopes are data.** The admin edits only each person's scope: region (province, kabupaten, kecamatan) or a named school list, level (SD/MI), and field (subject group or religion). Scope changes are logged (D-338). The console shows a read-only "Hak akses" matrix; an editable permission matrix was rejected (D-314).
- **Separation of duty.** A validator never signs off their own work (D-339). The person who records a payment is not the one who confirms it (D-340).
- **Support access** to a school's view is read-only, covered by the MoU each school signs, with religion and health data hidden; Tim SIGRAK keeps one internal row per support session, not per page view, and no log is shown to schools (D-341). This replaces the school-granted, time-limited access first proposed in `docs/drafts/internal-roles.md` §4.
- **Two-factor sign-in** uses an authenticator app (TOTP, through Laravel Fortify). It is required for Pemilik and Admin and optional for other staff; there are no recovery codes, and an admin resets a lost 2FA, with every reset logged (D-343).
- **Partners** (*Mitra*: teacher communities such as KKG and KKM, and resellers) get a referral code and a partner report, not a console account. Terms and rewards come in a later task (D-342).

### 2.4 Guards and hosts

One Laravel codebase serves three hostnames, each with its own guard and its own host-only session cookie (D-048, D-068, D-069, D-164):

| Host | Serves | Guard (table) |
|---|---|---|
| `app.sigrak.com` | School app | `web` (`users`) |
| `ujian.sigrak.com` | TKA exam site | `exam` (`exam_participants`) |
| `admin.sigrak.com` | Staff console, Horizon, Telescope | `staff` (`staff_users`) |

The hostnames come from `APP_HOST`, `EXAM_HOST`, and `ADMIN_HOST`, so staging can use `staging-app.`, `staging-ujian.`, and `staging-admin.sigrak.com` from the same build (D-095, D-164). Locally they are `app.`, `ujian.`, and `admin.sigrak.localhost` (D-166).

### 2.5 Login-only

Every page needs a session. The only school-app routes open without one are sign-in (`/masuk`), password reset (`/lupa-kata-sandi`, `/kata-sandi-baru/{token}`), and invitation acceptance (`/undangan/{invitation}`) (D-010). `tests/Feature/Auth/SessionRequiredTest.php` fails when another route is left open. The one technical exception is Laravel's `/up` health route, which shows no data (D-134). The pengesahan verification page `/verifikasi/{code}` also needs login and works only for that school's members and its assigned supervisor (D-281). There are no public pages, no share links, and no public redirect routes; an LKPD QR code points straight to an allowlisted third-party URL (D-330).

### 2.6 Visibility of sensitive data

| Data | Visible to | Never |
|---|---|---|
| Student religion | The class's teachers, operator, principal | Supervisors, AI prompts, exam site, offline cache (D-036, D-051) |
| Health data | The class's teachers, operator, principal | AI, supervisors (D-215) |
| Social-emotional, special-needs, interest results | The class's teachers and assigned GPK, operator, principal | AI, permanent labels; supervisors see totals only (D-350) |
| BK (counselling) notes | Author, homeroom teacher, principal | Berkas, supervisor views, AI (data model; Part B B-09) |
| Teacher supervision results | The teacher and the principal | Pengawas, who sees school recaps only (D-224) |
| PKKS result | The assessed principal and that pengawas | Everyone else (D-308) |
| Teacher AI keys | Only the owner's generations use them; others see status only | Browser props, logs (D-012, D-023) |

Read more: [`product-concept.md`](product-concept.md#roles), [`data-model.md`](data-model.md#tenancy-and-access), [`architecture.md`](architecture.md#security-boundaries), [`docs/drafts/internal-roles.md`](../drafts/internal-roles.md), D-010, D-027, D-036, D-048, D-122, D-167, D-168, D-182…D-184, D-215, D-224, D-309, D-338…D-343, D-348, D-350.

## 3. The policy foundation

SIGRAK's position (D-352) only works if the app knows which national rules apply, how strong each one is, and which version was in force when a document was made. This chapter explains the model the owner accepted on 2026-11-09 (D-352…D-359). Most of it is **Designed**; the pieces that exist today are named.

### 3.1 Layers L0–L6

Every rule in SIGRAK belongs to a layer that says who set it. A higher layer bounds a lower one, never the other way. A lower layer may always go *beyond* a floor (more assessment than the minimum, more report content than required) unless a cap says otherwise (D-353; `policy-innovation-layers.md` §2).

| Layer | Who sets it | Examples | In SIGRAK |
|---|---|---|---|
| L0 Law | DPR, President | UU 20/2003 Sisdiknas, PP 57/2021 | Reference only |
| L1 Binding national standards | Minister (Kemendikdasmen; Kemenag for madrasah) | SKL, Standar Isi, Standar Proses, Standar Penilaian, Kurikulum (12/2024 as amended by 13/2025), KMA 1503/2025 | Locked, versioned, edited by staff after Tim Validasi review |
| L2 National references | Kepmen, Kepala BSKAP/BKPDM, Dirjen Pendis, official guides | CP decrees, Pembelajaran Mendalam (126/P/2025), KKA (127/P/2025), the 2025 guides, KMA 512/2026 (KBC), TKA framework | CP text is binding; guide rules carry their own strength, many only "contoh" |
| L3 Region | Pemda, Dinas, Kanwil, Kemenag kabupaten | Muatan lokal, provincial school calendar, regional supervision forms | `region_subjects`, region calendars, regional variants |
| L4 School | Principal | KSP, TP/ATP choice, KKTP approach, promotion criteria, enabled subjects | `school_subjects`, school curriculum changes, presets, overrides |
| L5 Teacher and class | Teacher | RPM, methods, media, LKPD, instruments, games, class agreement | Every content template |
| L6 SIGRAK innovation | SIGRAK | Game kelas, Pindai LJK, AI-assisted templates, Peta Asesmen, Konteks Daerah | Must name the L1/L2 element it serves; may never contradict an L1 mandatory rule |

### 3.2 Rule strength and how the app reacts

Every rule carries a strength (`force`), and the strength alone decides the app's behaviour (D-353). The vocabulary extends the `force` tags already present in the regulation data of P4-03 (D-203).

| Strength | Meaning | App behaviour |
|---|---|---|
| `mandatory` | Must be done exactly | **Block** (cannot save) |
| `minimum` | A floor; more is allowed | **Warn** below the floor |
| `maximum` | A cap; less is allowed | **Warn** above the cap |
| `recommended` | The guide advises it | **Hint**, default on |
| `not_recommended` | The guide advises against it | **Hint**, still allowed |
| `example` | A model only | A **default** labelled "Contoh" that can be replaced |
| `school_discretion` | The school decides | Free, set by principal or operator |
| `teacher_discretion` | The teacher decides | Free, set by the teacher |
| `free` | No rule | Free |

The app blocks only clear L1/L2 mandatory rules. Examples already in the decisions: a school cannot switch off a compulsory subject (`mandatory`, D-230); a formative score can never become a rapor grade (`mandatory`, D-205, D-290); the SD muatan lokal cap of 72 JP per year warns but does not block (`maximum`, D-232); written tests in Fase A are allowed with a hint (`not_recommended`, D-206). This keeps SIGRAK a guardrail, not a gate: a school that innovates is never stopped, it only sees how its choice relates to the rule. Form fields and defaults carry the labels "Wajib", "Disarankan", "Contoh", or "Bebas" so teachers can see which parts come from a rule.

### 3.3 Versioned rule sets and the policy profile per school year

Rules change every year, so they are data with effective dates, not code constants (D-354).

**Target model (Designed):**

- `regulations`: one row per legal document, with kind, number, issuer, ministry, issue, promulgation, and effective dates, status (draft, in force, amended, revoked), source file with SHA-256, and a verification level (official copy, mirror, secondary).
- `regulation_relations`: amends, revokes, implements, references (for example Permendikdasmen 13/2025 *amends* Permendikbudristek 12/2024; Permendikdasmen 1/2026 *revokes* Permendikbudristek 16/2022).
- `rule_sets`: a versioned package of rules for one domain (structure and JP, CP, TP/ATP defaults, assessment and rapor, kokurikuler, ekstrakurikuler, calendar, JTM workload, supervision, TKA, taxonomy), with `applies_from_school_year`, level and region scope, a transition (phase-in by grade), and `supersedes_id`.
- `rules`: the single rules inside a rule set, each with key, strength, value, and source reference.
- `curriculum_versions` becomes the curriculum kind of rule set **by adding columns**, not by rebuilding it.

**The policy in force** is resolved from three inputs: school year, level, and region. For each domain, the active rule set whose school-year range covers that year wins. Two rules follow from this:

- **Documents remember their rule set.** An RPM, an assessment plan, or a rapor stores the rule-set codes it was made under, the same way a document stores its template version. A rule change never silently changes an existing document.
- **Rules follow the school year.** A rule published mid-year applies from the next school year unless the regulation itself sets an earlier date. Example: Bahasa Inggris becomes compulsory in SD/MI from 2027/2028, so the 2027/2028 structure rule set marks it mandatory while 2026/2027 still lists it as optional.

**What exists today (Built):** `curriculum_versions` with `code`, `name`, `status` (draft, active, retired), `meta`, and `imported_at`; the version in force is a config pointer, `config/curriculum.php` `allocation_versions` (`sd` → `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, `mi` → `ALOKASI-MI-KMA-1503-2025`, D-230).

**The minimum before P5-04 (D-355):** `curriculum_versions` gets `effective_on`, `applies_from_school_year`, and `supersedes_id`; a policy-profile resolver replaces the `allocation_versions` pointer with no change in behaviour; and the Profil Jenjang data exists. The reason: the first RPM ever made must already record its rule set. The full `regulations` and `rules` tables, a console for every domain, and *Kabar Regulasi* (a notice to schools when a new rule set is published, with the affected documents) come with P5-09 or their own slice.

The workflow for a new regulation is: watch the official channels (JDIH sites, peraturan.go.id, the curriculum and Kemenag sites); capture the PDF in `docs/sources/` with SHA-256 and a verification level; draft the rule set with page references (Kurator Kurikulum); review by Tim Validasi (D-113); publish through the curriculum console with compare, impact report, and roll back (D-317); tell schools through Kabar Regulasi.

### 3.4 Profil Jenjang: SD and MI as data over one core

SD and MI follow the same national standards and differ in a few domains. SIGRAK keeps one codebase with a shared core, and each level (`sd`, `mi`) has a versioned **Profil Jenjang** record (D-356):

| Setting | SD | MI |
|---|---|---|
| Ministry | Kemendikdasmen | Kemenag (Ditjen Pendis) |
| Structure and JP | Permendikdasmen 13/2025 | KMA 1503/2025, up to 6 extra JP per week |
| Religion subjects | Six Pendidikan Agama subjects | Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, plus Bahasa Arab |
| Character frameworks | 8 DPL, 7 KAIH | 8 DPL, 7 KAIH, KBC / Panca Cinta |
| Wali kelas JTM default | 2 | 6 (D-212) |
| RPM extra fields | — | Tema KBC, Materi Insersi (D-126) |
| Rapor export target | e-Rapor SD | RDM (D-218) |
| External assessment | AN, TKA | TKA; AKMI to be verified |
| Data of record | Dapodik | EMIS |

Code reads the profile; it **never** branches on `level == mi`. A new level (SDLB, or later SMP/MTs) becomes a new profile, not a new code branch. Today the level lives in `schools.level` ∈ {`sd`, `mi`} and decides the subject set and allocation (Built); the profile record itself is Designed and due before P5-04.

### 3.5 Master taxonomy

One master taxonomy names every concept once, with a stable code, an Indonesian label, a parent, a level scope, a source, a strength, a status, and a version, plus crosswalks between versions (for example Profil Pelajar Pancasila → 8 DPL, P5 → kokurikuler, RPP/Modul Ajar → RPM, peserta didik → murid) (D-357). It lives as reviewed JSON in `docs/data/taxonomy/` (`status: draft` until Tim Validasi review) and has eight facets:

| Facet | Contents |
|---|---|
| A Policy | Regulation kinds, standards, rule strengths, layers |
| B Curriculum structure | Level, phase, grade, semester, learning type, subject kind, subject, CP element, CP, TP, ATP, topic, fokus |
| C Profile and character | 8 DPL, 7 KAIH, Panca Cinta, safe school culture |
| D Learning | 3 principles, 3 experiences, 4 framework parts, practices, modes, media |
| E Assessment | Peta Asesmen: domains, purposes, methods, media, scoring (D-344) |
| F Content | Content types, LKPD patterns, game templates, question types |
| G Context | Province, regency, local context, local languages, muatan lokal |
| H Administration | School and staff roles, documents, performance indicators, supervision instruments |

Lists are closed only where a rule fixes them (8 DPL, 3 principles, phases); open lists (methods, media, local context) let a school add its own items. The seed data is task P4-22, which joins the Peta Asesmen dataset and the legacy app catalogs (P4-15) instead of duplicating them. **Status: Designed; the seed data is not written yet.**

### 3.6 Dasar Kebijakan panel and Cek Keselarasan

Two features make innovation accountable without slowing teachers (D-358):

- **Dasar Kebijakan panel.** Every document shows a short panel: the CP and TP it serves, the DPL and principles it touches, and the regulation and rule-set version it follows. The data comes from the taxonomy tags the template fills.
- **Cek Keselarasan.** One checker reads the rule strengths and returns block, warning, and hint lines. It runs on save and in the principal's and supervisor's review. It **never rewrites** the teacher's work.

Both start with the RPM in P5-04 and grow with each slice. **Status: Designed.**

### 3.7 Kurikulum Berbasis Cinta (KBC) and KMA 512/2026

Keputusan Menteri Agama No. 512 Tahun 2026 (Pedoman Kurikulum Berbasis Cinta, set and in force on 31 March 2026) is the KBC source. It replaces SK Dirjen Pendis 6077/2025, which stays useful for its example lessons (D-359). The KMA keeps the five Panca Cinta themes (cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air) and adds six nilai dasar (spiritual, personal, sosial, ekologis, kebangsaan, intelektual), six principles, 12 suggested methods, 10 assessment instruments, and three levels of practice (merintis, berkembang, mekar).

What it means for SIGRAK:

- KBC is **not a subject** and changes no JP. Results are reported through Akhlak or Budi Pekerti and the attitude notes, never as labels or rankings.
- For MI, the RPM wizard adds two optional fields, *Tema KBC* and *Materi Insersi*. The AI pre-fills a suggestion from the CP and the material; the teacher may change or clear both (D-126, D-142). **Status: Designed (P5-04).**
- KMA 512/2026 also covers religion teachers in general schools, SD included. Whether SIGRAK offers the KBC fields to SD religion teachers is an **open owner question** (`docs/drafts/open-questions.md`). Until it is answered, KBC stays MI-only.

### 3.8 Where innovation is wide open

Inside the walls, SIGRAK gives the most room: methods and models (PjBL, inquiry, game-based, outdoor), organisation (thematic, block), media and mode (paper, IFP, phone-free class, licensed AR/VR embeds), assessment beyond the minimum (rubrics, observation, portfolio, readiness checks), any kokurikuler theme, local context, the weekly spread of yearly JP, and AI help through templates. The only fixed edges are L1 mandatory rules, the child data rules (D-023, D-347, D-350), no permanent labels on children, licences (D-337), and the rule that the teacher decides while AI only suggests.

Read more: [`docs/drafts/policy-innovation-layers.md`](../drafts/policy-innovation-layers.md), [`religious-education.md`](religious-education.md#mi-kurikulum-berbasis-cinta-kbc), [`docs/data/regulations/README.md`](../data/regulations/README.md), D-203, D-210, D-230, D-232, D-352…D-359.

## 4. The end-to-end flow

This chapter follows one school through one school year. Each section names who does the step, the screens and URLs where they are known (paths people see are plain Indonesian, D-149), the data touched, the rules that apply, the decisions, and the build status.

```
Tim SIGRAK creates school ─► invites first operator/principal
        │
        ▼
Operator: year ─► classes ─► teachers ─► subjects ─► timetable ─► calendar
        │
        ▼
Curriculum (imported once, versioned) feeds every wizard
        │
        ▼
Teacher: Buat (RPM, Bank Soal, LKPD, games, TKA package)
        │
        ▼
Di Kelas: attendance, journal from RPM, notes, observation (offline)
        │
        ▼
Asesmen: plan, scores, per-TP analysis, remedial ─► leger ─► rapor
        │
        ▼
Portals: principal and pengawas see the same data, validate, supervise
```

### 4.1 Onboarding: school directory, invitations, first accounts, role switch

**Who:** Tim SIGRAK (console), then the operator, then teachers.

**Steps.**

1. **The school directory.** Tim SIGRAK keeps a reference table of SD and MI from the Kemendikdasmen reference site, `school_directory_entries` (NPSN, name, level, address, village, district, region). It holds Kabupaten Kudus (570 schools), Jepara (772), and Pati (825), fetched by hand with `scripts/school-directory/fetch.cjs` and loaded with `php artisan school-directory:import <file>`, idempotent by NPSN (D-169, D-170). Directory rows are not tenants.
2. **Creating the tenant.** Staff create the school in the console (Sekolah, `/sekolah` on the admin host). Typing a known NPSN fills the empty fields from the directory. One school is one tenant; `schools.level` is `sd` or `mi`, and each school belongs to a region (`regions`: province, regency).
3. **The first operator or principal.** Staff invite the first operator or principal from the console (Undangan, `/undangan`). In the school app, only the operator invites, and only teachers (D-182).
4. **Invitation mail and acceptance.** The `invitations` table stores only a SHA-256 hash of the mailed token. A link is valid for 7 days. Re-inviting the same school, email, and role renews the open invitation with a new token. The invitee opens `/undangan/{invitation}?token=…`, sets a password, and gets a membership. An unknown, used, or expired link shows one neutral page that reveals nothing. Accepting marks the email as verified (D-182, D-183).
5. **Sign-in and accounts.** `/masuk` (sign-in), `POST /keluar` (sign-out), `/lupa-kata-sandi` and `/kata-sandi-baru/{token}` (reset), `/akun` (profile and password at `/akun/kata-sandi`). School passwords need at least 8 characters with letters and numbers; after 5 failed sign-ins per email and IP there is a 1-minute wait; loose per-IP limits allow a whole school behind one IP. A reset request gets the same answer whether or not the email exists. The email is read-only on Akun; changing it goes through Tim SIGRAK for now (D-183). Mail goes through Gmail SMTP, transactional only (D-110).
6. **Teachers.** The operator opens Guru (`/guru`), invites teachers, and can resend (`kirim-ulang`) or cancel an invitation.
7. **Role switch.** A user with several memberships switches with `PUT /ganti-peran`. The chosen membership is kept in the session; `SetSchoolContext` reads it (or the user's oldest active membership) and runs before route model binding, so a school-scoped `{model}` from another school is a 404. Memberships carry a `public_id`, so no bigint id ever reaches a prop (D-182, DB-11).
8. **Supervisor assignments.** Staff assign existing user accounts to schools in Penugasan pengawas (`/penugasan-pengawas`). Creating pengawas accounts and the request-and-approve flow come with P5-09 (D-184, D-309).

**Data:** `regions`, `schools`, `school_directory_entries`, `users` (with `nip`), `school_memberships`, `supervisor_assignments`, `staff_users`, `invitations`.

**Rules:** one active principal per school (a unique index on a generated column); every school-owned table is scoped by `BelongsToSchool`, fail-closed; `FindInvitation` is one of the few places that reads without the school scope, because the token itself authorizes it.

**Also designed:** the console gets a district (kecamatan) column on `schools` for targeted Kabar and filters (D-319); a 3-step onboarding shown once after sign-up (class, timetable, students) is in the product concept; *Pindah Sekolah* (teacher transfer, approved by the target school) keeps attendance, journals, scores, notes, rapor, and a read-only copy of documents at the old school, while the teacher takes copies of their own documents, AI keys, and plan (D-307).

**Status:** **Built** (P5-03a, P5-03b, P5-03f): tenancy core, sign-in, reset, invitations, Akun, role switch, console pages for regions, schools, staff, memberships, invitations, supervisor assignments, the school directory, Sebaran sekolah (`/sebaran-sekolah`) with Excel download, and Peta sekolah (`/peta-sekolah`, a bundled Leaflet map). **Designed:** pengawas accounts and requests (P5-09), teacher transfer, 3-step onboarding, kecamatan targeting, 2FA for staff.

### 4.2 School year setup: years, classes, assignments, subjects, timetable, calendar

**Who:** the operator. Principals and teachers get read views later.

**Academic year (Tahun Ajaran, `/tahun-ajaran`).** A year's name follows its start date (`2026/2027`) and is never typed. It lasts at most 12 months, does not overlap another year of the school, and has an even-semester start date (`even_semester_starts_on`). The form suggests 1 July to 30 June, with semester 2 from 1 January. The "current" year is the one that includes today, else the next, else the latest. A year with classes cannot be deleted (D-241).

**Classes (Kelas, `/kelas`).** A class has a grade (1–6), a free-text name of up to 20 characters unique per year, and an optional homeroom teacher. Its model is `SchoolClass` because `Class` is reserved in PHP. URLs use `public_id`: `/kelas/{class}`, `/kelas/{class}/ubah` (D-241).

**Copy classes.** When a new year is empty, the operator can copy the previous year's classes one grade up (`POST /tahun-ajaran/{academicYear}/salin-kelas`). Teachers are left empty; students move later through Kenaikan. Grade 6 is not copied; a checkbox (on by default) recreates last year's grade 1 classes; a name that starts with its grade number gets the new number (`4A` → `5A`), other names keep their name; a clash adds the grade; copying into a year that already has classes is refused (D-242…D-246).

**Subjects of a class.** A class takes the compulsory subjects with an intrakurikuler allocation for its grade in the level's structure version, plus the region's local content unless the school switches it off, plus anything the school switches on. Optional content (Bahasa Inggris and IPAS in grades 1–2, Tata Boga, Seni Suara Daerah) is off by default and stays out of the rapor unless `in_report` is set. A compulsory subject cannot be switched off. This logic lives in `app/Curriculum/SubjectsForGrade` (D-204, D-230, D-231). The operator page that switches school subjects on and off, and the muatan lokal cap warning (D-232), are not built yet.

**Teaching assignments (`/kelas/{class}/guru-mapel`).** `teaching_assignments.teacher_id` is `NOT NULL`: a class subject without a row simply has no teacher yet. When a class gets a homeroom teacher, every class-teacher subject without a row goes to them; guru mapel subjects wait for the operator. A new homeroom teacher takes over the rows the previous one held; changing a class's grade drops rows the new grade does not take; a row for a subject the school later switches off is shown as "Tidak diajarkan lagi" (D-240).

**Timetable (Jadwal).** The operator fills `/kelas/{class}/jadwal`; teachers see Kelas Saya > Jadwal at `/kelas-saya/jadwal`. Rules (D-247…D-251):

- Rows are lessons or non-lesson rows: Istirahat, Upacara, Kegiatan (with an optional label such as "Senam"). Non-lesson rows have no subject and 0 JP.
- The operator types start and end times; `period_no` is computed from them.
- The teacher of a lesson is read through the class's teaching assignment; there is **no `teacher_id` column** on `timetable_slots`. A subject without a teacher cannot be scheduled.
- A teacher clash with another class is refused, naming the class and time. Rows of one class may not overlap, except that the six religion subjects may run in parallel.
- A subject's weekly JP must equal its allocation. Over the allocation is refused; under is shown, and the timetable is "complete" only when everything matches. 1 JP = 35 minutes.

**Calendar (Kaldik).** Designed in detail in P5-03i (D-252…D-261, D-323):

- `national_holidays` is one central read-only table (national holidays and cuti bersama, with decree and a provisional flag), merged into every calendar view and updated by Tim SIGRAK each year after the SKB; `php artisan calendar:import-holidays <file>` loads it, after an online check of the official SKB and Tim Validasi sign-off (D-253, D-254). Cuti bersama counts as a holiday by default (D-255).
- Tim SIGRAK builds region reference calendars. For Kudus, Pati, and Jepara this is one Jawa Tengah calendar from the provincial guide's 6-day dates (D-257, D-323). The operator copies it with "Salin dari kalender wilayah", adds school events, and later sees "Terapkan perubahan wilayah" when the region calendar changes (D-256).
- `calendar_events.class_id` makes an event apply to one class only (D-258).
- `app/Calendar/EffectiveDays` counts effective days and weeks per class. Acceptance check: a 6-day SD/MI in Jawa Tengah 2026/2027 has 254 effective days (134 + 120) (D-259, D-323).
- `app/Calendar/ConflictChecker` gives one set of clash messages (D-260); an impact preview ("12 pertemuan RPM di 6 kelas akan bergeser") appears before a change moves RPM meetings (D-261).

**Draft timetable generator (P5-03j, D-262).** "Susun draf jadwal" for one class or all, from the day frame, allocations, assignments, and locked slots. Hard limits cannot be changed; soft preferences are on/off switches per school (no numeric weights). The output is always a draft with a comparison screen. It runs on the `default` queue.

**Data:** `academic_years`, `classes`, `teaching_assignments`, `timetable_slots`, `school_subjects`, `region_subjects`; later `calendars`, `calendar_events`, `national_holidays`.

**Status:** **Built** (P5-03d, P5-03e, P5-03h): academic years, classes, copy classes, teaching assignments, the timetable with all its checks. **Designed:** the school-subject switches page and muatan lokal warning, the calendar (P5-03i), the draft timetable generator (P5-03j). P5-03 was reopened for P5-03i and P5-03j (D-252); both come before or alongside P5-04.

> **Differs from the final docs.** `data-model.md` still shows `timetable_slots.teacher_id` and no `kind`/`label`; D-247 and D-249 win. It shows region calendars as "defaults a school calendar overrides"; D-256 replaces that with copy-plus-offered-updates. It also lacks the `public_id` columns on years, classes, and memberships (D-182, D-241).

### 4.3 Curriculum: data, import, versions, overrides, validation

**Who:** Tim SIGRAK (data, import, console), Tim Validasi SIGRAK (review), the school (changes), the principal and pengawas (validation).

**The data files.** All curriculum data lives in the repo as JSON in `docs/data/curriculum/` and `docs/data/regulations/`, each with provenance and `status: draft` until Tim Validasi review (D-113, D-203):

| File | Content |
|---|---|
| `subjects.json` | Subject catalogue: codes, kinds, default teacher, religion, level, aliases, regional defaults (D-227) |
| `data_kurikulum_dengan_cp.json`, `struktur_alokasi_sd.json` | KM-SD-2026-R2 from the legacy app: 432 topics, 2,021 fokus, 1,372 TP, 12 subjects |
| `curriculum-structure-permendikdasmen-13-2025.json` | SD structure and JP, including kokurikuler |
| `mi-allocation-kma-1503-2025.json` | MI structure and JP (verified, D-038) |
| `cp-pendidikan-agama-sd-bkpdm-020-2026.json` | CP for six SD religions |
| `cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` | CP for the MI Kemenag subjects and Bahasa Arab |
| `tp-atp-pendidikan-agama-sd-2026.json` | Draft TP/ATP for five non-Islam SD religions |
| `tp-atp-pai-bahasa-arab-mi-2026.json` | Draft TP/ATP for the five MI subjects |

**The shape.** A subject has topics (materi pokok, the ATP rows) per grade and semester, with JP, weeks, and references. Each topic has fokus items and learning objectives (TP). `focus_item_objectives` links fokus to TP and is filled by a data task. CP-only versions store CP rows in `learning_outcomes` (D-226). `subject_allocations` holds JP per subject, grade, and learning type (intrakurikuler or kokurikuler).

**The import (`php artisan curriculum:import`).** With no file named, it loads the files listed in `config/curriculum.php` in order. Rules (D-229): each file is one transaction; rows are matched by `code` and never deleted; a new version is `draft` and an import never changes an existing version's status; mismatches (JP sums, phase vs grade, fokus–TP links outside the topic) are reported and do not stop the import unless `--strict` is given; legacy IDs are kept as aliases in `legacy_codes`. Documented corrections live in `app/Curriculum/Import/SourceOverrides.php` while the source files stay byte-for-byte (D-234 trims Tata Boga grade 6 semester 2 to 28 JP). KKA rows for grades 5–6 are not imported as intrakurikuler; KKA runs through kokurikuler (D-208).

**Versions in v1.** `KM-SD-2026-R2`, `CP-PABP-BKPDM-020-2026`, `CP-PAI-BA-MADRASAH-9941-2025`, `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, `ALOKASI-MI-KMA-1503-2025`, `TPATP-PABP-SD-2026-R1`, `TPATP-PAI-BA-MI-2026-R1` (data model; D-139, D-145). The versioning additions of D-355 come before P5-04 ([section 3.3](#33-versioned-rule-sets-and-the-policy-profile-per-school-year)).

**The CP → TP → ATP → fokus chain in the app.** In every wizard the chain is pick-only: Kelas → Mata pelajaran → Semester → Materi pokok (ATP) → Fokus → TP, all read from the data (content engine, wizard rule 4). Teachers never type CP or JP.

**School overrides.** A school can use the defaults, adjust a topic, or add its own (`school_topic_changes`, with `base_topic_id` NULL for a new topic). Only these changes go through validation: guru → kepala sekolah → pengawas, recorded append-only in `curriculum_validations`, and every return carries a note (D-019). Default curriculum used as-is never enters the queue. "Pulihkan bawaan" per topic and "Reset semua penyesuaian" let the principal or operator restore defaults with confirmation and an audit log; old changes stay as history and existing RPMs do not change (D-276).

**Tim Validasi SIGRAK.** Curated data (curriculum, religion data, catalogs, seed lists, content packs) is reviewed and signed off by Tim Validasi SIGRAK, recorded as such with the review date, not by individual teacher names (D-112, D-113). With D-339 the Validator becomes a tracked console role.

**The curriculum console (D-317).** Staff compare a draft version with the active one, publish, roll back, and see an impact report (schools and RPMs affected). A JP audit flags RPMs whose JP no longer match the latest ATP; the teacher gets a Kabar and decides. There is no AI draft from Buku Guru (SIBI licence). With D-354 the console extends to every rule-set domain.

**Religion.** SD offers six religion subjects (D-034), each 3 JP per week (D-141). MI teaches the Kemenag subjects instead of one PAI subject (D-035, D-146). `students.religion` exists only to group religion classes and is sensitive (D-036). Pendidikan Kepercayaan is not in v1 (D-037); the special-education CP is in scope for inclusive schools and still to be transcribed (D-155).

**Status:** **Built** (P5-03c, P5-03g): curriculum tables, `learning_outcomes`, the import command, the subject catalogue, `SubjectsForGrade`. **Designed:** versioning columns and resolver (D-355), school topic changes and validation (P5-09), the curriculum console (D-317), Kabar Regulasi. **Data still draft:** every curriculum and regulation file awaits Tim Validasi review.

Read more: [`data-model.md`](data-model.md#curriculum), [`religious-education.md`](religious-education.md), [`docs/data/curriculum/README.md`](../data/curriculum/README.md), [`docs/drafts/km-sd-2026-r2-source-check.md`](../drafts/km-sd-2026-r2-source-check.md), D-019, D-020, D-113, D-204, D-208, D-226…D-234, D-276, D-317.

### 4.4 AI content creation: keys, templates, wizards, documents, exports

**Who:** teachers (and a principal who makes content). Tim SIGRAK tunes templates in the console.

**Step 0: Kunci AI Saya.** The first time a teacher uses an AI feature, SIGRAK asks for a Gemini key (D-012, D-023). The teacher pastes the key and types a project label; the server makes a tiny test generation and saves the key only if it passes. A duplicate key is refused by fingerprint. A key from a project that already has a key gets a warning that it shares the quota. The second and later keys need a consent tick, because rotating several free projects may break Google's terms and the risk is the teacher's. A key can be marked *cadangan* (backup) and is used only when every normal key is unavailable (D-119). The page shows each key's status, cooldowns, and requests left today (D-128). Principals need no key unless they make content. Details are in [chapter 7](#7-ai-layer-and-child-data-protection).

**One template per content type.** There is no generic generator (D-024). Each content type has its own template class, wizard, queued job, and renderers, and they share only the pipeline, the AI gateway, the editor shell, and export. A template has nine parts: metadata, wizard configuration, input schema, prompt instructions, output schema, design, validation rules, renderers, and version. Definitions live in code; the database holds a registry (`templates`) and immutable `template_versions`, and staff can override prompt wording in the console, which creates a new version without a deploy (D-013).

**Wizard rules.** Ask only what a teacher knows pedagogically (class, subject, topic, goal, students' condition, duration, difficulty, number of items, learning model, assessment type). Never ask what the system knows (JP, meetings, CP, phase, TP codes, school identity, dates). At most 4 steps, each with defaults, and "Biarkan otomatis" where AI can choose. One optional note of up to 300 characters goes into the prompt as delimited *data*, with the hint "Jangan tulis nama atau data pribadi siswa".

**The pipeline.**

```
Wizard answers
  -> SpecificationBuilder (validate, enrich from DB)
  -> Specification (stored, immutable)
  -> for each step: PromptBuilder -> AIGateway -> OutputValidator
       invalid? -> OutputRepairer (fixes, then at most 1 AI repair)
  -> Document saved: content JSON + template key + version
```

Data fields (identity, CP, TP codes, meetings, minutes, references) are merged from the database after generation; AI output for them is ignored. Large templates run in steps (for RPM: design, meetings in batches, assessment), all on the same pinned key. Each content type has its own job: `GenerateRpm`, `GenerateBankSoal`, `GenerateLkpd`, `GenerateQuizBattle`, `GenerateTkaPackage`, plus `ReviseSection`. The page shows "Langkah 2 dari 4"; the teacher can leave and gets a Kabar notice when it is done (D-152), with a toast, and a sound and a screen notification depending on the teacher's settings (D-285).

**Editing.** The preview is a structured editor per section, never raw JSON, and AI text is always escaped. "Perbaiki bagian ini" revises one section (*sederhanakan*, *buat diferensiasi*, *sesuaikan untuk siswa berkebutuhan khusus*, *perpanjang*, *perpendek*) and shows old and new side by side. Each save or revision keeps the previous content; the last 5 revisions are kept (D-153), and a revision linked to an approval is never pruned (D-282).

**RPM.** The wizard steps are *Kapan* (start date, or "RPM lepas jadwal"), *Apa* (materi pokok and fokus; JP and meetings are computed and locked), *Cara* (learning model per meeting, or automatic), *Periksa*. Sections follow the official deep-learning frame: Identifikasi, Desain Pembelajaran, Pengalaman Belajar (per meeting, with Memahami, Mengaplikasi, Merefleksi and the principles berkesadaran, bermakna, menggembirakan), Asesmen, plus attachments and continuity. The 8 dimensi profil lulusan (DPL1…DPL8) are a fixed set. MI adds the optional KBC fields ([section 3.7](#37-kurikulum-berbasis-cinta-kbc-and-kma-5122026)). A **kokurikuler variant** has its own schema following Panduan Kokurikuler 2025, with the forms Lintas Disiplin, Gerakan 7 KAIH (the seventh habit is "Tidur Cepat", D-207), and Cara Lainnya; KKA runs here (D-208). The annual kokurikuler JP target comes from `subject_allocations` (D-277).

**RPM scheduling (P5-04, designed in detail by D-267…D-275).** Meetings are placed on the class timetable from the start date. A journal outcome drives the schedule: `not_done` shifts later meetings; `partial` does not shift but carries the unfinished experiences into a "Lanjutan" block of the next meeting (D-267). Every shift is applied automatically, shown ("2 pertemuan bergeser…"), and undoable with Batalkan, and the Promes print gains a Realisasi view (D-274). Semester-2 RPMs can be drafted early and are placed from the even-semester start (D-272). An RPM lepas jadwal can later be placed with "Pasang ke jadwal" (D-273). "Pakai untuk kelas lain" copies an RPM to a parallel class with no AI call (D-275). One JP balance per class and subject before SAS (*Neraca JP*: Pas, Kurang n JP, Ada n JP cadangan) replaces several warnings (D-269). RPM readiness per subject (Siap, Segera habis, Perlu RPM) uses defaults of 10 and 3 effective days set in the console (D-270), with a Monday summary Kabar to teachers only (D-271).

**Bank Soal.** The wizard covers scope (Penilaian Harian, STS/SAS, Ujian Sekolah), composition per type (PG, PG Kompleks, PGK Kategori, Benar-Salah, Menjodohkan, Isian, Uraian), difficulty and cognitive mix, and timing. In Fase A a note recommends observation and performance, but written items are never blocked (D-206). The AI fills only per-item data; kisi-kisi, naskah, kunci, pembahasan, indikator, and kartu soal are rendered from it with no extra AI call (D-293). `question_items` indexes every item across documents; "Ambil dari soal saya dulu" reuses matching items and asks the AI only for the gap, and recently used items are marked "pernah dipakai" (D-294). Distractors may carry misconception tags (D-351).

**LKPD.** v1 is the current LKPD (title, goal, instructions, steps, table, questions, reflection) plus a fixed block set for grades 1–3 drawn by the renderer: `trace`, `match_lines`, `cut_and_stick`, `colour_by_rule`, `count_pictures`, `fill_table`, `short_answer` (D-296). The output schema gains only a `pattern` field, so the pattern library (*Bentuk LKPD*) arrives in waves without a migration (D-329). Wave 1 has nine patterns: Detektif Teks, Peta Cerita, Data Kelasku, Etnomatematika, Misi Petualangan, Tiga Jalur, Tiket Keluar, Kotak Terkunci, Pikir Komputasional Tanpa Komputer (D-332). Later waves add numeracy, literacy, game, and hybrid blocks, a school *Konteks Daerah* profile with reviewed seed lists for Kudus, Jepara, and Pati, so the AI never invents local facts (D-333), school presets shared inside one school only (D-335), QR links straight to allowlisted third-party URLs (D-330), embeds only with a clear licence (D-337), and a self-hosted H5P player and 3D viewer (D-334). Every LKPD feature is core (D-336). Interactive steps run online in the teacher's app or the IFP session (D-331).

**Quiz Battle and Game kelas.** Quiz Battle uses a saved Bank Soal set (auto-gradable types) or new questions, with teams or individual play, timer, points, and rounds. The vanilla JS runtime owns layout, controls, scoring, and animation; the AI only supplies content and allowed config values; no AI-written code (D-011, D-015). Two ways to reach students:

- *Tayangkan*: a fullscreen route in the teacher's session.
- *Unduh game*: a single offline HTML file with inlined runtime, content as escaped JSON, and a CSP that blocks all network access. It holds teaching content only.

Game kelas (an add-on, D-171) grows this into an IFP learning mode: a playlist from one RPM meeting (goal, prior-knowledge check, manipulative, team practice, exit ticket, reflection). It keeps a class-level summary per item, team, and TP with no student data (D-172), feeding "Perlu diulang", AI follow-up ideas from the TP and wrong-answer pattern, the journal, and Asesmen. On the shared panel the teacher pairs their phone with a 6-digit *Kode Tayang* at `/pid` on the app host instead of typing a password (D-173, D-233). New templates follow learning functions: Lomba Tim, Pilah & Kelompokkan, Pasangkan first (D-176). Manipulatives and classroom helpers are part of Sigrak Guru (D-175). A weekly offline bundle ships with the Game kelas core (D-181). Kartu Jawab (printed A–D cards scanned on the phone) comes last (D-174). The reference device is the Kemendikdasmen 75-inch Android IFP; its WebView version must be checked on a real unit (D-179).

**Paket Soal TKA.** A practice package for Bahasa Indonesia (reading only) or Matematika, 30 items by default, in the three TKA forms: PG, PGK MCMA, PGK Kategori, with AN literasi and numerasi competencies offered too (D-111). Stimulus visuals are never AI images: tables and charts are structured data, plane shapes come from a bundled figure set, and cube stacks are height maps drawn with three.js with keys computed by code (D-030). PGK Kategori uses the label pairs Benar/Salah, Sesuai/Tidak Sesuai, Setuju/Tidak Setuju (D-045). It feeds Simulasi TKA ([section 4.8](#48-tka-practice-exam)).

**New assessment templates (Designed, D-346).** Rubrik Kinerja, Observasi DPL, Cek Kesiapan Awal, Akomodasi Murid, and self and peer assessment cards, each its own small template (see [section 4.6](#46-assessment)).

**Dokumen Saya.** Every result is a document in Dokumen Saya (account menu) with its template version, revisions, validation status, comments from the principal and pengawas, and exports. Generated content is usable at once; principal and supervisor can view and comment (D-019). RPM, Prota, Promes, rencana asesmen, and Berkas can also be sent for *pengesahan* ([section 4.9](#49-portals-principal-operator-supervisor)).

**Exports.** PDF through Gotenberg, DOCX through PhpWord, Excel through Laravel Excel, the game HTML through `GameExporter`, all from the content JSON and never from a screenshot. *Buka di Canva* downloads the file and opens canva.com (level 1). Details are in [chapter 8](#8-exports-localization-and-ui-writing).

**Status:** **Designed** for all of this section. Nothing of the content engine, the AI layer, Dokumen Saya, or the exports is built yet; it is P5-04 (content core: AI provider engine, template engine, RPM, Dokumen Saya, PDF/DOCX export) and P5-05 (Bank Soal, LKPD, Quiz Battle). The `lang/id` files for these areas exist only as placeholders. Game kelas beyond Quiz Battle, the LKPD waves, and the assessment templates are **Later** waves after v1.

> **Differs from the final docs.** `content-engine.md` still describes the LKPD output without blocks or `pattern` (D-296, D-329), Bank Soal exports without the item library (D-293, D-294), RPM scheduling without `partial`, the JP balance, or undo (D-267…D-275), a PGK Kategori pair "mendukung/tidak mendukung" that D-045 dropped, and "one PDF per download, never a merge", which D-280 amends for Berkas Administrasi.

### 4.5 In class: Di Kelas, offline sync, attendance, journal, observation, class tools

**Who:** teachers on a phone, mostly during or right after a lesson.

**Hari Ini** is the teacher's home: today's schedule, what still needs filling, this week's progress, and Kabar. It shows readiness warnings (yellow and red only) and, for tomorrow's slots without an RPM meeting, a "Siapkan besok" button that opens the RPM wizard pre-filled (D-266).

**Di Kelas** holds the daily records:

- **Kehadiran (attendance).** Daily, one record per student per day, taken by the homeroom teacher; everyone starts as present and the teacher taps the absentees (D-029). The menu is named *Kehadiran* (path `/kehadiran`), not *Absensi* (D-302). Recaps per week, month, and semester print and export to Excel. An early warning flags a student when unexplained absences bring attendance below 90% in a calendar month, or after 3 consecutive school days absent without explanation; both thresholds are school settings (D-303).
- **Jurnal.** Filled from the RPM meeting: the teacher marks the outcome `done`, `partial` (with the unfinished experiences), or `not_done` (with a required reason: Kegiatan sekolah, Guru bertugas di luar, Murid tidak hadir, Bencana/cuaca, Lainnya). The meeting status is derived from the journal (D-267, D-268).
- **Catatan Siswa.** One form for anecdotal, counselling (BK), and achievement notes; BK notes have restricted visibility. One-tap tags and "masuk rapor" for achievements (Part B B-09).
- **Observation and rubric entry** for the new instruments: a tap per indicator level per student (D-349).

**Offline.** The school app is an installable PWA (D-093). Offline work is limited on purpose:

| Works offline | Decision |
|---|---|
| Kehadiran, Jurnal, Catatan Siswa | D-016 |
| Today's and tomorrow's RPM meetings, read-only, text only | D-289 |
| Pindai LJK scan queue (answers and confidence, no images) | D-289 |
| Observation and rubric entry | D-349 |

Everything else (editing, AI, exports) needs a connection.

```
Phone (online) caches the Di Kelas shell and today's data
Phone (offline) saves rows in IndexedDB with a client_uuid
Phone (back online) sends the queue: POST /sync (session + CSRF)
Server upserts by client_uuid (repeats are no-ops), checks policy
```

Sync uses the normal web session and CSRF token, not an API token. Queued rows survive an expired session until the next sign-in. Cached student data is cleared on logout and never includes religion (D-051). Last write wins per row, with the device time kept in `recorded_at_device`. After a release, the app shows "Versi baru tersedia, muat ulang" and syncs the offline queue before reloading (D-321).

**Completeness.** One service, `app/Progress/`, produces slot rows (teacher, class, subject, date, item, state ∈ done, missing, not due, excused) for RPM, Jurnal, Kehadiran, teacher reflection, Prota/Promes/assessment plan, diagnostic assessment, and kokurikuler JP. Every screen and every role reads the same rows (D-263). Windows are Hari ini, Minggu ini, Semester ini (D-264). A Monday job writes weekly `progress_snapshots` for trends (D-265).

**Kelas Saya** holds class data: Murid (with transfers and Kenaikan), Jadwal (built), Piket, Keuangan, Inventaris.

- **Piket:** *Bagi otomatis* splits active students over the class's school days, balanced by count and gender, with optional rotation and per-student tasks; an absent student's task passes to a groupmate (D-297). The school may show today's group on the Game kelas idle screen (D-298).
- **Keuangan kelas** (school may switch it off): income and expenses in integer rupiah; *Sumbangan sukarela* records voluntary contributions, and the words "tunggakan", "kurang bayar", and "belum lunas" never appear (D-300). The wali kelas may share a transparency report by hand through WhatsApp; the app never sends money reminders (D-299).
- **Tabungan (savings):** a ledger per student that follows the student across classes; on graduation or leaving, the app prepares a handover record (D-219, D-301).
- **Inventaris:** a room view of the school's inventory; the wali kelas edits their own room (D-306).
- **Other homeroom features** accepted in P4-04: class agreement and positive discipline notes, 7 KAIH tracker with a printable home sheet, home visits, light health data, parent messages as plain `wa.me` text with a contact log, ekstrakurikuler and Pramuka records early in v1 (D-214…D-216).

**Berkas Administrasi.** A semester bundle in Dokumen Saya: cover, completeness summary, contents, title pages, and NIHIL pages for empty parts, as one merged PDF (Gotenberg merge route, the one exception to "no merge") or a ZIP. Profiles: Standar SIGRAK, Format Kudus, and a K3 preset. Files are kept 7 days. BK notes are never included (D-278, D-280).

**Status:** **Built:** Kelas Saya > Jadwal only. **Designed:** everything else in this section is slice P5-07 (Di Kelas with offline sync, Jurnal from RPM), plus parts of P5-04 and P5-08.

> **Differs from the final docs.** `product-concept.md` and `design-system.md` still use *Absensi* and "siswa"; D-302 and D-210 use *Kehadiran* and *murid*. `architecture.md` and `data-model.md` limit offline to three forms and `journals.status` to `done`/`not_done`; D-289, D-349, and D-267 widen both.

Read more: [`content-engine.md`](content-engine.md), [`ifp-classroom.md`](ifp-classroom.md), [`docs/drafts/lkpd-exploration.md`](../drafts/lkpd-exploration.md), [`docs/drafts/legacy-upgrade-01.md`](../drafts/legacy-upgrade-01.md), [`docs/drafts/administration-features.md`](../drafts/administration-features.md), [`architecture.md`](architecture.md#3-offline-sync-di-kelas), D-011…D-016, D-024, D-029, D-171…D-181, D-263…D-278, D-289, D-293…D-303, D-329…D-337, D-349.

### 4.6 Assessment

**Who:** teachers (guru kelas and guru mapel) for their own subjects; the principal sets school-wide assessment settings.

**Plan and assessments.** *Rencana Penilaian* (`assessment_plans`) lists the assessments per class, subject, and semester. An assessment (`assessments`) is formative, summative (per lingkup materi), STS, or SAS. When it uses a Bank Soal document, the items are **copied** into `assessment_items` as a frozen snapshot with `kode_tp`, cognitive level, and maximum score, so later edits to the document never change a past score (architecture: "data that crosses a boundary is a snapshot"). Scores go into `scores` per item and student, `decimal(5,2)`. A fast score grid (*grid cepat*) is part of P5-08 (D-286).

**KKTP and per-TP analysis.** The KKTP approach (description, rubric, interval, or percentage) is the school's choice, with a numeric threshold set per school when used (D-214 A3; strength `school_discretion`). Analysis per TP is automatic. "Analisis Daya Serap" is a print layout and search alias of the per-TP analysis, not a separate page. *Kelompok belajar* per TP (perlu bimbingan, cukup, siap pengayaan) can be adjusted by the teacher, and the next RPM wizard offers the group **counts** (never names) as the students' condition (D-295). Item analysis writes real difficulty and discrimination back to `question_items` (D-214 A7, D-294).

**Remedial and enrichment.** Planned from the per-TP analysis (D-214 A1). A remedial score replaces the lingkup materi score, capped at the KKTP by default (a school may remove the cap); the pre-remedial score is kept (D-291). A JP surplus before SAS suggests remedial or enrichment (D-269).

**Diagnostic and reflection.** A start-of-year diagnostic assessment with short prerequisite items and a non-cognitive checklist (D-214 A2); a teacher reflection at least once a semester, pre-filled from journals and scores (D-214 A4); the murid feedback asked by Standar Proses is gathered on paper and stored as class totals only (D-225).

**Pindai LJK (answer-sheet scanning).** Core in P5-08 (D-286). The printed sheet looks like the sheet schools already use: header with Mata Pelajaran, Kelas, Nama, Nomor; letter boxes the student crosses out; numbered lines for Isian and Uraian; four corner markers and a QR that holds only the sheet's random `public_id` (D-287, D-325). Scanned forms: PG, PG Kompleks, Benar-Salah and PGK Kategori, Menjodohkan. Isian and Uraian are entered by hand with partial credit. No AI reads student answers, and sheet images are never uploaded (D-287). The scan queue works offline and reaches `scores` only after sync and teacher confirmation (D-289). The same camera code later serves Kartu Jawab (D-174) and a paper fallback for Simulasi TKA (D-288, after P5-08).

> **Open point.** D-287 and D-289 describe scanning on the teacher's device, with only the results synced. D-347, recorded later, says multiple-choice answers "are read by OMR on SIGRAK's own server". Both agree that no AI reads answers and no student data goes to an AI provider. Where the OMR runs (phone or server) should be confirmed in P4-19 or at the start of P5-08; the OMR library choice is already listed as a one-day spike on a low-end Android phone (`legacy-upgrade-01.md`, open questions).

**Peta Asesmen and the new instruments (Designed, D-344…D-351).** The owner's *Master Assessment Landscape* becomes one curated dataset in `docs/data/assessment/` (domains, purposes, methods, media, scoring, uses), reviewed by Tim Validasi, plus tags on every instrument (D-344). Its design is its own task, P4-19 (D-345). New templates: Rubrik Kinerja (practice, demonstration, project, product, MI ibadah practice), Observasi DPL (behaviour indicators observed several times, shown as trends), Cek Kesiapan Awal (a profile, not a score), Akomodasi Murid, and self and peer assessment cards (D-346). A guided "five questions" wizard in Asesmen (*Apa yang diukur? Untuk apa? Bagaimana? Bukti apa? Tindak lanjutnya apa?*) picks from the taxonomy. Wave A covers the dataset v1, tags on Bank Soal and LKPD, Rubrik Kinerja, Observasi DPL with phone entry, Cek Kesiapan Awal, and misconception tags on distractors (D-351). P5-08 itself is unchanged.

**Sensitive data and AI.** Teacher-side AI makes instruments, tags misconceptions, and drafts feedback from class patterns. Student names on paper are read by text recognition on the teacher's device and matched to the roster there. The text of an essay answer, **without any student data**, may be sent to Google AI for scoring help, and the teacher decides the score. Reading handwriting and voice with AI comes later (D-347). Social-emotional, special-needs, and interest results follow the health-data rule: the class's teachers (and assigned GPK), operator, and principal only; never to AI; never permanent labels or diagnoses; supervisors see totals only (D-350). Not planned: IRT/Rasch per school, adaptive engines, prediction models on children, AI on children's input, copies of national surveys (`assessment-landscape.md` §6–§7).

**Status:** **Designed** (P5-08 for the core; P4-19 and Wave A for the landscape). Nothing is built.

### 4.7 Report cards and exports

**Who:** guru mapel and wali kelas (grades, descriptions, notes), the principal (sign-off), the operator (exports).

**The 12-component rapor.** Following Panduan Pembelajaran dan Asesmen 2025, the rapor has twelve minimum components: identity, school, class, semester, subject, score, competency description, kokurikuler description, ekstrakurikuler, absences, catatan wali kelas, and parent response (D-210, D-214 H1). Fase A may report without numbers (`report_cards.numeric_grades = false`).

**Grades.** A subject's rapor grade is the weighted mean of the summative lingkup materi average, STS, and SAS, by default 50/25/25, set per school in Pengaturan Asesmen. A component not held is dropped and the rest normalised. **A formative assessment can never be a component**; this is a server check with a test (D-205, D-290).

**Descriptions.** The competency description per subject is drafted from the highest and lowest TP achievement in the formative results (D-205). The AI draft sends only per-TP mastery levels for an anonymous "Murid A"; the real name is merged in after generation on the server (`ai-provider-engine.md`, privacy rule 2). The catatan wali kelas may also be drafted by AI from attendance counts, note types, and per-TP results, never names or personal data, and the homeroom teacher edits and approves it (D-213).

**Kokurikuler and ekstrakurikuler.** Kokurikuler is assessed on the 8 DPL; the rapor gets a description of dimensions strengthened and still developing (D-210, D-214 A5). Ekstrakurikuler records (activities, pembina, members, attendance, rapor description), with Pramuka offered by default, are built early in v1 (D-216); `report_extracurriculars` holds the rapor rows (D-205).

**Leger and sign-off.** Each guru mapel confirms their subject's grades, the wali kelas confirms the class leger, then the principal signs off and the leger locks. Rapor prints and exports always come from the locked version. Only the principal can unlock, with a logged reason. "Cek kesiapan rapor" checks the 12 components per student and warns but does not block (D-292).

**Exports.** v1 exports report-card data in SIGRAK's own Excel layout (D-144). The export in the import layout of **e-Rapor SD** and **RDM** (Rapor Digital Madrasah) is built right after slice 6 (Asesmen), since it removes double entry (D-218). Files for those systems follow the system's own spec (XP-24). Per-student files are named by class and roster number, never by name or NISN (D-107, XP-32).

**Kenaikan and kelulusan.** Kenaikan (naik, tinggal, lulus) and mutasi (masuk, keluar, pindah rombel) are drafts that can be edited and printed until *Tetapkan*; the kenaikan draft feeds the dewan guru meeting sheet and berita acara (D-215 H3, D-304). Grade 6 graduation covers SKL and ijazah data (D-215 H4). Savings balances follow the student; on graduation or leaving, a handover record is prepared (D-301).

**Status:** **Designed** (P5-08, then the e-Rapor/RDM export task P4-10).

### 4.8 TKA practice exam

**Who:** a teacher or the operator creates a session; the teacher proctors; grade 6 students take it on the exam site; the principal sees results.

**What it is.** A practice simulation of the national Tes Kemampuan Akademik for SD/MI, for drill. It does not have to match the official TKA exactly; the official framework (`docs/data/tka/kerangka-tka-sd.json`, Perka BSKAP 047/H/AN/2025) is a reference for item style, not a compliance target. Duration (75 minutes), item count (30), category limits, and scoring are practice defaults that teachers can change (D-089). It is the *Simulasi TKA* add-on (D-158). The target is the 2028 TKA season (D-162). The UI never says "TKA" alone, always "Simulasi TKA" (design system).

**The flow.**

1. **Siapkan soal.** Make a *Paket Soal TKA* with its template ([section 4.4](#44-ai-content-creation-keys-templates-wizards-documents-exports)), then check and edit it.
2. **Buat sesi.** Pick the package, the grade 6 class, date, time window, and duration. One session is one class of 10–25 participants (D-092). The package is **frozen** into `exam_items` when the session is scheduled; later edits never change a running exam (D-124).
3. **Kartu login.** The system prints one card per student (name, class, session, username, password), several per page, through the `exports` queue. Credentials work only for that session.
4. **Jalankan.** The proctor opens the session and reads out the current token: 6 characters derived from the session secret and the current 15-minute window (TOTP-style), so tokens are never stored (D-027). Students sign in on `ujian.sigrak.com` with username, password, and token. The proctor watches progress, can reset a stuck participant's login, or extend time for one participant; both are logged in `exam_events`.
5. **The exam client.** A plain TKA-like screen: question area, number grid, *ragu-ragu* flag, timer, A−/A/A+ font control at the standard default size (D-121). Item and option order is shuffled per participant, with stimulus groups kept together. Every answer autosaves with a sequence number (idempotent), so a reconnect resumes where it stopped. Time is enforced by the server; the client timer only displays it. No AI calls, no links into the app, nothing beyond the participant's own exam (D-027, D-094).
6. **Hasil.** Score 0–100 per subject, with partial credit for PGK forms, normalised and labelled as a practice estimate (D-030, D-042). The result shows the category (Baik, Memadai, Kurang) plus the Istimewa predicate for scores of 95 or more, for example "Baik-Istimewa" (D-041, D-120). Per-competency and per-item analysis, Excel and PDF export, and an optional copy into Asesmen.

**Integrity and capacity.** Exam-site login throttling is keyed by session and username, not by IP, because a whole class shares one school IP; Cloudflare rules allow bursts of 30–40 logins from one IP (D-054). Passwords are encrypted so cards can be reprinted, and purged 30 days after the session closes. The exam site is not installable as a PWA (D-093).

**Why students may use it.** Gemini's terms bar services likely to be used by people under 18. SIGRAK's answer: AI runs only in teachers' sessions *before* the exam to prepare questions; students receive finished, teacher-checked content and never use or trigger AI (D-124).

**Status:** **Designed** (P5-06). Built so far: the `ujian.` route group and guard, its own Inertia + Svelte entry in `resources/exam-client/`, a placeholder home page, and the `ExamParticipant` model used by the `exam` guard.

> **Differs from the final docs.** `content-engine.md` lists four categories "Kurang, Memadai, Baik, Istimewa" and `data-model.md` has `exam_results.category` ∈ {`kurang`, `memadai`, `baik`, `istimewa`}; D-041 and D-120 show three categories plus an Istimewa predicate ("Baik-Istimewa"). Settle the stored shape when P5-06 starts.

Read more: [`content-engine.md`](content-engine.md#tka-practice-exam-cbt), [`architecture.md`](architecture.md#4-tka-exam-session), [`docs/drafts/assessment-landscape.md`](../drafts/assessment-landscape.md), [`docs/drafts/administration-features.md`](../drafts/administration-features.md), D-026, D-027, D-030, D-041…D-047, D-089, D-092, D-094, D-111, D-120, D-124, D-144, D-205, D-213…D-218, D-286…D-295, D-301, D-304, D-344…D-351.

### 4.9 Portals: principal, operator, supervisor

**Who:** the principal, the operator, and the pengawas, through the role switch. All three read the same data the teachers produce, through the same services, so every role sees the same numbers (`legacy-upgrade-01.md` §1, rule P1).

**Portal sections** (product concept):

| Portal | Sections |
|---|---|
| Kepala Sekolah | Ringkasan, Pantau Guru, Validasi, Supervisi, Program Sekolah, Agenda & Rapat, Buku Sekolah, Data Sekolah, Pengaturan Sekolah (cetak, langganan) |
| Operator Sekolah | Tahun Ajaran, Kelas, Guru (undang), Jadwal, Kaldik, Profil Sekolah |
| Pengawas | Sekolah Binaan, Pantau (read-only), Validasi, Supervisi, Laporan Kepengawasan |

Later decisions add Pusat Tindak Lanjut, Arsip Tahun Ajaran, and PKKS for the pengawas (`legacy-upgrade-01.md` §4).

**Watching completeness.** *Ringkasan* and *Pantau Guru* read the completeness service (D-263) with the window Semester ini by default; principal and pengawas may also pick a free date range for prints (D-264). Pantau Guru shows readiness counts per teacher (D-270), lost JP per not-done reason (D-268), and trends from the weekly snapshots (D-265). The principal gets no readiness Kabar; teachers do (D-271). The supervisor and the console see school totals only.

**Reminders and follow-up.** A *Pengingat* from the principal to a teacher moves Terkirim → Dibaca → Ditindaklanjuti; the pengawas sends pengingat to the principal, never to teachers; at most one per item per teacher per week; the word "teguran" is never used (D-311). *Pusat Tindak Lanjut* is one follow-up list per role that reads existing records (RTL from supervision, the school programme, pengawas visits, PKKS, returned validations), with no new table (D-310).

**Validation and comments.** Two separate flows:

- **Curriculum validation (D-019).** Only school-changed or school-added curriculum goes guru → kepala sekolah → pengawas, with a note on every return.
- **Pengesahan dokumen (D-281).** RPM, Prota, Promes, rencana asesmen, and Berkas Administrasi can be sent with *Ajukan pengesahan*. The principal approves or returns them in the Validasi queue. Approval stores a SHA-256 hash of the revision and a 10-character code; prints carry a QR, the code, and "Disahkan melalui SIGRAK, [tanggal]" only while the active revision equals the approved one. `/verifikasi/{code}` works only after login, for the school's members and its assigned supervisor. Only the principal can revoke. The term is "pengesahan dokumen", never "tanda tangan elektronik". Approved revisions are never pruned (D-282).

Principal and supervisor can comment on any document (`document_comments`).

**Supervision instruments.** The principal supervises teachers with 18 instruments, S-00…S-17, shipped together as editable templates (D-221, D-222): the programme and schedule, document reviews (CP–TP–ATP, Prota/Promes, RPM, assessment, analysis and follow-up, kokurikuler), the observation cycle (pre-observation, observation, post-observation, RTL monitoring), reflections, murid feedback, the wali kelas administration check, and the school recap and follow-up programme. Scales: classroom observation uses the 3 Ruang GTK levels (Belum dilakukan, Dilakukan tetapi belum efektif, Dilakukan dan efektif); document reviews use 1–4 with descriptors; checklists keep 0/1/2; final bands 91–100 Sangat Baik, 81–90 Baik, 71–80 Cukup, below 71 Kurang (D-223). Only the principal observes; the pengawas sees school recaps only (D-224). For S-01 and S-15 SIGRAK suggests scores from the completeness data, and an item counts only after the principal confirms it (D-312). A findings bank of ready phrases per item, editable, no AI (D-313). The Kudus instrument set is a regional variant (D-327).

**Principal supervision (PKKS).** The pengawas supervises the principal in v1 with a PKKS / supervisi manajerial instrument set drafted from current rules, kept as templates with regional variants (D-308, D-324). Items SIGRAK does not produce (dokumen pendirian, RKAS, SPJ BOS, letters, guest book) are scored by hand with an optional evidence upload; items backed by SIGRAK data get a suggested score the pengawas confirms (D-328). A *Kinerja Kepala Sekolah* evidence download serves Ruang GTK (D-308).

**Other principal and operator features (D-217).** Supervisi akademik for Pembelajaran Mendalam; bukti dukung kinerja download for Ruang GTK; pembagian tugas with JTM and an SK generated from SIGRAK data (wali kelas JTM 2 in SD and 6 in MI by default, D-212); budaya sekolah aman with tata tertib, SOP, and an internal incident log (no public channel); complete student transfers with the 14-working-day Dapodik reminder; school inventory; a cross-school recap and Pembelajaran Mendalam mentoring for pengawas; the starter KSP template (D-209). The principal can download every teacher's Berkas Administrasi for a semester as one ZIP (D-279). Game kelas usage per class and subject appears for principal and pengawas (D-178). Penyusun and evaluasi KSP come after v1.

**Arsip Tahun Ajaran.** A closed academic year becomes read-only: viewable and downloadable, not changeable. Rapor, leger, graduation data, and buku induk are never deleted automatically; supervision and PKKS results are kept at least 5 years (D-318).

**Status:** **Built:** the operator's Guru, Tahun Ajaran, Kelas, and Jadwal pages. **Designed:** everything else (P5-09, with P4-08 for the per-item descriptors). Kaldik is P5-03i.

### 4.10 Internal console and business

**Who:** Tim SIGRAK.

**The console.** A Filament 5 panel at the root of `admin.sigrak.com`, sign-in at `/masuk`, behind the `staff` guard and Cloudflare Access. Horizon and Telescope sit behind the same guard on the same host (D-055, D-164). Staff powers are fixed in code and listed on a read-only "Hak akses" page (D-314).

| Console feature | Status | Decision |
|---|---|---|
| Wilayah, Sekolah, Akun staf | Built | P5-03a, D-167 |
| Anggota sekolah, Undangan, Penugasan pengawas | Built | P5-03b |
| School directory, Sebaran sekolah, Peta sekolah | Built | D-169, D-170 |
| Template registry and prompt overrides | Designed | D-013 |
| AI model per purpose with ordered fallbacks, logged | Designed | D-315 |
| Kesehatan Sistem (queues, failed jobs, disk, generations, AI errors, last backup) | Designed | D-316 |
| Curriculum console: compare, publish, roll back, impact, JP audit | Designed | D-317 |
| Retention settings per data type, logged | Designed | D-318 |
| Targeted Kabar (all, kabupaten, kecamatan, level, roles) | Designed | D-319 |
| Minta Bantuan with Baru, Diproses, Selesai | Designed | D-320 |
| National holidays and region calendars | Designed | D-253, D-256 |
| Pengawas requests for sekolah binaan | Designed | D-309 |
| Plan mapping core/add-on per feature | Designed | D-211 |
| Eleven staff roles with scopes, 2FA | Designed | D-338…D-343 |

**AI providers for staff.** Besides Gemini, `app/AI/` gets two adapters for console use: an OpenAI-compatible adapter (base URL, key, model; covers OpenAI, OpenRouter, DeepSeek) and an adapter for one further commercial model provider's own API. An admin can test keys of these providers and pick them as model or fallback per purpose, with every change logged. **Teacher keys stay Gemini only** (D-023). Before a purpose that runs on teachers' generations uses a Tim SIGRAK key from another provider, the owner decides who pays (D-326). Tests of model settings always use a Tim SIGRAK key, never a teacher's key (D-315).

**Support.** Support views a school's data read-only under the school MoU, religion and health hidden, one internal row per support session (D-341). The help-request status is visible to the sender (D-320).

**Trial, subscriptions, add-ons.** `subscriptions` has exactly one owner (a school, a user, or a supervisor) and a plan ∈ {`trial`, `game_trial`, `teacher`, `school`, `cluster`}; `school_addons` holds a school's add-ons per academic year and needs an active school subscription (data model). A small entitlement check reads both before a feature, a generation, or an export is used (architecture). After the 7-day trial an account can only view and download (D-157, D-160). The feature-to-plan mapping comes from the console (D-211).

**Manual payment.** Penjualan records a payment claim; Keuangan, or the owner, confirms it, and the tier is activated (D-091, D-340). No payment gateway in v1.

**Mode Contoh.** A seeded demo school with fictional data only, for sales demos. AI calls are allowed with the salesperson's own Gemini key under the same key rules as teachers (D-090). Demo data lives only in factories and a `DemoSeeder` that never runs in production (DB-14).

**Partners.** Referral codes and partner reports for teacher communities and resellers, designed in a later task (D-342).

**Status:** **Built:** the console pages listed as Built above, `php artisan staff:create`, three staff roles. **Designed:** slice P5-10 (trial, subscriptions, add-ons, manual payment, Mode Contoh) and the console features, many of them alongside P5-04 and P5-09.

Read more: [`product-concept.md`](product-concept.md#navigation), [`docs/drafts/teacher-supervision-instruments.md`](../drafts/teacher-supervision-instruments.md), [`docs/drafts/administration-features.md`](../drafts/administration-features.md), [`docs/drafts/internal-roles.md`](../drafts/internal-roles.md), [`docs/drafts/legacy-upgrade-01.md`](../drafts/legacy-upgrade-01.md), D-019, D-090, D-091, D-157…D-160, D-209…D-212, D-217, D-221…D-224, D-263…D-271, D-278…D-282, D-308…D-328, D-338…D-343.

## 5. Architecture

### 5.1 One app, one server

SIGRAK is **one Laravel 13 application** on **one Ubuntu Server 24.04 VPS** (IDCloudHost, Jakarta; 8 vCPU, 16 GB RAM, 320 GB NVMe) behind Cloudflare, reached only through a Cloudflare Tunnel (D-070, D-086…D-088, D-096). It serves three front ends on three hostnames, each with its own guard ([section 2.4](#24-guards-and-hosts)). Background work runs on Redis queues under Horizon. Gotenberg is the only Docker service. The only outside service the server calls is the Gemini API (plus the mail server).

```
Users -> Cloudflare (DNS, proxy, TLS, WAF)
      -> Cloudflare Tunnel (outbound only)
      -> Nginx 127.0.0.1:8080 (app., ujian., admin.)
      -> PHP-FPM: Laravel (web / exam / staff guards)
           -> MySQL 8.4   -> Redis 8.2   -> local disk
Supervisor -> Horizon (ai, exports, default) + schedule:work
Docker     -> Gotenberg 8 (localhost, basic auth, no egress)
Outbound   -> Gemini API, Gmail SMTP
```

### 5.2 Front ends

- **School app:** Inertia.js with plain Svelte 5 (runes) and TypeScript, Vite build, Tailwind with TailAdmin components ported to Svelte (D-008, D-075, D-076). Pages live in `resources/js/Pages/<Area>/<Name>.svelte`. URLs come from Ziggy's typed `route()`; each root view sends only its own route group, so staff and vendor routes never reach a browser (D-163). UI text comes from `lang/id` through a shared Inertia prop and one `t()` helper (D-080, D-097).
- **Exam client:** its own Inertia + Svelte entry and root view in `resources/exam-client/`, its own guard and hostname, sharing no bundle with the school app; answer autosave and the heartbeat go through one small JSON endpoint (D-094).
- **Staff console:** Filament 5, with its own theme using the brand colour (design system).
- **Game runtime:** vanilla JS and CSS per game template in `resources/game-runtime/`, typed with JSDoc and `// @ts-check`, playable standalone and offline (D-011, D-075).
- **Libraries:** three.js, KaTeX, ApexCharts, Lucide, Plus Jakarta Sans, and an Arabic font, all pinned to exact versions and bundled, never from a CDN; heavy ones are lazy-loaded (D-033, D-083, SV-11).

### 5.3 Module boundaries

```
app/
  AI/          Gemini only: keys, selection, errors, privacy guard,
               usage logs. Knows nothing about education.
  Content/     Templates, generation pipeline, export.
               Knows nothing about Gemini.
  Tenancy/     School context, BelongsToSchool, memberships, invitations
  Curriculum/  Import, versions, SubjectsForGrade, school changes
  Classroom/   Di Kelas: attendance, journal, notes, offline sync
  Exam/        TKA sessions, snapshot, cards, token, scoring
  Calendar/    EffectiveDays, ConflictChecker (designed)
  Progress/    CompletenessLedger, ProgressReport (designed)
  Filament/    Staff console only
```

`Exam` and `Classroom` never depend on `AI`; `Content` uses `AI` only through `AIGateway`; `Filament` is the normal place to bypass the school scope (D-049; Pest architecture tests enforce the rules, D-074). Business operations live in single-purpose Action classes with one `handle()` method, inside the module folder or in `app/Actions/<Domain>/` (D-078). **Built today:** `app/Tenancy`, `app/Curriculum`, `app/Actions/SchoolYear`, `app/Actions/Timetable`, `app/Filament`, `app/Enums`. `app/AI`, `app/Content`, `app/Classroom`, `app/Exam`, `app/Calendar`, and `app/Progress` do not exist yet.

### 5.4 Queues, Horizon, and the scheduler

- Three queues: `ai` (generation), `exports` (PDF, DOCX, Excel, game HTML, Berkas), `default` (mail, notifications, scoring, imports, the draft timetable). Horizon gives `ai` its own supervisor so long AI calls never block exports or mail.
- Generation jobs have `tries = 1`, no queue-level retries, and use `ShouldBeUniqueUntilProcessing` plus `WithoutOverlapping` keyed by the generation id, so a job can re-dispatch itself for the next step (D-050).
- Worker timeouts sit above the longest AI client timeout (180 s for long RPM steps), and `retry_after` above that.
- Supervisor runs Horizon and `schedule:work`; there is no cron entry (D-064). Scheduled work includes the stuck-step sweeper, revision and usage-log pruning, exam housekeeping, export cache cleanup, and later the weekly completeness snapshot and the Monday Kabar (D-265, D-271).
- Redis uses separate databases (or prefixes) for cache, sessions, queues/Horizon, and AI state, so `cache:clear` never wipes sessions, jobs, or key cooldowns (D-053). Redis runs with `noeviction` and AOF (D-058).
- Horizon mails long waits per queue (`ai` 600 s, `exports` 120 s, `default` 60 s), and the scheduler pings a heartbeat (D-137).

> **Differs from the final docs.** `architecture.md` shows the scheduler as a cron entry running `schedule:run`; D-064 (and `server-setup.md`) runs `schedule:work` under Supervisor.

### 5.5 Gotenberg

`gotenberg/gotenberg:8.39.0-chromium`, published on `127.0.0.1:3000` with basic auth and a 1 GB memory limit, on its own Docker network with no outbound connections (D-059). Every asset (HTML, fonts, scripts, images, the letterhead) is sent inside the request (D-052), so a malicious string in a document cannot make Chromium fetch anything. Laravel calls it through `spatie/laravel-pdf` with its Gotenberg driver (D-028, D-073). Only Berkas Administrasi uses the merge route (D-280).

### 5.6 PWA and offline

The school app is an installable PWA (manifest, icons, service worker). The exam site is not installable (D-093). The service worker caches the Di Kelas shell; IndexedDB holds today's data and the offline queue, synced by `client_uuid` through the web session and CSRF (D-016, D-051, D-289, D-349). A "Versi baru tersedia" banner reloads after syncing (D-321). Details are in [section 4.5](#45-in-class-di-kelas-offline-sync-attendance-journal-observation-class-tools).

### 5.7 File storage

Files (letterheads, exports, uploads) live on the VPS's local disk. Cloudflare R2 is only an optional store for small, rarely read files within the free tier, and the off-site backup target (D-071, D-114). Exports are cached in `document_exports` by content hash and served through an authorized route, never a public URL. Disk use is bounded by retention settings (D-318) and the 7-day Berkas file life (D-278); portfolio photo limits are still to be designed.

### 5.8 Security boundaries in short

Three guards with separate tables and host-only cookies; login-only; fail-closed tenancy with a `BelongsToSchool` global scope, policies on every action, and an IDOR test per resource; roles from memberships; sensitive data rules ([section 2.6](#26-visibility-of-sensitive-data)); write-only AI keys; prompt privacy; AI output rendered as text only; exam isolation; no-network game files; Gotenberg without egress; Cloudflare WAF and no public IP. The full table is in [`architecture.md`](architecture.md#security-boundaries).

Read more: [`architecture.md`](architecture.md), [`coding-standards.md`](coding-standards.md), D-048…D-055, D-059, D-064, D-074…D-083, D-093, D-094, D-114, D-163, D-164.

## 6. Data model tour

This chapter explains the shape. Column lists are in [`data-model.md`](data-model.md); the migrations in `database/migrations/` are the truth for what is built.

### 6.1 Conventions that shape every table

- English, plural snake_case tables; singular StudlyCase models.
- `bigint` primary keys for joins. Rows exposed in URLs or props also get a `public_id` (UUIDv7), and the bigint id never appears in a URL or prop (DB-11).
- Every foreign key is declared and indexed with an explicit `ON DELETE`.
- Fixed sets are a `varchar` plus a `CHECK (col IN (…))` generated from a PHP backed enum in `app/Enums/`.
- Money is integer rupiah (`bigint`); scores are `decimal(5,2)`.
- Times are stored and shown in WIB: `config/app.php` uses `Asia/Jakarta` and the MySQL session `+07:00`; the server OS stays UTC (D-165).
- **No soft deletes.** Hard delete after a confirmation that names the item; lifecycles use an explicit `status`.
- JSON only for template-defined content and imported reference blobs, never for queryable fields.
- Every school-owned table has `school_id NOT NULL`, indexed, first in composite indexes.
- Migrations are forward-only; `down()` is optional (D-085).

### 6.2 The main groups and how they connect

```
regions ─< schools ─< school_memberships >─ users
                  └─< invitations
users ─< supervisor_assignments >─ schools      (no school scope)
schools ─< academic_years ─< classes ─< teaching_assignments
classes ─< timetable_slots          classes ─< enrollments >─ students
curriculum_versions ─< topics ─< focus_items / learning_objectives
curriculum_versions ─< subject_allocations, learning_outcomes
subjects ─< region_subjects, school_subjects, topics
users ─< ai_credentials ;  users ─< generations ─< generation_steps
generations ─> documents ─< revisions, comments, exports, meetings
meetings ─< journals ;  assessments ─< assessment_items ─< scores
documents (tka_package) ─> exam_sessions ─< exam_items, participants
```

- **Accounts and schools (Built).** `regions`, `schools`, `users` (no role column), `school_memberships` (role ∈ principal, operator, teacher; one active principal per school), `supervisor_assignments`, `staff_users`, `invitations` (token hash, `invited_by` or `invited_by_staff_id`), and the reference table `school_directory_entries`.
- **School year (Built).** `academic_years`, `classes`, `teaching_assignments` (unique class and subject; `teacher_id NOT NULL`), `timetable_slots` (kind lesson, break, ceremony, activity; teacher through the assignment).
- **Students (Designed).** `students` (a record, never an account; `religion` sensitive) and `enrollments` (one active enrollment per student per year; covers transfers and promotion).
- **Curriculum (Built).** `curriculum_versions`, `subjects`, `subject_allocations`, `learning_outcomes`, `topics`, `focus_items`, `learning_objectives`, `focus_item_objectives`, `region_subjects`, `school_subjects`. Designed: `school_topic_changes`, `curriculum_validations`.
- **Calendar and meetings (Designed).** `calendars`, `calendar_events` (with `class_id`), `national_holidays`, `meetings` (status derived from the journal), `meeting_schedule_history`.
- **Content (Designed).** `templates`, `template_versions` (immutable once used), `generations` (unique user and client token), `generation_steps` (output written once by compare-and-set), `documents`, `document_revisions`, `document_comments`, `document_exports`, `template_feature_requests`; later `question_items`, `document_approvals`.
- **AI (Designed).** `ai_credentials` (no `school_id`: keys belong to the teacher), `ai_credential_cooldowns`, `ai_usage_logs` (no key, no prompt text).
- **Administration (Designed).** `journals`, `attendance_records`, `student_notes`, `assessment_plans`, `assessments`, `assessment_items`, `scores`, `report_descriptions`, `report_cards`, `report_extracurriculars`, `class_finance_entries`, inventory, rosters, Kabar, Pengingat, supervisions, school books and programmes, `school_print_settings`, `letterheads`, `subscriptions`, `school_addons`.
- **Exam (Designed).** `exam_sessions`, `exam_items` (frozen snapshot), `exam_participants`, `exam_answers` (upsert only when `client_seq` is higher), `exam_results`, `exam_events` (append-only).

**Snapshots across boundaries.** Bank Soal items are copied into `assessment_items`; a TKA package is copied into `exam_items`; a generation stores its specification and step outputs. Later edits to a source never change a running exam or a past score.

### 6.3 Tables added by later decisions

`legacy-upgrade-01.md` §4 lists what P4-14 must add to `data-model.md`. The main ones: `national_holidays`, `progress_snapshots`, `document_approvals`, a Berkas job record, `answer_sheets`, `sheet_scans`, `question_items`, `savings_entries`, `handover_records`, `class_contribution_kinds`, `class_contributions`, leger state with confirmations and an unlock log, retention and AI-model settings with logs, staff announcements with targets, help-request status; new columns such as `calendar_events.class_id`, `journals.status` with `partial` and the not-done reason, `school_print_settings.margin_profiles`, `schools.district`, a request status on `supervisor_assignments`, and a closed flag on `academic_years`. Internal roles add `staff_role_assignments` (D-339).

### 6.4 Versioning tables, now and planned

| Area | Now | Planned |
|---|---|---|
| Curriculum and rules | `curriculum_versions` (code, status, meta); config pointer for allocations | `effective_on`, `applies_from_school_year`, `supersedes_id` before P5-04 (D-355); then `regulations`, `regulation_relations`, `rule_sets`, `rules` (D-354) |
| Subjects | Global, not versioned | `since_rule_set`, `retired_in_rule_set` (`policy-innovation-layers.md` §6.2) |
| Templates | — | `template_versions`, immutable once used (D-013) |
| Documents | — | Store template version and rule-set codes; `document_revisions` (last 5, approved kept) |
| Meetings | — | `meeting_schedule_history` for undo (D-274) |
| Levels | `schools.level` | Versioned Profil Jenjang (D-356) |
| Taxonomy | — | `docs/data/taxonomy/` codes with `since` and `retired_in` (D-357) |

Read more: [`data-model.md`](data-model.md), [`coding-standards.md`](coding-standards.md#4-migrations-and-database), [`docs/drafts/legacy-upgrade-01.md`](../drafts/legacy-upgrade-01.md) §4, D-007, D-085, D-165, D-182, D-226, D-240, D-241, D-247…D-249, D-354…D-357.

## 7. AI layer and child data protection

### 7.1 The rules in one list

1. **Gemini free tier only, teacher-owned keys.** Each teacher adds one or more keys from Google projects they own. There is no school or platform key, and AI cost is never a SIGRAK cost (D-012, D-023).
2. **Adults only.** AI users are teachers. Students never use or trigger the AI, and the exam site, Tayangkan, and downloaded games serve only finished, teacher-checked content (D-027, D-124).
3. **No student personal data in any prompt:** no names, NISN, birth dates, attendance, notes, religion, health data, or individual scores tied to an identity. `app/AI/Privacy/PromptDataGuard` checks every request built from school data (D-023, D-036, D-215, D-347, D-350).
4. **Templates, not prompts.** Teachers answer pedagogical questions; instructions come only from the template; teacher notes and curriculum text go into delimited data blocks; output is accepted only through schema validation (D-024).
5. **The teacher decides.** AI suggests; every result is editable; AI never scores a child on its own (D-347).

### 7.2 Keys and rotation

- **Storage.** The secret uses Laravel's `encrypted` cast; a fingerprint (HMAC-SHA256) detects duplicates; only the last four characters are shown. Keys are write-only: never in Inertia props, never in logs, decrypted only in `ApiKeyManager` and `GeminiClient`, sent only as the `x-goog-api-key` header, never in a URL.
- **Quotas are per Google project and per model,** and the daily quota resets at midnight Pacific (14:00 or 15:00 WIB). Two keys from one project share one quota.
- **Consent.** The second and later keys need an explicit consent tick, because rotating several free projects may count as circumventing limits; the risk is the teacher's. SIGRAK never creates projects or keys for teachers (D-023).
- **Selection.** Round robin over the teacher's usable keys, filtered by state and per-model cooldown, with a sticky key per generation, failover on failover-class errors, a circuit breaker per key, and backup keys last (D-119). At most 2 running generations per teacher.
- **Daily counter.** SIGRAK counts each key's requests per model per day in Redis and skips a key at its limit until the reset; *Kunci AI Saya* and the wizard show "sekitar N RPM lagi hari ini" (D-128). Any 429 that still arrives gets a 60-second cooldown.
- **Errors** are classified once (rate limited per minute or per day, bad request, project unavailable, key invalid, model unavailable, transient, timeout, blocked, malformed output), and each class has one action. At most 4 attempts and 5 minutes per step.
- **Duplicates.** Gemini has no idempotency key, so a timeout retries once on the same key before failing over, every attempt is logged, a step's output is accepted exactly once, and double clicks are stopped by a client token.

### 7.3 Free tier and models

The default model is Gemini 3.8 Flash; its free-tier limits when checked were 5 requests per minute, 250,000 tokens per minute, and 20 requests per day per project (D-117). Templates name a **purpose** (for example `document_long`, `repair`), never a model. `config/ai.php` holds the starting purpose-to-model map; an admin sets the active model and an ordered fallback list per purpose in the console, logged (D-315). No `temperature`, `top_p`, or `top_k` is sent. Generations use as few calls as possible: no background pings, no automatic regeneration, stable models only.

### 7.4 Where student-related AI is allowed

| Use | Allowed input |
|---|---|
| Rapor description draft | Per-TP mastery levels for an anonymous "Murid A"; the name is merged on the server afterwards |
| Catatan wali kelas draft | Attendance counts, note types, per-TP results; no name or personal data (D-213) |
| Next RPM after analysis | Group counts per TP, never names (D-295) |
| Game kelas follow-up | TP and wrong-answer pattern of the class (D-172) |
| Essay scoring help | The essay text only, without any student data; the teacher decides the score (D-347) |
| Misconception tags | Made when the distractor is written, before any student answers |

Names on paper are read on the teacher's device, never by an AI provider (D-347). Reading handwriting or voice with AI is later.

### 7.5 Usage and visibility

`ai_usage_logs` has one row per attempt (user, school, key id, generation, step, model, purpose, outcome, tokens, latency), never the key and never the prompt or response text. Detail rows are kept 1 month, then pruned; monthly aggregates stay (D-118). The teacher sees each key's status and recent failures; the principal sees per-teacher totals only; Tim SIGRAK sees errors by class and template in the console.

### 7.6 Console providers

Console-only adapters for an OpenAI-compatible API and one more commercial provider exist for Tim SIGRAK's own work (D-326). They never change the teacher's key model. See [section 4.10](#410-internal-console-and-business).

**Status:** **Designed** (P5-04). Nothing in `app/AI` is built yet.

> **Differs from the final docs.** `ai-provider-engine.md` says no second provider is planned and that models change only in config; D-315 and D-326 add console model settings and console adapters, while teacher keys stay Gemini only. It also mentions plain `ShouldBeUnique`; D-050 uses `ShouldBeUniqueUntilProcessing` plus `WithoutOverlapping`.

Read more: [`ai-provider-engine.md`](ai-provider-engine.md), D-012, D-022…D-024, D-036, D-050, D-116…D-119, D-124, D-128, D-152, D-213, D-315, D-326, D-347, D-350.

## 8. Exports, localization, and UI writing

### 8.1 Indonesian UI through `lang/id`

- All user-visible text lives in `lang/id/*.php`, one file per area, with English keys that name the meaning (`journals.saved`). There are no string literals in PHP, Blade, or Svelte, and there is no `lang/en`; locale and fallback are both `id` (TR-1…TR-8).
- `common`, `nav`, `terms`, and `enums` are always shared. A page adds its files with the `translations:<files>` route middleware; server-only files (`export`, `mail`, `notifications`, `validation`) are refused (D-097). Svelte reads text through one `t('key', params)` helper (D-080).
- The glossary of standard terms lives in `lang/id/terms.php`, and a test rejects banned old terms in any value (D-098). A `lang:audit` command reports missing, unused, and hard-coded strings; missing keys fail CI (D-100).
- Validation messages start from a one-time copy of the MIT `laravel-lang` Indonesian file, edited to SIGRAK's voice (D-101). Database notifications store a key and parameters, so copy changes reach old notifications (D-102).
- **Built:** the `translations:` middleware (`ShareTranslations`), the shared prop, and filled `auth`, `account`, `school`, `classes`, `timetable`, and `validation` files.

### 8.2 UI writing

SIGRAK speaks like a quick, polite fellow teacher (design system):

- Verbs on buttons ("Buat RPM", "Simpan jurnal", "Unduh PDF", "Tayangkan").
- Sentence case, no ALL-CAPS.
- One term per thing.
- Errors say what happened and what to do, with technical detail behind "Lihat detail".
- A delete confirmation names the item and the consequence.
- "Anda" for every role.
- No emoji, and no "cerdas", "canggih", or "AI" as decoration.
- **Never ask for a prompt.**

Fixed terms include *Unduh* (never Download), *Buat* (never Generate), *Templat* (KBBI spelling, D-151), *Kunci AI*, *Kop*, *Kabar*, *Pengingat*, *Simulasi TKA*, *Kartu login*, *Dokumen Saya*, *RPM lepas jadwal*. Later decisions add *Kehadiran* (D-302), *murid* (D-210), *Berkas Administrasi*, *pengesahan dokumen*, *Sumbangan sukarela*, and the banned words *tunggakan*, *kurang bayar*, *belum lunas*, *teguran*, and *tanda tangan elektronik* (D-281, D-300, D-311).

The look is a tidy teacher's workbook: plain surfaces, one teal brand colour, thin lines, Plus Jakarta Sans, Lucide icons with text, tokens as CSS custom properties mapped into Tailwind. Every screen is checked at 360 px and 1280 px wide; tables become card lists below 640 px; touch targets are at least 44 px; status is never colour alone (SV-8…SV-10). Copy comes from the paraphrase spreadsheet of 1,499 legacy strings, tracked row by row in `docs/data/ui-copy/key-map.csv` (D-099).

### 8.3 URLs

Paths people see are plain Indonesian in kebab-case (`/masuk`, `/lupa-kata-sandi`, `/dokumen/{document}/ubah`, `/kunci-ai/{credential}`); resource verbs are `buat` and `ubah` through `Route::resourceVerbs`; route names, parameters, and code stay English (`login`, `documents.edit`). Technical paths nobody reads (`/up`, built assets) may stay English (D-149).

### 8.4 Dates, numbers, money

All formatting goes through `App\Support\IndonesianFormat` (PHP) and `resources/js/lib/format.ts` (TS), which share one API and one set of test fixtures (XP-26):

| Value | Output |
|---|---|
| Date | `31/10/2026` |
| Date in prose | `31 Oktober 2026` |
| Time | `14.00` ("WIB" only for outside events such as the quota reset and exam windows, D-108) |
| Number | `1.234.567,89` |
| Rupiah | `Rp1.234.567` (no space, no decimals, not built by ICU, D-104) |
| Score | `76,67` |

### 8.5 PDF, DOCX, Excel, CSV

- **Common (XP-1…XP-5).** Rendered from content JSON or query data, never a screenshot; on the `exports` queue when it may take more than about 2 seconds; every label from `lang/id/export.php`; never more data than the user can see in the app; religion only for those allowed to see it.
- **PDF (Gotenberg).** One Blade view per PDF. Paper A4 or F4 (215 × 330 mm, D-109). The letterhead is **one uploaded image** (school default, or the teacher's own), placed at the top of the first page, at most 4 cm high (D-031); a school without one can press "Buat kop otomatis" (D-284). Print font Arial or Times New Roman, rendered as Liberation Sans or Serif; Arabic in Noto Naskh Arabic; math in KaTeX fonts (D-105, D-125). Page numbers "Halaman X dari Y"; signers as text; headings repeat on each page; no watermark. Print rules: a shared black-and-white cover for book-type documents, three school-editable margin profiles plus a duplex option, landscape for tables with 9 or more columns, and a "NIHIL" row for empty tables (D-283).
- **DOCX (PhpWord).** SIGRAK's own layout per template, built in code; letterhead in the first-page header; `id-ID` language; Word heading styles; no uploaded Word templates (XP-15…XP-19).
- **Excel (Laravel Excel 4).** Indonesian headings and sheet names; real cell types (numbers, Excel dates, "Ya"/"Tidak"); NISN, NIP, and NPSN as text; a separate "Info" sheet; protection against formula injection (XP-20…XP-23).
- **CSV.** `;` separator, UTF-8 with BOM, CRLF, comma decimals; XLSX is the default (D-103).
- **Imports read back what exports write.** Headings match regardless of case, extra spaces, a trailing `*` or `:`, or column order; old heading labels stay valid forever as aliases; Indonesian values ("Hadir", "Ya", `dd/mm/yyyy`, comma decimals) are read back to English database values (D-150).
- **File names.** `<kind>-<context>-<date>.<ext>`, at most 80 characters, no student or teacher names (`rapor-kelas-6a-no-07-semester-1.pdf`) (D-107, XP-31…XP-34).
- **Arabic and RTL.** `dir="auto"` and `<bdi>` for mixed text, separate RTL blocks for `arabic_passages`, RTL runs in DOCX, RTL reading order in Excel (LOC-32…XP-38).

**Built:** the Sebaran sekolah Excel export (`app/Exports/SchoolDirectoryExport`). Everything else in this chapter is **Designed** and lands with P5-04 onward.

Read more: [`export-localization.md`](export-localization.md), [`design-system.md`](design-system.md), [`coding-standards.md`](coding-standards.md) §7–§8, D-031, D-097…D-109, D-125, D-149…D-151, D-280, D-283, D-284.

## 9. Infrastructure and operations

### 9.1 Production

| Item | Choice | Decision |
|---|---|---|
| Server | IDCloudHost Cloud VPS, Jakarta, 8 vCPU / 16 GB / 320 GB NVMe | D-070, D-087, D-096 |
| OS | Ubuntu Server 24.04 LTS, server clock in UTC | D-086, D-066 |
| Web | Ubuntu's Nginx package on `127.0.0.1:8080` | D-061, D-088 |
| PHP | 8.5 (≥ 8.5.12) from `ppa:ondrej/php`, PHP-FPM | D-056 |
| Database | MySQL 8.4 LTS from Oracle's repository | D-007, D-057 |
| Cache, queues | Redis 8.2, `noeviction`, AOF | D-058 |
| PDF | Gotenberg 8.39.0 (Chromium) in Docker | D-028, D-059 |
| Node | 24 LTS, for builds only (preferably in CI) | D-060 |
| Domain | `sigrak.com`: `app.`, `ujian.`, `admin.`, `ssh.` | D-069 |
| Mail | Gmail SMTP with an App Password, transactional only | D-110 |

**Cloudflare Tunnel.** The VPS has **no public IP**. A locally managed `cloudflared` tunnel routes `app.`, `ujian.`, `admin.`, and `ssh.` to localhost. UFW denies all incoming traffic. SSH and `admin.` sit behind Cloudflare Access. Nginx restores the real client IP from `CF-Connecting-IP`, trusted only from 127.0.0.1. Authenticated HTML is never cached by Cloudflare (D-088).

**Deploys.** Release folders (`releases/`, `shared/`, a `current` symlink), keeping 5 releases; PHP-FPM is reloaded on each deploy because OPcache does not check timestamps (D-063). Assets are built in CI or locally. After deploys that bring new reference data, run the import commands (`curriculum:import`, `school-directory:import`). The server `.env` needs `APP_HOST`, `EXAM_HOST`, `ADMIN_HOST`, the Gotenberg keys, and the mail keys (D-073, D-164).

### 9.2 Staging

Staging is a second site on the production VPS, isolated by its own system user, PHP-FPM pool, database and database user, Redis database numbers and prefixes, and Horizon and scheduler programs. It uses `staging-app.`, `staging-ujian.`, and `staging-admin.sigrak.com` on the same tunnel behind Cloudflare Access, with seeded data only. MySQL, Redis, and Gotenberg are shared, so only app releases are staged; OS and service upgrades are rehearsed on a temporary VM or snapshot (D-095). Telescope is allowed on staging, never public in production.

### 9.3 Backups

Daily at 01:30 WIB: `mysqldump` plus a `storage/app` tarball. Kept 14 days locally and 30 days off-site on Cloudflare R2 through an encrypted `rclone` crypt remote with a bucket-scoped token. Restore test monthly; off-site restore twice a year. `APP_KEY` stays in the password manager, never in a backup (D-065, D-071). The backup script pings a heartbeat at start, success, and failure (D-136).

### 9.4 Monitoring

- **Healthchecks.io** (free Hobbyist) is the main alerting service, as a push-based dead man's switch that suits a server without a public IP. 13 checks; urgent ones go to Telegram and email, minor ones to email only. Alerts go to the owner only (D-129, D-130, D-144).
- One script, `/usr/local/sbin/sigrak-health`, runs every minute, every 15 minutes, and daily; a check must fail twice in a row before it reports (D-131).
- UptimeRobot (free) checks `app.` and `ujian.` `/up` from outside (D-132). Cloudflare Tunnel notifications go by email (D-133).
- Thresholds: urgent at disk ≥ 90%, RAM available < 1.5 GB, an OOM kill, or Redis ≥ 90% of `maxmemory`; minor at disk ≥ 80%, swap ≥ 50%, load15 ≥ 8, MySQL connections ≥ 160, Redis ≥ 70%, ≥ 5 failed jobs in 15 minutes, or pending security updates (D-135). Staging alerts by email only (D-138).
- The console's *Kesehatan Sistem* page will show queues, failed jobs, disk, generations, AI errors, and the last backup; alerts stay with the server monitoring (D-316).

**Status:** P5-02 (server, staging, and monitoring setup) is **todo**: the guides are final, but the running VPS is not yet a recorded output. The owner set the server mail keys on 2026-11-07 (P5-03b result).

### 9.5 Local development (WSL2)

Developers on Windows use WSL2 with Ubuntu 24.04, not Laragon, because Horizon needs `pcntl` and `posix`, and Ubuntu 24.04 matches the VPS (D-166). PHP, MySQL, Redis, and Node run inside Ubuntu; Gotenberg runs in Docker Desktop with WSL integration. Keep the code inside Linux (`~/sigrak`), never under `/mnt/c`. First run: `composer install && npm ci`, then `php artisan key:generate && php artisan migrate --seed`. Daily: `composer dev` (serve on port 8000, `queue:listen`, `pail`, Vite); `php artisan horizon` to try the named queues. Local hosts are `app.`, `ujian.`, and `admin.sigrak.localhost:8000`. Before a push, run the same checks CI runs ([section 10.5](#105-coding-standards-tests-and-ci)).

Read more: [`server-setup.md`](server-setup.md), [`server-monitoring.md`](server-monitoring.md), [`local-setup-wsl2.md`](local-setup-wsl2.md), D-056…D-073, D-086…D-088, D-095, D-096, D-110, D-129…D-138, D-166.

## 10. How we work

### 10.1 Phases and tasks

Work is split into **phases** (`P0`, `P1`, …) and each phase into **tasks** (`P5-03`, `P5-03e`, …), small enough for one agent or session, with clear inputs, outputs, and dependencies. Tasks without mutual dependencies run in parallel, as sub-agents or separate sessions; two tasks never edit the same file. Each task has a file in `docs/agent/tasks/` (from `docs/templates/TASK.md`) (D-006, `WORKFLOW.md`).

| Phase | Content | Status |
|---|---|---|
| P0 | Repository conventions | Done |
| P1 | Product concept docs | Done |
| P2 | Data and technical docs | Done |
| P3 | Final docs and Indonesian PDFs, PDF build script | Done |
| P4 | Add-on and follow-up designs | Some done, others todo or on hold |
| P5 | v1 build | P5-01 done; P5-03 open for P5-03i and P5-03j; the rest todo |

### 10.2 The agent files

- The repository rules file at the root is the entry point and holds the hard rules. `docs/agent/CONTEXT.md` is the stack and convention summary. Read these two, then `PROGRESS.md`, then your task file.
- `docs/agent/PROGRESS.md` is the single source of truth for what is happening: phases, task status (`todo`, `in-progress`, `blocked`, `review`, `done`), owners, and short handoff notes, newest first.
- `docs/agent/DECISIONS.md` is an append-only log, one entry per decision (`D-NNN`, status, date, decision, reason). A changed decision is a new entry that supersedes the old one. Read it when a choice touches an earlier decision.
- `docs/agent/WORKFLOW.md` describes claiming, splitting, and finishing tasks.

**Task lifecycle.** Claim in `PROGRESS.md` (set `in-progress` with your owner label) → work, reading only what the task lists → record new decisions in `DECISIONS.md` (and `CONTEXT.md` if the stack or a convention changes) → finish with `review` or `done`, fill the task file's Result, add a handoff note → after the merge, delete the task branch. A blocked task is marked `blocked` with the reason. Shared files get small, line-level edits only.

### 10.3 Docs pipeline

| Stage | Folder | Language |
|---|---|---|
| Draft | `docs/drafts/` | English |
| Final | `docs/final/` (approved by the owner, moved with `git mv`) | English |
| PDF | `docs/pdf/` | Indonesian |

The Indonesian translation of each final doc is committed in `docs/pdf/src/<name>.md` (D-156). When a final doc changes, translate only the changed lines and rebuild with `node scripts/docs-pdf/build.cjs <name>` (Pandoc, then headless Chromium through Playwright, A4, footer "Halaman X dari Y"). The build fails on any table or code block wider than the page; `node scripts/docs-pdf/check.cjs` lists leftover English prose. Keep code blocks, identifiers, commands, paths, decision IDs, URLs, and Indonesian or Arabic terms unchanged. Owner material in `docs/sources/` is kept as received (with the D-360 brand exception). This handbook is updated when a phase ends or a major decision changes the flow (D-361).

### 10.4 Branches, identity, attribution

- **One branch: `main`.** A task may use a short-lived branch named `<phase>/<task-id>-<slug>`; once merged, it is deleted on the remote and locally. Between tasks the remote holds only `main` (D-148).
- **Owner identity.** Every commit is authored and committed as `Arumi Studios <arumiforge@gmail.com>`; a session-start hook in the repository sets this each session. Check `git config user.email` before the first commit (D-148).
- **No AI attribution anywhere** (D-001): no co-author or session lines in commits, nothing in PR titles or descriptions, comments, code, or docs. This rule overrides any built-in attribution habit of a tool or agent.
- **Legacy brand.** Never name the owner's previous app; write "the legacy app" or "aplikasi lama" (D-360).
- **Language policy.** Repo markdown, code identifiers, tables, branches, and commits in English; URLs in Indonesian; UI text, exports, and generated PDFs in Indonesian; owner sources as received.

### 10.5 Coding standards, tests, and CI

Highlights from [`coding-standards.md`](coding-standards.md):

- **PHP.** `declare(strict_types=1)` everywhere; Pint (PSR-12); Larastan from level 6 (D-077); typed code; backed enums for fixed sets; Form Requests and Policies on every action; thin controllers that call one Action; explicit `$fillable`; `config()` instead of `env()`; no N+1; dates through `now()` and `CarbonImmutable`.
- **Database.** One table per migration; never edit a migration on `main`; `CHECK` constraints from enums; no soft deletes; seeders for reference data are idempotent upserts.
- **Tenancy.** Every school-owned model uses `BelongsToSchool` (an arch test fails otherwise); `withoutSchoolScope()` only in `app/Filament/` and in jobs or commands that name the school, each with a comment; a foreign `public_id` gives 404, not 403; one IDOR test per resource and action.
- **Svelte.** TypeScript, runes, pages per route, props built server-side (never a whole model), `public_id`s only, no `{@html}` on user or AI text, Tailwind tokens only, no CDN.
- **Tests.** Pest with architecture tests (D-074) on MySQL 8, not SQLite (D-079). No real AI calls; `Http::preventStrayRequests()` in the base test case; recorded Gemini fixtures; Gotenberg faked in CI. Factories produce valid rows within one school; fake names from `fake('id_ID')`, never real people's data. A bug fix comes with a failing test.
- **CI.** GitHub Actions on every push and pull request to `main` (`.github/workflows/ci.yml`, D-084): MySQL 8.4 service, PHP 8.5, Node 24, `composer audit`, `npm audit --omit=dev`, Pint, Larastan, Pest, ESLint, `svelte-check`, and the build. A PR merges only when green. Locally: `vendor/bin/pint --test && vendor/bin/phpstan analyse --memory-limit=1G && composer test`, then `npm run lint && npm run check && npm run build`.
- **Commits and PRs.** English, imperative, subject at most 72 characters; one task per branch and PR; the PR says what changed, why (task and decision IDs), how it was tested, with phone and laptop screenshots for UI; never commit `.env`, keys, real student data, dumps, or build output.
- **Dependencies.** Lock files committed; exact pins for output-affecting front-end libraries; Docker images pinned to a tag; a new dependency needs a reason, its licence, and its maintenance state.

### 10.6 Research digest rule

Before any research (a regulation, a field or journal study, a benchmark, a vendor's terms), check [`docs/final/research-digest.md`](research-digest.md). Use a listed finding and follow its link; re-check only when it is old, marked secondary or unverified, or touches money, law, or a vendor's terms. After researching, add the findings there in the same shape (source, date checked, verification level, finding, implication, decisions, repo doc) and put the detail in the task's draft (D-362).

Read more: [`docs/agent/WORKFLOW.md`](../agent/WORKFLOW.md), [`docs/agent/PROGRESS.md`](../agent/PROGRESS.md), [`coding-standards.md`](coding-standards.md), [`scripts/docs-pdf/README.md`](../../scripts/docs-pdf/README.md), D-001, D-002, D-006, D-074…D-085, D-148, D-149, D-156, D-360…D-362.

## 11. Roadmap

### 11.1 Phase 4: open design and data tasks

| Task | Content | Status | Depends on |
|---|---|---|---|
| P4-01 | Buku Induk and Dapodik sync design | Blocked, held as upcoming (D-161) | Owner restart |
| P4-05 | Final docs and glossary for the 2025–2026 rules ("murid", 12-component rapor, ekskul, JTM) + PDFs | Todo | D-210…D-221 |
| P4-06 | Design: assessment and rapor extensions | Todo | P4-05 |
| P4-07 | Design: class and homeroom extensions | Todo | D-214…D-219 |
| P4-08 | Design: principal, operator, supervisor administration (per-item descriptors) | Todo | P4-12 |
| P4-09 | Design: console mapping of core vs add-on | Todo | D-211 |
| P4-10 | Design: e-Rapor SD and RDM export | Todo | D-218, P5-08 |
| P4-11 | Design: Penyusun KSP and evaluasi KSP (after v1) | Todo | D-209, D-217 |
| P4-13 | Legacy app upgrade decisions | Review | D-251 |
| P4-14 | Apply the legacy upgrade to `docs/final/` + PDFs | Todo | P4-13 |
| P4-15 | Import the legacy app catalogs to `docs/data/catalogs/` | Todo | — |
| P4-19 | Design: assessment landscape (Peta Asesmen, tags, wizard, Wave A) | Todo | D-344…D-351 |
| P4-20 | Apply D-329…D-351 to `docs/final/` + PDFs | Todo | P4-16…P4-18 |
| P4-22 | Taxonomy seed and regulation registry in `docs/data/taxonomy/` | Todo | P4-21 |
| P4-23 | Get and extract missing regulations, verify KMA 512/2026 | Todo | — |
| P4-24 | Apply the policy foundation to `docs/final/` + PDFs | Todo | P4-21 |

Done in Phase 4: P4-02 (Game kelas), P4-03 and P4-03b (official documents), P4-04 (administration features), P4-12 (supervision instruments), P4-16 (LKPD), P4-17 (internal roles), P4-18 (assessment landscape), P4-21 (policy foundation), P4-25 (this handbook), P4-26 (research digest), P4-27 (legacy brand removal).

The doc-update tasks P4-05, P4-14, P4-20, and P4-24 together close the gaps this handbook flags between the final docs and later decisions.

### 11.2 Phase 5: the v1 slices in order

The owner confirmed the order (D-162): foundation, content core, more templates, TKA, Di Kelas, Asesmen, portals, business, after the scaffold and the server.

| Slice | Content | Status | Depends on |
|---|---|---|---|
| P5-01 | Project scaffold and CI | Done | — |
| P5-02 | Server, staging, and monitoring setup | Todo | — |
| P5-03 | Foundation: auth, invitations, schools and roles, years and classes, curriculum import, timetable, school directory, copy classes | Done except P5-03i, P5-03j | P5-01 |
| P5-03i | Calendar (D-252…D-261, D-323) | Todo | P5-03e |
| P5-03j | Draft timetable generator (D-262) | Todo | P5-03i |
| P5-04 | Content core: AI provider engine, template engine, RPM, Dokumen Saya, PDF/DOCX export | Todo | P5-03 incl. P5-03i, D-355 minimum |
| P5-05 | Bank Soal, LKPD, Quiz Battle | Todo | P5-04 |
| P5-06 | TKA: package, exam site, sessions, cards, token, results (2028 season) | Todo | P5-05 |
| P5-07 | Di Kelas with offline sync, Jurnal from RPM | Todo | P5-03 (and P5-04 for the RPM flow) |
| P5-08 | Asesmen: plan, scores, per-TP analysis, rapor, Pindai LJK | Todo | P5-05 |
| — | e-Rapor SD and RDM export, right after P5-08 | Todo | P5-08 (D-218) |
| P5-09 | Portals: principal, operator, supervisor; validation and comments | Todo | P5-03 |
| P5-10 | Business: trial, subscriptions, add-ons, manual payment, Mode Contoh | Todo | P5-03 |

```
P5-01 ─► P5-03 (a…h done) ─► P5-03i ─► P5-03j
                         │        │
                         │        └─► D-355 minimum ─► P5-04 ─► P5-05
                         │                                       │
                         │                     P5-06 ◄───────────┤
                         │                     P5-08 ◄───────────┘
                         │                       └─► e-Rapor / RDM export
                         ├─► P5-07 (needs P5-04 for RPM-driven journal)
                         ├─► P5-09
                         └─► P5-10
P5-02 runs in parallel with all of it.
```

### 11.3 What to build next

1. **P5-03i, the calendar,** starting with the online SKB check (D-254) and the Jawa Tengah 6-day reference calendar (D-323). P5-04 needs effective days for Prota, Promes, and RPM scheduling (D-252).
2. **The D-355 versioning minimum,** before P5-04: version columns on `curriculum_versions`, a policy-profile resolver in place of `config/curriculum.php` `allocation_versions` (same behaviour), and the Profil Jenjang data. The first RPM must already store its rule set.
3. **P5-03j,** the draft timetable, in parallel with P5-04.
4. **P5-04, the content core,** with the decisions added by P4-13 (D-267, D-269, D-270, D-272…D-277, D-283…D-285) and the console parts D-315 and D-316, plus the Dasar Kebijakan panel and the first Cek Keselarasan rules on the RPM (D-358).
5. In parallel, data and doc work that unblocks later slices: P4-22 (taxonomy), P4-23 (missing regulations), P4-15 (catalogs), Tim Validasi review of the curriculum and regulation data, and the doc updates P4-14, P4-20, P4-24.

Additions by slice from the legacy upgrade are listed in `legacy-upgrade-01.md` §5: P5-05 gets D-293, D-294, D-296; P5-07 gets D-263…D-271 (teacher side), D-278, D-280, D-289, D-297…D-303, D-321; P5-08 gets D-286…D-292, D-295, D-301, D-304; P5-09 gets the portal views and D-279, D-281, D-282, D-304 (mutasi), D-306…D-313; the console gets D-314 and D-317…D-320.

Read more: [`docs/agent/PROGRESS.md`](../agent/PROGRESS.md), [`docs/agent/tasks/P5-03-foundation.md`](../agent/tasks/P5-03-foundation.md), [`docs/drafts/legacy-upgrade-01.md`](../drafts/legacy-upgrade-01.md) §5, D-162, D-252, D-355.

## 12. Decision map

Decisions live in [`docs/agent/DECISIONS.md`](../agent/DECISIONS.md). The ranges below group them by topic. D-185…D-202 are unused and D-235…D-239 are reserved.

| Range | Topic | In one line |
|---|---|---|
| D-001…D-006 | Repository rules | No AI attribution, language policy, core stack, docs pipeline |
| D-007…D-037 | Product foundation | MySQL, Svelte, scope, users, games, teacher keys, templates, exams, documents, religion |
| D-038…D-047 | Curriculum and TKA data | MI allocation, MI CP shape, TKA categories, forms, scoring |
| D-048…D-055 | Architecture | One codebase, three hosts and guards, modules, jobs, Redis, exam limits |
| D-056…D-073 | Server | PHP 8.5, MySQL 8.4, Redis 8.2, Gotenberg, deploys, backups, domain, VPS |
| D-074…D-085 | Code and CI | Pest, TypeScript, runes, Larastan, Actions, MySQL tests, CI, forward-only migrations |
| D-086…D-096 | Owner choices | Ubuntu 24.04, VPS size, tunnel, TKA as practice, Mode Contoh, manual payment, PWA, staging |
| D-097…D-110 | Localization and print | Translation groups, glossary, CSV, rupiah, fonts, F4, file names, mail |
| D-111…D-128 | Data review and AI | AN competencies, Tim Validasi, storage, model, backup keys, approvals, KBC, daily counter |
| D-129…D-147 | Monitoring and religion data | Healthchecks.io, thresholds, religion and MI TP/ATP versions |
| D-148…D-156 | Repo identity and docs | Only `main`, owner author, Indonesian URLs, imports, Templat, revisions, translations |
| D-157…D-162 | Business and order | Prices, add-ons, trials, Buku Induk on hold, v1 build order |
| D-163…D-170 | Scaffold and foundation | Ziggy, hosts from env, WIB, WSL2, staff roles, supervisor scope, school directory |
| D-171…D-181 | Game kelas | IFP mode, class results, Kode Tayang, Kartu Jawab, templates, content packs |
| D-182…D-184 | Auth | Invitations, passwords, supervisor accounts later |
| D-203…D-209 | Official 2025 documents | Regulation data, optional content, rapor rows, Fase A, 7 KAIH, KKA, KSP |
| D-210…D-221 | Administration features | Follow 2025–2026 rules, core features, JTM, rapor, homeroom, portals |
| D-222…D-225 | Teacher supervision | 18 instruments, scales, principal-only observation, murid feedback |
| D-226…D-234 | Curriculum import | CP table, subjects, import rules, class subjects, muatan lokal, Kode Tayang path |
| D-240…D-251 | School year and timetable | Assignments, years, classes, copy classes, timetable rules |
| D-252…D-262 | Calendar | Holidays, region calendars, class events, effective days, conflicts, draft timetable |
| D-263…D-277 | Completeness and RPM flow | One completeness service, journal outcomes, JP balance, readiness, shifts |
| D-278…D-285 | Berkas and print | Berkas Administrasi, merge, pengesahan, print rules, kop otomatis, sound |
| D-286…D-296 | Assessment and rapor | Pindai LJK, offline scope, weights, remedial, leger lock, Bank Soal, LKPD blocks |
| D-297…D-307 | Kelas Saya | Piket, class cash, contributions, savings, Kehadiran, drafts, catalogs, transfer |
| D-308…D-313 | Supervision portals | PKKS, sekolah binaan requests, follow-up, Pengingat, suggested scores |
| D-314…D-321 | Console | Fixed roles, AI model per purpose, system health, curriculum console, retention |
| D-322…D-328 | Legacy upgrade wrap-up | Not carried over, Jawa Tengah calendar, regional instruments, LJK layout, console adapters |
| D-329…D-337 | LKPD | Patterns in waves, QR, student session later, Wave 1, local context, embeds |
| D-338…D-343 | Internal roles | Scopes, eleven roles, payments, support access, partners, 2FA |
| D-344…D-351 | Assessment landscape | Peta Asesmen, instruments, AI without student data, GPK, offline, Wave A |
| D-352…D-359 | Policy foundation | Position, rule strength, versioning, Profil Jenjang, taxonomy, panel, KMA 512 |
| D-360…D-362 | Repository and docs | Legacy brand removed, this handbook, research digest |

### 12.1 Where final docs and later decisions differ

This handbook follows the later decision in each case. The doc-update tasks (P4-05, P4-14, P4-20, P4-24) will bring the final docs in line.

| Final doc says | Later decision |
|---|---|
| Menu *Absensi*; "siswa" in UI copy | *Kehadiran* (D-302); "murid" (D-210) |
| Offline: Absensi, Jurnal, Catatan Siswa only | Plus read-only RPM meetings, the scan queue, observation entry (D-289, D-349) |
| `journals.status` done/not_done; `meetings.status` without partial | `partial`, reasons, derived meeting status (D-267, D-268) |
| `timetable_slots.teacher_id` | No teacher column; `kind` and `label` (D-247, D-249) |
| Region calendar as an override default | School copies the region calendar, with offered updates (D-256) |
| Last 5 revisions, pruned | Approved revisions never pruned (D-282) |
| One PDF per download, no merge | Merge for Berkas Administrasi only (D-280) |
| Print margins as plain settings | Three margin profiles, duplex, cover, landscape, NIHIL (D-283) |
| LKPD output without blocks | Grade 1–3 blocks and a `pattern` field (D-296, D-329) |
| Bank Soal exports only | Per-item data, six derived prints, item library (D-293, D-294) |
| TKA: four categories incl. Istimewa | Three categories plus an Istimewa predicate (D-041, D-120) |
| PGK Kategori pair "mendukung/tidak mendukung" | Dropped (D-045) |
| `staff_users.role` ∈ admin, support, content | Eleven roles with scopes (D-338, D-339); code still has three |
| Add-on list fixed | Mapping set in the console (D-211) |
| Scheduler by cron | Supervisor runs `schedule:work` (D-064) |
| No second AI provider; models in config | Console model per purpose and console adapters (D-315, D-326) |
| Plain `ShouldBeUnique` (AI doc) | `ShouldBeUniqueUntilProcessing` + `WithoutOverlapping` (D-050) |
| Data model lacks `learning_outcomes`, `public_id` on years, classes, memberships | Added (D-226, D-182, D-241) |
| Support access granted by the school, logged for it (internal-roles draft §4) | MoU-based read-only access, one internal row per session (D-341) |
| `curriculum_versions` without dates; config pointer | Versioning columns and resolver before P5-04 (D-355) |

One inconsistency inside the decisions themselves: D-287 and D-289 put answer-sheet scanning on the teacher's device, while D-347 says multiple-choice answers are read by OMR on SIGRAK's own server. Confirm before P5-08 ([section 4.6](#46-assessment)).

## 13. Glossary

Indonesian education and product terms used in this handbook and in the code comments. "SIGRAK" marks a product term.

| Term | Meaning |
|---|---|
| AKM | Asesmen Kompetensi Minimum, the literacy and numeracy part of AN |
| AKMI | Asesmen Kompetensi Madrasah Indonesia (Kemenag); its 2026 status is unverified |
| AN | Asesmen Nasional, a national system survey, not a student score |
| ATP | Alur Tujuan Pembelajaran: the ordered flow of TP; in the data, topics (materi pokok) |
| Berkas Administrasi | SIGRAK: the semester bundle of a teacher's documents (D-278) |
| BK | Bimbingan dan Konseling (counselling); BK notes are restricted |
| BOS | Bantuan Operasional Sekolah, the school operating grant; never a payment source for SIGRAK |
| CP | Capaian Pembelajaran: learning outcomes per subject and phase, set nationally |
| Cuti bersama | Joint leave days set by the SKB; a holiday by default (D-255) |
| Dapodik | Kemendikdasmen's school data system of record for SD |
| DPL | Dimensi Profil Lulusan: the 8 graduate profile dimensions (DPL1…DPL8) |
| e-Rapor SD | The government report-card app for SD; SIGRAK exports to its import layout |
| EMIS | Kemenag's data system of record for madrasah |
| Fase | Phase: A (grades 1–2), B (3–4), C (5–6) |
| Fokus (materi) | A focus item inside a topic, usually one per week |
| GPK | Guru Pendamping Khusus; in SIGRAK a teacher assignment, not a role (D-348) |
| Guru kelas | Class teacher who teaches the class subjects of one class |
| Guru mapel | Subject teacher of one special subject across classes |
| IFP | Interactive Flat Panel, the classroom touch screen |
| JP | Jam Pelajaran: one lesson period, 35 minutes in SD/MI |
| JTM | Jam Tatap Muka: a teacher's weekly workload unit |
| Kabar | SIGRAK: the in-app inbox for news and notifications |
| Kaldik | Kalender pendidikan, the school calendar |
| KAIH | Gerakan 7 Kebiasaan Anak Indonesia Hebat: seven habits, the seventh "Tidur Cepat" |
| KBC | Kurikulum Berbasis Cinta (KMA 512/2026), value education in madrasah |
| Kenaikan | Promotion to the next grade, decided by the school |
| KKA | Koding dan Kecerdasan Artifisial; in SIGRAK through kokurikuler (D-208) |
| KKG / KKM | Teacher working groups (Kelompok Kerja Guru / Madrasah) |
| KKTP | Kriteria Ketercapaian Tujuan Pembelajaran: criteria for reaching a TP |
| Kokurikuler | Co-curricular learning that strengthens the DPL; replaced P5 |
| Kop | Letterhead; in SIGRAK one uploaded image |
| Korwil | Koordinator wilayah, a regional supervisor role in schools |
| KSP | Kurikulum Satuan Pendidikan: the school's own curriculum, set by the principal |
| Leger | The class grade ledger behind the rapor |
| LJK | Lembar Jawaban Komputer, the answer sheet; *Pindai LJK* scans it |
| LKPD | Lembar Kerja Peserta Didik: student worksheet |
| MI | Madrasah Ibtidaiyah: Islamic primary school under Kemenag |
| Muatan lokal | Local content subject (for example Bahasa Jawa) |
| Murid | Student; the current official UI term (D-210) |
| Mutasi | Student transfer in or out, or between rombel |
| NISN, NIP, NPSN | National student number, civil-servant number, school number |
| Operator | School staff member who administers school data |
| Panca Cinta | The five love themes of KBC |
| Pembelajaran Mendalam | Deep learning: principles berkesadaran, bermakna, menggembirakan; experiences memahami, mengaplikasi, merefleksi |
| Pengawas | School supervisor who oversees several schools |
| Pengesahan dokumen | SIGRAK: the principal's internal approval of a document, with a code (D-281) |
| PG, PGK MCMA, PGK Kategori | Multiple choice; complex multiple choice; true/false per statement |
| PKKS | Penilaian Kinerja Kepala Sekolah: principal supervision by the pengawas |
| Prota, Promes | Yearly and semester programmes |
| Rapor | Report card |
| RDM | Rapor Digital Madrasah, Kemenag's report-card app |
| Rombel | Rombongan belajar, a class group in Dapodik |
| RPM | SIGRAK's lesson plan (Rencana Pembelajaran Mendalam), replacing RPP/modul ajar |
| RTL | Rencana Tindak Lanjut: follow-up plan after supervision |
| Ruang GTK | The national teacher and staff performance platform |
| SAS, STS | End-of-semester and mid-semester summative assessments |
| SD | Sekolah Dasar: primary school under Kemendikdasmen |
| Simulasi TKA | SIGRAK: the TKA practice exam |
| SKB | Joint ministerial decree on holidays and cuti bersama |
| SKL | Standar Kompetensi Lulusan: graduate standards |
| Tayangkan | SIGRAK: present mode on a projector or IFP |
| Tim Validasi SIGRAK | SIGRAK's review team that signs off curated data (D-113) |
| TKA | Tes Kemampuan Akademik: the national academic test for grade 6 |
| TP | Tujuan Pembelajaran: learning objective |
| Wali kelas | Homeroom teacher of a class |

## 14. Where to read more

**Final docs** (English in `docs/final/`, Indonesian PDFs in `docs/pdf/`):

| Doc | Read it for |
|---|---|
| [`product-concept.md`](product-concept.md) | Product, roles, navigation, flows, plans, feature status |
| [`content-engine.md`](content-engine.md) | Templates, wizards, pipeline, RPM, Bank Soal, LKPD, games, TKA, export |
| [`ai-provider-engine.md`](ai-provider-engine.md) | Keys, selection, errors, duplicates, privacy, usage logs |
| [`data-model.md`](data-model.md) | Tables, keys, invariants, tenancy |
| [`architecture.md`](architecture.md) | Components, modules, flows, deployment, security |
| [`design-system.md`](design-system.md) | Tokens, components, voice, terms |
| [`coding-standards.md`](coding-standards.md) | PHP, database, tenancy, Svelte, tests, commits |
| [`export-localization.md`](export-localization.md) | `lang/id`, export rules, formatting, RTL |
| [`religious-education.md`](religious-education.md) | Six SD religions, MI subjects, KBC |
| [`ifp-classroom.md`](ifp-classroom.md) | Game kelas and the IFP |
| [`server-setup.md`](server-setup.md) | The VPS, step by step |
| [`server-monitoring.md`](server-monitoring.md) | Health checks and alerts |
| [`local-setup-wsl2.md`](local-setup-wsl2.md) | Local development on Windows |
| [`research-digest.md`](research-digest.md) | Every research finding so far; check before researching (D-362) |

**Key drafts** (accepted designs not yet in the final docs):

| Draft | Content |
|---|---|
| [`policy-innovation-layers.md`](../drafts/policy-innovation-layers.md) | Layers, rule strength, versioning, Profil Jenjang, taxonomy (D-352…D-359) |
| [`legacy-upgrade-01.md`](../drafts/legacy-upgrade-01.md) | Legacy app upgrade map to D-252…D-328, doc and task changes |
| [`administration-features.md`](../drafts/administration-features.md) | 2025–2026 rules and administration features (D-210…D-221) |
| [`teacher-supervision-instruments.md`](../drafts/teacher-supervision-instruments.md) | The 18 supervision instruments (D-222…D-225) |
| [`assessment-landscape.md`](../drafts/assessment-landscape.md) | Peta Asesmen and new instruments (D-344…D-351) |
| [`lkpd-exploration.md`](../drafts/lkpd-exploration.md) | LKPD patterns, blocks, local context (D-329…D-337) |
| [`internal-roles.md`](../drafts/internal-roles.md) | Staff roles, scopes, matrix (D-338…D-343) |
| [`dokumen-resmi-sd-mi.md`](../drafts/dokumen-resmi-sd-mi.md) | What the official 2025 documents change (D-203…D-209) |
| [`buku-induk-dapodik.md`](../drafts/buku-induk-dapodik.md) | Buku Induk and Dapodik sync, on hold (D-161) |
| [`km-sd-2026-r2-source-check.md`](../drafts/km-sd-2026-r2-source-check.md) | Findings on the KM-SD-2026-R2 source files |
| [`open-questions.md`](../drafts/open-questions.md) | Remaining owner questions |

**Data:** [`docs/data/curriculum/README.md`](../data/curriculum/README.md) (curriculum files and provenance), [`docs/data/regulations/README.md`](../data/regulations/README.md) (official 2025 rules as data), `docs/data/schools/` (school directory), `docs/data/tka/` (TKA framework). Planned: `docs/data/taxonomy/`, `docs/data/assessment/`, `docs/data/catalogs/`.

**Agent files:** [`CONTEXT.md`](../agent/CONTEXT.md), [`PROGRESS.md`](../agent/PROGRESS.md), [`DECISIONS.md`](../agent/DECISIONS.md), [`WORKFLOW.md`](../agent/WORKFLOW.md), and the task files in [`docs/agent/tasks/`](../agent/tasks/).
