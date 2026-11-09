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
Reason: owner's choice in the 2026-10-30 interview; matches the legacy app developer notes.

### D-008 — Frontend: Inertia.js + plain Svelte
Accepted · 2026-10-30 · supersedes D-005
`@inertiajs/svelte` with Laravel routing. No SvelteKit.
Reason: owner confirmed the recommendation in D-005.

### D-009 — Product scope: content creation + administration from v1
Accepted · 2026-10-30
SIGRAK (working name) is an AI-assisted platform for making teaching content (Guided Generator, Template Engine, AI Provider Engine) and keeps the legacy app's school administration features. Both ship in v1. See `docs/final/product-concept.md`.
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
Each teacher adds one or more Gemini keys from Google projects they own. Round robin + failover run only within that teacher's keys. No school or platform key. Principals need no key. AI cost is never a SIGRAK cost. Supersedes the optional school fallback key in the legacy app notes.
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
Accepted · 2026-10-30 · amended by D-289 · observation and rubric entry by D-349
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
Reason: owner kept the legacy app rule.

### D-020 — Fresh build, curriculum-only import
Accepted · 2026-10-30
No Firebase migration of accounts, RPM, questions, or scores. Only curriculum JSON (KM-SD-2026-R2) is imported. Teachers are re-invited. Supersedes the staged migration plan in the legacy app notes.
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
Reason: see `docs/final/ai-provider-engine.md`. Awaiting owner review.

### D-023 — AI keys: Gemini free tier only
Accepted · 2026-10-30 · refines D-012 · console adapters added by D-326 (teacher keys unchanged)
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
Accepted · 2026-10-30 · refines D-028 · amended by D-280 (PDF merge for Berkas) and D-284 (kop otomatis)
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
Superseded by D-127 · 2026-10-31 · supersedes D-040 for the MI CP
The MI CP outcome text and `arabic_passages` correct the known source errors: خَالِيْ → عَمِّيْ, كَمْ → كَمِ السَّاعَةُ, the repeated "berupa kritik sosial", and the typos madarasah, peyelidikan, kongkrit. `raw_text` keeps the source as printed, and `extraction.source_corrections` lists each change. The Khonghucu "(孔)" in the SD CP stays as printed; the owner confirmed it is fine.
Reason: owner's decision.

### D-116 — AI engine technical choices accepted
Accepted · 2026-10-31 · accepts D-022 items 1, 3, 4
(1) SIGRAK uses its own `AIProvider` interface with a Gemini adapter on Laravel's HTTP client, not the Laravel AI SDK. (3) Generation progress is shown by polling (Inertia partial reload); websockets come only if needed. (4) Education logic lives in `app/Content/`, provider logic in `app/AI/`. Item 2 stays superseded by D-024.
Reason: owner approved.

### D-117 — Default model and free-tier limits
Accepted · 2026-10-31 · within D-023 · amended by D-315 (model per purpose set in the console)
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

### D-127 — MI CP shows the printed text, with reviewer notes
Accepted · 2026-10-31 · supersedes D-115 · P2-02
The MI CP outcome text and `arabic_passages` show the decree as printed again, including خَالِيْ, كَمْ السَّاعَةُ, the repeated "berupa kritik sosial", and madarasah, peyelidikan, kongkrit. Each such row carries `review_notes[]` (9 rows) for the reviewers. The notes are never shown to teachers or students. A correction needs the owner's approval. The Khonghucu "(孔)" decision in D-115 is unchanged.
Reason: owner's decision.

### D-128 — Daily AI usage counter per key
Accepted · 2026-10-31 · P2-04 · builds on D-117, D-119
Detailed 429 handling is not built yet. Instead, SIGRAK counts each key's AI requests per model per day in Redis, and the count resets at midnight Pacific (14:00 or 15:00 WIB). The limit comes from `config/ai.php` (20 per day for the default model).
- *Kunci AI Saya* and the wizard show what is left today and warn early: use another key (D-023) or wait for the reset.
- A key at its limit is skipped until the reset. Backup keys come last.
- The counter counts requests, because one RPM can take several calls. The UI shows an estimate ("sekitar N RPM lagi hari ini").
- Any 429 that still arrives gets a 60 s cooldown. Reading Google's quota id waits until later.
Reason: owner — a simple early alarm for teachers.

### D-129 — Alert channels and recipient
Accepted · 2026-10-31 · P2-08
Server alerts go by Telegram and email to the owner only. Urgent alerts may be sent at night; minor alerts go by email only.
Reason: owner's decision.

### D-130 — Healthchecks.io as the main alerting service
Accepted by D-143 · 2026-10-31 · P2-08
Healthchecks.io (free Hobbyist) works as a push-based dead man's switch, which suits a server without a public IP. 13 checks: urgent ones go to Telegram + email, minor ones to email only. Channel by severity replaces quiet hours.
Reason: needs only outbound HTTPS (D-088).

### D-131 — One health script, run by cron
Accepted by D-143 · 2026-10-31 · P2-08
`/usr/local/sbin/sigrak-health` runs every minute, every 15 minutes, and daily. A check must fail twice in a row before it reports. No monitoring agent (Netdata, Prometheus) on the VPS for now.
Reason: small footprint on one VPS.

### D-132 — External uptime checks
Accepted by D-143 · 2026-10-31 · P2-08
UptimeRobot (free) runs keyword checks of `app.` and `ujian.` `/up` and alerts by email.
Reason: catches outages the server cannot report itself.

### D-133 — Cloudflare tunnel notifications
Accepted by D-143 · 2026-10-31 · P2-08
Cloudflare Tunnel Health and Tunnel Creation/Deletion notifications go out by email.
Reason: first signal when the tunnel drops (D-088).

### D-134 — Laravel `/up` route enabled
Accepted by D-143 · 2026-10-31 · P2-08 · exception to D-010
`/up` is the only page reachable without login, and it shows no data.
Reason: external checks need one public endpoint.

### D-135 — Monitoring thresholds
Accepted by D-143 · 2026-10-31 · P2-08
- Urgent: disk ≥ 90 %, RAM available < 1.5 GB, an OOM kill, or Redis ≥ 90 % of `maxmemory`.
- Minor: disk ≥ 80 %, swap ≥ 50 %, load15 ≥ 8, MySQL connections ≥ 160, Redis ≥ 70 %, ≥ 5 failed jobs in 15 min, or pending security updates or a required reboot.
Reason: sized for 8 vCPU / 16 GB (D-087).

### D-136 — Backup heartbeat
Accepted by D-143 · 2026-10-31 · P2-08
The backup script pings `backup/start`, then `backup` on success or `backup/fail` on error, so a missed backup alerts. The change lands in `server-setup.md` section 12.
Reason: a silent backup failure is the worst case.

### D-137 — App-side monitoring for Phase 3
Accepted by D-143 · 2026-10-31 · P2-08
Horizon long-wait mail notifications (`waits`: `ai` 600 s, `exports` 120 s, `default` 60 s) and a scheduler heartbeat with `pingOnSuccess`.
Reason: queue and scheduler stalls are invisible from outside.

### D-138 — Staging alerts by email only
Accepted by D-143 · 2026-10-31 · P2-08
Staging has one combined check that alerts by email only.
Reason: staging must not page anyone at night.

### D-139 — Curriculum version for SD religion TP/ATP
Accepted by D-143 · 2026-10-31 · P2-09
New curriculum version `TPATP-PABP-SD-2026-R1` holds the TP/ATP for the five non-Islam SD religion subjects, with subject codes PAKR, PAKT, PAH, PAB, PAKH. It stays `draft` until Tim Validasi SIGRAK reviews it (D-113).
Reason: PAI stays in KM-SD-2026-R2.

### D-140 — Religion TP/ATP in the data-model shape
Accepted by D-143 · 2026-10-31 · P2-09
The data is stored in the data model's English-keyed shape (`topics`, `learning_objectives`, `focus_items`, `focus_item_objectives`), not in the legacy row shape. The importer accepts both.
Reason: matches the target schema directly.

### D-141 — Religion allocation follows PAI
Accepted by D-143 · 2026-10-31 · P2-09 · follows D-034
3 JP per week, 5 topics per semester (54/54 JP, 54/42 in grade 6), and one fokus per week of 3 JP.
Reason: same allocation as PAI.

### D-142 — KBC fields are optional and pre-filled
Accepted · 2026-11-01 · refines D-126
*Tema KBC* and *Materi Insersi* in the MI RPM wizard are optional. The AI pre-fills a suggestion from the CP and the material: one or more Panca Cinta themes plus the values to insert. The teacher may change or clear both fields, and an RPM can be generated without them.
Reason: owner's decision.

### D-143 — Monitoring and religion TP/ATP decisions accepted
Accepted · 2026-11-01 · accepts D-130…D-141
The owner accepted the P2-08 monitoring design (D-130…D-138) and the P2-09 religion TP/ATP data decisions (D-139…D-141) as written.
Reason: owner's decision.

### D-144 — Healthchecks.io free plan, one recipient; e-Rapor format later
Accepted · 2026-11-01
(1) Monitoring uses the free Healthchecks.io Hobbyist plan. Alerts go to the owner only, with no second recipient. (2) The e-Rapor import format is deferred. For now, report-card data is exported in SIGRAK's own Excel layout. The owner will design a dedicated e-Rapor export later.
Reason: owner's decision.

### D-145 — Curriculum version for MI Kemenag TP/ATP
Accepted by D-147 · 2026-11-01 · P2-10
New curriculum version `TPATP-PAI-BA-MI-2026-R1` holds the TP/ATP for the five MI Kemenag subjects, with subject codes QH, AA, FIK, SKI, BAR. It uses the same shape as D-140 and stays `draft` until Tim Validasi SIGRAK reviews it (D-113).
Reason: one version per source CP set.

### D-146 — MI topics count intrakurikuler JP only
Accepted by D-147 · 2026-11-01 · P2-10 · follows D-038
MI topics use the intrakurikuler JP from KMA 1503/2025: 2 JP per week, 4 topics per semester (36/36 JP, 36/28 in grade 6), and one fokus per week. Kokurikuler JP stay with the Kokurikuler module.
Reason: RPM scheduling counts intrakurikuler JP only.

### D-147 — MI TP/ATP decisions accepted
Accepted · 2026-11-01 · accepts D-145, D-146
The owner accepted the P2-10 decisions as written: version `TPATP-PAI-BA-MI-2026-R1` with codes QH, AA, FIK, SKI, BAR, and intrakurikuler-only JP for MI topics.
Reason: owner's decision.

### D-148 — Only `main` stays; commits authored by the owner
Accepted · 2026-11-01 · extends D-001
`main` is the only branch that stays: a task branch is deleted as soon as its work is merged into `main`. Every commit is authored and committed as `Arumi Studios <arumiforge@gmail.com>`, never as Claude; `.claude/hooks/session-start.sh` sets this identity each session. On 2026-11-01 the history of `main` was rewritten to replace the earlier Claude author and committer on all commits, and every other branch was deleted.
Reason: owner's requirement — Claude must not appear as a contributor to the repository.

### D-149 — Indonesian URLs
Accepted · 2026-11-01 · narrows D-002 for routes
URL paths people see are plain, friendly Indonesian in kebab-case: `/masuk`, `/lupa-kata-sandi`, `/dokumen/{document}/ubah`, `/kunci-ai/{credential}`. Route names, route parameters, controllers, and all other identifiers stay English (`login`, `documents.edit`). Resource verbs are `buat` and `ubah`. Technical paths nobody reads (`/up`, webhooks, built assets) may stay English. Replaces the English URL rule in `docs/final/coding-standards.md` §3.
Reason: owner's requirement — users read the address bar, so it should be in their language.

### D-150 — Imports read back Indonesian headings and values
Accepted · 2026-11-01 · refines XP-25 in `docs/final/export-localization.md`
An import accepts exactly what its export writes. Indonesian headings are matched without regard to letter case, extra spaces, a trailing `*` or `:`, or column order. Old heading labels stay valid forever as aliases (`export.column_aliases.*`). Indonesian cell values are read back: enum labels ("Hadir" → `present`), "Ya"/"Tidak", `dd/mm/yyyy` dates, and comma decimals. The database stays English. Files for other systems (e-Rapor) still follow that system's spec (XP-24).
Reason: owner's requirement — teachers only ever see friendly Indonesian headings, and a filled template must keep importing after a label changes.

### D-151 — "Templat" in UI copy
Accepted · 2026-11-01
UI copy uses the KBBI spelling "Templat", never "template". Code identifiers stay English (`template_key`, `templates.php`).
Reason: owner's decision; consistent with the other Indonesian terms ("Unduh", "Kunci AI").

### D-152 — Kabar notice when a generation finishes
Accepted · 2026-11-01 · accepts the proposal in `ai-provider-engine.md`
When an AI generation finishes, the teacher gets a database notification in *Kabar*, in addition to the live status on the generation page.
Reason: owner's decision; teachers often leave the page before a long generation is done.

### D-153 — Keep the last 5 document revisions
Accepted · 2026-11-01 · replaces the proposed "last 10" · amended by D-282
`document_revisions` keeps the last 5 revisions per document; the daily prune job removes older ones.
Reason: owner's decision.

### D-154 — Game runtime palette by a designer
Accepted · 2026-11-01
The game runtime palette is a designer task, briefed together with the logo. Until then, games use the design-system tokens.
Reason: owner's decision.

### D-155 — Special-education religion CP in scope
Accepted · 2026-11-01 · changes the v1 scope in `religious-education.md`
Inclusive schools need the special-education CP (Lampiran II of BKPDM 020/2026). It is transcribed for the six religions in the same shape as the SD religion CP and reviewed by Tim Validasi SIGRAK (D-113).
Reason: owner's decision; target schools include inclusive schools.

### D-156 — Indonesian translations of final docs are committed
Accepted · 2026-11-01 · changes the P3-02 docs pipeline
The Indonesian translation of each final doc lives in `docs/pdf/src/<name>.md` and is committed. When a final doc changes, only the changed lines are translated again, then the PDF is rebuilt. `docs/.build/` keeps only generated HTML and stays git-ignored.
Reason: owner's decision; re-translating a whole document for a one-line change was slow.

### D-157 — Plans and prices
Accepted · 2026-11-01 · replaces the tier table in `product-concept.md`; D-025 still applies
Only teachers and schools pay; students and parents are never part of any payment. Prices are per academic year:
- **Uji coba:** 2 weeks with every feature. Afterwards an unpaid account can only view and download its data. There is no permanent free plan and no watermark.
- **Sigrak Guru:** Rp60.000; Rp50.000 when the teacher's school has Sigrak Sekolah.
- **Sigrak Sekolah:** Rp100.000 for 2 accounts, principal and operator, with their portals. Teachers buy Sigrak Guru themselves; a teacher's time no longer moves to the school.
- **Sigrak Binaan:** not sold; the supervisor portal opens free once 5 or more supervised schools have Sigrak Sekolah.
Reason: owner's decision.

### D-158 — Add-on features
Accepted · 2026-11-01
Features outside the core are add-ons at Rp50.000 per year each, switched on by a school that has Sigrak Sekolah: Simulasi TKA, Buku Induk and Dapodik sync (import of the Dapodik Excel export: students, rombel, ekstrakurikuler, staff gaji berkala and kepangkatan), Keuangan and Inventaris kelas, Buku Sekolah, Game kelas (Quiz Battle, Tayangkan, Unduh game), and Rapor. More may follow.
Reason: owner's decision.

### D-159 — Game kelas trial for teachers
Accepted · 2026-11-01 · exception to D-158
A teacher may try Game kelas (Quiz Battle, Tayangkan, Unduh game) for 7 days, once per teacher, even when the school has not switched the add-on on. Every other add-on needs the school. After the 2-week trial, an unpaid teacher can still only view and download (D-157).
Reason: owner's decision; lets teachers see games in class before asking the school to buy the add-on.

### D-160 — Teacher trial is 1 week
Accepted · 2026-11-01 · supersedes the trial length in D-157
The teacher trial lasts 1 week (7 days) with every feature, not 2 weeks. The Game kelas trial (D-159) stays 7 days and can never be repeated.
Reason: owner's decision.

### D-161 — Buku Induk and Dapodik sync held as upcoming
Accepted · 2026-11-01 · narrows D-158 for v1
Buku Induk, Dapodik sync, KGB and kepangkatan history, and ekstrakurikuler records are not in v1. The app may show them as upcoming. The design draft `docs/drafts/buku-induk-dapodik.md` (P4-01) is kept on hold, with its open questions, until the owner restarts it.
Reason: owner's decision.

### D-162 — v1 build order
Accepted · 2026-11-01 · confirms the slice order in `product-concept.md`
The v1 slices are built in the proposed order: foundation, content core, more templates, TKA, Di Kelas, Asesmen, portals, business. Before slice 1, the project is scaffolded and the server is set up (Phase 5). TKA no longer targets the January–March 2027 practice season; it targets the 2028 TKA season.
Reason: owner's decision.

### D-163 — Ziggy as the typed route helper
Accepted · 2026-11-01 · P5-01 · resolves D-081
The frontend builds URLs with Ziggy (`route()` from `ziggy-js`, strict route-name types from `php artisan ziggy:generate --types-only`). Each root view sends only its own route group: `@routes('app')` or `@routes('exam')` (`config/ziggy.php`), so staff and vendor routes never reach a browser.
Reason: Wayfinder writes the hostname into the built JavaScript (`//app.sigrak.test`), so one build could not serve production and staging (`staging-app.`). Ziggy reads the hosts at request time.

### D-164 — Hostnames come from env keys
Accepted · 2026-11-01 · P5-01 · refines D-048
`APP_HOST`, `EXAM_HOST`, and `ADMIN_HOST` set the three hostnames (`config('app.hosts.*')`). Horizon, Telescope, and the Filament panel sit at the root of the admin host; the panel's sign-in page is `/masuk`.
Reason: production and staging use different hostnames on one codebase (D-095).

### D-165 — App time is WIB
Accepted · 2026-11-02 · P5-01 · supersedes D-082
`config/app.php` timezone is `Asia/Jakarta` (WIB) and the MySQL connection's `timezone` is `+07:00`. The app reads, stores, and shows times in WIB. The server OS and PHP CLI stay on UTC; Laravel sets its own timezone.
Reason: owner's decision.

### D-166 — Local development runs in WSL2
Accepted · 2026-11-02 · owner's choice
Developers on Windows run the app in WSL2 with Ubuntu 24.04, not Laragon. PHP, MySQL, Redis, and Node live inside Ubuntu, and Gotenberg runs in Docker Desktop with WSL integration. The three local hostnames are `app.`, `ujian.`, and `admin.sigrak.localhost`. Steps: `docs/final/local-setup-wsl2.md`.
Reason: Horizon needs `pcntl`/`posix`, which Windows PHP lacks. Ubuntu 24.04 matches the VPS (D-086), so local behaviour stays close to staging and production.

### D-167 — Staff console access by staff role
Accepted · 2026-11-03 · P5-03a · role catalogue replaced by D-339
`admin` manages everything in the console, including staff accounts. `support` manages regions and schools. `content` gets the template registry when it is built and sees nothing else yet. The first admin on a server is made with `php artisan staff:create <email>`.
Reason: the data model names the three roles but not their reach; this is the smallest split that keeps staff accounts with admins only.

### D-168 — Supervisor assignments have no school scope
Accepted · 2026-11-03 · P5-03a · exception to TEN-1
`supervisor_assignments` has `school_id` but no `BelongsToSchool` scope. A supervisor's rows are read by `user_id` across schools, and only the staff console manages them. `school_memberships` keeps the scope; the one place that reads them before a school is chosen (`ResolveSchoolContext`) uses `withoutSchoolScope()` limited to the user's own rows.
Reason: scoping the supervisor's own access list by a "current school" would hide the schools they supervise.

### D-169 — School directory from the Kemendikdasmen reference site
Accepted · 2026-11-03 · P5-03f · owner's choice
A separate reference table, `school_directory_entries` (NPSN, name, level, address, village, district, region), holds SD and MI from referensi.data.kemendikdasmen.go.id. It starts with Kabupaten Kudus only (`docs/data/schools/kudus.json`, 570 schools). The data is fetched by hand with `scripts/school-directory/fetch.cjs` and loaded with `php artisan school-directory:import <file>`, which is idempotent by NPSN. When staff type an NPSN in the console's school form, empty fields are filled from it. Directory rows are not tenants; a school becomes a tenant only when staff create it.
Reason: there is no official public API (2026-11-03 search), and filling a school from its NPSN saves typing and typos. Kudus is the pilot region (D-018); other regencies can be added with the same script.

### D-170 — Jepara and Pati in the directory; school map for marketing
Accepted · 2026-11-03 · P5-03f · owner's choice · extends D-169
The school directory also holds Kabupaten Jepara (772 schools) and Kabupaten Pati (825). The staff console has a read-only page, Sebaran sekolah (`/sebaran-sekolah`), for admin and support. It lists the directory grouped by district (or regency, or level), shows the number of schools and of customers per group, and filters customers or not-yet-customers. For now a customer is a directory school whose NPSN matches a school created in the console; it moves to "has an active subscription" when billing exists (P5-10).
The page also has: a switch to one summary row per group; an Excel download of the rows as filtered on screen (`app/Exports/SchoolDirectoryExport`, Laravel Excel 4); and a link to Peta sekolah (`/peta-sekolah`), a Leaflet 1.9.4 map (bundled, D-033) of schools with a location from the reference site, coloured by customer or not. Map tiles come from OpenStreetMap in the staff member's browser; staff only, so no school-user traffic reaches OSM. The Sekolah list shows whether each school matches the directory (Cocok, NPSN kosong, Tidak ada di data referensi), and Sebaran sekolah says how many customers lack an NPSN and so are not counted.
Reason: the team wants to see where SIGRAK is already used around Kudus and which schools to approach next.

### D-171 — Game kelas becomes the IFP learning mode
Accepted · 2026-11-04 · P4-02 · owner's choice · extends D-011, D-158
The Game kelas add-on keeps its name. It grows from Quiz Battle, Tayangkan, and Unduh game into a fullscreen IFP mode that plays a lesson sequence from an RPM meeting (goal, prior-knowledge check, manipulative, team practice, exit ticket, reflection). Design: `docs/final/ifp-classroom.md`.
Reason: free IFP game sites are catalogues with no link to TP, RPM, or results; tying play to the lesson is SIGRAK's difference.

### D-172 — Class-level results from Game kelas are kept
Accepted · 2026-11-04 · P4-02 · owner's choice · refines D-011
Every Game kelas session keeps a summary per item, per team, and per TP (correct share, most chosen wrong option, time), with no student data. It feeds "Perlu diulang", AI follow-up suggestions (from TP and wrong-answer pattern only), the Jurnal entry, and Asesmen as formative evidence. The teacher can delete it.
Reason: owner's choice; the teacher needs evidence after the screen closes, and item-level data holds no personal data.

### D-173 — Kode Tayang and phone remote
Accepted · 2026-11-04 · P4-02 · owner's choice
On the shared IFP the teacher does not type a password. The IFP shows a 6-digit code or QR, the teacher confirms it on their phone, and the IFP gets a present-only session (today's Game kelas items only; no students, documents, or settings) that ends from the phone or after a time limit. The paired phone is the remote and the only place the answer key shows. Hostname or path is still open (D-164).
Reason: one panel is shared by several classes; a teacher session left open on it is a risk.

### D-174 — Kartu Jawab, last step
Accepted · 2026-11-04 · P4-02 · owner's choice · reuses the Pindai LJK camera code (D-286)
Per-student responses come only from Kartu Jawab: printed numbered cards with sides A–D, scanned with the teacher's phone camera and mapped to the roster on the phone. Names never show on the IFP. It is built after the class-level loop (D-172) is in use.
Reason: owner's choice; most SD/MI classes have no student devices.

### D-175 — Manipulatives and classroom helpers in Sigrak Guru
Accepted · 2026-11-04 · P4-02 · owner's choice · exception to D-158
IFP manipulatives (blok puluhan, garis bilangan, pita pecahan, jam analog, uang rupiah, bangun datar, diagram data, peta, huruf, hijaiyah, aksara Jawa) and classroom helpers (timer, group maker, turn rotation, name picker, blank board) use no AI and are part of Sigrak Guru. Game templates, the playlist, and the learning loop stay in the Game kelas add-on.
Reason: shows the IFP value to every paying teacher, which helps sell the add-on to schools.

### D-176 — Game template roadmap by learning function
Accepted · 2026-11-04 · P4-02 · owner's choice · amends D-014
The later game list "Drag & Drop, Word Puzzle, Maze, Flashcard" is replaced by learning-function templates: Lomba Tim, Pilah & Kelompokkan, Pasangkan, Urutkan, Papan Petak, Kotak Kejutan, Tiket Keluar, Pemanasan TKA. After Quiz Battle is IFP-ready, the first three are Lomba Tim, Pilah & Kelompokkan, and Pasangkan.
Reason: every template must state what it teaches, not only how it plays.

### D-177 — First Game kelas content packs
Accepted · 2026-11-04 · P4-02 · owner's choice
The first content packs are MI (Al-Qur'an Hadis and Bahasa Arab; Fikih, Akidah Akhlak, and SKI) and Bahasa Jawa with aksara Jawa. The six SD religion packs follow. All are reviewed by Tim Validasi SIGRAK (D-113).
Reason: the benchmarks barely cover MI and local content, and the pilot region is Kudus, Jepara, and Pati.

### D-178 — Game kelas usage report for principal and supervisor
Accepted · 2026-11-04 · P4-02 · owner's choice
The principal and supervisor portals show how often Game kelas runs per class and subject and which TPs were practised. No student data.
Reason: supports supervision and the school's reporting of IFP use.

### D-179 — Reference IFP is the Kemendikdasmen unit
Accepted · 2026-11-04 · P4-02 · owner's choice
Game kelas targets the 75-inch Android 4K multi-touch panel from the Kemendikdasmen digitalisation programme, using its built-in browser. Its Android and WebView versions are checked on a real unit in a pilot school before building.
Reason: almost every SD/MI got this unit in 2025; the exact software version is not published.

### D-180 — Camera and movement activities only when tied to a TP
Accepted · 2026-11-04 · P4-02 · owner's choice
Camera-based and body-movement activities come last and only where the movement is part of the learning and tied to a TP (for example PJOK).
Reason: camera permission and heavy processing are costly; movement as decoration adds no learning.

### D-181 — Weekly offline bundle with the Game kelas core
Accepted · 2026-11-04 · P4-02 · owner's choice · extends D-011
A teacher can download all Game kelas activities for a week as one offline bundle for a USB drive. It ships with the Game kelas core (playlist step), with the same rules as Unduh game: teaching content only, no network calls.
Reason: school connections drop, and the IFP can run without internet.

### D-182 — Invitation links and who invites whom
Accepted · 2026-11-07 · P5-03b · exception to TEN-2
The `invitations` table stores only a SHA-256 hash of the mailed token. A link is valid for 7 days. Sending an invitation again to the same school, email, and role renews the open one with a new token instead of adding a row. In the school app, only the operator invites, and only teachers. Tim SIGRAK invites any role from the staff console, usually the first operator or principal. The data model has no staff inviter, so `invited_by` (users) is nullable and `invited_by_staff_id` (staff_users) records console invitations. Opening a link happens before there is a session or school, so `FindInvitation` looks the invitation up with `withoutSchoolScope()`. The token, compared in constant time, is what authorizes it. An unknown, used, or expired link shows one neutral page. `school_memberships` gets a `public_id`, so the role switch never puts a bigint id in props (DB-11).
Reason: no default passwords (D-010), no stored usable tokens, and a link that reveals nothing about other schools.

### D-183 — School account password and email rules
Accepted · 2026-11-07 · P5-03b
School account passwords need at least 8 characters with letters and numbers. (Staff keep 12 characters, from P5-03a.) Failed sign-ins are counted per email and IP: after 5 failures there is a 1-minute wait. Loose per-IP limits cover sign-in (30/min), reset links (5/min), and invitation acceptance (10/min), because a whole school can share one IP. A reset request gets the same answer whether or not the email exists. The email shown on Akun is read-only, and changing it goes through Tim SIGRAK for now. Accepting an invitation marks the email as verified.
Reason: the email is both the sign-in name and the invitation address, so changing it needs a verified flow that v1 does not have yet.

### D-184 — Supervisor accounts come with the supervisor portal
Accepted · 2026-11-07 · P5-03b · owner's choice
How pengawas/korwil accounts are created is decided and built with the supervisor portal (P5-09). Until then, Penugasan pengawas in the staff console assigns existing user accounts only.
Reason: invitations always name a school role; a supervisor has none, and the portal defines what the account needs.

### D-203 — Official 2025 documents as SD/MI data in `docs/data/regulations/`
Accepted · 2026-11-08 · P4-03 · owner's choice
The eight official documents supplied in `docs/dokumen-resmi/` are moved unchanged to `docs/sources/` under descriptive names, and listed in `docs/sources/README.md`. The JSON files keep the original path in `source.received_as`. Their SD/MI parts (SD, MI, Fase A–C, plus rules shared by all levels) are stored as one JSON file per document in `docs/data/regulations/`. Each file has `status: draft`, source metadata (title, number, year, file, SHA-256), and PDF page references on every item. Tim Validasi SIGRAK checks them before import (D-113). What the final docs need is listed in `docs/drafts/dokumen-resmi-sd-mi.md`. The owner approves those changes before anything in `docs/final/` changes.
Reason: the same draft, source-quoted, page-referenced shape as `docs/data/curriculum/`, kept apart because these are rules and guides rather than CP or allocation rows.

### D-204 — Optional SD content: Bahasa Inggris and IPAS in grades 1–2, Tata Boga
Accepted · 2026-11-08 · P4-03 · owner's choice
Permendikdasmen 13/2025 has no Bahasa Inggris in grades 1–2. SIGRAK keeps the KM-SD-2026-R2 Bahasa Inggris topics for grades 1–2 as content a school can switch on, off by default. IPAS in grades 1–2 (also not in the structure; IPAS starts in grade 3) is optional in the same way. When a school switches either on, it is not a required rapor subject: it carries no rapor grade unless the school chooses to report it. KM-SD-2026-R2 has no IPAS topics for grades 1–2, so they still have to be written. Keterampilan Tata Boga becomes a school-optional muatan lokal. The SD muatan lokal cap of 72 JP per year (64 in grade 6) still applies per school. MI keeps 72–216 JP (KMA 1503/2025).
Reason: schools that teach these keep using them, without making them look compulsory. Resolves C1 and C2 in `docs/drafts/dokumen-resmi-sd-mi.md`.

### D-205 — Rapor v1: ekstrakurikuler row; descriptions from formative highs and lows
Accepted · 2026-11-08 · P4-03 · owner's choice · narrows D-161
(1) The v1 rapor shows ekstrakurikuler per student (activity and qualitative description). Fuller ekstrakurikuler records and the Dapodik sync stay out of v1 (D-161). (2) Formative scores never appear as rapor grades. The competency description per subject is drafted from the highest and lowest TP achievement in the formative results.
Reason: Panduan Pembelajaran dan Asesmen lists ekstrakurikuler among the rapor minimum and asks for the highest and lowest achievement in the description; formative stays out of grades.

### D-206 — Written tests in Fase A are optional
Accepted · 2026-11-08 · P4-03 · owner's choice
For grades 1–2, Bank Soal still offers written items as an option. The guide recommends observation and performance in Fase A, but SIGRAK does not block written tests.
Reason: the guide does not recommend written tests in Fase A, but some schools still use them.

### D-207 — 7 KAIH label "Tidur Cepat"
Accepted · 2026-11-08 · P4-03 · owner's choice
The seventh habit of Gerakan 7 KAIH is labelled "Tidur Cepat". "Tidur tepat waktu" is used only as its explanation.
Reason: Panduan Kokurikuler uses both. The label follows the main text.

### D-208 — KKA runs through kokurikuler
Accepted · 2026-11-08 · P4-03 · owner's choice (confirmed 2026-11-08)
In SIGRAK, Koding dan Kecerdasan Artifisial is delivered through the kokurikuler programme, one of the options in Kepmendikdasmen 127/P/2025. It is not set up as an intrakurikuler subject with its own allocation. The Koding dan KA rows for grades 5–6 in KM-SD-2026-R2 move to kokurikuler as well.
Reason: KKA is an elective with no CP decree yet. Kokurikuler lets a school offer it without a fixed weekly slot.

### D-209 — KSP: free format, with a starter template
Accepted · 2026-11-08 · P4-03 · owner's choice
The KSP format is free (Panduan KSP), so SIGRAK provides a starter KSP template that each school adapts. The template follows the guide's components and five-step cycle, and nothing in it is mandatory.
Reason: gives schools a head start without imposing a uniform format.

### D-210 — The product follows the 2025–2026 rules
Accepted · 2026-11-08 · P4-04 · owner's choice
Every new rule found in P4-04 (`docs/drafts/administration-features.md` §1) is applied to SIGRAK: the rapor has the 12 minimum components of Panduan Pembelajaran dan Asesmen 2025; kokurikuler is assessed on the 8 dimensi profil lulusan (Permendikdasmen 10/2025, 13/2025); ekstrakurikuler with Pramuka or another kepanduan is offered (13/2025 Pasal 22); teacher workload counts JTM under Permendikdasmen 11/2025 and KMA 736/2026; class agreements and positive discipline follow Permendikdasmen 6/2026; teachers reflect at least once a semester; transfers follow Permendikdasmen 3/2025; the UI term for students follows Permendikdasmen 1/2026 ("murid"). KKA stays inside kokurikuler (D-208). The final docs and the glossary are updated in a follow-up task.
Reason: the owner wants the app aligned with the current rules, which every SD/MI is checked against.

### D-211 — New administration features are core; plan mapping set in the console
Accepted · 2026-11-08 · P4-04 · owner's choice · changes D-158
Every feature accepted in P4-04 starts as a core feature. Which features are core and which are add-ons is not fixed in code: Tim SIGRAK sets it per feature in the internal console (admin role), and the app reads that mapping. The D-158 add-on list becomes the starting configuration, changeable the same way.
Reason: the owner wants to decide packaging later, from real use, without a release.

### D-212 — Wali kelas JTM default: 2 for SD, 6 for MI
Accepted · 2026-11-08 · P4-04 · owner's choice
The workload calculation counts wali kelas as 2 JTM in SD (Kepmendikdasmen 221/P/2025) and 6 JTM in MI (KMA 736/2026) by default. A school can change these values and the other extra-duty values.
Reason: the two ministries differ, and regional practice varies.

### D-213 — AI drafts the catatan wali kelas
Accepted · 2026-11-08 · P4-04 · owner's choice
The rapor's catatan wali kelas may be drafted by AI from attendance counts, note types, and per-TP results, under the existing rule that no student name or personal data goes into a prompt (`PromptDataGuard`, D-036). The homeroom teacher edits and approves the draft.
Reason: saves the homeroom teacher time on 20–30 notes per semester, without exposing student data.

### D-214 — Teacher assessment and class features accepted
Accepted · 2026-11-08 · P4-04 · owner's choice
In scope: full 12-component rapor with a Fase A "without numbers" option (H1), kokurikuler assessment per dimensi profil lulusan (A5), remedial and enrichment from per-TP analysis (A1), KKTP per TP (A3), start-of-year diagnostic assessment (A2), semester teacher reflection pre-filled from journals and scores (A4), 7 KAIH tracker with a printable home sheet (A6), item analysis (A7), class agreement and positive-discipline notes (A8), student work portfolio with photos (A10), and effective weeks printed with Prota/Promes (A11).
Reason: these extend the CP → RPM → jurnal → asesmen → rapor chain no competitor covers.

### D-215 — Homeroom features accepted; parent messages as plain WhatsApp links
Accepted · 2026-11-08 · P4-04 · owner's choice
In scope: early absence warning (H2), kenaikan kelas meeting sheet and berita acara (H3), grade 6 graduation with SKL and ijazah data (H4), home visits and light health data (H7, H8), and messages to parents (H6). Parent messages are ready text opened in the teacher's own WhatsApp through a plain `wa.me` link: no WhatsApp API, no links into SIGRAK, and a contact log. A fuller parent channel is later. Health data is sensitive: never sent to AI, visible to the class's teachers, operator, and principal only.
Reason: owner's choice; keeps D-010 (no parent accounts, no public links).

### D-216 — Ekstrakurikuler and Pramuka early in v1
Accepted · 2026-11-08 · P4-04 · owner's choice · narrows D-161, extends D-205
Ekstrakurikuler records (activities, pembina, members, attendance, rapor description), with Pramuka offered by default, are built early in v1 rather than held with Buku Induk. The Dapodik import of ekstrakurikuler members stays with P4-01 (D-161).
Reason: Permendikdasmen 13/2025 makes kepanduan mandatory to offer, and the rapor needs it.

### D-217 — Principal, operator, and supervisor features
Accepted · 2026-11-08 · P4-04 · owner's choice
In scope: supervisi akademik for Pembelajaran Mendalam (K2), bukti dukung kinerja download for Ruang GTK (K3), pembagian tugas with JTM and an SK (K4), budaya sekolah aman with tata tertib, SOP, and an internal incident log but no public channel (K8), complete student transfers with the 14-working-day Dapodik reminder (O2), school inventory (O4), cross-school recap (P2) and Pembelajaran Mendalam mentoring for supervisors (P1). Penyusun KSP and evaluasi KSP (K1, K6) come after v1 and build on the starter template (D-209). Not needed now: teacher and staff attendance (K9), since schools already have it from staffing systems.
Reason: owner's choice.

### D-218 — e-Rapor SD and RDM export after the Asesmen slice
Accepted · 2026-11-08 · P4-04 · owner's choice · updates D-144
The export in the import layout of e-Rapor SD and RDM (Rapor Digital Madrasah) is built right after slice 6 (Asesmen), not left as an open later item.
Reason: removes double entry, the strongest argument for MI.

### D-219 — No BOS/RKAS reports; student savings recap in scope
Accepted · 2026-11-08 · P4-04 · owner's choice
Reports tied to BOS or RKAS are not in the app. A student savings (tabungan) ledger and recap per class and per school is in scope, next to Keuangan kelas: integer rupiah, deposits and withdrawals per student, printable recap.
Reason: ARKAS and e-RKAM own BOS reporting (see also D-025); savings are common in SD/MI and kept by hand today.

### D-220 — Upcoming and later features
Accepted · 2026-11-08 · P4-04 · owner's choice
Shown as upcoming (future features): parent portal, SPP, MBG log, perpustakaan. Later candidates, not shown: kartu pelajar, persuratan, monthly school report (laporan bulanan), accreditation readiness.
Reason: owner's choice; the parent portal would reopen D-010 when it starts.

### D-221 — Owner samples as starting formats for SK and supervision
Accepted · 2026-11-08 · P4-04 · owner's choice
The SK pembagian tugas lampiran and the supervision instrument start from samples the owner supplies (one SD's SK lampiran and the owner's instrument). They are first drafts: the school or Tim SIGRAK adjusts them as rules change, so they are kept as editable templates, not hard-coded.
Reason: matches what schools already use, while staying adjustable.

### D-222 — All 18 teacher supervision instruments in one go
Accepted · 2026-11-08 · P4-12 · owner's choice
The supervision module ships all 18 instruments of `docs/drafts/teacher-supervision-instruments.md` (S-00…S-17, with the PJOK, religion/MI, Bahasa Inggris, and kelas rangkap variants of S-08) together, as editable templates (D-221). The MI version is a first draft and gets a revised version later.
Reason: owner's choice; schools are supervised on the whole set.

### D-223 — Supervision scales and bands
Accepted · 2026-11-08 · P4-12 · owner's choice
Classroom observation (S-08 and variants) uses the 3 Ruang GTK levels: Belum dilakukan, Dilakukan tetapi belum efektif, Dilakukan dan efektif (Perdirjen GTK 7607/2023). Document reviews (S-02…S-06, S-14) use 1–4 with a short descriptor per item. Checklists (S-01, S-15) keep 0/1/2 from the owner's sample. Final value bands follow the sample (91–100 Sangat Baik, 81–90 Baik, 71–80 Cukup, below 71 Kurang), and a school can change them. Items not applicable or not seen are left out of the total.
Reason: the observation scale carries over to Ruang GTK; descriptors make reviews consistent; the bands match local practice.

### D-224 — Only the principal observes; supervisors see school totals
Accepted · 2026-11-08 · P4-12 · owner's choice
Only the principal fills observation and review instruments for a teacher; no tim kinerja or appointed teachers. Peer reflection (S-13) stays as a discussion record without scores. The pengawas sees school-level recaps (S-16) only, never teacher-level results.
Reason: owner's choice; keeps supervision results between the teacher and the principal.

### D-225 — Murid feedback as class totals
Accepted · 2026-11-08 · P4-12 · owner's choice
Murid feedback (S-12) is gathered in class on paper or cards, and the teacher enters only the count per answer for the class. No per-murid answers are stored, since murid are not users (D-010).
Reason: Permendikdasmen 1/2026 asks for murid reflection each semester; class totals meet it without student data.

### D-226 — CP rows in `learning_outcomes`
Accepted · 2026-11-08 · P5-03c · adds a table to the data model's Curriculum section
The CP-only versions (`CP-PABP-BKPDM-020-2026`, `CP-PAI-BA-MADRASAH-9941-2025`) are stored in a new table `learning_outcomes`: `curriculum_version_id`, `subject_id`, `phase`, `code` (unique, `<version>-<subject>-<phase>-<nn>`), `order`, `element` (NULL = the phase summary), `sub_element` nullable, `text`, `extras` JSON (`lead_in`, `element_intro`, `arabic_passages`, `review_notes`, D-039, D-127). Sub-elements printed with no CP in a phase (`no_cp_in_phase`) get no row. Topics keep their own `cp_text`.
Reason: the data model lists these versions for v1 but none of its tables holds CP rows without topics; the RPM wizard needs CP and elements per phase.

### D-227 — Subject catalogue file
Accepted · 2026-11-08 · P5-03c
`docs/data/curriculum/subjects.json` (`status: draft`) is the source of `subjects`: code, name, kind, default teacher, religion, level, and aliases, plus regional local content defaults (Kudus: Bahasa Jawa, 2 JP per week). Other files name subjects by code, name, or alias; matching ignores case, punctuation, and spaces, and an unknown subject makes the file invalid. Codes follow the KM-SD-2026-R2 topic IDs (`PP`, `BIN`, `MTK`, `IPAS`, `PJOK`, `BIG`, `BJW`, `PAI`, `KKA`, `KTB`, `SSD`) and the religion and MI TP/ATP files. Seni dan Budaya is one subject `SB`, and the five codes its topic IDs use (`MUS`, `SM`, `SMU`, `SR`, `SRP`) are aliases. Seni Suara Daerah (`SSD`) is school-optional muatan lokal for SD until the owner decides C5 of `docs/drafts/km-sd-2026-r2-source-check.md`; it is not a region default.
Reason: KM-SD-2026-R2 has no subject code field, and every source names subjects differently.

### D-228 — Subject defaults not covered by D-032
Accepted · 2026-11-08 · P5-03c · extends D-032, D-035, D-208
The MI Kemenag subjects and Bahasa Arab default to a guru mapel, as Pendidikan Agama does for SD. Keterampilan Tata Boga, a muatan lokal (D-204), defaults to a guru mapel like Bahasa Jawa. Koding dan KA has kind `cocurricular` (D-208) and gets no intrakurikuler allocation. `subjects.religion` is set only for the six SD religion subjects; the MI subjects are taken by every MI student, so it stays NULL.
Reason: `subjects.default_teacher` and `religion` are NOT NULL or meaningful for every subject; the operator can still reassign (D-032).

### D-229 — Import rules for `curriculum:import`
Accepted · 2026-11-08 · P5-03c · implements D-020, D-113
Each file is one transaction: an invalid file writes nothing. A version made from several files (KM-SD-2026-R2) records each file's hash and reference data under `meta.files`; its name comes from the file that creates it. Rows are matched by `code` (allocations by version, subject, grade, and learning type) and never deleted; a row missing from a newer file is reported and kept. A new version is `draft`; an import never changes the status of an existing version, whatever the file says. Data mismatches (JP sums, phase vs grade, printed totals, fokus–TP links outside the topic, subject code differences) are listed and do not stop the import unless `--strict` is given. Sources that print only JP per year get 18 effective weeks in semester 1 and the rest in semester 2 (14 in grade 6), as in KM-SD-2026-R2. The muatan lokal limits go to the version's `meta`. The KM-SD `kokurikuler_default` rows (Bahasa Inggris grades 1–2) are not imported, since that is optional content (D-204). Fokus–TP links are synced only when the file has them, so a KM-SD-2026-R2 re-import keeps links a data task added. `legacy_codes` holds `Legacy_ID` and its aliases minus the topic's own code; aliases are a fallback lookup after `topics.code`, and aliases shared by several topics or equal to another topic's code are reported. A topic that runs past its semester's effective weeks (18, or 14 in grade 6 semester 2) is a mismatch.
Reason: repeatable imports that report data issues without losing data or approvals.

### D-230 — A class's subjects
Accepted · 2026-11-08 · P5-03c · implements D-204 and the data model's `region_subjects`/`school_subjects`
A class takes the compulsory subjects with an intrakurikuler allocation for its grade in the level's structure version (`config/curriculum.php`: SD `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`, MI `ALOKASI-MI-KMA-1503-2025`), plus the region's local content unless the school switches it off, plus anything the school switches on (optional content is off by default and outside the rapor unless `in_report`). A school cannot switch off a compulsory subject. A `school_subjects` row for one grade wins over the every-grade row. The SD structure file's `version_code` was changed to the data model's `STRUKTUR-SD-PERMENDIKDASMEN-13-2025`.
Reason: one rule set for P5-03d (default teaching assignments) and later the timetable and rapor.

### D-231 — Seni Suara Daerah is school-optional muatan lokal
Accepted · 2026-11-08 · P5-03c · owner's choice · resolves C5 of `docs/drafts/km-sd-2026-r2-source-check.md`
Seni Suara Daerah (`SSD`, KM-SD-2026-R2) is imported as optional muatan lokal for SD that a school switches on in `school_subjects`; it is off by default and is not a region default. Its CP and JP stay `draft` until Tim Validasi SIGRAK reviews them (D-113).
Reason: owner's choice; the CP is a system draft marked "PERLU VERIFIKASI".

### D-232 — Muatan lokal cap warns, for now
Accepted · 2026-11-08 · P5-03c · owner's choice · provisional · resolves C6 of `docs/drafts/km-sd-2026-r2-source-check.md`
When a school's muatan lokal (Bahasa Jawa, Tata Boga, Seni Suara Daerah, or other local subjects) goes over the SD cap of 72 JP per year (64 in grade 6, D-204), SIGRAK warns and does not block. The owner will revisit this once field practice is clear. The check itself is built with the operator pages that switch subjects on (P5-03d or later).
Reason: owner's choice; practice in schools is not settled yet and may change.

### D-233 — Kode Tayang lives at `/pid` on the app host
Accepted · 2026-11-08 · P4-02 · owner's choice · resolves open question 2 of `docs/final/ifp-classroom.md` (D-164, D-173)
The Kode Tayang page for Game kelas is at the path `/pid` on the app host (`APP_HOST`), not on a `papan.` subdomain and not at `/papan`. The IFP opens it, shows the code or QR, and runs the present-only session there.
Reason: owner's choice; one hostname less to set up and route.

### D-234 — Tata Boga grade 6 semester 2 trimmed to 28 JP at import
Accepted · 2026-11-08 · P5-03c · owner's choice · resolves C4 of `docs/drafts/km-sd-2026-r2-source-check.md`
Keterampilan Tata Boga grade 6 semester 2 (`KM-SD-2026-R2-KTB-K6-04`) has 34 JP over weeks 1–17 in the source, but that semester has 14 effective weeks. SIGRAK trims it to 28 JP (14 weeks × 2 JP, weeks 1–14, 14 JP per fokus) at import, as an explicit correction in `app/Curriculum/Import/SourceOverrides.php` that keeps the source values in the topic's `extras.import_override`. The source file in `docs/data/curriculum/` stays exactly as received. The correction applies only while the source still has 34 JP to week 17.
Reason: owner's choice; the source is kept byte-for-byte (P5-03g) and the topic must fit its semester.

### D-240 — Teaching assignments: no row means no teacher yet
Accepted · 2026-11-09 · P5-03d · implements D-032 and the data model's `teaching_assignments`
`teaching_assignments.teacher_id` stays `NOT NULL`: a class subject without a row has no teacher yet, and the Kelas page lists every subject from `SubjectsForGrade` (D-230) with its row, if any. When a class gets a homeroom teacher, each class-teacher subject without a row goes to that teacher; guru mapel subjects wait for the operator. A new homeroom teacher takes over the rows the previous one held in that class, and rows the operator gave to others stay; removing the homeroom teacher removes the rows they held. Changing a class's grade drops the rows for subjects the new grade does not take. A row for a subject the school later switches off is kept and shown as "Tidak diajarkan lagi" until the operator clears it. Teachers are picked by their teacher membership's `public_id` (DB-11, D-182) and must be active teachers of the school.
Reason: the data model's "the rest wait for the operator" without a nullable teacher column, and no silent loss of the operator's choices.

### D-241 — Academic years and classes in the operator portal
Accepted · 2026-11-09 · P5-03d · applies DB-11 and D-149
`academic_years` and `classes` get a `public_id` (UUIDv7) for URLs: `/tahun-ajaran/{academicYear}/ubah`, `/kelas/{class}`, `/kelas/{class}/guru-mapel`. The model for `classes` is `SchoolClass` (`Class` is reserved in PHP). A year's name follows its start date (`2026/2027`) and is not typed; a year lasts at most 12 months and does not overlap another year of the school; the form suggests the year after the latest one (1 July to 30 June, semester 2 from 1 January). The "current" year is the one that includes today, else the next one, else the latest. A year with classes cannot be deleted; deleting a class removes its assignments. Class names are free text up to 20 characters, unique per year. Only the operator uses these pages for now; principals and teachers get their views with P5-09 and Kelas Saya.
Reason: DB-11 needs public ids for rows in URLs; deriving the name keeps it in step with the dates.

### D-242 — Copy classes into a new academic year
Accepted · 2026-11-09 · P5-03h · owner's choice
When a new academic year is created, the operator can copy the classes of the previous year. Each class name is copied with the grade moved up one (4A becomes 5A). Homeroom teachers and subject teachers are left empty so they are filled in again. Students move later through Kenaikan in the student slice.
Reason: schools keep the same class set from year to year; teachers change, so assignments are chosen again.

### D-243 — Copy classes: grade 6 is not copied
Accepted · 2026-11-09 · P5-03h · owner's choice, applies D-242
Grade 6 classes are not copied (they graduate). Grade 6 in the new year comes from last year's grade 5.
Reason: there is no grade 7 in SD/MI.

### D-244 — Copy classes: optional new grade 1 classes
Accepted · 2026-11-09 · P5-03h · owner's choice, applies D-242
The copy dialog has a checkbox, on by default, to recreate last year's grade 1 classes with the same names (1A, 1B) for the new intake.
Reason: after the move-up there is no grade 1 left, and most schools open the same grade 1 classes each year.

### D-245 — Copy classes: names without a leading grade number
Accepted · 2026-11-09 · P5-03h · owner's choice, applies D-242
Only a name that starts with the class's grade number gets the new number (`4A`, `4 B`, `4-Umar` → `5A`, `5 B`, `5-Umar`). Other names (`Kelas Merah`) keep their name and move up a grade; the operator can rename them. If two copied names would clash in the new year, the later one gets the new grade added (`Kelas Merah 5`), cut to the 20-character limit (D-241).
Reason: free names carry no grade to change; a clash must not stop the whole copy.

### D-246 — Copy classes only into an empty year
Accepted · 2026-11-09 · P5-03h · owner's choice, applies D-242
Copying works only when the target year has no classes; otherwise it is refused and the copy button is hidden. The source is the school's latest year that starts before the target year.
Reason: a second copy would duplicate or mix classes; refusing keeps it safe to retry.

### D-247 — Timetable: breaks and non-lesson rows are recorded
Accepted · 2026-11-09 · P5-03e · owner's choice, refines the data model's `timetable_slots`
A class timetable has lesson rows and non-lesson rows: Istirahat, Upacara, and Kegiatan (with an optional label such as "Senam" or "Literasi"). `timetable_slots` gets `kind` ∈ {`lesson`,`break`,`ceremony`,`activity`} and `label` (nullable); `subject_id` is set only for lessons and `jp` is 0 for other rows (CHECKs). The operator types the start and end time of each row, so a shorter Friday or Saturday is just other times; `period_no` is the row's place in the day, recomputed from the start times, and never typed.
Reason: the owner wants breaks and ceremonies visible in the timetable.

### D-248 — Timetable: teacher clashes are refused
Accepted · 2026-11-09 · P5-03e · owner's choice
Saving a lesson row is refused when its teacher already has a lesson in another class that overlaps it on the same day; the message names the class and the time. Rows of one class may not overlap either. Picking a guru mapel on the Kelas page is refused the same way when it would make a clash. A clash that comes from a homeroom change is shown as a warning on the Jadwal page until the operator fixes it.
Reason: the teacher's timetable feeds RPM scheduling and Jurnal, so it must be right.

### D-249 — Timetable: the teacher follows the class's teaching assignments
Accepted · 2026-11-09 · P5-03e · owner's choice, refines the data model's `timetable_slots.teacher_id`
A lesson row's teacher is the teacher of that subject in that class (`teaching_assignments`, D-240). The table has no `teacher_id` column: the teacher is read through the assignment, so changing a teacher on the Kelas page changes the timetable with it. A subject without a teacher cannot be put on the timetable; a row whose assignment is cleared later stays and shows "Belum ada guru".
Reason: one place to set teachers; a copied column could drift from the assignments.

### D-250 — Timetable: lesson hours must match the allocation
Accepted · 2026-11-09 · P5-03e · owner's choice
A subject's JP per week on the timetable must equal its allocation for the class (`SubjectsForGrade`, D-230). A row that would put a subject over its allocation is refused; the Jadwal page lists each subject's scheduled and allocated JP and calls the timetable complete only when every allocated subject matches. Subjects without an allocation (optional content) have no cap. 1 JP = 35 minutes (data model); the JP of a row is suggested from its length and the operator may change it (1–4).
Reason: the curriculum allocation is binding; the timetable is filled row by row, so "under" can only be shown, not refused.

### D-251 — Timetable: religion lessons run in parallel
Accepted · 2026-11-09 · P5-03e · owner's choice, refines D-248 and D-250
Lessons of different religion subjects (the six Pendidikan Agama subjects) may share the same time in a class, since students split by religion; any other overlap in a class is still refused, and every teacher is still checked for clashes. A religion subject counts towards "complete" only when it has a teacher in that class; a religion without a teacher is shown as not counted.
Reason: all six religions are in the structure at 3 JP, but a class only teaches the religions of its students, at the same hour.

### D-252 — Calendar work is P5-03i; draft timetable is P5-03j
Accepted · 2026-11-09 · P4-13 · owner's choice, reopens P5-03
National holidays, region calendars, school calendar copy and updates, class-level events, effective days, the conflict checker, and the calendar impact preview are task P5-03i, done before P5-04. The draft timetable generator is P5-03j, after P5-03i and in parallel with P5-04. usulan-01's names P5-03h (taken by copy classes) and P5-03e2 are not used.
Reason: follows the P5-03f/g/h pattern; P5-04 (Prota/Promes, RPM scheduling) needs effective days.

### D-253 — National holidays: one central read-only table
Accepted · 2026-11-09 · P5-03i · owner's choice, refines the data model's `calendars`
`national_holidays` (`date`, `name`, `kind` ∈ {`national_holiday`,`joint_leave`}, `decree`, `academic_year`, `is_provisional`) sits outside `calendars` and is merged into every calendar view, even when a school has no calendar yet. Tim SIGRAK updates it in the console each year when the new SKB is out (usually September); `php artisan calendar:import-holidays <file>` imports a file, idempotent per date. Years not yet covered by an SKB get the fixed-date holidays (1 Jan, 1 May, 1 Jun, 17 Aug, 25 Dec), marked "sementara".
Reason: holidays apply nationally and change once a year; one copy keeps every school's effective days right.

### D-254 — Holiday data is checked against the official SKB online
Accepted · 2026-11-09 · P5-03i · owner's choice, applies D-113 to holiday data
Before import, an agent finds the official SKB 3 Menteri for 2026 and 2027 on government sites (Kemenag, KemenPANRB, Kemnaker) and checks every date, name, and kind in `docs/sources/usulan-01/data/libur-nasional-cuti-bersama.json`, including the unchecked 2027 SKB number. It records the source URLs, the check date, and any differences; Tim Validasi SIGRAK signs off. The data stays `draft` until then.
Reason: the file was copied from the legacy app; an online check is quick, and sign-off keeps the D-113 pattern.

### D-255 — Joint leave is a holiday by default
Accepted · 2026-11-09 · P5-03i · owner's choice
Cuti bersama dates are non-effective days by default. A region or school calendar may mark a single joint-leave date as a school day when the dinas calendar differs.
Reason: most kaldik follow the SKB, but some dinas keep school open on some joint-leave days.

### D-256 — School calendar is copied from the region calendar, with offered updates
Accepted · 2026-11-09 · P5-03i · owner's choice, replaces the data model's "region defaults that a school calendar overrides"
Tim SIGRAK builds region reference calendars from the dinas kaldik. The operator copies one with "Salin dari kalender wilayah" and adds school events. When the region calendar changes later, the operator sees "Terapkan perubahan wilayah" with the list of changes and the impact preview (D-261), and applies or skips them. Teachers only read the calendar, except as in D-257.
Reason: the school owns its calendar but still hears about dinas changes instead of drifting silently.

### D-257 — Region calendars for Kudus, Jepara, and Pati in v1
Accepted · 2026-11-09 · P5-03i · owner's choice, applies D-018 and D-170
2026/2027 region reference calendars for Kabupaten Kudus, Jepara, and Pati are required before the calendar feature ships. Other regencies are added later as data, with no code change.
Reason: all three are in the school directory (D-170) and are the first market.

### D-258 — Calendar events for one class
Accepted · 2026-11-09 · P5-03i · owner's choice
`calendar_events` gets `class_id` (nullable, FK to `classes` of the calendar's school). When set, the event applies only to that class; a `no_lessons` event for a class means no meetings and no effective day for that class, and its meetings shift (teachers get the D-274 preview). The operator may add events for any class or the whole school, a wali kelas only for their own class; guru mapel only read.
Reason: a class trip must not remove lessons from other classes.

### D-259 — Effective days and weeks per class
Accepted · 2026-11-09 · P5-03i · owner's choice
`app/Calendar/EffectiveDays` counts per class: days with a lesson slot in the class timetable, minus national holidays, active joint leave, and `holiday`, `exam`, `school_event`, and `no_lessons` events of the school or the class. Effective weeks = effective days ÷ the class's school days per week; the full value is stored and shown to one decimal. The table has Bulan, Hari sekolah, Libur, Asesmen, Kegiatan, Hari efektif, Minggu efektif, with subtotals for semester 1 and 2 (split at `even_semester_starts_on`) and a year total. It feeds S-01.1, Prota/Promes (A11), completeness targets, RPM readiness, the JP balance, and RPM scheduling.
Reason: timetables differ per class (5 or 6 days); one service gives every role the same numbers.

### D-260 — One conflict checker, rules added per slice
Accepted · 2026-11-09 · P5-03i · owner's choice
`app/Calendar/ConflictChecker` checks every dated item with the same messages. P5-03i covers calendar events and the timetable; supervision schedules and Agenda & Rapat add their rules in P5-09. Refused: an assessment (STS/SAS) on a national holiday or an active joint-leave day. Warning only: an event on a Sunday or outside the class's school days, an event at the same time as a meeting (rapat), supervision on a non-effective day.
Reason: one place for clash rules without holding the calendar back until P5-09.

### D-261 — Calendar change impact preview
Accepted · 2026-11-09 · P5-03i, P5-04 · owner's choice
When the operator or a wali kelas saves an event, or applies region updates, and the change would move RPM meetings, they first see the impact ("12 pertemuan RPM di 6 kelas akan bergeser") and confirm. Nothing is shown when there is no impact. Afterwards teachers get the shift preview with Batalkan (D-274). There is no draft calendar table.
Reason: keeps the legacy app's kaldik calon idea without a second calendar.

### D-262 — Draft timetable generator
Accepted · 2026-11-09 · P5-03j · owner's choice
"Susun draf jadwal" for one class or all classes uses the operator's day frame, the JP allocation (D-230), the teaching assignments, and locked slots. Hard limits (D-248…D-251, locked slots untouched) cannot be changed. Soft preferences are on/off switches per school, without numeric weights: at most 2 consecutive JP per subject per day (3 for PJOK), PJOK before 10.00, Matematika and Bahasa Indonesia early, spread over the week, no split across a break. The output is always a draft with a comparison screen and the reasons for JP that could not be placed; every save passes the D-248…D-251 validator. It runs as a `default` queue job (about 10 s per school), with Acak ulang.
Reason: P5-03e (manual) is done; switches are simpler for operators than weights.

### D-263 — One completeness service for every screen
Accepted · 2026-11-09 · P5-07 (teacher), P5-09 (portals) · owner's choice, merges usulan-01 U-01 and Part B B-01
One domain, `app/Progress/`. Its base, `CompletenessLedger`, produces slot rows (`teacher`, `class`, `subject`, `date`, `item`, `state`) with state ∈ {done, missing, not_due, excused (holiday, `no_lessons`, class event)}. The per-teacher `ProgressReport` and the school report are built only from these rows. Kinds: RPM, Jurnal, Kehadiran (wali kelas only, D-029), Refleksi guru (1 per semester, D-214 A4), Prota / Promes / rencana asesmen (present or not), asesmen diagnostik (wali kelas, start of year), kokurikuler JP (D-277), and untargeted rows shown as "Ada (n entri)" or "Belum ada". Statuses are text plus colour: Lengkap, Belum lengkap: n, Belum dimulai, Tidak berlaku. Only subjects the teacher holds in `teaching_assignments` count (D-240); subjects with no teacher show as information. The supervisor and the console get school totals only (D-224).
Reason: one formula gives the same numbers in every role.

### D-264 — Completeness windows
Accepted · 2026-11-09 · P5-07, P5-09 · owner's choice
Windows: Hari ini, Minggu ini, Semester ini. Hari Ini opens on Minggu ini, Pantau Guru on Semester ini. The principal and the supervisor may also pick a free date range for prints and reports; teachers get the fixed windows only.
Reason: a simple teacher view, while leaders still get report periods.

### D-265 — Weekly completeness snapshots
Accepted · 2026-11-09 · P5-07 · owner's choice
Every Monday at 00.30 WIB a job writes `progress_snapshots` (`school_id`, `teacher_id`, `academic_year_id`, `week_starts_on`, `kind`, `target`, `available`), unique (`teacher_id`, `week_starts_on`, `kind`), for trend charts. Live results are cached per teacher per day in Redis and dropped on source events (journal, meeting, timetable, or calendar change). No progress columns on other tables.
Reason: the principal sees a trend, not only today's number.

### D-266 — "Siapkan besok"
Accepted · 2026-11-09 · P5-07 · owner's choice
On Hari Ini, tomorrow's slots without an RPM meeting show one button that opens the RPM wizard with class, subject, date, and the next ATP topic filled in. Every missing row links to the screen that fixes it.
Reason: turns the completeness list into the next action.

### D-267 — The journal records a meeting's outcome; "terlaksana sebagian"
Accepted · 2026-11-09 · P5-04, P5-07 · owner's choice, refines the data model's `meetings` and `journals`
`journals.status` ∈ {`done`,`partial`,`not_done`} with `not_done_reason`, `not_done_note`, and `unfinished_experiences` (a subset of memahami, mengaplikasi, merefleksi, for `partial`). `meetings.status` ∈ {`planned`,`done`,`partial`,`not_done`} is derived from the meeting's journal and stores no reason. `not_done` shifts later meetings (D-274). `partial` does not shift: the unfinished experiences become a "Lanjutan" block at the top of the next meeting and in Game kelas, and 3 partials in a row for one class and subject trigger the JP balance (D-269). For completeness, `done` and `partial` count as RPM and journal present; `not_done` with a reason counts as a journal and removes the slot from the RPM target.
Reason: one place records the outcome, so nothing is duplicated, and a partial lesson neither repeats the meeting nor hides the gap.

### D-268 — Not-done reasons are required; lost JP recap
Accepted · 2026-11-09 · P5-07, P5-09 · owner's choice
Marking a journal `not_done` needs a reason: Kegiatan sekolah (`school_event`), Guru bertugas di luar (`teacher_duty`), Murid tidak hadir (`students_absent`), Bencana/cuaca (`disaster_or_weather`), Lainnya (`other`, note required). Pantau Guru shows JP lost per reason ("Bulan ini 12 JP hilang karena kegiatan sekolah").
Reason: the recap feeds the review of the calendar and school programme.

### D-269 — One JP balance before SAS (Neraca JP)
Accepted · 2026-11-09 · P5-04, P5-07 · owner's choice, merges U-02 item 3, U-12 item 2, and B-02 item 2
One calculation per class and subject: remaining ATP JP against available slot JP up to the semester's SAS (the calendar's SAS exam event), recomputed after every shift (journal, calendar, timetable, change of the semester-2 start) and after 3 partials in a row. Results: Pas, Kurang n JP, Ada n JP cadangan. When short, one warning with three actions: padatkan or merge meetings (AI section revision), move the topic to the next semester, or accept with a note that appears in the teacher's semester reflection (A4). With a surplus, the app suggests remedial or pengayaan (A1).
Reason: one warning instead of three for the same problem.

### D-270 — RPM readiness per subject
Accepted · 2026-11-09 · P5-04, P5-07 · owner's choice
`app/Content/Readiness/RpmReadiness` per class and subject: Siap (all slots in the next 10 effective days have a meeting), Segera habis (meetings run out within 4–10), Perlu RPM (an empty slot within 3 effective days, or a past slot without RPM, naming the first empty date), and grey Tidak ada jadwal / Belum ada ATP. Shown in Buat (subject card with ATP progress), Hari Ini (yellow and red only), and Pantau Guru (counts). The defaults 10 and 3 are set by Tim SIGRAK in the console; a school operator may override them for the school, not per teacher.
Reason: central defaults, with room for schools with a different rhythm.

### D-271 — Readiness and JP balance Kabar go to teachers only
Accepted · 2026-11-09 · P5-07 · owner's choice
Teachers get a Monday 06.00 WIB summary ("2 mapel perlu RPM, 1 mapel kurang JP") and an immediate Kabar when a subject turns Perlu RPM or its balance turns Kurang, at most once per subject per day, each with an action button. The principal gets no Kabar for these and sees per-teacher counts in Pantau Guru only.
Reason: nudges the teacher without flooding the principal.

### D-272 — Semester-2 RPMs may be drafted early
Accepted · 2026-11-09 · P5-04 · owner's choice, clarifies `content-engine.md` scheduling
Teachers may create semester-2 RPMs at any time. Their meetings are placed only from `academic_years.even_semester_starts_on`, and Buat shows "Dijadwalkan mulai [tanggal]". When the operator changes that date, semester-2 meetings are rescheduled with the D-274 preview, a Kabar, and a `meeting_schedule_history` version.
Reason: teachers plan in the December break without breaking the data rule.

### D-273 — "Pasang ke jadwal" for RPM lepas jadwal
Accepted · 2026-11-09 · P5-04 · owner's choice
An RPM lepas jadwal (`planned_on` NULL) can be placed on one class's timetable from a start date, with the same preview as a shift. From then on it counts in ATP progress, readiness, the JP balance, and completeness.
Reason: a stand-alone RPM can still join the plan later.

### D-274 — RPM shifts are automatic, shown, and undoable; Promes shows plan vs actual
Accepted · 2026-11-09 · P5-04 · owner's choice (Part B Q1)
Every shift (journal, calendar, timetable change) applies automatically and shows "2 pertemuan bergeser: Pertemuan 3 → Kamis 13/11, …" with Batalkan, which restores the previous `meeting_schedule_history` version. The Promes print gets a second view, Realisasi, built from journals: planned week against actual week per topic.
Reason: the reflow stays, but the teacher sees what moved and can undo it.

### D-275 — RPM copy for a parallel class, without AI
Accepted · 2026-11-09 · P5-04 · owner's choice (Part B Q3)
"Pakai untuk kelas lain" copies an RPM to a parallel class of the same grade and subject with no AI call; its meetings are placed on the target class's timetable. Each copy then lives on its own (edits, journals, shifts).
Reason: a guru mapel teaches the same topic to several classes; this saves quota.

### D-276 — Restore curriculum defaults
Accepted · 2026-11-09 · P5-04 · owner's choice
Kurikulum gets "Pulihkan bawaan" per topic and "Reset semua penyesuaian". Only the principal or operator can use them, with confirmation and an audit log. Old `school_topic_changes` stay as history (marked reverted) and are never deleted. Existing RPMs do not change.
Reason: a safe way back without losing history.

### D-277 — Automatic annual kokurikuler JP target
Accepted · 2026-11-09 · P5-04, P5-07 · owner's choice
The school's annual kokurikuler plan takes its JP target from `subject_allocations` (`cocurricular`): SD 216/216/252/252/252/224 for grades 1–6 (Permendikdasmen 13/2025), MI per KMA 1503/2025. The school may adjust it with a note. Completeness counts held kokurikuler JP against it, and kokurikuler taught by a guru mapel becomes that guru mapel's target.
Reason: follows the 2025 structure and the legacy app's guru mapel rule.

### D-278 — Berkas Administrasi: one semester bundle
Accepted · 2026-11-09 · P5-07 (teacher), P5-09 (principal) · owner's choice, merges usulan-01 U-03 and Part B B-10 (Q6)
The feature is named **Berkas Administrasi** ("Paket" stays for Paket Soal TKA and XP-6 packages). Its home is Dokumen Saya; the principal opens it for one teacher from Pantau Guru; Hari Ini shows a shortcut only near the end of a semester. Profiles: Standar SIGRAK (default; S-01 order, plus S-15 for a wali kelas), Format Kudus (24 components for guru kelas, 18 for guru mapel), and a K3 preset on the same builder. Range: current semester, school year, or free dates, for teachers too. Contents: cover, completeness summary sheet (D-263), contents page, a title page per part, and a NIHIL page for an empty part; BK notes are never included. Output: one merged PDF by default, a ZIP of separate PDFs as an option, no DOCX. Files are kept 7 days, then deleted, and reused while the data is unchanged.
Reason: "Berkas" does not clash with Paket Soal TKA, and one builder serves the teacher, the principal, and K3.

### D-279 — Principal downloads a whole school's Berkas
Accepted · 2026-11-09 · P5-09 · owner's choice
The principal can download every teacher's Berkas Administrasi for a semester as one ZIP with one PDF per teacher, built as a job on the `exports` queue.
Reason: one PDF per teacher avoids a single thousand-page file.

### D-280 — PDF merge for Berkas Administrasi only
Accepted · 2026-11-09 · P5-07 · owner's choice, amends D-031 and XP-6
Berkas Administrasi renders each part as its own PDF (also used for the ZIP) and joins them with Gotenberg's merge route; the contents page takes page numbers from each part's page count, so there is no two-pass render. Every other document stays one view → one PDF, no merge.
Reason: the ZIP option already needs per-part PDFs; one code path is simpler than a merge fallback for thick bundles.

### D-281 — Document approval (pengesahan) with a verification code
Accepted · 2026-11-09 · P5-09 · owner's choice (usulan-01 W-2), keeps D-010
RPM, Prota, Promes, rencana asesmen, and Berkas Administrasi can be sent with **Ajukan pengesahan**; the principal approves or returns them with a note in the Validasi queue (apart from the D-019 curriculum flow). Approval stores a SHA-256 `content_hash` of the canonical revision text and a 10-character code. Prints carry QR, code, and "Disahkan melalui SIGRAK, [tanggal]" only while the active revision equals the approved one; otherwise the status is "Berubah setelah disahkan". For a Berkas the one QR sits in the principal's signature column on the summary sheet. `/verifikasi/{code}` and "Periksa kode pengesahan" work only after login, for that school's members and its assigned supervisor; anyone else gets "Kode tidak ditemukan atau Anda tidak berwenang". Only a principal of that school can revoke an approval. The term is "pengesahan dokumen", never "tanda tangan elektronik".
Reason: internal school proof without a public page and without a legal e-signature claim.

### D-282 — Approved revisions are never pruned
Accepted · 2026-11-09 · P5-09 · owner's choice, amends D-153
The daily prune keeps the last 5 revisions per document, but never removes a revision linked to an `approved` `document_approvals` row.
Reason: a verification code must stay checkable after later edits.

### D-283 — Print rules: cover, margin profiles, landscape, NIHIL
Accepted · 2026-11-09 · P5-04 · owner's choice, refines XP-7 and D-031
A shared black-and-white cover for book-type documents and Berkas: no page number; document year by semester (semester 1 = first year of the school year, semester 2 = second year, annual = current year); optional school logo upload, no bundled government logos; the letterhead goes on the first content page after the cover. `school_print_settings.margin_profiles` holds three school-editable profiles in mm: Buku administrasi 15/12/15/25, Dokumen ajar 20/20/20/20, Lembar tunggal 15/15/15/15, plus a duplex option that mirrors left and right margins on even pages. Tables with 9 or more columns print landscape unless the template says otherwise. An empty table gets one full-width "NIHIL" row.
Reason: owner accepted the four usulan-01 U-15 rules and their defaults.

### D-284 — Automatic letterhead from the school profile
Accepted · 2026-11-09 · P5-04 · owner's choice, extends D-031
A school without a letterhead image can press "Buat kop otomatis": SIGRAK composes a PNG from the logo, name, address, and contact in the school profile and saves it as an ordinary, replaceable letterhead. The letterhead stays an image.
Reason: helps schools with no designed kop while keeping the one-image model.

### D-285 — Completion sound and screen notification
Accepted · 2026-11-09 · P5-04 · owner's choice
When an AI generation finishes or fails, an export or Berkas is ready, or an answer-sheet sync finishes, there is always a toast and a Kabar entry; sound only adds to that. Sound is on by default with the Lembut pattern (three Web Audio patterns: Lembut, Lonceng, Tegas; failure sounds different) and plays only while the tab is visible. A screen notification shows only while the tab is hidden; permission is asked at the first generation. Settings live in Akun > Tampilan and are stored on the server per user. Web Push is out of scope.
Reason: information never depends on sound alone, and settings follow the user across devices.

### D-286 — Answer-sheet scanning (Pindai LJK) is core in P5-08
Accepted · 2026-11-09 · P5-08 · owner's choice (usulan-01 W-3), replaces Part B Q5
Pindai LJK is built in the Asesmen slice as its own task. Kartu Jawab (D-174) later reuses its camera and roster-mapping code. The fast score grid (grid cepat) also stays in P5-08.
Reason: summative scores should not wait for the last Game kelas step.

### D-287 — Answer-sheet content and scanned forms
Accepted · 2026-11-09 · P5-08 · owner's choice
The QR holds only the sheet's random `public_id`; the server maps sheet → assessment → student → `layout_version`. Scanned forms: PG (A–D or A–E), PG Kompleks (MCMA), Benar-Salah and PGK Kategori (one B/S row per statement), and Menjodohkan (one bubble row per pair). Isian and Uraian are entered by hand. No AI reads student answers, scanning runs on the device, and sheet images are never uploaded.
Reason: no guessable student data on paper, and every auto-gradable Bank Soal form is covered.

### D-288 — Answer sheet as paper backup for Simulasi TKA, later
Accepted · 2026-11-09 · after P5-08 · owner's choice
The same answer sheet becomes the paper fallback for Simulasi TKA when a school lacks devices, added after Pindai LJK exists. It is not part of P5-06.
Reason: keeps the slice order.

### D-289 — Offline: read-only RPM meetings and the scan queue
Accepted · 2026-11-09 · P5-07, P5-08 · owner's choice (Part B Q9), amends D-016
Besides attendance, journal, and student notes, two things work offline. (1) The Di Kelas cache holds today's and tomorrow's RPM meetings, read-only, text only. (2) Pindai LJK: the assessment data (key, roster, sheet map) is downloaded when the scan page opens online; scan results (answers read and confidence, no images) queue on the phone with `client_uuid`; the teacher can review and confirm offline. Confirmed results reach `scores` only after sync, and a Kabar reports sync done or a conflict. Editing, AI, and everything else stay online-only.
Reason: classrooms often lack signal; the PWA scope otherwise stays small.

### D-290 — Rapor grade from weighted summative components
Accepted · 2026-11-09 · P5-08 · owner's choice, follows D-205
A subject's rapor grade is the weighted mean of the summative lingkup materi average, STS, and SAS: default 50/25/25, set per school in Pengaturan Asesmen, totalling 100%. A component not held is dropped and the other weights are normalised. A formative assessment can never be a component (server check, tested). Fase A without numbers (`numeric_grades = false`) gives descriptions only.
Reason: transparent weights that follow D-205.

### D-291 — Remedial cap
Accepted · 2026-11-09 · P5-08 · owner's choice
A remedial score replaces the lingkup materi score, capped at the KKTP by default; a school may switch to no cap. When the KKTP is not a number, the cap is a school-wide numeric threshold set in Pengaturan Asesmen. The pre-remedial score is kept.
Reason: matches common practice and keeps the history.

### D-292 — Leger: teacher confirmations, principal sign-off locks it
Accepted · 2026-11-09 · P5-08 · owner's choice, merges usulan-01 U-09 and Part B B-08
Each guru mapel confirms their subject's grades, the wali kelas confirms the class leger, then the principal signs off and the leger locks. Rapor prints and exports (including e-Rapor/RDM, D-218) always come from the locked version. Only the principal can unlock, with a required reason that is logged; teachers can ask for an unlock; after unlocking, the leger is confirmed and signed off again. "Cek kesiapan rapor" (12 components per student, with links to fix each gap) warns but does not block: the wali kelas confirms after ticking "tetap lanjutkan".
Reason: teachers own their numbers, the principal owns the final version.

### D-293 — Bank Soal output: per-item data, six derived prints
Accepted · 2026-11-09 · P5-05 · owner's choice
The AI fills only per-item data (type, stem, options, key, explanation, indicator, `kode_tp`, cognitive level, difficulty). Kisi-kisi, naskah, kunci, pembahasan, indikator, and kartu soal are rendered from that data with no extra AI call and follow item edits.
Reason: always consistent, and cheaper on quota.

### D-294 — Item library across Bank Soal documents
Accepted · 2026-11-09 · P5-05, P5-08 · owner's choice (Part B Q4)
`question_items` indexes every Bank Soal item (document, position, type, `kode_tp`, cognitive level, difficulty, last use, stats) and stays in sync with the document content, which remains the source for rendering. The wizard's "Ambil dari soal saya dulu" fills the composition from matching items and asks the AI only for the gap; recently used items are not skipped but marked "pernah dipakai" with date and class. After an assessment, item analysis (A7) writes real difficulty and discrimination back to the item.
Reason: SD item pools are small; a hard skip would force more AI calls.

### D-295 — Daya serap print and Kelompok belajar
Accepted · 2026-11-09 · P5-08 · owner's choice
"Analisis Daya Serap" is a print layout and search alias of Analisis per TP, not a separate page. Kelompok belajar per TP (perlu bimbingan, cukup, siap pengayaan) can be adjusted by the teacher; the next RPM wizard for that subject offers the group counts as kondisi murid, counts only, never names (PromptDataGuard).
Reason: the familiar print stays, and the result feeds the next lesson.

### D-296 — LKPD blocks for grades 1–3
Accepted · 2026-11-09 · P5-05 · owner's choice (Part B Q10)
A fixed set of LKPD blocks drawn by the renderer, as with TKA figures (D-030, no AI images): `trace` (letters, numbers, Arabic letters for MI), `match_lines`, `cut_and_stick`, `colour_by_rule`, `count_pictures` (bundled pictograms), `fill_table`, `short_answer`. The AI picks blocks and fills text and data; the wizard suggests blocks by phase, Fase A first.
Reason: lower-grade worksheets are mostly visual.

### D-297 — Piket: generated groups with rotation, tasks, and absence marks
Accepted · 2026-11-09 · P5-07 · owner's choice, merges usulan-01 U-07 and Part B B-13.1
*Bagi otomatis* splits the class's active students over its school days (5 or 6), balanced by count and by gender (each difference at most 1); optional rotation every 1, 2, or 4 weeks; optional per-student tasks rotating inside the group, with a list the wali kelas can edit. The result is a draft, editable by drag and drop, then saved and printable. Mutasi keluar removes a student from the group; a new student joins the smallest group and the wali kelas gets a Kabar. A student recorded absent today is only marked "tidak hadir" on today's list, and their task passes to another member of the same group: no substitute from another group, no "owed" duty.
Reason: keeps the roster correct without punishing absence.

### D-298 — Piket on the Game kelas idle screen, school's choice
Accepted · 2026-11-09 · P5-07 · owner's choice
Today's piket group and tasks may show on the Game kelas idle screen when the school switches it on (off by default), with first or short names only.
Reason: the school decides what goes on the shared screen.

### D-299 — Class-cash report may be shared by hand; no money reminders
Accepted · 2026-11-09 · P5-07 · owner's choice, applies D-215 (H6)
The wali kelas may share a class-cash transparency report (income including voluntary contributions, expenses, balance) as `wa.me` text or a print, sent by hand from their own WhatsApp whenever they choose; the app never schedules or reminds. There is no collection template and no per-student money message to parents. The shared report shows totals by default; the wali kelas may choose to include per-student detail.
Reason: transparency to the paguyuban without pressure to pay.

### D-300 — Voluntary class contributions (sumbangan sukarela)
Accepted · 2026-11-09 · P5-07 · owner's choice (usulan-01 W-4), within D-158 and D-211
Kelas Saya > Keuangan gets *Sumbangan sukarela*: kinds (`class_contribution_kinds`: name, purpose, optional agreed amount per student, period, optional link to meeting minutes) and receipts per student (`class_contributions`), each receipt automatically one `class_finance_entries` income row. The recap shows the total received, the number of contributors, and "sisa dari kesepakatan" only when an agreed amount is set. The words "tunggakan", "kurang bayar", and "belum lunas" are never used in UI, prints, or `lang/id` (tested). It follows the school's Keuangan switch, and switching it on shows a note that mandatory fees are not allowed in public schools.
Reason: matches how classes collect money, in rule-compliant language, with one cash source.

### D-301 — Student savings follow the student; handover on leaving
Accepted · 2026-11-09 · P5-07, P5-08 · owner's choice, details D-219
The savings book belongs to the student: `savings_entries` (`school_id`, `student_id`, `class_id` at transaction time, `kind` ∈ {`deposit`,`withdrawal`,`closing_withdrawal`}, `amount` bigint > 0, `occurred_on`, `recorded_by`); balance = sum per student per school. When kenaikan is set, the balance moves with the student without a transfer entry, and the old class recap closes with "Saldo dibawa ke kelas [x]". On graduation or mutasi keluar with a balance above 0, the app prepares a berita acara penyerahan tabungan (signed by wali kelas, parent or guardian, and principal) and a closing withdrawal, recorded in `handover_records` (`student_id`, `amount`, `reason` ∈ {`graduation`,`transfer_out`}, `handed_over_on`, `document_id`). Graduation or transfer is not blocked; "Tabungan belum diserahkan" stays in the wali kelas's Kabar until closed. The kenaikan screen shows balance per student and the class total.
Reason: a balance never stays behind in an old class, and handover leaves a paper trail.

### D-302 — Menu name "Kehadiran"
Accepted · 2026-11-09 · P5-07 · owner's choice, changes the `product-concept.md` menu
Di Kelas > Absensi is renamed *Kehadiran* in UI and prints; help search still finds "absensi". Table names stay (`attendance_records`); the URL path follows D-149 (`/kehadiran`).
Reason: matches the rapor term (ketidakhadiran) and the 2025 assessment guide.

### D-303 — Attendance recap per period and early warning
Accepted · 2026-11-09 · P5-07 · owner's choice, details D-215 (H2)
Kehadiran has a recap per week, month, and semester (print and Excel, D-150). The early warning has two triggers, both changeable or switchable off by the school: days without explanation (tanpa keterangan) bring attendance below 90% in a calendar month (sakit and izin are not counted), and 3 consecutive school days absent without explanation. A flag shows on the wali kelas's Hari Ini and to the principal, with a suggested action (call via H6, home visit H7).
Reason: a percentage rule as the owner prefers, plus a consecutive-days rule that catches a sudden drop.

### D-304 — Kenaikan and mutasi are drafts until set
Accepted · 2026-11-09 · P5-08 (kenaikan), P5-09 (mutasi) · owner's choice
Kenaikan (naik, tinggal, lulus) and mutasi (masuk, keluar, pindah rombel) are saved as drafts that can be edited and printed; the kenaikan draft feeds the H3 meeting sheet. Nothing changes (enrolments, piket, savings) until *Tetapkan*. "Tandai semua naik/lulus" works on the draft.
Reason: the decision comes from the dewan guru meeting; a draft avoids moving students by mistake.

### D-305 — Legacy app catalogs: one reviewed import task
Accepted · 2026-11-09 · new data task · owner's choice, applies D-113
One task copies the usulan-01 data files to `docs/data/catalogs/` (`cocurricular-activities.json`, `extracurricular.json`, `class-inventory.json`, `form-options.json` from pilihan-isian), keeping provenance and `status: draft`. Tim Validasi SIGRAK reviews them before seeders read them from `docs/data/`. Only the `sigrak` labels reach the UI; the `legacy` labels stay as reference.
Reason: one review pass, one place for the data, and no slice blocked on it.

### D-306 — Class inventory is the room view of school inventory
Accepted · 2026-11-09 · P5-09 · owner's choice (Part B Q7), refines D-217 (O4)
An inventory item belongs to the school and sits in a room; the wali kelas edits condition and count for their own room. One record, two views. Tim SIGRAK sets in the console whether the room view needs the class add-on (D-158).
Reason: the same item is not typed twice.

### D-307 — Teacher transfer: what moves and what stays
Accepted · 2026-11-09 · P5-09 · owner's choice (Part B Q8)
On Pindah Sekolah the old school keeps attendance, journals, scores, notes, rapor, and a read-only copy of the teacher's documents. The teacher takes copies of their own documents (RPM, Bank Soal, LKPD, games), their AI keys, and their plan. School-specific parts inside a document (letterhead, school name) are replaced on the first export at the new school.
Reason: school data belongs to the school; the teacher's own work goes with them.

### D-308 — Principal supervision (PKKS / supervisi manajerial) in v1
Accepted · 2026-11-09 · P4-08, P5-09 · owner's choice, extends D-221…D-224
The pengawas supervises the principal in v1: a PKKS / supervisi manajerial instrument set filled by the pengawas, plus a Kinerja Kepala Sekolah evidence download (K3 extended to the principal, for Ruang GTK). The instruments are drafted from current rules (principal performance management, Permendikdasmen 26/2025, IA 2024), checked against a local sample if the owner supplies one, and kept as editable templates (D-221). Only the assessed principal and that pengawas see a PKKS result. D-224 still holds for teachers.
Reason: the pengawas portal needs its own supervision duty, and principals need Ruang GTK evidence too.

### D-309 — Pengawas requests sekolah binaan; Tim SIGRAK approves
Accepted · 2026-11-09 · P5-09 · owner's choice, extends D-168 and D-184
A pengawas picks a school (from the NPSN directory or the school list) and requests it as sekolah binaan. Tim SIGRAK approves or rejects in the console, and the principal is notified. Staff can still assign and revoke directly. School data opens only after approval.
Reason: cross-school access stays under Tim SIGRAK's control.

### D-310 — Pusat Tindak Lanjut
Accepted · 2026-11-09 · P5-09 · owner's choice
One follow-up list per role that reads existing records, with no new table: RTL from S-09/S-10, the S-17 programme, pengawas visit follow-ups (P1), PKKS follow-ups, and returned validations, each with status and due date. Teachers see their own, the principal the school's, the pengawas school-level and PKKS items only (D-224).
Reason: follow-ups spread across modules get lost.

### D-311 — Pengingat with read and acted-on status; never "teguran"
Accepted · 2026-11-09 · P5-09 · owner's choice
The word "teguran" is not used. A Pengingat from the principal to a teacher moves Terkirim → Dibaca → Ditindaklanjuti; the teacher marks the last step. The pengawas sends pengingat to the principal, never to teachers. The B-12 limit of one Ingatkan per item per teacher per week applies.
Reason: a supportive tone, while the sender still sees the result.

### D-312 — Suggested scores for S-01 and S-15
Accepted · 2026-11-09 · P5-09 · owner's choice, refines D-222 and D-223
For the S-01 and S-15 checklists, SIGRAK suggests 0 when the data is missing and 1 ("ada, belum diperiksa") when it exists, taken from the completeness service (D-263). The principal raises an item to 2 ("sesuai") or changes it; an item counts toward the total only after the principal confirms it.
Reason: saves the principal's time while the judgment stays with them.

### D-313 — Findings bank per supervision item
Accepted · 2026-11-09 · P5-09 · owner's choice
Each instrument item has ready phrases for strengths, priorities, and RTL, seeded by Tim SIGRAK as editable template data (D-221); a school can add its own. The principal picks a phrase and edits it. No AI.
Reason: faster, more consistent feedback.

### D-314 — Console access stays role-based, with a read-only matrix
Accepted · 2026-11-09 · console · owner's choice, keeps D-167
Staff roles stay fixed in code (admin, support, content). Every new console feature names the roles that may use it, and a read-only "Hak akses" page shows the matrix. An editable permission matrix is rejected.
Reason: a small team; fixed roles are testable and safe.

### D-315 — AI model per purpose set in the console
Accepted · 2026-11-09 · console, with P5-04 · owner's choice, amends D-117
An admin sets the active model and an ordered fallback list per purpose (not per template) in the console. `config/ai.php` holds the starting values, and every change is logged. A test button calls the model with a Tim SIGRAK key, never a teacher's key.
Reason: model names change often; switching should not need a deploy.

### D-316 — Kesehatan Sistem page
Accepted · 2026-11-09 · console, with P5-04 · owner's choice
One Filament page shows queue sizes and failed jobs (Horizon), free disk, generations per day, AI errors per day, and the last backup time. Alerts stay with the server monitoring (`server-monitoring.md`).
Reason: one place for staff to see the system's state.

### D-317 — Curriculum console: compare, publish, roll back, impact, JP audit
Accepted · 2026-11-09 · console · owner's choice, extends D-229
Staff compare a draft curriculum version with the active one, publish it, roll it back to the previous version, and see an impact report (schools and RPMs affected). A JP audit flags RPMs whose JP no longer match the latest ATP; the teacher gets a Kabar and decides. No AI draft from Buku Guru (SIBI licence; books are references only).
Reason: curriculum files change between years, and publishing must be safe and visible.

### D-318 — Read-only Arsip Tahun Ajaran; retention set in the console
Accepted · 2026-11-09 · console, school app · owner's choice
A closed academic year becomes read-only: it can be viewed and downloaded but not changed. Retention per data type is set by an admin in the console, starting from the current values (D-118 usage logs 1 month, D-153 last 5 revisions); every change is logged. Floors the console enforces: rapor, leger, graduation data, and buku induk are never deleted automatically, and supervision and PKKS results are kept at least 5 years.
Reason: older years stay available for accreditation, and storage stays within the 320 GB disk (D-096).

### D-319 — Targeted Kabar from Tim SIGRAK; kecamatan on schools
Accepted · 2026-11-09 · console · owner's choice, extends D-169
A Kabar from Tim SIGRAK targets all schools, a kabupaten, a kecamatan, a level (SD/MI), and/or roles, with a start and end date. `schools` gets a district (kecamatan) column filled from the NPSN directory, so console lists of schools and users filter and group by kabupaten and kecamatan.
Reason: regional notices reach only the people they concern.

### D-320 — Help-request status
Accepted · 2026-11-09 · console · owner's choice
Minta Bantuan requests have the statuses Baru, Diproses, Selesai, visible to the sender. Staff change the status in the console, and the sender gets a Kabar.
Reason: users see that their request is handled.

### D-321 — "Versi baru tersedia" banner
Accepted · 2026-11-09 · P5-07 · owner's choice
After a new release, the app (PWA, including offline Di Kelas) shows "Versi baru tersedia, muat ulang". The offline queue is synced before the reload.
Reason: prevents stale assets and lost offline data after a deploy.

### D-322 — Legacy app features not carried over
Accepted · 2026-11-09 · P4-13 · from the usulan-01 walk-through, consistent with earlier decisions
Not re-adopted from the legacy app: the free generator and Bahan Tayang, SMP–SMA levels, a mandatory AI key at activation, default passwords, browser-screenshot PDFs (html2canvas/html2pdf), the 15-minute idle logout, exam mode (D-026), AI reading student handwriting (D-287), public verification pages (D-010, D-281), and the terms siswa, P5, taraf serap, and teguran. Deferred to a later proposal (usulan-02): an AI draft score for S-04 Telaah RPM and "Cocokkan berkas" (comparing an uploaded PDF with an approval).
Reason: these either conflict with existing decisions or the 2025–2026 rules, or need more testing first.

### D-323 — One Jawa Tengah reference calendar, 6-day version, for Kudus, Pati, and Jepara
Accepted · 2026-11-09 · P5-03i · owner's choice, refines D-254, D-256, and D-257
Kudus, Pati, and Jepara follow the provincial kaldik (Peraturan Kepala Dinas Pendidikan Provinsi Jawa Tengah 400.3/23862/2026, in `docs/sources/`), and their schools use a 6-day week. Tim SIGRAK builds one Jawa Tengah reference calendar from the guide's **6-day** dates, owned by a province-level region; every regency of the province uses it unless that regency gets its own calendar. The 5-day dates are not imported. Where the guide disagrees with itself, the owner's reading applies: Maulid Nabi Muhammad SAW is on 25 August 2026. The guide is the initial data. When the government publishes newer holiday or cuti bersama dates (for 2027 the guide lists estimates), an admin replaces them in the console (D-253). Each school still copies the calendar and adds its own events (D-256). Acceptance check for `EffectiveDays` (D-259), SD/MI with a 6-day week: 254 effective days (134 in semester 1, 120 in semester 2), as in Lampiran XIII.
Reason: the three regencies use the provincial guide with a 6-day week; one calendar is kept once, and schools keep their own context.

### D-324 — PKKS instruments from the rules now, regional variants later
Accepted · 2026-11-09 · P4-08 · owner's choice, refines D-308
The PKKS / supervisi manajerial instruments are drafted from current rules only; no local sample is awaited. They are kept as templates (D-221) that can have a variant per region (for example Kudus, Pati, Jepara) when a local sample or a regional rule differs; a school uses its region's variant, or the default.
Reason: the rules are enough for a first draft, and regional formats can be added as data later.

### D-325 — Answer-sheet layout follows the schools' own format
Accepted · 2026-11-09 · P5-08 · owner's choice, refines D-287
The printed LJK looks like the sheet schools already use: a header with Mata Pelajaran, Kelas, Nama, and Nomor; PG items in two columns of letter boxes (A–D, or A–E when used) that the student crosses out; numbered lines for Isian Singkat and Uraian. SIGRAK adds four corner markers and the QR (D-287). The scanner accepts a cross or a filled box; two marks or a scribbled correction are marked yellow for the teacher to check. The other scanned forms (PG Kompleks, Benar-Salah, Menjodohkan) use the same letter-box style. Isian and Uraian take a per-item score that can be above 1 (partial credit), entered in the score grid.
Reason: students and teachers already know this sheet; SD students need no training for bubbles.

### D-326 — Console AI adapters: OpenAI-compatible and Anthropic; teachers stay on Gemini
Accepted · 2026-11-09 · console, with P5-04 · owner's choice, extends D-116 and D-315, keeps D-023 for teacher keys
Besides Gemini, `app/AI/` gets two adapters: OpenAI-compatible (base URL, key, model; covers OpenAI, OpenRouter, DeepSeek) and Anthropic (Claude models through the Anthropic API). In the console an admin can test a key of any of these providers and pick them as model or fallback per purpose (D-315), with every change logged. Teacher keys stay Gemini only (D-023); teacher keys from other providers are deferred to usulan-02, after every template has been tested per provider. Before a purpose that runs on teachers' generations uses a Tim SIGRAK key from another provider, the owner decides who pays for it.
Reason: more providers for Tim SIGRAK's own work without changing the teacher's free-tier model.

### D-327 — Kudus supervision instruments as a regional variant
Accepted · 2026-11-09 · P4-08, P5-09 · owner's choice, applies D-324, extends D-221…D-223
The owner's Kudus instrument set (`docs/sources/Instrumen_Supervisi_Kudus_Pra_Observasi_Pasca.docx`) becomes the Kudus variant of these instruments; other regions may choose it, and the standard SIGRAK instruments (D-222, D-223) stay the default elsewhere:
- Telaah administrasi pembelajaran (S-01): 24 components for guru kelas, 18 for guru mapel; this is also the Format Kudus profile of Berkas Administrasi (D-278).
- Telaah perencanaan pembelajaran (S-04 and S-07): 20 items, Ya/Tidak.
- Observasi (S-08): 27 items in parts A–F, scored 1–4 with an evidence column.
- Pasca-observasi (S-09): 5 reflection questions, feedback on 5 aspects, and an RTL table.
- Telaah administrasi kepala sekolah: 24 components, filled by the pengawas (D-308, D-328).

Item order and scales stay as in the source (1–5, Ya/Tidak, 1–4), with final score = score ÷ maximum × 100 and the bands 91–100 Sangat Baik, 81–90 Baik, 71–80 Cukup, below 71 Kurang. The maximum is always computed as items × top score: the source's "Skor Maksimal (24)" for guru mapel becomes 90. Labels use SIGRAK terms (RPM for RPP/MA, kokurikuler for P5, jurnal mengajar for agenda harian, kehadiran for absensi), with the content of each item unchanged.
Reason: Kudus schools and supervisors already use these forms; the regional-variant model keeps the national default clean.

### D-328 — Principal administration items without SIGRAK data
Accepted · 2026-11-09 · P5-09 · owner's choice, keeps D-220
In the principal's administration review, items SIGRAK does not produce are scored by hand by the pengawas, and the school may upload an evidence file for each (optional). This covers dokumen pendirian, RKAS, SPJ BOS, surat masuk/keluar, buku klaper, buku induk guru, buku tamu, and similar. SIGRAK builds no RKAS, BOS, or letter module for this (D-220; O3 stays deferred). Items backed by SIGRAK data get a suggested score that the pengawas confirms, as in D-312: kalender pendidikan, KSP, 7 KAIH, supervision documents, school inventory, mutasi, ekstrakurikuler, new intake and graduation.
Reason: the pengawas can review everything on the form without SIGRAK taking on finance or correspondence.

### D-329 — LKPD v1 unchanged; patterns come in waves
Accepted · 2026-11-09 · P4-16 · owner's choice (Q1)
The v1 LKPD template (P5-05) stays as decided: the current output plus the D-296 blocks. Its output schema gains only a `pattern` field, so the pattern library (*Bentuk LKPD*) in `docs/drafts/lkpd-exploration.md` can arrive in later waves without a migration.
Reason: keeps v1 on schedule; the patterns need their own schemas, layouts, and seed data.

### D-330 — LKPD QR codes point straight to the third-party URL
Accepted · 2026-11-09 · P4-16 · owner's choice (Q4), keeps D-010
A `qr_link` block encodes the allowlisted third-party URL itself. There is no public SIGRAK redirect route. A dead link on paper is handled by the stored title and description and the printed no-device fallback task, and by a link checker that warns the teacher in the app.
Reason: no new public route; D-010 stays whole.

### D-331 — Interactive LKPD runs online; a student session is planned
Accepted · 2026-11-09 · P4-16 · owner's choice (Q5), opens a later exception to D-010
Interactive LKPD steps (embeds, H5P, self-check) run online, in the teacher's app and in the IFP present session. A student session for working on a device comes later, with its own design task. It is session-scoped like the exam site (D-027): no student accounts, a code or card per session, no student data beyond the session, and no AI facing students (Gemini terms bar users under 18). Until then, answers come back through paper (Pindai LJK, D-286; Kartu Jawab, D-174) or the teacher's checklist.
Reason: owner's choice; the exam site already shows how a session-scoped student access can stay inside the privacy rules.

### D-332 — LKPD Wave 1: nine patterns
Accepted · 2026-11-09 · P4-16 · owner's choice (Q2)
The first patterns after v1 are Detektif Teks, Peta Cerita, Data Kelasku, Etnomatematika, Misi Petualangan, Tiga Jalur, Tiket Keluar, Kotak Terkunci, and Pikir Komputasional Tanpa Komputer. The Kotak Terkunci code is checked on the teacher's sheet, in the app, or on the IFP (online, D-331).
Reason: owner's choice; adds the escape-room pattern and unplugged computational thinking (strong evidence, KKA through kokurikuler, D-208).

### D-333 — Local context profile per school, seed lists first for Kudus, Jepara, Pati
Accepted · 2026-11-09 · P4-16 · owner's choice (Q3)
A school sets a *Konteks Daerah* profile: local language, foods, crafts, places, festivals, occupations, stories, and themes to avoid. Seed lists start with Kudus, Jepara, and Pati and are reviewed by Tim Validasi SIGRAK (D-113). The AI uses only items from the profile and the seed list, and the teacher confirms them. Items unsuitable for children (for example tobacco) are excluded in the seed data.
Reason: local context has the most consistent research support; the AI must not invent local facts.

### D-334 — Self-hosted H5P player and 3D/AR viewer
Accepted · 2026-11-09 · P4-16 · owner's choice (Q6), within D-033
Wave 3 bundles `h5p-standalone` (MIT, not the GPL H5P PHP library) and Google `model-viewer` (Apache-2.0), with a curated library of CC-BY 3D models and H5P packages on local disk (D-114). No student account and no third party at view time.
Reason: AR and interactive content without building an engine and without sending students to outside services.

### D-335 — LKPD school presets stay inside one school
Accepted · 2026-11-09 · P4-16 · owner's choice (Q7)
A teacher can offer an adapted LKPD to the school library; other teachers of the same school start from it without AI. Sharing across schools is not planned.
Reason: no moderation or licence questions across schools.

### D-336 — All LKPD features are core
Accepted · 2026-11-09 · P4-16 · owner's choice (Q8), under D-211
Every LKPD feature (patterns, blocks, local context, school presets, QR, embeds, interactive steps, and the step that continues in Game kelas) starts as a core Sigrak Guru feature. Game kelas templates stay in the Game kelas add-on (D-158, D-175). The console mapping can still change this (D-211).
Reason: owner's choice; the LKPD is a daily teacher tool.

### D-337 — Third-party embeds only where the licence clearly allows it
Accepted · 2026-11-09 · P4-16 · owner's choice (Q9)
A third-party tool (PhET, GeoGebra, Assemblr EDU, others) is embedded only when its licence clearly allows use inside a paid product like SIGRAK. Otherwise SIGRAK shows a link or QR only (D-330). The licence check is recorded in the embed allowlist.
Reason: PhET and GeoGebra restrict commercial use; Assemblr EDU terms are unverified.

### D-338 — Staff powers fixed in code, scopes editable
Accepted · 2026-11-09 · P4-17 · owner's choice (RQ1), keeps D-314
What each staff role may do stays in code. The admin edits only each staff member's scope: region (province, kabupaten, kecamatan) or a named school list, level (SD/MI), and field (subject group or religion). Scope changes are logged.
Reason: fixed powers are testable and safe; scopes change with the team.

### D-339 — Eleven staff roles, all active
Accepted · 2026-11-09 · P4-17 · owner's choice (RQ2), amends D-167
Staff roles: Pemilik (`owner`), Admin (`admin`), Validator (`validator`), Penyusun Konten (`content`), Kurator Kurikulum (`curriculum`), Support (`support`), Pemasaran (`marketing`), Penjualan (`sales`), Koordinator Wilayah (`regional_lead`), Keuangan (`finance`), Teknis (`engineer`). All are available from the start. A person may hold several roles, each with its own scope (`staff_role_assignments` replaces `staff_users.role`). Prices and AI models move from admin to owner. Powers per role: `docs/drafts/internal-roles.md`. A validator never signs off their own work.
Reason: more content, curated data, and a regional sales network need clear, limited powers.

### D-340 — Payments are confirmed by Keuangan or the owner
Accepted · 2026-11-09 · P4-17 · owner's choice (RQ3)
Penjualan records a payment claim; Keuangan confirms it, or the owner while no one holds the Keuangan role.
Reason: the person who records a payment is not the one who confirms it.

### D-341 — Support may view school data under the school MoU
Accepted · 2026-11-09 · P4-17 · owner's choice (RQ4, refined the same day)
Support can open a school's view read-only without a separate grant, because the MoU signed with each school says Tim SIGRAK support helps the school, including viewing its accounts and data. Religion and health data stay hidden (D-036, D-215). No access log is shown to schools. Tim SIGRAK keeps one internal row per support session (staff member, school, start and end time), not per page view.
Reason: many schools will ask for help; the MoU covers access, and a per-session row is small enough to keep.
### D-342 — Partner programme: teacher communities and resellers
Accepted · 2026-11-09 · P4-17 · owner's choice (RQ5)
Partners (*Mitra*) are teacher communities, including KKG and KKM, and resellers. They get a referral code and a partner report, not a console account. Terms and rewards are designed in a later task.
Reason: owner's choice; partners reach schools in their region.

### D-343 — 2FA with an authenticator app (TOTP)
Accepted · 2026-11-09 · P4-17 · owner's choice (RQ6, refined the same day)
Two-factor sign-in uses a 6-digit code from an authenticator app (TOTP, for example 482 913), built with a mature library (Laravel Fortify), not our own engine. It is required for the Pemilik and Admin roles now and optional for other staff. It can later be offered to every account, teachers included, since teachers already know this flow. There are no recovery codes: a user who loses access asks a SIGRAK admin, who resets the 2FA so the user can enrol the authenticator again. Every reset is logged.
Reason: familiar to teachers, standard, and simple to support.
### D-344 — Peta Asesmen is one curated dataset
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ1)
The assessment taxonomy (domains, purposes, methods, media, scoring, uses) lives in `docs/data/assessment/` with `status: draft` until Tim Validasi review, plus `docs/drafts/assessment-landscape.md`. The owner's MAP-01…MAP-08 codes are kept as section references, not separate documents. Every instrument carries its tags.
Reason: one source; tags let one instrument serve several purposes.

### D-345 — Assessment landscape design is its own task
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ2)
The assessment landscape gets its own design task (P4-19), separate from P4-06 (assessment and rapor extensions). The two link where they meet (diagnostic, item analysis, rapor).
Reason: owner's choice; the landscape is wider than the rapor extensions.

### D-346 — New assessment instrument templates
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ3), follows D-024
New templates, one per kind: Rubrik Kinerja (practice, demonstration, project, product, MI ibadah practice), Observasi DPL (behaviour indicators per dimensi profil lulusan, observed several times, shown as trends), Cek Kesiapan Awal (grade 1 and new students; result is a profile, not a score), Akomodasi Murid (per-student accommodations), and self and peer assessment cards.
Reason: covers what written tests cannot measure.

### D-347 — Assessment AI without student data; names read on the device
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ4, refined the same day), keeps D-023
Teacher-side AI makes instruments, tags misconceptions, and drafts feedback from class patterns. Student names on paper are read by text recognition on the teacher's device and matched to the roster there, so the teacher need not check names by hand. Multiple-choice answers are read by OMR on SIGRAK's own server (Pindai LJK, D-286). The text of an essay answer, without any student data, may be sent to Google AI for scoring help, and the teacher decides the score. Reading handwriting and voice with AI comes later. No student personal data goes to an AI provider (D-023).
Reason: saves teachers' checking time while staying inside the Gemini terms and UU PDP.
### D-348 — GPK is a teacher assignment
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ5)
A guru pendamping khusus is a teacher assigned to certain students, not a new school role. School roles stay teacher, principal, operator, supervisor.
Reason: keeps school roles simple.

### D-349 — Observation and rubric entry works offline
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ6), amends D-016
Observation and rubric results are entered on the phone in Di Kelas offline and sync by `client_uuid`, like attendance.
Reason: practice and observation often happen outside the classroom without signal.

### D-350 — Sensitive assessment results follow the health-data rule
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ7), applies D-215
Social-emotional, special-needs, and interest-or-talent results are visible to the class's teachers (and the assigned GPK), the operator, and the principal only, never sent to AI, and never shown as permanent labels or diagnoses. Supervisors see class or school totals only.
Reason: children's sensitive data and the risk of labelling.

### D-351 — Assessment Wave A scope
Accepted · 2026-11-09 · P4-18 · owner's choice (AQ8)
Wave A: Peta Asesmen data v1 (literacy, numeracy, DPL, readiness domains; purposes; methods), tags on Bank Soal and LKPD, Rubrik Kinerja, Observasi DPL with phone entry, Cek Kesiapan Awal, and misconception tags on distractors. P5-08 (Asesmen v1) is unchanged.
Reason: paper-first, low risk, builds on v1.

### D-352 — SIGRAK's position: grounded in policy, free to innovate
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ1)
SIGRAK's position is *Berpijak pada kebijakan, leluasa berinovasi*. National standards fix the what and the limits (outcomes, structure, minimum document content, child protection). The school and the teacher choose the how. SIGRAK gives them tools for new ways of learning, and every innovation can show which national element it serves (`docs/drafts/policy-innovation-layers.md` §1–§3).
Reason: the 2025–2026 rules lock outcomes and structure, not methods; aligning with them makes innovation applicable and accountable.

### D-353 — Rule strength decides how the app reacts
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ2), extends the `force` tags of D-203
Every rule carries a strength: `mandatory`, `minimum`, `maximum`, `recommended`, `not_recommended`, `example`, `school_discretion`, `teacher_discretion`, `free`. The app blocks only clear L1/L2 mandatory rules, warns below a minimum or above a maximum (as D-232), shows a hint for recommended and not recommended (as D-206), and leaves the rest free. Defaults that are only examples are labelled "Contoh". Layers L0–L6 follow §2 of the P4-21 draft.
Reason: a guardrail, not a gate; schools that innovate are never stopped.

### D-354 — Regulations and rule sets are versioned data
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ3), extends D-229 and D-317
Target model: `regulations` (legal documents with issue, promulgation, and effective dates, status, verification level), `regulation_relations` (amends, revokes, implements, references), `rule_sets` (a versioned package per domain with `applies_from_school_year`, level and region scope, transition, supersedes), and `rules` (key, strength, value, source reference). `curriculum_versions` becomes the curriculum kind of rule set by adding columns. The policy in force is resolved from school year, level, and region. A document stores the rule-set codes it was made under and never changes silently. Rules published mid-year apply from the next school year unless the regulation says otherwise.
Reason: rules change every year; schools and documents must stay traceable.

### D-355 — Minimal versioning before P5-04
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ4)
Before P5-04 (content core): `curriculum_versions` gets `effective_on`, `applies_from_school_year`, and `supersedes_id`; a policy-profile resolver replaces the `config/curriculum.php` `allocation_versions` pointer (D-230 behaviour unchanged); the Profil Jenjang data (D-356) exists. The full `regulations` and `rules` tables, the console for every domain, and Kabar Regulasi come with P5-09 or their own slice.
Reason: an RPM must record its rule set from the first one made.

### D-356 — Profil Jenjang: SD and MI as data over one core
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ5)
One codebase with a shared core. Each level (`sd`, `mi`) has a versioned Profil Jenjang record: ministry, rule-set codes per domain, character frameworks on (MI adds KBC), RPM extra fields, rapor export target (e-Rapor or RDM), wali kelas JTM defaults (D-212), external assessments. Code reads the profile and never branches on `level == mi`. New levels become new profiles.
Reason: SD and MI share the national standards and differ in a few domains; data keeps that cheap and safe.

### D-357 — Master taxonomy
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ6)
One master taxonomy in `docs/data/taxonomy/` (`status: draft` until Tim Validasi review, D-113) with facets A Policy, B Curriculum structure, C Profile and character, D Learning, E Assessment (Peta Asesmen, D-344), F Content, G Context, H Administration. Each node has a stable code, Indonesian label, parent, level scope, source, strength, status, and version, plus crosswalks between versions (e.g. Profil Pelajar Pancasila → 8 DPL, P5 → kokurikuler). Closed lists only where a rule fixes them; schools may add local items to open lists. Seed data is task P4-22; it joins the legacy app catalogs (P4-15) instead of duplicating them.
Reason: one name and code per concept lets features tag content and show which standard an innovation serves.

### D-358 — Dasar Kebijakan panel and Cek Keselarasan
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ7)
Every document shows a short Dasar Kebijakan panel (CP, TP, DPL, principles, regulation and rule-set version), read from its taxonomy tags. One checker, Cek Keselarasan, returns block, warning, and hint lines by rule strength (D-353) on save and in principal and supervisor review; it never rewrites the teacher's work. Both start with the RPM in P5-04 and grow with each slice.
Reason: makes innovation accountable without slowing teachers.

### D-359 — KMA 512/2026 is the KBC source
Accepted · 2026-11-09 · P4-21 · owner's choice (PQ8), updates the source of D-126
Keputusan Menteri Agama No. 512 Tahun 2026 (Pedoman Kurikulum Berbasis Cinta, set 31 March 2026, in force on that date; official PDF supplied by the owner, `docs/sources/KMA_No_512_Tahun_2026_Pedoman_Kurikulum_Berbasis_Cinta.pdf`) replaces SK Dirjen Pendis 6077/2025 as the KBC source. It keeps Panca Cinta (cinta Tuhan, cinta diri dan sesama manusia, cinta lingkungan, cinta ilmu, cinta tanah air) and adds six nilai dasar (spiritual, personal, sosial, ekologis, kebangsaan, intelektual), six principles, 12 suggested methods, 10 assessment instruments, and three levels of practice (merintis, berkembang, mekar). KBC is not a subject; results are reported through Akhlak or Budi Pekerti and the attitude notes, never as labels or rankings. D-126 stays as it is (MI only). The KMA also covers religion teachers in general schools, SD included; whether SIGRAK offers KBC fields to SD religion teachers is an open question (`docs/drafts/open-questions.md`).
Reason: the official KMA supersedes the Dirjen guide; the SD scope needs the owner's call.

### D-360 — The repository drops the legacy brand
Accepted · 2026-11-09 · owner's choice
Current files no longer name the owner's previous app. English text says "the legacy app"; Indonesian text says "aplikasi lama"; curriculum data notes say "SIGRAK". Files with the old name were renamed (e.g. `docs/drafts/legacy-upgrade-01.md`, `app/Curriculum/Import/Formats/KmSdTopics.php`). As an exception to "kept as received", owner sources in `docs/sources/` may be renamed and edited for the brand only; the old functionality PDF is kept under a new name with its content unchanged. Git history is not rewritten. Decision entries before this one keep their meaning; their wording was updated in place for the brand only.
Reason: the owner wants the repository focused on the new brand.

### D-361 — Developer handbook
Accepted · 2026-11-09 · owner's choice
`docs/final/developer-handbook.md` tells the whole SIGRAK process end to end for developers (position, users, policy layers, the flow from school setup to rapor and portals, architecture, data, AI, exports, operations, way of working, decision map, glossary), marking what is built and what is still design. Its Indonesian PDF, `docs/pdf/developer-handbook.pdf`, is the one complete reading for a new developer. Update it when a phase ends or a major decision changes the flow.
Reason: the owner wants one complete reading for developers.

### D-362 — Research digest
Accepted · 2026-11-09 · owner's choice
`docs/final/research-digest.md` (with its Indonesian PDF) collects every research finding so far: regulations, field and journal studies, benchmarks, technical research, with source, date checked, verification level, implication, and related decisions. Before new research, check it; after research, add the findings to it.
Reason: avoid repeating research across sessions.
