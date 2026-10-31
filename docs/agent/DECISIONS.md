# Decisions

Append-only log. One entry per decision; newest at the bottom. Change a decision by adding a new entry that supersedes the old one.

Format: `D-NNN — Title` · Status (accepted / open / superseded by D-NNN) · Date · Decision · Reason.

---

### D-001 — No AI attribution
Accepted · 2026-10-30
No Claude/AI attribution in commits, PRs, GitHub comments, code, or docs. Enforced by `CLAUDE.md` and `.claude/settings.json`.
Reason: owner's requirement.

### D-002 — Language policy
Accepted · 2026-10-30
Repo markdown and code naming in English. UI text, app exports (Excel/CSV/Word/PDF), and generated PDFs in Indonesian.
Reason: owner's requirement; English keeps code/docs standard, Indonesian serves end users and readers.

### D-003 — Core stack
Accepted · 2026-10-30
Laravel, Nginx, PHP-FPM, Redis, multiple queue workers, Node.js (tooling), Python when needed, Cloudflare, Ubuntu Server 22.04 VPS, Tailwind + TailAdmin, Inertia.js, Filament, Telescope.
Reason: owner's requirement.

### D-004 — Database engine
Superseded by D-007 · 2026-10-30
MySQL vs MariaDB not chosen yet. Until decided, keep migrations and queries portable.

### D-005 — Svelte vs SvelteKit with Inertia
Superseded by D-008 · 2026-10-30
Owner mentioned "Inertia.js SvelteKit". Inertia works with plain **Svelte** (`@inertiajs/svelte`) — Laravel handles routing, so SvelteKit's router/server is not used. SvelteKit would only fit as a separate frontend talking to a Laravel API (no Inertia).
Recommendation: Inertia.js + Svelte. Awaiting owner confirmation.

### D-006 — Docs pipeline and parallel work
Accepted · 2026-10-30
Docs flow `docs/drafts/` → `docs/final/` → `docs/pdf/` (Indonesian). Work is phased; tasks run in parallel via sub-agents or sessions, coordinated through `PROGRESS.md` and `WORKFLOW.md`.
Reason: owner's requirement for staged, parallel, consistent, token-efficient work.

### D-007 — Database: MySQL 8
Accepted · 2026-10-30 · supersedes D-004
MySQL 8 (CHECK constraints from 8.0.16, mature JSON columns). Migrations may use MySQL 8 features.
Reason: owner's choice in the 2026-10-30 interview; matches the EDUNEXA developer notes.

### D-008 — Frontend: Inertia.js + plain Svelte
Accepted · 2026-10-30 · supersedes D-005
`@inertiajs/svelte` with Laravel routing. No SvelteKit.
Reason: owner confirmed the recommendation in D-005.

### D-009 — Product scope: content creation + administration from v1
Accepted · 2026-10-30
SIGRAK (working name) is an AI-assisted platform for making teaching content (Guided Generator, Template Engine, AI Provider Engine) and keeps EDUNEXA's school administration features. Both ship in v1. See `docs/drafts/product-concept.md`.
Reason: owner's concept message and interview.

### D-010 — Users and access
Accepted · 2026-10-30
Roles: teacher (class/subject), principal, school operator, supervisor (read-only over supervised schools), internal staff (separate Filament console and guard). Students are records, never users. Every page needs login; only login, invitation acceptance, and password reset are reachable without a session. No public pages or share links.
Reason: owner's concept message and interview.

### D-011 — How games reach students
Accepted · 2026-10-30
Present mode inside the teacher's session, plus a standalone offline HTML download (no login, no network calls, teaching content only).
Reason: students are not users and there are no public pages (D-010), but students still need to play.

### D-012 — AI keys are teacher-owned, several per teacher
Accepted · 2026-10-30
Each teacher adds one or more Gemini keys from Google projects they own. Round robin + failover run only within that teacher's keys. No school or platform key. Principals need no key. AI cost is never a SIGRAK cost. Supersedes the optional school fallback key in the EDUNEXA notes.
Reason: owner's concept message and interview. Gemini quota is per project.

### D-013 — Template storage: code + DB registry
Accepted · 2026-10-30
Template definitions, schemas, renderers, and game runtimes live in the repo. The DB holds the registry (active flag, versions) and optional prompt-instruction overrides edited in Filament, which create a new immutable version.
Reason: testable, versioned code with prompt tuning that needs no deploy.

### D-014 — v1 templates
Accepted · 2026-10-30
RPM (incl. kokurikuler variant), Bank Soal, LKPD, Quiz Battle, and Buat bebas (guided). Drag & Drop, Word Puzzle, Maze, and Flashcard come later.
Reason: owner's choice. Quiz Battle reuses Bank Soal, so the document-to-game path is proven early.

### D-015 — No AI-generated code in v1
Accepted · 2026-10-30
Games are built only from template runtimes. AI supplies content and allowed config values. Teachers' requests for missing features are logged for the team.
Reason: quality, consistency, and security.

### D-016 — Offline: Di Kelas only
Accepted · 2026-10-30
Attendance, journal, and student notes work offline and sync by `client_uuid`. Everything else needs a connection.
Reason: owner's choice. Keeps the PWA scope small.

### D-017 — Brand line
Accepted · 2026-10-30
Tagline "Kerja guru, lebih sigrak." Promise "Bahan ajar siap pakai dan administrasi beres, tanpa menulis prompt." The name SIGRAK stays a working name until checked in PDKI.
Reason: the old promise covered administration only.

### D-018 — Market: SD/MI, not limited to Kudus
Accepted · 2026-10-30
SD/MI grades 1–6 anywhere in Indonesia. Local content and the school calendar are configurable per region and per school. Kudus is the pilot and supplies the defaults. SMP/MTs come later, once curriculum data exists.
Reason: owner's choice. Supersedes "Kudus only" in the rebranding notes.

### D-019 — Validation flow
Accepted · 2026-10-30
Generated content is usable at once. Principal and supervisor can view and comment. Only school-changed or school-added curriculum goes through teacher → principal → supervisor.
Reason: owner kept the EDUNEXA rule.

### D-020 — Fresh build, curriculum-only import
Accepted · 2026-10-30
No Firebase migration of accounts, RPM, questions, or scores. Only curriculum JSON (KM-SD-2026-R2) is imported. Teachers are re-invited. Supersedes the staged migration plan in the EDUNEXA notes.
Reason: owner's choice. A new stack and a new data model.

### D-021 — Teacher navigation
Accepted · 2026-10-30
Hari Ini · Di Kelas · Buat · Asesmen · Kelas Saya. "Rencana" becomes "Buat" (template gallery, Buat bebas, Kurikulum, Kokurikuler). Dokumen Saya goes in the account menu.
Reason: owner's choice. Keeps a 5-item bottom nav.

### D-022 — AI engine technical choices
Accepted by D-116 · 2026-10-30 · item 2 superseded by D-024
(1) Own `AIProvider` interface with a Gemini adapter on Laravel's HTTP client, not the Laravel AI SDK (per-call key choice and raw error classification are core; the SDK can become the transport later).
(2) ~~One generic `RunGeneration` job that runs the next step and re-dispatches itself.~~ Superseded by D-024.
(3) Progress by polling (Inertia partial reload), websockets later only if needed.
(4) Education logic lives in `app/Content/`, provider logic in `app/AI/`.
Reason: see `docs/drafts/ai-provider-engine.md`. Awaiting owner review.

### D-023 — AI keys: Gemini free tier only
Accepted · 2026-10-30 · refines D-012
Only Google Gemini keys are accepted, and the design targets the free tier: few calls per generation, stable models only, no background pings. Multi-key rotation stays, at the teacher's own risk. Google's API terms forbid circumventing limits, so the second and later keys need explicit consent. No student personal data goes into prompts, because free-tier prompts may be read by Google's reviewers. AI users are adults (teachers) only.
Reason: owner's decision; Gemini API Additional Terms and Google APIs Terms checked 2026-10-30.

### D-024 — No generic generator; one job per content type
Accepted · 2026-10-30 · supersedes D-022 item 2 and removes "Buat bebas" from D-014
Every content type has its own template, wizard, and queued job (`GenerateRpm`, `GenerateBankSoal`, `GenerateLkpd`, `GenerateQuizBattle`, `GenerateTkaPackage`, plus `ReviseSection`). They share only the runner, the AI gateway, the editor shell, and export. There is no "make anything" generator.
Reason: owner — a generic generator is something teachers can already do in Gemini directly.

### D-025 — Subscriptions are paid outside BOS
Accepted · 2026-10-30
Subscriptions are paid from funds other than BOS. Sales material never presents BOS as a payment source.
Reason: owner's decision.

### D-026 — Exams: Mode Ujian inactive, TKA practice CBT in v1
Accepted · 2026-10-30
The general Bank Soal "Mode Ujian" is switched off for now. v1 adds a TKA practice exam (CBT) for grade 6, modelled on the national TKA SD/MI (Bahasa Indonesia and Matematika, 75 minutes each, the forms PG / PGK MCMA / PGK Kategori, scores 0–100 with the TKA categories). Questions come from a dedicated "Paket Soal TKA" template that follows the Kerangka Asesmen TKA (Perka BSKAP 047/H/AN/2025).
Reason: owner's decision. Timed to be ready before TKA 2027 practice.

### D-027 — TKA exam access: exam cards + proctor token on a separate exam site
Accepted · 2026-10-30 · narrow exception to D-010
Students take the practice exam on a separate exam site (own subdomain and guard) with a per-session exam card (username + password) and a rotating proctor token (TOTP-style, 15 min). Credentials expire when the session closes. The exam site never calls the AI and links nowhere into the app. Students remain records, not users.
Reason: owner's choice. Also keeps Gemini's under-18 restriction away from the student-facing part.

### D-028 — Document engines: Gotenberg for PDF, Laravel Excel for spreadsheets
Accepted · 2026-10-30 · resolves the open PDF engine question · Word-to-PDF and merge dropped by D-031
PDF: Gotenberg 8 (Chromium + LibreOffice, Docker, internal network only) through `spatie/laravel-pdf`'s Gotenberg driver. It gives HTML/CSS fidelity, Arabic shaping, headers and footers, DOCX→PDF, merge, watermark, and PDF/A. Excel: Laravel Excel (`maatwebsite/excel`, PhpSpreadsheet), with styled multi-sheet exports, queued exports, and validated imports. DOCX: PhpWord TemplateProcessor. This adds Docker to the server, for this one service.
Reason: owner asked for powerful engines. Dompdf/mPDF are too weak for the documents; Browsershot would need Node/Chromium on the host.

### D-029 — Attendance is daily
Accepted · 2026-10-30
One attendance record per student per day, taken by the homeroom teacher. Per-lesson attendance comes later.
Reason: owner's decision.

### D-030 — TKA figures and scoring
Accepted · 2026-10-30
Figures come from a bundled set (SVG) chosen by id. Cube stacks are drawn from a height map with three.js, and the view keys are computed by code. There are no AI images. PGK MCMA and PGK Kategori get partial credit, normalised to the TKA SD range 0–100 and shown as a practice estimate.
Reason: owner's choice. AI is unreliable at spatial reasoning and image generation.

### D-031 — Letterhead is an uploaded image
Accepted · 2026-10-30 · refines D-028
The school uploads a default letterhead image, and a teacher may upload their own, which replaces it on their documents. The layout and logo live in the image. Rules: PNG/JPG, ≥ 2,000 px wide, ratio 4:1–8:1, ≤ 2 MB, printed height ≤ 4 cm, previewed on A4/F4. As a result, Word-to-PDF conversion and PDF merging are not needed: one HTML view → one PDF, and DOCX is built from SIGRAK's own layouts.
Reason: owner — simpler for teachers. Removes per-field letterhead settings.

### D-032 — Class teachers and special subject teachers
Accepted · 2026-10-30
SD/MI has guru kelas (homeroom) and guru mapel. Guru mapel subjects by default: PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, Pendidikan Agama. Every other subject defaults to the homeroom teacher. The operator can reassign per class.
Reason: owner's description of SD staffing.

### D-033 — Rendering libraries; no presentation feature
Accepted · 2026-10-30
Added where templates need them: three.js (figures), KaTeX (math), ApexCharts (charts), an Arabic font. All pinned and bundled, no CDN. No slide/presentation feature and no presentation library.
Reason: owner allowed powerful libraries when needed, and declined the "Bahan Tayang" feature.

### D-034 — SD religion subjects: six religions in v1
Accepted · 2026-10-30
SD offers Pendidikan Agama dan Budi Pekerti for Islam, Kristen, Katolik, Hindu, Buddha, and Khonghucu as separate subjects. The CP source is Kepka BKPDM No. 020 Tahun 2026, Lampiran I. It was transcribed from the scanned copy, because no text version exists, and must be verified before import. PAI keeps its KM-SD-2026-R2 data, which already follows 020/2026. For the other five religions the team builds topics, TP, ATP, fokus, and JP from the CP: AI-assisted drafts, reviewed by a teacher of that religion. Allocation is 3 JP per week, as for PAI.
Reason: owner's choice; official decree supplied by the owner.

### D-035 — MI uses the Kemenag PAI subjects and Bahasa Arab
Accepted · 2026-10-30
MI teaches Al-Qur'an Hadis, Akidah Akhlak, Fikih, Sejarah Kebudayaan Islam (grades 3–6), and Bahasa Arab instead of one PAI subject. CP comes from SK Dirjen Pendis No. 9941 Tahun 2025, the latest found. The structure and JP come from KMA 450/2024 as amended by KMA 1503/2025, and the official copy is still needed. The team builds topics, TP, ATP, fokus, and JP for these subjects.
Reason: owner's choice; the latest official documents found on 2026-10-30.

### D-036 — Student religion is sensitive data
Accepted · 2026-10-30
`students.religion` exists only to group religion classes. It is visible to the class's teachers, the operator, and the principal. It is hidden from supervisors, never sent to the AI, and never shown on the exam site.
Reason: religious belief is sensitive personal data. This follows the privacy rules in D-023.

### D-037 — Religion scope details
Accepted · 2026-10-30
(1) Pendidikan Kepercayaan terhadap Tuhan YME is not in v1. (2) MI's Kemenag subjects (Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab) are taught by guru mapel. (3) Religion curriculum data is reviewed and signed off by a guru agama of that same religion (for MI, a guru of that subject). The reviewer's name, school, and date are recorded in the dataset.
Reason: owner's decisions.

### D-038 — MI allocation confirmed from the official KMA 1503/2025
Accepted · 2026-10-30 · closes the "official copy still needed" part of D-035
The MI subject structure and JP come from KMA 1503 Tahun 2025, Lampiran II, BAB III B, Tabel 1–5 (pp. 28–31), read from the official scanned copy supplied by the owner. Data: `docs/data/curriculum/mi-allocation-kma-1503-2025.json` (`ALOKASI-MI-KMA-1503-2025`), `verified: true`. 36 weeks per year (32 in grade VI), 1 JP = 35 minutes. Required intrakurikuler totals: I 1,044 · II 1,116 · III–IV 1,368 · V 1,368 · VI 1,216 JP per year; kokurikuler 108 (I–V) and 96 (VI). Koding dan Kecerdasan Artifisial is an elective in grades V–VI. Muatan lokal is 72–216 JP per year (64–192 in grade VI), and a madrasah may add up to 6 JP per week. Only an official-channel copy of SK 9941/2025 is still open.
Reason: the official decree is now archived in `docs/sources/`; every figure was checked against the scan and the decree's printed totals.

### D-039 — MI CP row shape and Arabic passages
Accepted by D-123 · 2026-10-31 · P2-02
MI CP rows use the SD shape (`element`, `sub_element`, `text`) plus `lead_in`, `element_intros`, and for Bahasa Arab `arabic_passages[] {pdf_page, text}`. Arabic stays inline in `text`. UIs and PDFs render `text` with bidi isolation, or show `arabic_passages` as separate RTL blocks.
Reason: one import shape for SD and MI CP, and no text is lost.

### D-040 — CP source typos kept as printed
Superseded by D-115 · 2026-10-31 · P2-02
Outcome text keeps source typos as printed and flags them in `extraction.source_notes`. Only sub-element names are normalised, because they act as keys. A guru of the subject decides whether the app shows the printed or the corrected form.
Reason: the data must stay traceable to the decree.

### D-041 — TKA 2026 categories and the Istimewa predicate
Accepted by D-120 · 2026-10-31 · P2-03 · refines `content-engine.md`
TKA SD/MI results use three categories (Baik, Memadai, Kurang) and an Istimewa predicate for scores ≥ 95, shown as "Baik-Istimewa" (Kepmendikdasmen 56/2026). This replaces the 2025 four-category list in the draft.
Reason: the 2026 decree changed the rule.

### D-042 — TKA scoring wording
Accepted by D-120 · 2026-10-31 · P2-03 · keeps D-030
The ministry describes classical scoring on 0–100 (two decimals). PGK partial credit is still unpublished, so D-030's partial credit stays a practice estimate, but the UI may describe official scoring more precisely.
Reason: new official statements since the draft was written.

### D-043 — AN items inside TKA
Accepted, open part resolved by D-111 · 2026-10-31 · P2-03
From 2026, AN literasi items are part of the Bahasa Indonesia test and AN numerasi items are part of Matematika (Kepmendikdasmen 56/2026 BAB IV D). The data lists their competencies as observed only. Open: whether the Paket Soal TKA wizard offers them as extra competencies.
Reason: official 2026 sample sets include these items.

### D-044 — Matematika items are tagged by cognitive level
Accepted by D-120 · 2026-10-31 · P2-03
A Matematika item stores a scope point (element → sub-element → scope) and a cognitive level (Pengetahuan dan Pemahaman, Aplikasi, Penalaran) with its thinking process.
Reason: that is the framework's own tagging dimension (Perka BSKAP 047/H/AN/2025).

### D-045 — PGK Kategori labels
Accepted by D-120 · 2026-10-31 · P2-03
Allowed label pairs: Benar/Salah, Sesuai/Tidak Sesuai, Setuju/Tidak Setuju. Mendukung/Tidak Mendukung is dropped, because it was not found in the SD sources.
Reason: follows the decree and the official samples.

### D-046 — TKA duration is configurable
Accepted by D-120 · 2026-10-31 · P2-03
The default is 75 minutes per subject (2026). It was 60 minutes in 2025, so the duration is stored per framework year.
Reason: the value changes between years.

### D-047 — Default form mix for a 30-item package
Accepted by D-120 · 2026-10-31 · P2-03
The default form mix follows the official sample sets: Bahasa Indonesia 16 PG / 6 PGK MCMA / 8 PGK Kategori; Matematika 18 PG / 3 MCMA / 9 Kategori. PGK MCMA has 3 statements with 2 correct. This is not an official blueprint.
Reason: the closest official reference available.

### D-048 — One codebase, three hostnames, three guards
Accepted by D-123 · 2026-10-31 · P2-04 · confirmed by D-068, hostnames by D-069
The school app, exam site, and staff console are domain-routed groups in one Laravel app (`app.`, `ujian.`, `admin.`). Each has its own guard and a host-only session cookie.
Reason: one deploy and one data model on one VPS, while keeping D-010 and D-027 isolation.

### D-049 — Module folders
Accepted by D-123 · 2026-10-31 · P2-04
Module folders besides `app/AI` and `app/Content`: `app/Tenancy`, `app/Curriculum`, `app/Classroom`, `app/Exam`, with the dependency table in `architecture.md#module-boundaries`. `Exam` and `Classroom` never depend on `AI`.
Reason: clear boundaries that Pest architecture tests can enforce (D-074).

### D-050 — Generation job uniqueness
Accepted by D-123 · 2026-10-31 · P2-04 · refines D-024
Generation jobs use `ShouldBeUniqueUntilProcessing` plus `WithoutOverlapping`, keyed by the generation id, instead of plain `ShouldBeUnique`.
Reason: with plain `ShouldBeUnique`, a job cannot re-dispatch itself for the next step.

### D-051 — Offline sync uses the web session
Accepted by D-123 · 2026-10-31 · P2-04
Di Kelas offline sync uses the web session + CSRF, not an API token. Queued rows survive an expired session until the next login. Cached student data is cleared on logout and excludes religion (D-036).
Reason: no second auth system for one small endpoint.

### D-052 — Gotenberg gets every asset in the request
Accepted by D-123 · 2026-10-31 · P2-04
Every asset is sent inside the Gotenberg request, and Gotenberg has no outbound network (see D-059).
Reason: no remote fetches from a headless browser.

### D-053 — Separate Redis databases
Accepted by D-123 · 2026-10-31 · P2-04
Cache, sessions, queues/Horizon, and AI state each use their own Redis database (or a prefixed connection).
Reason: `cache:clear` must not wipe sessions, jobs, or key cooldowns.

### D-054 — Exam-site rate limits by session and username
Accepted by D-120 · 2026-10-31 · P2-04
Login throttling on the exam site is keyed by exam session and username, not by IP alone. Cloudflare rules for that hostname allow bursts of 30–40 logins from one IP.
Reason: a whole class shares one school IP.

### D-055 — Staff tools on the admin hostname
Accepted by D-123 · 2026-10-31 · P2-04
Filament and Horizon live only on `admin.` behind the `staff` guard.
Reason: keeps staff tooling apart from school users (D-010).

### D-056 — PHP 8.5
Accepted by D-123 · 2026-10-31 · P2-05
PHP 8.5 (≥ 8.5.12) from `ppa:ondrej/php`. PHP 8.4 is used only if a required package blocks 8.5.
Reason: Laravel 13 supports 8.3–8.5, and 8.5 has the longest support window.

### D-057 — MySQL 8.4 LTS
Accepted by D-123 · 2026-10-31 · P2-05 · within D-007
MySQL 8.4 LTS from Oracle's APT repository (`mysql-8.4-lts`), not Ubuntu's 8.0 (end of life) and not 9.x.
Reason: the supported MySQL 8 line.

### D-058 — Redis 8.2
Accepted by D-123 · 2026-10-31 · P2-05
Redis 8.2 from packages.redis.io, pinned to `6:8.2.*`, with `noeviction` and AOF on.
Reason: a supported release, and queue data must not be evicted.

### D-059 — Gotenberg image and isolation
Accepted by D-123 · 2026-10-31 · P2-05 · refines D-028
`gotenberg/gotenberg:8.39.0-chromium`, published on `127.0.0.1:3000` with basic auth and a 1 GB memory limit, on its own Docker network that drops new outbound connections. Fonts are mounted from `/opt/gotenberg/fonts`.
Reason: pinned, local-only, no egress.

### D-060 — Node 24 LTS for builds only
Accepted by D-123 · 2026-10-31 · P2-05
Node 24 LTS for builds only, preferably in CI. No Node on the server unless a runtime need appears.
Reason: CONTEXT already prefers building assets outside the server.

### D-061 — Ubuntu's Nginx package
Accepted by D-123 · 2026-10-31 · P2-05
Use Ubuntu's Nginx package (patched by Canonical), not nginx.org builds.
Reason: security updates come with the OS.

### D-062 — Cloudflare TLS and origin lock
Superseded by D-088 · 2026-10-31 · P2-05
TLS mode Full (strict) with a 15-year Origin CA wildcard certificate and global Authenticated Origin Pulls. UFW allows 80/443 only from Cloudflare ranges, and a weekly job refreshes those ranges and the Nginx real-IP list.
Reason: the origin is reachable only through Cloudflare.

### D-063 — Release-folder deploys
Accepted by D-123 · 2026-10-31 · P2-05
Deploys use `releases/`, `shared/`, and a `current` symlink, keeping 5 releases. PHP-FPM is reloaded on each deploy, because OPcache does not check timestamps.
Reason: atomic deploys with quick rollback.

### D-064 — Supervisor runs Horizon and the scheduler
Accepted by D-123 · 2026-10-31 · P2-05
Supervisor runs Horizon and `schedule:work`, so there is no cron entry. Worker counts live in `config/horizon.php`.
Reason: one process manager for all long-running processes.

### D-065 — Backups
Accepted by D-123 · 2026-10-31 · P2-05 · storage by D-071
Daily at 01:30 WIB: `mysqldump` + a `storage/app` tarball. Kept 14 days locally and 30 days off-site through an encrypted `rclone` remote. Restore test monthly, and off-site restore twice a year. `APP_KEY` stays in the password manager, never in a backup.
Reason: no draft covered backups.

### D-066 — Servers run in UTC
Accepted by D-123 · 2026-10-31 · P2-05, P2-06 · see D-082
Server, PHP, and MySQL run in UTC, and the app displays `Asia/Jakarta`. Cron times are UTC.
Reason: one storage timezone, local display.

### D-067 — Ubuntu version
Closed by D-086 · 2026-10-31 · P2-05 · concerns D-003
Standard support for Ubuntu 22.04 ends in mid-2027, right after the first TKA season. The guide recommends 24.04 LTS, and its commands work unchanged there. The alternative is to stay on 22.04 with Ubuntu Pro. Until the owner decides, D-003 (22.04) stands.
Reason: needs an owner decision.

### D-068 — Three Nginx vhosts for one codebase
Accepted by D-123 · 2026-10-31 · P2-05 · same as D-048
Nginx has three vhosts (`app.`, `ujian.`, `admin.`) for one Laravel codebase, with host-only session cookies (no shared `SESSION_DOMAIN`).
Reason: the server-side half of D-048.

### D-069 — Domain sigrak.com
Accepted · 2026-10-31 · P2-05
The domain is `sigrak.com`, with hostnames `app.sigrak.com`, `ujian.sigrak.com`, and `admin.sigrak.com`. The brand check in PDKI stays an open question.
Reason: owner's decision.

### D-070 — VPS provider: IDCloudHost Jakarta
Accepted · 2026-10-31 · P2-05
Production runs on an IDCloudHost Cloud VPS in Jakarta. The plan size is still open.
Reason: owner's decision; low latency for schools.

### D-071 — Off-site backups on Cloudflare R2
Accepted · 2026-10-31 · P2-05
Off-site backups go to Cloudflare R2 with a bucket-scoped token, encrypted with an `rclone` crypt remote.
Reason: owner's decision; Cloudflare is already in the stack.

### D-072 — Staging server
Superseded by D-095 · 2026-10-31 · P2-05
A staging server is required: a second, smaller IDCloudHost VPS in Jakarta on `staging-*.sigrak.com`, behind Cloudflare Access, with seeded data only. Telescope is allowed there.
Reason: owner's decision.

### D-073 — Gotenberg env keys; monitoring as a separate task
Accepted · 2026-10-31 · P2-05
(1) Gotenberg `.env` keys follow `spatie/laravel-pdf` v2: `LARAVEL_PDF_DRIVER=gotenberg`, `GOTENBERG_URL`, `GOTENBERG_USERNAME`, `GOTENBERG_PASSWORD`. The owner asked the team to define them. (2) Server monitoring and alerts get a separate task.
Reason: owner's answers.

### D-074 — Pest for tests
Accepted · 2026-10-31 · P2-06
Pest is the test framework, and Pest architecture tests enforce the layering rules (D-049). PHPUnit stays underneath.
Reason: owner approved; it makes the rules machine-checkable.

### D-075 — TypeScript in Svelte
Accepted · 2026-10-31 · P2-06
`<script lang="ts">` in the school app and exam client, with `svelte-check` in CI. The game runtime stays vanilla JS with JSDoc + `@ts-check` (D-011).
Reason: owner approved; typed Inertia props.

### D-076 — Svelte 5 runes
Accepted · 2026-10-31 · P2-06 · refines D-008
Components use Svelte 5 runes syntax.
Reason: owner approved; the current Svelte major, supported by `@inertiajs/svelte`.

### D-077 — Larastan
Accepted by D-123 · 2026-10-31 · P2-06
Larastan static analysis, starting at level 6.
Reason: catches type errors before review.

### D-078 — Action classes
Accepted by D-123 · 2026-10-31 · P2-06 · adds to D-049
Business operations live in Action classes, and controllers stay thin. Actions live inside module folders, and in `app/Actions/<Domain>/` only for features without a module.
Reason: one place for each operation, reusable from controllers, jobs, and commands.

### D-079 — Tests run on MySQL 8
Accepted by D-123 · 2026-10-31 · P2-06
Tests run on MySQL 8, not SQLite.
Reason: the schema uses MySQL 8 features (D-007).

### D-080 — Frontend translations via a shared prop
Accepted by D-123 · 2026-10-31 · P2-06
Frontend text comes from `lang/id` through a shared Inertia prop and one `t()` helper. There are no separate JS translation files.
Reason: one source of UI text.

### D-081 — Typed route helper
Accepted by D-123 · 2026-10-31 · P2-06
The frontend uses a typed route helper: Laravel Wayfinder, or Ziggy if Wayfinder does not fit plain Svelte. Chosen at scaffold time.
Reason: no hard-coded URLs in Svelte.

### D-082 — App timezone UTC
Accepted by D-123 · 2026-10-31 · P2-06 · same as D-066
`config/app.php` timezone is UTC. Display and exports use `Asia/Jakarta`.
Reason: makes the data model's "stored in UTC" explicit.

### D-083 — Exact pins for output-affecting libraries
Accepted by D-123 · 2026-10-31 · P2-06 · refines D-033
Front-end libraries that affect generated output are pinned to exact versions.
Reason: same input gives the same document.

### D-084 — CI on GitHub Actions
Accepted · 2026-10-31 · P2-06
GitHub Actions runs the GIT-4 checks on every PR to `main`, with a MySQL 8 service container. A PR merges only when it is green.
Reason: owner's decision.

### D-085 — Migrations without down()
Accepted · 2026-10-31 · P2-06
`down()` is optional. Production is forward-only: a mistake is fixed with a new migration, never a rollback.
Reason: owner's decision.

### D-086 — Ubuntu Server 24.04 LTS
Accepted · 2026-10-31 · P2-05 · closes D-067, supersedes the OS in D-003
The VPS runs Ubuntu Server 24.04 LTS, not 22.04.
Reason: owner's decision. Standard support for 22.04 ends in mid-2027, right after the first TKA season.

### D-087 — Production VPS size
Accepted · 2026-10-31 · P2-05 · refines D-070
Production is an IDCloudHost custom package with 8 vCPU and 16 GB RAM. PHP-FPM, MySQL, Redis, Gotenberg, and Horizon sizing in `server-setup.md` follows it. Disk size and the staging package are still open.
Reason: owner's decision.

### D-088 — No public IP; Cloudflare Tunnel
Accepted · 2026-10-31 · P2-05 · supersedes D-062
The VPS has no public IP. A locally-managed `cloudflared` tunnel per server routes `app.`, `ujian.`, `admin.`, and `ssh.` to localhost (Nginx on `127.0.0.1:8080`). The real client IP comes from `CF-Connecting-IP`, trusted only from 127.0.0.1. UFW denies all incoming. SSH and `admin.` sit behind Cloudflare Access. There is no Origin CA, no Authenticated Origin Pulls, and no Cloudflare IP allow-list. Staging has its own tunnel.
Reason: owner's decision; the VPS has no public IP.

### D-089 — TKA in SIGRAK is a practice simulation
Accepted · 2026-10-31 · P2-03 · frames D-041…D-047
TKA in SIGRAK is a practice simulation for student drill, and it does not have to match the official TKA exactly. The official framework (`docs/data/tka/kerangka-tka-sd.json`) is a reference for item style: competencies, forms, stimulus rules, and cognitive levels. It is not a compliance target. Duration (75 min), item count (30), the category limits (50; 76,67), the Istimewa predicate, and scoring are practice defaults that teachers can change. Nobody needs to look for official sources for the `confidence: secondary` values.
Reason: owner's decision ("tka disini hanya simulasi … soal2 nya mirip tka untuk latihan drill siswa").

### D-090 — Mode Contoh is a seeded demo school
Accepted · 2026-10-31
Mode Contoh is a seeded demo school tenant with fictional data only. AI calls are allowed, using the salesperson's own Gemini key under the same key rules as teachers (D-012, D-023).
Reason: owner's decision; a demo shows real generation.

### D-091 — Manual payment, no payment gateway
Accepted · 2026-10-31 · refines D-025
Subscriptions are paid outside the app, for example by bank transfer, and confirmed manually by an admin or the marketing team in the staff console, which then activates the tier. v1 has no automatic payment gateway.
Reason: owner's decision.

### D-092 — Exam capacity target
Accepted · 2026-10-31
One TKA practice session holds one grade 6 class of 10–25 participants. Several schools may run sessions at the same time; the 8 vCPU / 16 GB server (D-087) is sized well beyond this.
Reason: owner's decision.

### D-093 — The school app is installable
Accepted · 2026-10-31 · adds to D-016
The school app is a PWA (manifest, icons, service worker) that teachers can install on phone and desktop. The Di Kelas offline cache stays limited to Absensi, Jurnal, and Catatan Siswa (D-016). The exam site is not installable.
Reason: owner's decision.

### D-094 — Exam client on Laravel + Inertia + Svelte
Accepted · 2026-10-31 · team choice delegated by the owner · refines D-027
The exam site uses the same stack as the school app: Laravel, Inertia, and Svelte 5 + TypeScript. It has its own Vite entry and root view (`resources/exam-client/`), its own `exam` guard, and its own hostname, and it shares no bundle with the school app. Pages (login, token, exam, finish) are Inertia pages. Answer autosave and the heartbeat go through one small JSON endpoint in the same app. The timer runs in the browser against a server-side end time.
Reason: the owner asked to stay on Laravel if performance and effort allow. At 10–25 participants per session (D-092), a separate SPA brings no performance gain, while one stack shares components (KaTeX, figures, D-033), tests, and deploys.

### D-095 — Staging is a second site on the production VPS
Accepted · 2026-10-31 · P2-05 · supersedes D-072
Staging runs on the production VPS as a second site, isolated by its own system user, PHP-FPM pool, database and database user, Redis DB numbers and prefixes, and Horizon/scheduler programs. It uses `staging-app.`, `staging-ujian.`, and `staging-admin.sigrak.com` on the same Cloudflare Tunnel, behind Cloudflare Access, with seeded data only. MySQL, Redis, and Gotenberg are shared with production, so only app releases are staged. Service and OS upgrades are rehearsed on a temporary VM or snapshot.
Reason: owner's decision.

### D-096 — Production disk 320 GB
Accepted · 2026-10-31 · P2-05 · refines D-087
The production VPS is 8 vCPU / 16 GB RAM / 320 GB NVMe.
Reason: owner's decision.

### D-097 — Page-level translation groups
Accepted by D-123 · 2026-10-31 · P2-07 · refines D-080
`common`, `nav`, `terms`, and `enums` are always shared. A page adds files through a `translations:<files>` route middleware. Server-only files are refused, and the prop is lazy, sent on full visits only.
Reason: each page loads only the text it needs.

### D-098 — Glossary file and banned-terms test
Accepted by D-123 · 2026-10-31 · P2-07
The 37 *Istilah* terms live in `lang/id/terms.php`. A test rejects the old terms in any `lang/id` value.
Reason: one home for the standard terms.

### D-099 — Spreadsheet tracking file
Accepted by D-123 · 2026-10-31 · P2-07
`docs/data/ui-copy/key-map.csv` records, per row of the paraphrase spreadsheet, its key or why the row was dropped. It is filled in screen by screen.
Reason: every source string is traceable.

### D-100 — `lang:audit` command
Accepted by D-123 · 2026-10-31 · P2-07
An artisan command reports missing, unused, and hard-coded strings. Missing keys fail CI (D-084).
Reason: keeps `lang/id` complete.

### D-101 — Indonesian validation messages
Accepted by D-123 · 2026-10-31 · P2-07
Validation messages start from a one-time copy of the MIT `laravel-lang` Indonesian file, edited to SIGRAK's voice. It is not installed as a package.
Reason: a good base without a dependency.

### D-102 — Notifications store key + params
Accepted by D-123 · 2026-10-31 · P2-07
Database notifications store a translation key and its parameters, and the text is rendered when the notification is read.
Reason: copy changes reach old notifications.

### D-103 — CSV format
Accepted by D-123 · 2026-10-31 · P2-07
CSV uses a `;` separator, UTF-8 with BOM, CRLF, and comma decimals. XLSX is the default export, and files for external systems follow their own spec.
Reason: opens correctly in Excel with Indonesian settings.

### D-104 — Rupiah format
Accepted by D-123 · 2026-10-31 · P2-07
Rupiah is shown as `Rp1.234.567` (no space, no decimals), built by `IndonesianFormat::rupiah()` and `format.rupiah()`, not by ICU.
Reason: ICU output differs between versions.

### D-105 — Print fonts
Accepted, refined by D-109 · 2026-10-31 · P2-07 · adds to D-033
Gotenberg uses Liberation Sans/Serif as metric-compatible stand-ins for Arial and Times New Roman, and Noto Naskh Arabic for Arabic. All fonts are bundled.
Reason: free fonts with the same layout as the school-standard fonts.

### D-106 — F4 paper size
Accepted by D-109 · 2026-10-31 · P2-07
F4 is 215 × 330 mm. Open: whether some schools need 210 × 330 mm as a setting.
Reason: the common Indonesian folio size.

### D-107 — Per-student export file names
Accepted by D-123 · 2026-10-31 · P2-07 · follows D-023, D-036
Per-student export files are named by class and roster number, never by student name or NISN.
Reason: no personal data in file names.

### D-108 — When to show "WIB"
Accepted by D-123 · 2026-10-31 · P2-07
"WIB" is shown only for times tied to outside events or other time zones, such as the quota reset and exam windows.
Reason: less noise; all school times are local.

### D-109 — F4 size and one print font
Accepted · 2026-10-31 · accepts D-106 and refines D-105
F4 is 215 × 330 mm. Every printed document uses the same fonts: Arial, rendered as the metric-compatible Liberation Sans in Gotenberg; Noto Naskh Arabic for Arabic; KaTeX fonts for math. Schools cannot choose another print font, so `school_print_settings.font` is dropped.
Reason: owner's decision; uniform output.

### D-110 — Mail through Gmail SMTP
Accepted · 2026-10-31
Mail (invitations, password reset, notifications) goes through the free Gmail SMTP: `smtp.gmail.com:587`, STARTTLS, from a dedicated Google account with 2-step verification and an App Password. Gmail's daily sending limit applies, so mail stays transactional only, and the sender is that Gmail address. Changing to another SMTP provider later is a `.env` change.
Reason: owner's decision, to save cost; password-reset requests are rare.

### D-111 — AN competencies in Paket Soal TKA
Accepted · 2026-10-31 · resolves the open part of D-043
The Paket Soal TKA wizard offers the AN literasi membaca competencies (Bahasa Indonesia) and AN numerasi competencies (Matematika) next to the TKA competencies, as in the 2026 official sample sets.
Reason: owner's decision; practice should cover what students meet (D-089).

### D-112 — Reviewer recorded as Tim Validasi SIGRAK
Accepted · 2026-10-31 · refines D-037 (3)
Curriculum datasets record the reviewer as "Tim Validasi SIGRAK", with the review date, instead of individual teacher names and schools.
Reason: owner's decision.

### D-113 — Tim Validasi SIGRAK reviews religion data
Accepted · 2026-10-31 · supersedes D-037 (3) and the same-religion review in D-034
Tim Validasi SIGRAK reviews and signs off the religion and MI curriculum data. A teacher of the same religion or subject is not required. The dataset records "Tim Validasi SIGRAK" and the review date (D-112).
Reason: owner's decision.

### D-114 — File storage: local disk, R2 optional
Accepted · 2026-10-31
Files (letterheads, exports, uploads) are stored on the VPS's local disk. Cloudflare R2 is used only as an optional extra for small files that are rarely read, within the free tier. Backups also go to R2 (D-071).
Reason: owner's decision; R2 is on the free tier.

### D-115 — MI CP source errors are corrected
Accepted · 2026-10-31 · supersedes D-040 for the MI CP
The MI CP outcome text and `arabic_passages` correct the known source errors: خَالِيْ → عَمِّيْ, كَمْ → كَمِ السَّاعَةُ, the repeated "berupa kritik sosial", and the typos madarasah, peyelidikan, kongkrit. `raw_text` keeps the source as printed, and `extraction.source_corrections` lists each change. The Khonghucu "(孔)" in the SD CP stays as printed; the owner confirmed it is fine.
Reason: owner's decision.

### D-116 — AI engine technical choices accepted
Accepted · 2026-10-31 · accepts D-022 items 1, 3, 4
(1) SIGRAK uses its own `AIProvider` interface with a Gemini adapter on Laravel's HTTP client, not the Laravel AI SDK. (3) Generation progress is shown by polling (Inertia partial reload); websockets come only if needed. (4) Education logic lives in `app/Content/`, provider logic in `app/AI/`. Item 2 stays superseded by D-024.
Reason: owner approved.

### D-117 — Default model and free-tier limits
Accepted · 2026-10-31 · within D-023
The default model for generation is Gemini 3.8 Flash (text-out), as listed in AI Studio on 2026-10-31. Its free-tier limits are 5 requests per minute, 250,000 tokens per minute, and 20 requests per day, per Google Cloud project. The model id and limits live in `config/ai.php` (purpose → models, with fallbacks) and are re-checked in AI Studio at build time and when Google changes them. Key selection and cooldowns use these limits.
Reason: owner's choice from AI Studio.

### D-118 — Usage-log retention: one month
Accepted · 2026-10-31 · replaces the 180-day proposal
`ai_usage_logs` detail rows are kept for 1 month and then pruned daily. Monthly aggregate counts are kept for reports.
Reason: owner's decision.

### D-119 — Backup ("cadangan") keys
Accepted · 2026-10-31
A teacher can mark a key as *cadangan*. Backup keys are skipped by round robin and are used only when every normal key is on cooldown, invalid, or has an open circuit. Adding one follows the same consent rule as any extra key (D-023).
Reason: owner's decision.

### D-120 — TKA and exam-site proposals accepted
Accepted · 2026-10-31 · accepts D-041, D-042, D-044, D-045, D-046, D-047, D-054
(1) Results show the category (Baik, Memadai, Kurang) plus the Istimewa predicate for scores ≥ 95, for example "Baik-Istimewa" (D-041). (2) PGK items get partial credit, and the score is labelled as an estimate (D-042). (3) The item rules D-044…D-047 are accepted as written, as practice defaults (D-089). (4) Exam-site login throttling is keyed by exam session and username, not by IP (D-054). D-111 (AN competencies offered) stands.
Reason: owner's review in the P2-03 session.

### D-121 — Exam client font size
Accepted · 2026-10-31
The exam client uses the standard default font size, not a larger one. The A−/A/A+ control stays.
Reason: owner's decision.

### D-122 — Supervisor writes without school membership
Accepted by D-123 · 2026-10-31 · P2-04
A supervision visit row (`supervisions`) carries the visited school's `school_id` and the supervisor's `user_id`. A policy allows the write only while an active `supervisor_assignments` row links that supervisor to that school. *Laporan Kepengawasan* is built on request from the supervisor's visits across assigned schools. Supervisors get no `school_memberships` row and no student-level reads.
Reason: answers the data-model question with the existing tables.

### D-123 — All drafts approved
Accepted · 2026-10-31
The owner approved all ten drafts: product-concept, content-engine, ai-provider-engine, data-model, design-system, religious-education, architecture, server-setup, coding-standards, export-localization. The **Proposed** decisions recorded from them (D-039, D-048…D-053, D-055…D-061, D-063…D-066, D-068, D-077…D-083, D-097…D-104, D-107, D-108, D-122) are accepted with them. The drafts move to `docs/final/` with Indonesian PDFs (task P3-01). `open-questions.md` stays in drafts as a living list. The print font question (D-109 vs. "font cukup 2") is still open.
Reason: owner's decision.

### D-124 — Students only receive finished AI content
Accepted · 2026-10-31 · P2-04 · confirms D-027
AI runs only in teachers' sessions, before an exam or lesson, to prepare questions and material. A TKA package is frozen into `exam_items` before the session. The exam site serves that teacher-checked content and never generates or changes anything with AI while students take the exam. The same holds for downloaded games and *Tayangkan*. This is SIGRAK's answer to Gemini's under-18 clause: students never use or trigger the AI.
Reason: owner's decision.

### D-125 — Two print fonts, chosen by the school
Accepted · 2026-10-31 · supersedes the one-font part of D-109
A school chooses its print font in its print settings: Arial (default) or Times New Roman. Gotenberg renders them as the metric-compatible Liberation Sans and Liberation Serif (D-105). No other print fonts are offered. Arabic always uses Noto Naskh Arabic and math uses KaTeX fonts. `school_print_settings.font` ∈ {`arial`, `times_new_roman`} is kept. F4 stays 215 × 330 mm (D-109).
Reason: owner's decision ("font A").

### D-126 — Kurikulum Berbasis Cinta in the v1 RPM for MI
Accepted · 2026-10-31
For MI schools, the v1 RPM template adds two fields, following SK Dirjen Pendis 6077/2025. *Tema KBC* is one or more of the five Panca Cinta themes. *Materi Insersi* is the values inserted. The AI weaves both into TP, activities, and assessment. The fields appear for every MI subject, because the guide applies to all learning in madrasah, and they do not appear for SD. There is no new subject and no JP change.
Reason: owner's decision.

### D-127 — Daily AI usage counter per key
Accepted · 2026-10-31 · P2-04 · builds on D-117, D-119
Detailed 429 handling is not built yet. Instead, SIGRAK counts each key's AI requests per model per day in Redis, and the count resets at midnight Pacific (14:00 or 15:00 WIB). The limit comes from `config/ai.php` (20 per day for the default model).
- *Kunci AI Saya* and the wizard show what is left today and warn early: use another key (D-023) or wait for the reset.
- A key at its limit is skipped until the reset. Backup keys come last.
- The counter counts requests, because one RPM can take several calls. The UI shows an estimate ("sekitar N RPM lagi hari ini").
- Any 429 that still arrives gets a 60 s cooldown. Reading Google's quota id waits until later.
Reason: owner — a simple early alarm for teachers.
