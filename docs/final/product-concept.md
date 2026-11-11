# SIGRAK Product Concept

- **Status:** final (approved 2026-10-31, D-123)
- **Task:** P1-02; later decisions applied in P4-24 (D-210…D-371)
- **Last updated:** 2026-11-11 (guru kelas / wali kelas, menu placements, D-383, D-385, D-386)
- **Sources:** [`Konsep_Awal_Produk_SIGRAK.md`](../sources/Konsep_Awal_Produk_SIGRAK.md), [`Rebranding_dan_Perombakan_Fitur_Aplikasi_Lama.md`](../sources/Rebranding_dan_Perombakan_Fitur_Aplikasi_Lama.md), [`Dokumentasi-Fungsionalitas-Aplikasi-Lama.pdf`](../sources/Dokumentasi-Fungsionalitas-Aplikasi-Lama.pdf)

## Purpose

This document defines what SIGRAK is, who uses it, and what v1 contains. It is the product reference for every later design and build task. Two companion drafts cover the technical side: [`content-engine.md`](content-engine.md) and [`ai-provider-engine.md`](ai-provider-engine.md). Decisions are logged in [`../agent/DECISIONS.md`](../agent/DECISIONS.md) (D-007 to D-037 for the first version; later rows cite their own IDs).

## Summary

SIGRAK (working name) is a web app for SD/MI teachers and school leaders. It does two jobs, and both ship in v1:

1. **Content creation with AI help.** Teachers make RPM, question banks, LKPD, classroom games, and TKA practice packages. They pick a template and answer pedagogical questions. They never write a prompt. Developer-built templates fix the structure and design, and AI only fills in the content. There is no generic "make anything" generator, because a teacher can already get that from Gemini directly (D-024).
2. **School administration.** Attendance (*Kehadiran*), teaching journal, student notes, assessment and report cards, class data, Berkas Administrasi, and the principal and supervisor portals carried over from the legacy app and upgraded (see [Feature status](#feature-status)).
3. **TKA practice exam (CBT).** A computer-based exam that works like the national Tes Kemampuan Akademik for grade 6. Students take it on a separate exam site with an exam card and a proctor token (D-026, D-027).

Students ("murid" in UI copy, D-210) are not users of the app. Every page sits behind login, and there are no public pages. The one place students touch is the exam site, and only with a one-session exam card.

## What changed from the legacy app notes

| Topic | Legacy app notes (29 Oct) | SIGRAK now | Decision |
|---|---|---|---|
| Product core | Administration ("Administrasi beres sambil mengajar") | Content creation **and** administration | D-009 |
| Stack | CodeIgniter 4 + MySQL 8 + vanilla JS + PWA | Laravel + MySQL 8 + Inertia/Svelte + Tailwind/TailAdmin + Filament. Vanilla JS is used only for the game runtime | D-003, D-007, D-008 |
| Users | Teachers, principal, supervisor, operator, super admin | Same roles, but **students are explicitly records, never users**. Login-only, no public pages | D-010 |
| AI keys | One Gemini key per teacher, optional school fallback key | **Gemini free tier only.** Several keys per teacher from the teacher's own Google projects, with round robin and failover, at the teacher's own risk. No school or platform key | D-012, D-023 |
| AI generation | Prompt per feature | One dedicated template per content type, each with its own wizard and job. No generic generator. Teachers never write prompts | D-013, D-014, D-024 |
| Exams | Bank Soal "Mode Ujian" | Mode Ujian inactive for now. New **TKA practice exam (CBT)** with exam cards and proctor tokens | D-026, D-027 |
| Games | None | Quiz Battle in v1, played in class (present mode) or as a downloaded offline HTML file | D-011, D-014 |
| Market | Kudus only, SD/MI | SD/MI anywhere in Indonesia. Kudus is the pilot and supplies the defaults | D-018 |
| Teacher menu | Hari Ini · Di Kelas · Rencana · Asesmen · Kelas Saya | Hari Ini · Di Kelas · **Buat** · Asesmen · Kelas Saya | D-021 |
| Migration | Staged Firebase → SQL with `legacy_id` | Fresh build. Only curriculum data is imported, and teachers are re-invited | D-020 |
| Rules and terms | Pre-2025 terms (siswa, P5, taraf serap, Absensi, teguran) | The 2025–2026 rules and terms: murid, kokurikuler, daya serap, Kehadiran, Pengingat | D-210, D-302, D-311, D-322 |

Everything else agreed in the legacy app notes still holds: text-based PDF with per-school print settings, the "Buka di Canva" button, the material validation flow, and Di Kelas working without signal. Not carried over (D-322): the free generator and Bahan Tayang, SMP–SMA levels, a mandatory AI key at activation, default passwords, browser-screenshot PDFs, the 15-minute idle logout, exam mode, AI reading student handwriting images (D-287; D-370 later reads handwriting on the phone and sends only text), public verification pages, and the terms siswa, P5, taraf serap, and teguran. Deferred to usulan-02: an AI draft score for S-04 Telaah RPM and "Cocokkan berkas".

## Positioning and principles

**Category:** an AI-assisted platform for making teaching content, joined with teacher administration, for primary schools.

Principles, in priority order:

1. **Teachers answer questions they understand pedagogically, never prompts.** Every AI feature is a wizard: class, subject, topic, learning goal, students' condition, duration, difficulty. The system already knows the JP, meeting count, CP text, and school identity, so it never asks for them.
2. **Developers build the structure, AI fills the content.** A template fixes the layout, the game mechanics, and the rules. AI writes the questions, explanations, and activities. This makes results consistent in quality, layout, and behavior.
3. **The teacher stays the author.** Every AI result can be edited before use. Any single section can be revised with AI without redoing the whole document.
4. **Data the system already has is never asked for again.** Class and subject come from the timetable. Curriculum, JP, and dates come from the curriculum data and the school calendar.
5. **Works on a cheap phone with weak signal** where it matters: Di Kelas works offline, and a downloaded game file plays offline.
6. **School data belongs to the school. AI keys and AI usage belong to the teacher.** Student personal data is never sent to the AI (free-tier prompts may be read by Google's reviewers; see [`ai-provider-engine.md`](ai-provider-engine.md#privacy-rules)).
7. **Only build what Gemini alone cannot give.** Every AI feature must add structure, curriculum data, school formats, or classroom delivery that a teacher would not get by chatting with Gemini.

## Policy foundation

**Position: *Berpijak pada kebijakan, leluasa berinovasi*** (D-352). National standards fix the what and the limits: outcomes, structure, minimum document content, child protection. The school and the teacher choose the how. SIGRAK gives them tools for new ways of learning, and every innovation can show which national element it serves. Details: [`policy-innovation-layers.md`](../drafts/policy-innovation-layers.md).

- **The 2025–2026 rules (D-210).** The rapor has the 12 minimum components (Fase A may report without numbers); kokurikuler is assessed on the 8 dimensi profil lulusan (DPL); ekstrakurikuler with Pramuka is offered; workload counts JTM; class agreements and positive discipline; teacher reflection at least once a semester; transfers follow Permendikdasmen 3/2025. UI copy says **murid**; "siswa" stays only in table names and legacy quotes.
- **Rule strength (D-353).** Every rule carries a strength: `mandatory`, `minimum`, `maximum`, `recommended`, `not_recommended`, `example`, `school_discretion`, `teacher_discretion`, `free`. The app blocks only clear national mandatory rules, warns below a minimum or above a maximum, shows a hint for recommended and not recommended, and leaves the rest free. Example defaults are labelled "Contoh". A guardrail, not a gate.
- **Versioned rules (D-354, D-355).** Before P5-04, `curriculum_versions` gains `effective_on`, `applies_from_school_year`, and `supersedes_id`, and a policy-profile resolver (school year, level, region) replaces the `config/curriculum.php` pointer. A document stores the rule-set codes it was made under and never changes silently; rules published mid-year apply from the next school year. Target later (P5-09 or own slice): `regulations`, `regulation_relations`, `rule_sets`, `rules`, a console for every domain, and Kabar Regulasi.
- **Profil Jenjang (D-356, D-368).** SD and MI share one core. A small versioned record per level (`sd`, `mi`) names the ministry, rule-set codes per domain, character frameworks (MI adds KBC; SD KBC fields are off by default and an admin can switch them on), RPM extra fields, the rapor export target (e-Rapor SD or RDM), wali kelas JTM defaults (D-212), and external assessments. Code reads the profile and never branches on the level.
- **Master taxonomy (D-357).** One code per concept in `docs/data/taxonomy/` (facets Policy, Curriculum structure, Profile and character, Learning, Assessment, Content, Context, Administration), with crosswalks between versions (e.g. P5 → kokurikuler). Features tag their content with it.
- **Dasar Kebijakan and Cek Keselarasan (D-358).** Every document shows a short panel of the CP, TP, DPL, principles, and rule-set version it rests on. One checker returns block, warning, and hint lines by rule strength on save and in principal and supervisor review, and never rewrites the teacher's work. Both start with the RPM in P5-04.

## Access model

- **Login-only.** The only screens without a session are login, accepting an invitation, and password reset. There is no landing page, no public share link, and no public profile inside the app. A marketing site, if one is made, is a separate project.
- **Students are records, not accounts.** A student has a name, NISN, class enrollment, attendance, notes, and scores, all managed by teachers. Students never log in to the app.
- **The only exception is the TKA practice exam** (D-027). Students sign in to a separate exam site (for example `ujian.<domain>`) with an exam card (username + password) that is valid for one exam session only, plus the current proctor token. The exam site shows exam questions and nothing else, and it never calls the AI. Gemini's terms forbid using the API in a service likely to be used by people under 18.
- **Games reach students in two ways** (D-011):
  - *Tayangkan* (present mode): the teacher runs the game fullscreen from their logged-in session on a projector, TV, or their own device.
  - *Unduh game*: one self-contained HTML file that plays offline without login and makes no network calls. Teachers can share it, for example on WhatsApp. It holds only the teaching content, never student data.
- **Parents are not users.** Messages to parents are ready text that the teacher opens in their own WhatsApp through a plain `wa.me` link, with a contact log: no WhatsApp API and no links into SIGRAK (D-215). A parent portal is shown as upcoming (D-220).
- A session-scoped student session for interactive LKPD is planned later, built like the exam site (D-331).
- Accounts are created by invitation. Nobody signs up openly into a school.

## Roles

| Role (UI name) | Who | Main jobs in SIGRAK | Device |
|---|---|---|---|
| Guru kelas (wali kelas; the UI says "guru kelas" in SD and "wali kelas" in MI, D-383) | Teaches the class subjects of one class: Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS, Seni dan Budaya, and the others not listed for guru mapel | Make content, daily Kehadiran, journal, assessment, report cards, class data, homeroom duties (rapor notes, parent messages, kenaikan, graduation, savings) | Phone, home laptop |
| Guru mapel (khusus) | Teaches one special subject across classes: **PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, Pendidikan Agama** (in SD one subject per religion: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu; in MI the Kemenag subjects Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab, always taught by guru mapel) | Make content, reused across classes. Journal, student notes, and assessment for their subject (Kehadiran is daily and belongs to the homeroom teacher) | Phone |
| Kepala sekolah | Principal | Watch completeness and content made, validate school curriculum changes, Berkas Administrasi, pengesahan dokumen, Pengingat, supervision, leger sign-off, pembagian tugas, school books, print settings, subscription | Laptop, phone |
| Operator sekolah | School admin staff | Academic year (and Arsip Tahun Ajaran), classes, invite teachers, timetable, calendar, school profile, mutasi, school inventory | Laptop |
| Pengawas / korwil | Supervisor of several schools | Requests sekolah binaan (Tim SIGRAK approves), read-only school totals, final validation of curriculum changes, PKKS of the principal, supervision reports | Laptop |
| Tim SIGRAK | Internal staff | Eleven staff roles with scopes (D-338, D-339): template registry and prompt tuning, curriculum, schools and subscriptions, payments, support, sales. Uses a separate internal console (Filament), not the school app; see [Internal console](#internal-console) | Laptop |

These subject defaults follow how SD/MI staff their classes (D-032). The operator can still assign any subject of a class to a guru mapel, or a special subject to the homeroom teacher when the school has no specialist.

A guru pendamping khusus (GPK) is a teacher assignment to certain students, not a new role (D-348). Wali kelas counts as 2 JTM in SD and 6 JTM in MI by default; the school can change these values (D-212).

A principal, operator, or supervisor who also teaches keeps the five teacher menus and gets a switch to their portal. The principal does not need an AI key unless they make content themselves.

**Peserta ujian (exam participant)** is not a role. A murid gets session-scoped exam credentials for a TKA practice session, and they stop working when the session closes.

## Market

- **Level:** SD/MI, grades 1–6. Grades 7–12 stay hidden until curriculum data for them exists (the current data, SIGRAK-SD-2026-R1 and SIGRAK-MI-2026-R1, covers SD/MI only; D-363). The structure and JP follow Permendikdasmen 13/2025 for SD and KMA 1503/2025 for MI; the differences live in the Profil Jenjang (D-356). Bahasa Inggris and IPAS in grades 1–2 are optional content a school can switch on (D-204). SD offers all six religion subjects (D-034). MI follows the Ministry of Religious Affairs: Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, and Bahasa Arab instead of one PAI subject (D-035). Details are in [`religious-education.md`](religious-education.md).
- **Region:** anywhere in Indonesia (D-018). Local content (*muatan lokal*, for example Bahasa Jawa) and the school calendar (*kaldik*) can be set per region and per school. Kudus is the pilot, so Bahasa Jawa and the Kudus calendar are the default values for Kudus schools only.
- **Name risk:** "Sigrak" is Javanese. It may be less familiar outside Java, which matters now that the market is nationwide. The backup name from the rebranding notes is **Kapur**. The name stays a working name until a PDKI check is done (see Open questions).

## Vision, mission, values

**Vision.** Every SD/MI teacher can prepare teaching material and finish their administration between lessons, so their afternoons go back to students and family.

**Mission.**

1. Turn repetitive administration (RPM, journal, attendance, scores) into short forms that take minutes.
2. Turn content preparation into answering a few pedagogical questions, on top of templates built and quality-checked by the SIGRAK team.
3. Provide curriculum, schedules, and document formats that already follow the rules, so teachers never start from zero.
4. Let principals and supervisors see progress from the same data teachers use, without collecting files.
5. Stay usable on cheap phones and weak signal.
6. Keep school data owned by the school and AI keys owned by the teacher.

**Values.** These are kept from the rebranding notes and adjusted.

| Value | Meaning in the product | Example decision |
|---|---|---|
| Cekatan (quick) | Fewest possible steps. Data the system has is never asked again | Class and subject are filled from the timetable. A wizard has at most 4 steps |
| Jelas (clear) | One term per thing. Each screen answers "what do I do now" | "Unduh", never a mix of Download/DOCX/Simpan Lokal |
| Bisa diandalkan (reliable) | Data is not lost. Documents are print-ready. AI failures are explained | Autosave. Text PDF with the school letterhead. A failed AI key falls over to the next key |
| Membumi (grounded) | Follows how Indonesian primary schools work, region by region | Muatan lokal per region, F4 paper, the official curriculum terms |
| Menghormati guru (respects teachers) | The teacher is the author. AI only helps | All AI output is editable. No prompt writing |

## Brand

| Item | Value |
|---|---|
| Name | SIGRAK (working name). In UI copy it is written "Sigrak" (sentence case rule), and the wordmark is lowercase "sigrak" |
| Meaning | Javanese for lively, quick, energetic |
| Tagline | "Kerja guru, lebih sigrak." |
| Brand promise | "Bahan ajar siap pakai dan administrasi beres, tanpa menulis prompt." (D-017) |
| Tone | A quick, polite fellow teacher. See [`design-system.md`](design-system.md#voice-and-tone) |
| Visual | Calm work tool: one teal brand colour, plain surfaces. See [`design-system.md`](design-system.md) |
| Logo brief | Unchanged from the rebranding notes: two slanted chalk strokes forming a check mark and a step forward. No gradient, shadow, mascot, or batik |

The promise must be visible in the product as three proofs:

1. **Content:** a ready-to-print RPM in four steps, and a playable Quiz Battle from a saved question set in under five minutes.
2. **Administration:** Kehadiran and journal for one class done on a phone in under a minute, with or without signal.
3. **Leadership:** the principal and supervisor see completeness without anyone handing in files.

## Navigation

The teacher app has five menus (D-021). They are a bottom bar on phones and a sidebar in the same order on laptops.

```
Hari Ini | Di Kelas | Buat | Asesmen | Kelas Saya

Hari Ini    Today's schedule, what still needs filling, this week's progress, Kabar (news)
            Siapkan besok, Pusat Tindak Lanjut (own follow-ups)
Di Kelas    Kehadiran, Jurnal, Catatan Murid, observation and rubric entry (works offline)
Buat        Galeri templat: RPM, LKPD, Bank Soal, Paket Soal TKA, Quiz Battle,
            assessment instruments (more games later)
            Kurikulum (CP, TP, ATP, Prota, Promes), as reference and for school changes
            Kokurikuler
Asesmen     Rencana penilaian, Nilai, Pindai LJK, Analisis (per TP), Rapor
            Simulasi TKA: sessions, exam cards, proctor token, results
            "Buat soal" is a shortcut to the Bank Soal template
Kelas Saya  Murid (incl. pindah/keluar, kenaikan), Jadwal, Piket, Ekstrakurikuler,
            Keuangan and Tabungan (school may turn off), Inventaris

Account menu (profile icon): Dokumen Saya (incl. Berkas Administrasi), Kunci AI Saya,
Paket Saya, Akun, Bantuan, Minta Bantuan
```

Portals, reached through the role switch:

| Portal | Sections |
|---|---|
| Kepala Sekolah | Ringkasan, Pantau Guru (Berkas Administrasi per teacher or whole school, Pengingat), Validasi (incl. pengesahan dokumen, leger sign-off), Supervisi, Pusat Tindak Lanjut, Program Sekolah, Pembagian Tugas (JTM, SK), Agenda & Rapat, Buku Sekolah (tamu, humas, notulen), Budaya Sekolah, Data Sekolah, Pengaturan Sekolah (cetak, langganan) |
| Operator Sekolah | Tahun Ajaran (incl. Arsip Tahun Ajaran), Kelas, Guru (undang), Jadwal, Kaldik, Profil Sekolah, Mutasi, Inventaris Sekolah |
| Pengawas | Sekolah Binaan (request a school; Tim SIGRAK approves, D-309), Pantau (read-only, school totals), Validasi, Supervisi (teacher recaps and PKKS of the principal), Pusat Tindak Lanjut, Laporan Kepengawasan |

The menu name is *Kehadiran*, path `/kehadiran`; help search still finds "absensi" (D-302). *Berkas Administrasi* is the semester bundle of a teacher's documents (D-278); Hari Ini shows a shortcut near the end of a semester. *Pusat Tindak Lanjut* is one follow-up list per role, read from existing records: teachers see their own (in Hari Ini, D-385), the principal the school's, the pengawas school-level and PKKS items only (D-310). *Arsip Tahun Ajaran*: a closed year is read-only, viewable and downloadable, inside the operator's Tahun Ajaran menu (D-318, D-386). Ekstrakurikuler and Tabungan sit in Kelas Saya (D-386). *Supervisi* in the pengawas portal covers the principal: PKKS instruments drafted from the current rules, regional variants such as Kudus as data, and manual scores with optional evidence for items SIGRAK has no data for (D-308, D-324, D-327, D-328).

### Internal console

The internal console for Tim SIGRAK is a separate Filament panel with its own login. It is not part of the school app.

- **Staff roles (D-314, D-338, D-339, D-343).** Eleven roles, all active: Pemilik, Admin, Validator, Penyusun Konten, Kurator Kurikulum, Support, Pemasaran, Penjualan, Koordinator Wilayah, Keuangan, Teknis. Powers are fixed in code, with a read-only *Hak akses* matrix; an admin edits only each person's scope (region, level, field), and changes are logged. The code still has three roles (admin, support, content) until the console slice. Two-factor sign-in with an authenticator app (TOTP) is required for Pemilik and Admin.
- **AI models (D-315, D-326).** Active model and fallbacks per purpose, tested with a Tim SIGRAK key. OpenAI-compatible and Anthropic adapters serve Tim SIGRAK's own work; teacher keys stay Gemini only.
- **Kesehatan Sistem (D-316):** queues, failed jobs, disk, generations, AI errors, last backup.
- **Curriculum (D-317, D-305):** compare, publish, roll back, impact report, JP audit; legacy app catalogs imported once after review.
- **Retention (D-318):** per data type, with floors (rapor, leger, graduation data, and buku induk never deleted automatically; supervision and PKKS results kept at least 5 years).
- **Targeted Kabar (D-319)** by kabupaten, kecamatan, level, and role; **help-request status** Baru, Diproses, Selesai (D-320); **sekolah binaan requests** approved or rejected (D-309).
- **Plan mapping (D-211):** which features are core and which are add-ons is set here, not in code.
- **Payments (D-340):** Penjualan records a payment claim; Keuangan confirms it, or the owner while no one holds that role.
- **Support access (D-341):** support opens a school's view read-only under the MoU signed with each school; religion and health data stay hidden; one internal row per support session.
- **Partners (D-342):** teacher communities (KKG, KKM) and resellers get a referral code and a partner report, not a console account.

## Main flows

Each role has one main flow of at most four steps.

**Teacher: make content (any template)**

1. **Pilih:** open *Buat* and pick a template.
2. **Jawab:** answer the wizard. Class and subject are prefilled from the timetable. Topic, focus, and learning goal are picked from the curriculum. Then come the template's own pedagogical questions, each with a default.
3. **Buat:** generation runs in the background. The teacher can leave the page and gets a notification when it is done.
4. **Periksa & pakai:** preview, edit, or revise one section ("Perbaiki bagian ini"). Then save, *Unduh PDF/Word*, *Buka di Canva*, or, for games, *Tayangkan* / *Unduh game*.

**Teacher: RPM.** These are the same four steps, with the wizard named as in the rebranding notes: *Kapan* (start date, or "RPM lepas jadwal") → *Apa* (materi pokok and fokus; JP and meeting count are computed and locked) → *Cara* (learning model per meeting, or automatic) → *Periksa*.

**Teacher: a teaching day (phone)**

1. Open *Hari Ini* to see the schedule and what is still unfilled.
2. At the start of the day, open *Di Kelas → Kehadiran*. Everyone starts as present, and the teacher taps the absent murid.
3. After the lesson, the *Jurnal* is already filled from the RPM. The teacher marks it done, partly done (the rest carries over), or not done with a reason, and adds notes (D-267, D-268).
4. Student notes, observations, and rubric scores are written when something happens. Everything saves without signal and is sent once a connection returns (D-289, D-349).

**Teacher or operator: TKA practice exam (CBT)**

1. **Siapkan soal:** make a *Paket Soal TKA* (Bahasa Indonesia or Matematika, 30 items in TKA forms) with the template, then check and edit it.
2. **Buat sesi:** pick the package, the grade 6 class(es), date, and duration (75 minutes by default). The system prints *kartu login* for every student.
3. **Jalankan:** on exam day the teacher (as proctor) opens the session and reads out the current token, which changes every 15 minutes. Students sign in on the exam site, and the proctor watches progress and can reset a stuck participant.
4. **Hasil:** scores are 0–100, shown in four levels (Istimewa, Baik, Memadai, Kurang). Data and exports store the official category (Baik, Memadai, Kurang) and the Istimewa predicate (score ≥ 95,00) separately, so they match the SHTKA. The UI notes that these are practice results. Per-competency analysis and an Excel/PDF export follow (D-371).

**Operator: start of the school year.** Set the academic year and classes → invite teachers → enter the timetable → set the calendar.

**Principal: weekly.** *Ringkasan* (completeness per teacher this week, from one completeness service, D-263) → *Pantau Guru* (who is behind, what content was made, send a *Pengingat*, never "teguran", D-311) → *Validasi* (approve or return school curriculum changes with a note).

**Supervisor: per visit.** Pick a school → view completeness and school-level supervision recaps (read-only) → final validation, PKKS of the principal → download the supervision report.

**Validation (D-019).** Generated content is usable immediately. The principal and supervisor can view a teacher's documents and leave comments. Only curriculum that a school *changes or adds* goes through guru → kepala sekolah → pengawas. Default curriculum used as-is never enters the queue. A finished document can be approved by the principal (*pengesahan dokumen*, with a verification code; never called "tanda tangan elektronik"), and approved revisions are never pruned (D-281, D-282).

**Teacher supervision (D-222…D-225).** All 18 instruments ship together as editable templates, with the Ruang GTK observation scale and value bands a school can change. Only the principal observes and reviews teachers; the pengawas sees school totals only; murid feedback is entered as class totals.

## Business model

Plans are paid per academic year (July–June) by **teachers and schools only**. Students and parents never pay and never see a price (D-157). AI keys belong to teachers (D-012; Gemini free tier only, D-023), so AI usage is never a SIGRAK cost. **School subscriptions are paid from funds other than BOS** (D-025). Sales material must not suggest BOS as a payment source. SIGRAK has no BOS or RKAS reports (D-219, D-328).

| Plan | For | Contents | Price per year | Payer |
|---|---|---|---|---|
| Uji coba | One teacher | Every feature for 1 week (D-160). After that, the account can only view and download its data until a plan is bought | Free | — |
| Sigrak Guru | One teacher | All teacher features | Rp60.000, or Rp50.000 when the teacher's school has Sigrak Sekolah | Teacher |
| Sigrak Sekolah | One school | 2 accounts, principal and operator, with their portals: validation, school print settings, invitations, school data. Teachers buy Sigrak Guru themselves | Rp100.000 | School (non-BOS funds) |
| Add-on feature | One school with Sigrak Sekolah | One extra feature, switched on by the school for all its accounts (D-158) | Rp50.000 each | School (non-BOS funds) |
| Sigrak Binaan | One supervisor or KKG cluster | Supervisor portal. Not sold: it opens free once 5 or more of the supervised schools have Sigrak Sekolah | Free | — |

**Add-on features (D-158):** Simulasi TKA · Buku Induk and Dapodik sync (upcoming, D-161) · Keuangan and Inventaris kelas · Buku Sekolah · Game kelas (Quiz Battle, *Tayangkan*, *Unduh game*; grows into the IFP learning mode, D-171, [`ifp-classroom.md`](ifp-classroom.md)) · Rapor (leger, description drafts, report export). More add-ons may follow. A teacher may try Game kelas for 7 days, once, even when the school has not switched it on (D-159). This list is only the starting configuration: Tim SIGRAK sets which features are core and which are add-ons per feature in the console (D-211). Features accepted from P4-04 onward, and every LKPD feature, start as core (D-211, D-336).

## Feature status

This is the old 43 legacy app features plus new ones, revised for the new concept. Status values: **Keep**, **Merge**, **Replace**, **Remove**, **Add**, **Later**. Everything not marked *Remove* or *Later* is in v1.

| Area | Current feature (legacy app) | Status | Becomes | Reason |
|---|---|---|---|---|
| Home | Beranda: administration progress | Replace | Hari Ini: what to fill today + progress this week, from one completeness service with windows and weekly snapshots; "Siapkan besok"; RPM readiness and JP balance Kabar to teachers only | Teachers need to know what to do now (D-263…D-266, D-270, D-271) |
| Home | Broadcast board | Merge | Kabar (with Pemberitahuan) | Two news places confuse |
| Home | Quote of the day, tips & motivation | Remove | — | Decoration that takes phone space |
| Home | Pemberitahuan | Merge | Kabar | One inbox |
| Home | Demo/inspection mode | Replace | Mode Contoh for sales demos | Clearer name for buyers |
| Home | Session cut after 15 min idle | Replace | Session stays. Forms autosave | Teachers stop mid-form. The old cost reason disappears with SQL |
| Buat | Curriculum (CP, TP, ATP, Prota, Promes) | Merge | Buat > Kurikulum, with "restore defaults" and Promes plan vs actual (Promes Realisasi). Prota/Promes print effective weeks | Basis of planning and content (D-214, D-274, D-276) |
| Buat | RPM generator | Keep | Buat > RPM template, 4 steps (Kapan, Apa, Cara, Periksa) on the template engine. Upgrades: automatic shifts with preview and undo, Neraca JP before SAS, early semester-2 drafts, parallel-class copy without AI | Most valuable feature (D-269, D-272, D-274, D-275) |
| Buat | Modul mandiri (manual mode) | Merge | "RPM lepas jadwal" option in step 1, later placed with "Pasang ke jadwal" | One door for every RPM (D-273) |
| Buat | Kokurikuler module (7 KAIH, projek, lintas disiplin) | Keep | Buat > Kokurikuler, a variant of the RPM template that reads the school plan. Forms follow Panduan Kokurikuler 2025: Lintas Disiplin, 7 KAIH, Cara Lainnya (projects fall here). Koding dan Kecerdasan Artifisial runs here (D-208). Assessed on the 8 DPL; automatic annual JP target | Existing feature (D-210, D-214, D-277) |
| Buat | Worksheet / LKPD | Merge | Buat > LKPD template, with blocks for grades 1–3 and a `pattern` field. Also an RPM attachment | LKPD is part of the plan (D-296, D-329) |
| Buat | Bank Modul / RPM storage | Merge | Dokumen Saya | One archive for every document |
| Buat | Download / import backup | Replace | Server autosave. Full data export in Akun | Manual backup makes teachers doubt their data is safe |
| Buat | Match dates with content (kokurikuler) | Replace | Automatic adjustment + Kabar when the schedule changes | Teachers needn't know the mechanism |
| Buat | Screenshot PDF (html2canvas) | Replace | Server text PDF + preview before download | Light files, searchable text, no cut lines |
| Asesmen | Bank Soal (PH, STS, SAS, US, kisi-kisi, kartu soal) | Merge | Buat > Bank Soal template, with per-item data, six derived prints, and an item library across documents. Asesmen > "Buat soal" shortcut | Questions linked to TP, one content engine (D-293, D-294) |
| Asesmen | Bank Soal "Mode Ujian" | Later (inactive) | — | Switched off for now (D-026). The TKA practice exam is the only online exam in v1 |
| Asesmen | Program penilaian | Merge | Asesmen > Rencana Penilaian, with KKTP per TP, diagnostic assessment, and weighted summative components | (D-214, D-290) |
| Asesmen | Analisis nilai, taraf serap | Keep | Asesmen > Analisis, automatic per TP: daya serap print, Kelompok belajar, item analysis, remedial and enrichment (remedial cap) | "Daya serap" is the current term (D-214, D-291, D-295) |
| Asesmen | Leger and "inject e-Rapor" | Replace | Asesmen > Rapor: leger with teacher confirmations and a principal sign-off that locks it, description drafts, AI draft of the catatan wali kelas without student data. The rapor has the 12 minimum components (grades from summative results only, competency descriptions, kokurikuler description, ekstrakurikuler, ketidakhadiran, homeroom note, parent response), with a Fase A option without numbers. Export in the e-Rapor SD and RDM import layout, built right after the Asesmen slice | "Inject" is technical (D-205, D-210, D-213, D-214, D-218, D-292) |
| Asesmen | Levels SMP–SMK, grades 7–12 | Remove | Returns when SMP/MTs data exists | Options without data end in errors |
| Di Kelas | Absensi | Replace | Di Kelas > **Kehadiran**: **daily** (D-029), "all present" default, tap absentees, works offline. Recap per week, month, and semester; early warning on the wali kelas's Hari Ini | Filled on a phone at the start of the day (D-302, D-303) |
| Di Kelas | Jurnal and refleksi | Replace | Di Kelas > Jurnal filled from the RPM, with outcome done, partial (carry-over), or not done with a reason, and a lost-JP recap. Teacher reflection at least once a semester, pre-filled from journals and scores | Less retyping (D-214, D-267, D-268) |
| Di Kelas | Anecdotal, counselling (BK), achievement notes | Merge | Di Kelas > Catatan Murid (one form, type picked; one-tap tags; BK notes never in Berkas, supervisor views, or AI) | Same fields in three books (D-210) |
| Di Kelas | Guest book, public relations book, minutes | Merge | Portal Kepala Sekolah > Buku Sekolah | Belongs to the school, not the class |
| Kelas Saya | Student data | Keep | Kelas Saya > Murid (records only, D-010) | "Murid" in UI copy (D-210) |
| Kelas Saya | Student transfers | Merge | Kelas Saya > Murid > Pindah atau keluar. A draft until set; the operator gets the 14-working-day Dapodik reminder | Part of the student history (D-217, D-304) |
| Kelas Saya | Promotion | Merge | Kelas Saya > Murid > Kenaikan, active near year end; a draft until set, with the meeting sheet and berita acara. Grade 6 graduation with SKL and ijazah data | Used once a year (D-215, D-304) |
| Kelas Saya | Calendar and timetable | Keep | Kelas Saya > Jadwal. The school copies its region's kaldik, with offered updates (Jawa Tengah 6-day calendar for Kudus, Pati, Jepara); central national holidays and joint leave; class events; one school calendar kept by the operator, with change proposals from teachers and the principal; effective days and weeks (STS/SAS days not effective); one conflict checker; change impact preview; draft timetable generator | D-252…D-262, D-323, D-379, D-380 |
| Kelas Saya | Piket | Keep | Kelas Saya > Piket: generated groups with rotation, tasks, and absence marks; on the Game kelas idle screen if the school chooses | D-297, D-298 |
| Kelas Saya | Class finance | Keep | Kelas Saya > Keuangan, school can turn it off. Report shared by hand, no money reminders; *Sumbangan sukarela* (never "tunggakan") | Not every school allows it (D-299, D-300) |
| Kelas Saya | Class inventory | Keep | Kelas Saya > Inventaris, the room view of the school inventory | D-217, D-306 |
| Akun | Settings (theme, paper, profile, password) | Replace | Akun (profile, password, theme, own letterhead image) + Pengaturan Sekolah (paper, default letterhead image, signers) | The school sets defaults; a teacher may use their own letterhead image (D-031) |
| Akun | School profile "first save wins" | Replace | Owned by operator/principal. Teachers propose corrections | Clear data owner |
| Akun | Gemini API key required at teacher activation | Replace | Kunci AI Saya: several keys, asked at first AI use, with a test button and status | Fast sign-up. AI never blocks other features |
| Akun | API key required in principal profile | Remove | — | Principals don't need AI |
| Akun | Default shared password for new users | Replace | Invitation link, user sets own password | Account security |
| Akun | Tutorial | Replace | Bantuan: short help per page + help centre | Help when needed |
| Akun | Arsip | Replace | Dokumen Saya: RPM, soal, LKPD, games, reports, with validation status and comments | |
| Akun | Contact admin / inbox | Keep | Account menu > Minta Bantuan, with status Baru, Diproses, Selesai | D-320 |
| Akun | Request school transfer | Keep | Akun > Pindah Sekolah, approved by the target school; fixed rules for what moves and what stays | D-307 |
| Portal | Principal portal | Replace | Sections listed under Navigation. Adds supervisi akademik, bukti dukung kinerja (K3, incl. the principal's own), pembagian tugas with JTM and SK, budaya sekolah aman (tata tertib, SOP, internal incident log), school inventory, Pusat Tindak Lanjut, Pengingat | Built from the principal's work (D-217, D-308, D-310, D-311) |
| Portal | Supervisor portal | Replace | Sections listed under Navigation. Adds sekolah binaan requests, PKKS of the principal, cross-school recap, Pembelajaran Mendalam mentoring | D-217, D-308, D-309, D-324, D-327, D-328 |
| Portal | Super Admin / Control Center | Replace | Internal Filament console + subscriptions + template registry; full list under [Internal console](#internal-console) | Not part of the school app (D-314…D-320, D-326, D-338…D-343) |
| New | Template gallery | Add | Buat > Galeri templat | Content engine (D-013) |
| New | Quiz Battle game | Add | Buat > Quiz Battle, from a Bank Soal set or new questions | First game (D-014) |
| New | Present mode and game download | Add | *Tayangkan* and *Unduh game* buttons on every game | How games reach students (D-011) |
| New | Feature request from a template | Add | "Usulkan fitur" link in the game wizard | Replaces AI-written code in v1 (D-015) |
| New | TKA practice package template | Add | Buat > Paket Soal TKA | D-026 |
| New | TKA practice exam (CBT): sessions, exam cards, rotating token, proctor view, results | Add | Asesmen > Simulasi TKA + separate exam site | D-026, D-027 |
| New | Multiple AI keys (Gemini free tier) with rotation and failover | Add | Kunci AI Saya | D-012, D-023 |
| New | AI usage history | Add | Kunci AI Saya > Riwayat | Teachers see why AI stopped |
| New | Revise one section with AI | Add | "Perbaiki bagian ini" on every section of a preview | No full regeneration |
| New | Report description drafts per TP | Add | Asesmen > Rapor | Drafted from the highest and lowest formative TP achievement (D-205) |
| New | Starter KSP template (Kurikulum Satuan Pendidikan) | Add | Kepala Sekolah portal > Program Sekolah | The KSP format is free; SIGRAK gives a starting draft that follows the official components and the school adapts it (D-209) |
| New | Di Kelas offline | Add | Di Kelas menu: Kehadiran, Jurnal, Catatan Murid, plus today's and tomorrow's RPM meetings read-only, the Pindai LJK queue, and observation and rubric entry. "Versi baru tersedia" banner syncs the queue before reload | D-016, D-289, D-321, D-349 |
| New | Curriculum validation guru → kepala sekolah → pengawas | Add | Status in Dokumen Saya + portal queues | D-019 |
| New | Comments on documents by principal/supervisor | Add | Document view | D-019 |
| New | Print settings: letterhead as an **uploaded image** (school default, teacher may upload their own) or made automatically from the school profile, signers, A4/F4, font, cover, three margin profiles, duplex, auto landscape, NIHIL pages | Add | Pengaturan Sekolah > Cetak; Akun > Kop saya | Teachers control the letterhead layout and logo in the image itself (D-031, D-283, D-284) |
| New | Berkas Administrasi: a semester bundle (cover, completeness sheet, contents, NIHIL pages) as one merged PDF or a ZIP; profiles Standar SIGRAK, Format Kudus, K3 | Add | Dokumen Saya > Berkas Administrasi; principal per teacher or whole school | Replaces collecting files (D-278…D-280) |
| New | Pengesahan dokumen with a verification code; approved revisions kept | Add | Document view, principal | D-281, D-282 |
| New | Completion sound and screen notification | Add | Per-user setting | D-285 |
| New | Supervision: 18 teacher instruments as editable templates, suggested S-01/S-15 scores from completeness, findings bank per item | Add | Portal Kepala Sekolah > Supervisi | D-222…D-225, D-312, D-313 |
| New | Pindai LJK: the school's own answer-sheet layout with corner markers and QR, read in the teacher's browser by a JavaScript OMR library; only answers and confidence reach the server, photos never uploaded; offline queue; grid cepat | Add | Asesmen > Pindai LJK | D-286, D-287, D-289, D-325, D-369 |
| New | Teacher assessment and class features: diagnostic assessment, 7 KAIH tracker with home sheet, class agreement and positive-discipline notes, work portfolio with photos | Add | Asesmen, Di Kelas | D-214 |
| New | Assessment instruments: Peta Asesmen dataset and tags, Rubrik Kinerja, Observasi DPL (phone entry, offline), Cek Kesiapan Awal, Akomodasi Murid, self and peer cards, misconception tags. Assessment AI never sees student data; sensitive results follow the health-data rule | Add | Buat > Galeri templat; Di Kelas | Wave A (D-344…D-351) |
| New | Homeroom features: early absence warning, kenaikan meeting sheet, graduation, home visits and light health data (never to AI), parent messages through `wa.me` links | Add | Kelas Saya, Di Kelas | D-215, D-303 |
| New | Ekstrakurikuler with Pramuka offered by default: activities, pembina, members, attendance, rapor description | Add | Kelas Saya > Ekstrakurikuler, early in v1 | Kepanduan must be offered (D-216) |
| New | Tabungan murid: ledger per student that follows the student, recap per class and school, berita acara on leaving | Add | Kelas Saya > Keuangan | D-219, D-301 |
| New | LKPD patterns (*Bentuk LKPD*) in waves: Wave 1 Detektif Teks, Peta Cerita, Data Kelasku, Etnomatematika, Misi Petualangan, Tiga Jalur, Tiket Keluar, Kotak Terkunci, Pikir Komputasional Tanpa Komputer; Konteks Daerah profile; school presets; QR to third-party URLs; licensed embeds; later a self-hosted H5P and 3D viewer | Later (waves after v1) | Buat > LKPD, all core | D-329…D-337 |
| New | Buka di Canva | Add | Button on RPM, LKPD, soal previews (download + open canva.com) | |
| New | Subscription and billing | Add | Pengaturan Sekolah > Langganan (plan and add-ons). Akun > Paket Saya | Business model (D-157) |
| New | Buku Induk and Dapodik sync: import the Dapodik Excel export (students, rombel, ekstrakurikuler, and staff data such as gaji berkala and kepangkatan) and keep school data aligned | Later | Operator portal > Data Sekolah, as an add-on, shown as upcoming | Schools already keep this data in Dapodik (D-158). Held as upcoming; design draft P4-01 on hold (D-161) |
| New | Invite teachers | Add | Operator portal > Guru | Replaces default passwords |
| New | Regional local content and calendar defaults | Add | Internal console + Pengaturan Sekolah | D-018 |
| New | Policy foundation: Profil Jenjang, versioned rule sets, Dasar Kebijakan panel, Cek Keselarasan | Add | Every document, starting with the RPM in P5-04 | D-352…D-358 (see [Policy foundation](#policy-foundation)) |
| New | 3-step onboarding | Add | Shown once after sign-up: class, timetable, students | Ready on day one |
| Later | Game templates by learning function: Lomba Tim, Pilah & Kelompokkan, Pasangkan first, then Urutkan, Papan Petak, Kotak Kejutan, Tiket Keluar, Pemanasan TKA | Later | More game templates in Game kelas ([`ifp-classroom.md`](ifp-classroom.md)) | After Quiz Battle is IFP-ready (D-176 replaces D-014's later list) |
| Later | AI-written game code on request | Later | Needs sandbox and review design | D-015 |
| Later | Canva Connect API (direct import into the teacher's Canva) | Later | | Level 1 button is enough for v1 |
| Later | Offline beyond Di Kelas (editing, AI, everything else) | Later | | D-016; the v1 exceptions are in D-289 and D-349 |
| Later | Reading handwritten Uraian and Isian on the phone; only text with a code `nomor absen-nomor soal` (e.g. `12-3`) goes to AI, the teacher decides | Later | Asesmen, after Pindai LJK in P5-08 | D-370 |
| Later | Answer sheet as paper backup for Simulasi TKA | Later | | D-288 |
| Later | Upcoming (shown): parent portal, SPP, MBG log, perpustakaan. Later (not shown): kartu pelajar, persuratan, laporan bulanan, accreditation readiness. Penyusun and evaluasi KSP after v1 | Later | | D-217, D-220 |
| Removed | Generic content generator ("Buat bebas") | Remove | — | Teachers can do this in Gemini directly (D-024) |
| Removed | Slides per meeting ("Bahan Tayang") | Remove | — | Not needed; teachers present with their own tools (D-033) |
| Later | Per-lesson Kehadiran for subject teachers | Later | | Daily Kehadiran in v1 (D-029) |
| Removed | BOS/RKAS reports; teacher and staff attendance | Remove | — | ARKAS and e-RKAM own BOS reporting; staffing systems own staff attendance (D-217, D-219) |
| Later | Pendidikan Kepercayaan terhadap Tuhan YME | Later | | Not in v1 (D-037) |
| Later | SMP/MTs | Later | | Needs curriculum data |

## v1 scope

v1 contains every row above that is not *Remove* or *Later*. That is a large release, so Phase 2 should cut it into slices that can ship one by one. Order, confirmed by the owner (D-162):

1. Foundation: auth, invitations, schools and roles, academic year and classes, curriculum import, timetable.
2. Content core: AI Provider Engine, template engine, RPM template, Dokumen Saya, PDF/DOCX export.
3. More templates: Bank Soal, LKPD, Quiz Battle (present + download).
4. TKA: Paket Soal TKA template, exam site, sessions, exam cards, token, results. Target: the 2028 TKA season (D-162).
5. Di Kelas with offline sync, Jurnal from RPM.
6. Asesmen: plan, scores, Pindai LJK, analysis per TP, report cards; the e-Rapor SD and RDM export right after (D-218).
7. Portals: principal, operator, supervisor. Validation and comments.
8. Business: trial, subscriptions, add-ons, billing, Mode Contoh.

Before slice 2 (P5-04), the minimal rule versioning and the Profil Jenjang exist (D-355). Ekstrakurikuler and Pramuka come early in v1, not with Buku Induk (D-216). The console parts arrive with P5-04 and P5-10 (D-314…D-320).

## Open questions

- [x] Prices: 1-week trial (D-160), Sigrak Guru Rp60.000, Sigrak Sekolah Rp100.000, add-ons Rp50.000 each (D-157, D-158).
- [ ] Final name: check "Sigrak" (and backup "Kapur") in [PDKI](https://pdki-indonesia.dgip.go.id) classes 9, 41, 42, then register the .id and .com domains together. The name risk is higher now that the market is nationwide.
- [ ] Logo: brief a designer (colour, single-colour, negative, minimum 16 px).
- [x] The kokurikuler module ships in v1 as an RPM template variant (D-014).
- [ ] Test the five-tab menu with 5 class teachers and 2 subject teachers on a click prototype (no real data).
- [ ] Present the short version to principals and supervisors, and record their objections.
- [x] Gemini's under-18 clause: students only receive finished, teacher-checked AI content and never trigger the AI (D-124).
- [ ] TKA 2028 schedule for SD/MI, once published, to time the TKA release (D-162).
- [ ] Console plan mapping (D-211): whether the leger lock and weights sit in core Asesmen or the Rapor add-on, and whether sumbangan and savings sit in the Keuangan add-on.
- [ ] Religion data reviewers and an official-channel copy of SK 9941/2025 (see [`religious-education.md`](religious-education.md#open-questions)).
