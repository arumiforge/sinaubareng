# SIGRAK Product Concept

- **Status:** draft (owner review)
- **Task:** P1-02
- **Last updated:** 2026-10-30
- **Sources:** [`Konsep_Awal_Produk_SIGRAK.md`](../sources/Konsep_Awal_Produk_SIGRAK.md), [`Rebranding_dan_Perombakan_Fitur_EDUNEXA.md`](../sources/Rebranding_dan_Perombakan_Fitur_EDUNEXA.md), [`EDUNEXA-Dokumentasi-Fungsionalitas.pdf`](../sources/EDUNEXA-Dokumentasi-Fungsionalitas.pdf)

## Purpose

This document defines what SIGRAK is, who uses it, and what v1 contains. It is the product reference for every later design and build task. Two companion drafts cover the technical side: [`content-engine.md`](content-engine.md) and [`ai-provider-engine.md`](ai-provider-engine.md). Decisions are logged in [`../agent/DECISIONS.md`](../agent/DECISIONS.md) (D-007 to D-037).

## Summary

SIGRAK (working name) is a web app for SD/MI teachers and school leaders. It does two jobs, and both ship in v1:

1. **Content creation with AI help.** Teachers make RPM, question banks, LKPD, classroom games, and TKA practice packages. They pick a template and answer pedagogical questions. They never write a prompt. Developer-built templates fix the structure and design, and AI only fills in the content. There is no generic "make anything" generator, because a teacher can already get that from Gemini directly (D-024).
2. **School administration.** Attendance, teaching journal, student notes, assessment and report cards, class data, and the principal and supervisor portals carried over from EDUNEXA.
3. **TKA practice exam (CBT).** A computer-based exam that works like the national Tes Kemampuan Akademik for grade 6. Students take it on a separate exam site with an exam card and a proctor token (D-026, D-027).

Students are not users of the app. Every page sits behind login, and there are no public pages. The one place students touch is the exam site, and only with a one-session exam card.

## What changed from the EDUNEXA notes

| Topic | EDUNEXA notes (29 Oct) | SIGRAK now | Decision |
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

Everything else agreed in the EDUNEXA notes still holds: text-based PDF with per-school print settings, the "Buka di Canva" button, the material validation flow, and Di Kelas working without signal.

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

## Access model

- **Login-only.** The only screens without a session are login, accepting an invitation, and password reset. There is no landing page, no public share link, and no public profile inside the app. A marketing site, if one is made, is a separate project.
- **Students are records, not accounts.** A student has a name, NISN, class enrollment, attendance, notes, and scores, all managed by teachers. Students never log in to the app.
- **The only exception is the TKA practice exam** (D-027). Students sign in to a separate exam site (for example `ujian.<domain>`) with an exam card (username + password) that is valid for one exam session only, plus the current proctor token. The exam site shows exam questions and nothing else, and it never calls the AI. Gemini's terms forbid using the API in a service likely to be used by people under 18.
- **Games reach students in two ways** (D-011):
  - *Tayangkan* (present mode): the teacher runs the game fullscreen from their logged-in session on a projector, TV, or their own device.
  - *Unduh game*: one self-contained HTML file that plays offline without login and makes no network calls. Teachers can share it, for example on WhatsApp. It holds only the teaching content, never student data.
- Accounts are created by invitation. Nobody signs up openly into a school.

## Roles

| Role (UI name) | Who | Main jobs in SIGRAK | Device |
|---|---|---|---|
| Guru kelas (wali kelas) | Teaches the class subjects of one class: Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS, Seni dan Budaya, and the others not listed for guru mapel | Make content, daily attendance, journal, assessment, report cards, class data | Phone, home laptop |
| Guru mapel (khusus) | Teaches one special subject across classes: **PJOK, Muatan Lokal (Bahasa Jawa), Bahasa Inggris, Pendidikan Agama** (in SD one subject per religion: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu; in MI the Kemenag subjects Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab, always taught by guru mapel) | Make content, reused across classes. Journal, student notes, and assessment for their subject (attendance is daily and belongs to the homeroom teacher) | Phone |
| Kepala sekolah | Principal | Watch completeness and content made, validate school curriculum changes, supervision, school books, print settings, subscription | Laptop, phone |
| Operator sekolah | School admin staff | Academic year, classes, invite teachers, timetable, calendar, school profile | Laptop |
| Pengawas / korwil | Supervisor of several schools | Read-only view of supervised schools, final validation of curriculum changes, supervision reports | Laptop |
| Tim SIGRAK | Internal staff | Template registry and prompt tuning, schools and subscriptions, support. Uses a separate internal console (Filament), not the school app | Laptop |

These subject defaults follow how SD/MI staff their classes (D-032). The operator can still assign any subject of a class to a guru mapel, or a special subject to the homeroom teacher when the school has no specialist.

A principal, operator, or supervisor who also teaches keeps the five teacher menus and gets a switch to their portal. The principal does not need an AI key unless they make content themselves.

**Peserta ujian (exam participant)** is not a role. A student gets session-scoped exam credentials for a TKA practice session, and they stop working when the session closes.

## Market

- **Level:** SD/MI, grades 1–6. Grades 7–12 stay hidden until curriculum data for them exists (the current data, KM-SD-2026-R2, covers SD/MI only). SD offers all six religion subjects (D-034). MI follows the Ministry of Religious Affairs: Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, and Bahasa Arab instead of one PAI subject (D-035). Details are in [`religious-education.md`](religious-education.md).
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
2. **Administration:** attendance and journal for one class done on a phone in under a minute, with or without signal.
3. **Leadership:** the principal and supervisor see completeness without anyone handing in files.

## Navigation

The teacher app has five menus (D-021). They are a bottom bar on phones and a sidebar in the same order on laptops.

```
Hari Ini | Di Kelas | Buat | Asesmen | Kelas Saya

Hari Ini    Today's schedule, what still needs filling, this week's progress, Kabar (news)
Di Kelas    Absensi, Jurnal, Catatan Siswa (works offline)
Buat        Galeri templat: RPM, LKPD, Bank Soal, Paket Soal TKA, Quiz Battle (more games later)
            Kurikulum (CP, TP, ATP, Prota, Promes), as reference and for school changes
            Kokurikuler
Asesmen     Rencana penilaian, Nilai, Analisis (per TP), Rapor
            Simulasi TKA: sessions, exam cards, proctor token, results
            "Buat soal" is a shortcut to the Bank Soal template
Kelas Saya  Siswa (incl. pindah/keluar, kenaikan), Jadwal, Piket, Keuangan (school may turn off), Inventaris

Account menu (profile icon): Dokumen Saya, Kunci AI Saya, Paket Saya, Akun, Bantuan, Minta Bantuan
```

Portals, reached through the role switch:

| Portal | Sections |
|---|---|
| Kepala Sekolah | Ringkasan, Pantau Guru, Validasi, Supervisi, Program Sekolah, Agenda & Rapat, Buku Sekolah (tamu, humas, notulen), Data Sekolah, Pengaturan Sekolah (cetak, langganan) |
| Operator Sekolah | Tahun Ajaran, Kelas, Guru (undang), Jadwal, Kaldik, Profil Sekolah |
| Pengawas | Sekolah Binaan, Pantau (read-only), Validasi, Supervisi, Laporan Kepengawasan |

The internal console for Tim SIGRAK is a separate Filament panel with its own login. It is not part of the school app.

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
2. During the lesson, open *Di Kelas → Absensi*. Everyone starts as present, and the teacher taps the absent students.
3. After the lesson, the *Jurnal* is already filled from the RPM. The teacher marks it done and adds notes.
4. Student notes are written when something happens. Everything saves without signal and is sent once a connection returns.

**Teacher or operator: TKA practice exam (CBT)**

1. **Siapkan soal:** make a *Paket Soal TKA* (Bahasa Indonesia or Matematika, 30 items in TKA forms) with the template, then check and edit it.
2. **Buat sesi:** pick the package, the grade 6 class(es), date, and duration (75 minutes by default). The system prints *kartu login* for every student.
3. **Jalankan:** on exam day the teacher (as proctor) opens the session and reads out the current token, which changes every 15 minutes. Students sign in on the exam site, and the proctor watches progress and can reset a stuck participant.
4. **Hasil:** scores are 0–100 with the TKA categories (Kurang, Memadai, Baik, Istimewa), plus per-competency analysis and an Excel/PDF export.

**Operator: start of the school year.** Set the academic year and classes → invite teachers → enter the timetable → set the calendar.

**Principal: weekly.** *Ringkasan* (completeness per teacher this week) → *Pantau Guru* (who is behind, what content was made, send a reminder) → *Validasi* (approve or return school curriculum changes with a note).

**Supervisor: per visit.** Pick a school → view completeness and supervision results (read-only) → final validation → download the supervision report.

**Validation (D-019).** Generated content is usable immediately. The principal and supervisor can view a teacher's documents and leave comments. Only curriculum that a school *changes or adds* goes through guru → kepala sekolah → pengawas. Default curriculum used as-is never enters the queue.

## Business model

Four tiers, priced per academic year (July–June). AI keys belong to teachers in every tier (D-012; Gemini free tier only, D-023), so AI usage is never a SIGRAK cost. **Subscriptions are paid from funds other than BOS** (D-025). Sales material must not suggest BOS as a payment source.

| Tier | For | Contents | Payer |
|---|---|---|---|
| Sigrak Dasar (free) | One teacher | Di Kelas, timetable, student data, templates up to a monthly limit (own AI keys), print with a small watermark. No TKA sessions | — |
| Sigrak Guru | One teacher | All teacher features without limits, no watermark | Teacher |
| Sigrak Sekolah | One school, all teachers | Sigrak Guru for every account + principal and operator portals, validation, school print settings, invitations, Simulasi TKA | Principal (school, non-BOS funds) |
| Sigrak Binaan | One supervisor or KKG cluster | Supervisor portal + a discount on Sigrak Sekolah for supervised schools | Supervisor / korwil / cluster |

A teacher's paid Sigrak Guru time moves over automatically when their school subscribes, and the remainder becomes a discount.

## Feature status

This is the old 43 EDUNEXA features plus new ones, revised for the new concept. Status values: **Keep**, **Merge**, **Replace**, **Remove**, **Add**, **Later**. Everything not marked *Remove* or *Later* is in v1.

| Area | Current feature (EDUNEXA) | Status | Becomes | Reason |
|---|---|---|---|---|
| Home | Beranda: administration progress | Replace | Hari Ini: what to fill today + progress this week | Teachers need to know what to do now |
| Home | Broadcast board | Merge | Kabar (with Pemberitahuan) | Two news places confuse |
| Home | Quote of the day, tips & motivation | Remove | — | Decoration that takes phone space |
| Home | Pemberitahuan | Merge | Kabar | One inbox |
| Home | Demo/inspection mode | Replace | Mode Contoh for sales demos | Clearer name for buyers |
| Home | Session cut after 15 min idle | Replace | Session stays. Forms autosave | Teachers stop mid-form. The old cost reason disappears with SQL |
| Buat | Curriculum (CP, TP, ATP, Prota, Promes) | Merge | Buat > Kurikulum | Basis of planning and content |
| Buat | RPM generator | Keep | Buat > RPM template, 4 steps (Kapan, Apa, Cara, Periksa) on the template engine | Most valuable feature |
| Buat | Modul mandiri (manual mode) | Merge | "RPM lepas jadwal" option in step 1 | One door for every RPM |
| Buat | Kokurikuler module (7 KAIH, projek, lintas disiplin) | Keep | Buat > Kokurikuler, a variant of the RPM template that reads the school plan | Existing feature |
| Buat | Worksheet / LKPD | Merge | Buat > LKPD template. Also an RPM attachment | LKPD is part of the plan |
| Buat | Bank Modul / RPM storage | Merge | Dokumen Saya | One archive for every document |
| Buat | Download / import backup | Replace | Server autosave. Full data export in Akun | Manual backup makes teachers doubt their data is safe |
| Buat | Match dates with content (kokurikuler) | Replace | Automatic adjustment + Kabar when the schedule changes | Teachers needn't know the mechanism |
| Buat | Screenshot PDF (html2canvas) | Replace | Server text PDF + preview before download | Light files, searchable text, no cut lines |
| Asesmen | Bank Soal (PH, STS, SAS, US, kisi-kisi, kartu soal) | Merge | Buat > Bank Soal template. Asesmen > "Buat soal" shortcut | Questions linked to TP, one content engine |
| Asesmen | Bank Soal "Mode Ujian" | Later (inactive) | — | Switched off for now (D-026). The TKA practice exam is the only online exam in v1 |
| Asesmen | Program penilaian | Merge | Asesmen > Rencana Penilaian | |
| Asesmen | Analisis nilai, taraf serap | Keep | Asesmen > Analisis, automatic per TP | |
| Asesmen | Leger and "inject e-Rapor" | Replace | Asesmen > Rapor: leger, description drafts, e-Rapor export format | "Inject" is technical |
| Asesmen | Levels SMP–SMK, grades 7–12 | Remove | Returns when SMP/MTs data exists | Options without data end in errors |
| Di Kelas | Absensi | Replace | Di Kelas > Absensi: **daily** (D-029), "all present" default, tap absentees, works offline | Filled on a phone at the start of the day |
| Di Kelas | Jurnal and refleksi | Replace | Di Kelas > Jurnal filled from the RPM. Reflection built from journals | Less retyping |
| Di Kelas | Anecdotal, counselling (BK), achievement notes | Merge | Di Kelas > Catatan Siswa (one form, type picked) | Same fields in three books |
| Di Kelas | Guest book, public relations book, minutes | Merge | Portal Kepala Sekolah > Buku Sekolah | Belongs to the school, not the class |
| Kelas Saya | Student data | Keep | Kelas Saya > Siswa (records only, D-010) | |
| Kelas Saya | Student transfers | Merge | Kelas Saya > Siswa > Pindah atau keluar | Part of the student history |
| Kelas Saya | Promotion | Merge | Kelas Saya > Siswa > Kenaikan, active near year end | Used once a year |
| Kelas Saya | Calendar and timetable | Keep | Kelas Saya > Jadwal. Kaldik set once by the school (per region defaults) | |
| Kelas Saya | Piket | Keep | Kelas Saya > Piket | |
| Kelas Saya | Class finance | Keep | Kelas Saya > Keuangan, school can turn it off | Not every school allows it |
| Kelas Saya | Class inventory | Keep | Kelas Saya > Inventaris | |
| Akun | Settings (theme, paper, profile, password) | Replace | Akun (profile, password, theme, own letterhead image) + Pengaturan Sekolah (paper, default letterhead image, signers) | The school sets defaults; a teacher may use their own letterhead image (D-031) |
| Akun | School profile "first save wins" | Replace | Owned by operator/principal. Teachers propose corrections | Clear data owner |
| Akun | Gemini API key required at teacher activation | Replace | Kunci AI Saya: several keys, asked at first AI use, with a test button and status | Fast sign-up. AI never blocks other features |
| Akun | API key required in principal profile | Remove | — | Principals don't need AI |
| Akun | Default shared password for new users | Replace | Invitation link, user sets own password | Account security |
| Akun | Tutorial | Replace | Bantuan: short help per page + help centre | Help when needed |
| Akun | Arsip | Replace | Dokumen Saya: RPM, soal, LKPD, games, reports, with validation status and comments | |
| Akun | Contact admin / inbox | Keep | Account menu > Minta Bantuan | |
| Akun | Request school transfer | Keep | Akun > Pindah Sekolah, approved by the target school | |
| Portal | Principal portal | Replace | Sections listed under Navigation | Built from the principal's work |
| Portal | Supervisor portal | Replace | Sections listed under Navigation | |
| Portal | Super Admin / Control Center | Replace | Internal Filament console + subscriptions + template registry | Not part of the school app |
| New | Template gallery | Add | Buat > Galeri templat | Content engine (D-013) |
| New | Quiz Battle game | Add | Buat > Quiz Battle, from a Bank Soal set or new questions | First game (D-014) |
| New | Present mode and game download | Add | *Tayangkan* and *Unduh game* buttons on every game | How games reach students (D-011) |
| New | Feature request from a template | Add | "Usulkan fitur" link in the game wizard | Replaces AI-written code in v1 (D-015) |
| New | TKA practice package template | Add | Buat > Paket Soal TKA | D-026 |
| New | TKA practice exam (CBT): sessions, exam cards, rotating token, proctor view, results | Add | Asesmen > Simulasi TKA + separate exam site | D-026, D-027 |
| New | Multiple AI keys (Gemini free tier) with rotation and failover | Add | Kunci AI Saya | D-012, D-023 |
| New | AI usage history | Add | Kunci AI Saya > Riwayat | Teachers see why AI stopped |
| New | Revise one section with AI | Add | "Perbaiki bagian ini" on every section of a preview | No full regeneration |
| New | Report description drafts per TP | Add | Asesmen > Rapor | |
| New | Di Kelas offline | Add | Di Kelas menu | D-016 |
| New | Curriculum validation guru → kepala sekolah → pengawas | Add | Status in Dokumen Saya + portal queues | D-019 |
| New | Comments on documents by principal/supervisor | Add | Document view | D-019 |
| New | Print settings: letterhead as an **uploaded image** (school default, teacher may upload their own), signers, A4/F4, font | Add | Pengaturan Sekolah > Cetak; Akun > Kop saya | Teachers control the letterhead layout and logo in the image itself (D-031) |
| New | Buka di Canva | Add | Button on RPM, LKPD, soal previews (download + open canva.com) | |
| New | Subscription and billing | Add | Pengaturan Sekolah > Langganan. Akun > Paket Saya | Business model |
| New | Invite teachers | Add | Operator portal > Guru | Replaces default passwords |
| New | Regional local content and calendar defaults | Add | Internal console + Pengaturan Sekolah | D-018 |
| New | 3-step onboarding | Add | Shown once after sign-up: class, timetable, students | Ready on day one |
| Later | Drag & Drop, Word Puzzle, Maze Challenge, Flashcard | Later | More game templates | After Quiz Battle proves the pattern |
| Later | AI-written game code on request | Later | Needs sandbox and review design | D-015 |
| Later | Canva Connect API (direct import into the teacher's Canva) | Later | | Level 1 button is enough for v1 |
| Later | Offline beyond Di Kelas | Later | | D-016 |
| Removed | Generic content generator ("Buat bebas") | Remove | — | Teachers can do this in Gemini directly (D-024) |
| Removed | Slides per meeting ("Bahan Tayang") | Remove | — | Not needed; teachers present with their own tools (D-033) |
| Later | Per-lesson attendance for subject teachers | Later | | Daily attendance in v1 (D-029) |
| Later | Pendidikan Kepercayaan terhadap Tuhan YME | Later | | Not in v1 (D-037) |
| Later | SMP/MTs | Later | | Needs curriculum data |

## v1 scope

v1 contains every row above that is not *Remove* or *Later*. That is a large release, so Phase 2 should cut it into slices that can ship one by one. Proposed order (to confirm when Phase 2 is planned):

1. Foundation: auth, invitations, schools and roles, academic year and classes, curriculum import, timetable.
2. Content core: AI Provider Engine, template engine, RPM template, Dokumen Saya, PDF/DOCX export.
3. More templates: Bank Soal, LKPD, Quiz Battle (present + download).
4. TKA: Paket Soal TKA template, exam site, sessions, exam cards, token, results. Target: ready before schools start TKA practice for 2027 (January–March).
5. Di Kelas with offline sync, Jurnal from RPM.
6. Asesmen: plan, scores, analysis per TP, report cards.
7. Portals: principal, operator, supervisor. Validation and comments.
8. Business: subscriptions, billing, watermark, Mode Contoh.

## Open questions

- [ ] Prices for the four tiers (paid from non-BOS funds, D-025).
- [ ] Final name: check "Sigrak" (and backup "Kapur") in [PDKI](https://pdki-indonesia.dgip.go.id) classes 9, 41, 42, then register the .id and .com domains together. The name risk is higher now that the market is nationwide.
- [ ] Logo: brief a designer (colour, single-colour, negative, minimum 16 px).
- [ ] Confirm the kokurikuler module ships in v1 as an RPM template variant.
- [ ] Test the five-tab menu with 5 class teachers and 2 subject teachers on a click prototype (no real data).
- [ ] Present the short version to principals and supervisors, and record their objections.
- [x] Gemini's under-18 clause: students only receive finished, teacher-checked AI content and never trigger the AI (D-124).
- [ ] TKA 2027 schedule for SD/MI, once published, to time the TKA release.
- [ ] Religion data reviewers and an official-channel copy of SK 9941/2025 (see [`religious-education.md`](religious-education.md#open-questions)).
