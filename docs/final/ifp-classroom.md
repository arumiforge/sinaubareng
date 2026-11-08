# Game kelas: Interactive Flat Panel (IFP) Learning for SD/MI

- **Status:** final (approved 2026-11-08); owner interview done 2026-11-04, decisions D-171…D-181 and D-233 recorded (not in v1 scope yet)
- **Task:** P4-02
- **Last updated:** 2026-11-08
- **UI name:** *Game kelas*. The existing add-on (D-158) keeps its name and grows into this IFP mode (D-171)
- **Scope:** SD and MI, grades 1–6 (Fase A–C). PAUD, SMP, and SMA are out of scope.

## Purpose

Most schools now have an Interactive Flat Panel (IFP, also called *Papan Interaktif Digital* or PID) from the Kemendikdasmen digitalisation programme. The ministry targeted delivery to more than 288,000 schools by December 2025, and the pilot region Kudus already received units ([ANTARA, 2025-10-07](https://www.antaranews.com/berita/5159673/mendikdasmen-pengiriman-papan-interaktif-ditargetkan-selesai-desember)). The units are 75-inch, all-in-one Android panels with 4K UHD, multi-touch, a speaker and a microphone, delivered with a laptop and an external drive of learning content ([BBPMP Jatim](https://bbpmpjatim.kemendikdasmen.go.id/main/syarat-syarat-sekolah-penerima-ifp-dan-tipe-tipe-wilayahnya), [PAUDPEDIA](https://paudpedia.kemendikdasmen.go.id/), [detikEdu, 2025-10-23](https://www.detik.com/edu/sekolah/d-8174445/program-digitalisasi-pendidikan-berlanjut-dalam-5-tahun-sekolah-bakal-punya-6-ifp)). In 2025 each school got one unit (149,268 of the target schools are SD); the ministry aims for three per school in 2026, so one panel is still shared by several classes. In practice the panel is often used as a big TV for slides.

Free sites such as [papaninteraktif.com](https://papaninteraktif.com/) and [game.digitaleducation.id](https://game.digitaleducation.id/) show that teachers want ready-to-play IFP activities. They are popular, but they are game catalogues: a teacher picks a game, plays it, and nothing connects to the lesson plan, the learning objective (TP), or the class's results.

This document maps how those two sites use the IFP, finds what they lack, and proposes a SIGRAK feature where **every activity on the panel starts from a TP, sits in a step of the RPM, and feeds back into assessment and the next lesson**. Play is the delivery, not the goal.

The owner's answers from the 2026-11-04 interview are recorded as D-171…D-181 and listed under [Decisions](#decisions); the Kode Tayang path followed on 2026-11-08 (D-233).

## 1. Benchmark: what the two sites do

Both sites were reviewed on 2026-11-04. They are **reference only**: SIGRAK builds its own templates and mechanics, never copies of their games, names, art, or levels.

### 1.1 Papan Interaktif (MANDAI)

A free portal by an individual teacher-developer ("Media Anak Nusantara Digital Aktif Inspiratif"). It claims 42+ media, 20,000+ schools reached, and 4,000+ teachers.

| Aspect | What it offers |
|---|---|
| Levels | Two modes: **UMUM** (SD–SMA) and **JUNIOR** (PAUD/TK, early SD) |
| Categories | Numerasi, Literasi, Bahasa Inggris, Logika, Ice Breaking, Religion, Sport |
| Example titles | Aim-and-shoot answers, matrix and division (*porogapit*) drills, math domino, number line, fraction game, balance scale, English arena, word fishing, geometry squad, 3D space exploration, Qur'an themed game, *Lomba 17an* |
| IFP-specific ideas | Camera-based activities (AI basketball with body movement, draw-and-guess with AI), upload your own photo as a puzzle, a mind-mapping board |
| Teacher side | No login, no setup. A community wall ("Ruang Sapa Guru"), a WhatsApp channel, a teacher journal link |
| Learning frame | Positioned mainly as **ice breaking** and "belajar sambil bermain". No TP, no curriculum map, no results |

### 1.2 Game DigiEd (game.digitaleducation.id)

Part of a commercial education platform (PT Digital Education Indonesia), offered free and without login. About 70 titles, filterable by level (PAUD, grades 1–2, 3–4, 5–6, SMP, SMA), subject, and game type.

| Aspect | What it offers |
|---|---|
| Subjects | Matematika, B. Indonesia, B. Inggris, IPAS, PAI, IPS, PJOK, SBK, IPA, Koding & KA, PAUD basics, general knowledge |
| Mechanic families | Team race on a split screen (tug of war, pole climb, bike race), runner/flappy/maze drills, falling-target drills, memory pairs, word scramble, crossword and word search, board games (snakes and ladders, city-buying), TV-quiz formats, sport games with a quiz before each serve |
| Simulations and labs | Analog clock with draggable hands, market buying and change, cook with ratios and *Isi Piringku*, habitat and food chains, human organs, Indonesia map with provinces and culture, coding robot with sequence/loop/branch, Hijaiyah, Asmaul Husna |
| Play modes | 1 player, 2 players, 2–6 teams, whole class, or vs. computer |
| Learning support | Many games show an **explanation after each answer**; difficulty from Mudah to Tantangan; usage guide "pilih game → atur cara main → main dan bahas" |
| Own questions | **Kuis Kelas:** fill an Excel template, upload, and the same questions play as three different games (tic-tac-toe, mystery box, snakes and ladders). Shareable to students by a WhatsApp link valid 30 days |
| Constraints | Needs internet to load; no student data; no results kept |

### 1.3 Common patterns worth keeping

1. **Zero setup.** Open, choose, play within one minute.
2. **Team play on one screen.** Split left/right zones so two groups touch at the same time.
3. **Immediate feedback with a reason.** The explanation after each answer is what turns a game into learning.
4. **One question set, many formats.** Teachers reuse their own questions across mechanics.
5. **Big targets, no typing.** Custom on-screen keypads instead of the panel's keyboard.
6. **Simulations and manipulatives** (clock, money, map, organs) where the panel shows something a whiteboard cannot.

### 1.4 What both sites lack (the gap SIGRAK fills)

| Gap | Effect in class | SIGRAK answer |
|---|---|---|
| No link to CP/TP or the lesson plan | The game is a filler, chosen by fun, not by objective | Every activity carries `kode_tp` and is placed in an RPM meeting step |
| Fixed, generic content | Not the teacher's class, material, or level | AI fills the template from the teacher's TP, Bank Soal, or RPM (D-024) |
| No record of what the class got wrong | The teacher cannot plan remedial work | Class-level result summary per item and per TP (see §5) |
| "Ice breaking" framing | Principals and supervisors see play, not learning | Each activity states its learning function and phase of the lesson |
| Weak religious and MI coverage (Islam only, mostly SMP/SMA topics) | MI and non-Muslim students in SD are left out | Content from SIGRAK's curriculum data: six SD religions and the five MI Kemenag subjects |
| No local content | Bahasa Jawa (muatan lokal in the pilot region) missing | Templates that accept Bahasa Jawa and *aksara Jawa* content |
| Online only (DigiEd) | Fails when the school's signal drops | *Unduh game* offline HTML, playable from the IFP browser or a USB drive (D-011) |
| No TKA link | Grade 6 practice is separate from class games | Games that use the TKA item forms (PG, PGK MCMA, PGK Kategori) as warm-ups |
| No guidance on running the activity | Teachers repeat the same game; quiet students never touch the board | Built-in run sheet: duration, roles, turn rotation, discussion prompts |

## 2. What the IFP is good for in learning

Use the panel for what it does better than a whiteboard or a printed sheet. A game is only one of five uses.

| # | Use | Learning function | Lesson phase (RPM) | Example in SD/MI |
|---|---|---|---|---|
| 1 | **Manipulate** | Build concepts from concrete to abstract (CPA) | Memahami | Drag base-ten blocks, cut a fraction bar, set clock hands, sort living things |
| 2 | **Explore / simulate** | See cause and effect, ask "what if" | Memahami, Mengaplikasi | Water cycle, food chain, shadow and light, map scale |
| 3 | **Practise with feedback** | Fluency and correction of misconceptions | Mengaplikasi | Team practice where each wrong option shows why it is wrong |
| 4 | **Check understanding** | Formative assessment, assessment *for* learning | Opening, end of lesson | Prior-knowledge check, exit ticket, hinge question mid-lesson |
| 5 | **Discuss and reflect** | Explain reasoning, metacognition | Merefleksi | Show two class answers and discuss, mood/reflection board, mind map |

Design principles that follow from the deep learning approach already used in the RPM template (mindful, meaningful, joyful):

- **Mindful:** the screen shows the goal ("Hari ini kita belajar …") before play, and a short pause for thinking before answers open.
- **Meaningful:** contexts from the students' life (pasar, ongkos angkot, panen, kalender hijriah), not abstract drills only.
- **Joyful:** team play and movement, without public ranking of single students.

## 3. Feature: Game kelas

### 3.1 Concept

*Game kelas* (D-171) is a fullscreen classroom mode for the IFP, built on *Tayangkan* (D-011). It plays a **lesson sequence** taken from the RPM meeting, not a single game:

```
RPM meeting (Kegiatan Pembuka → Inti → Penutup)
        │  teacher taps "Buka Game kelas"
        ▼
Game kelas playlist for today
  1. Tujuan hari ini        (goal screen, from TP)
  2. Cek awal               (2–3 quick items, prior knowledge)
  3. Alat peraga / simulasi (manipulative for the core concept)
  4. Latihan tim            (game template, items from Bank Soal or AI)
  5. Tiket keluar           (3 items, exit ticket)
  6. Refleksi               (prompt from the RPM)
        │
        ▼
Class summary (per item, per TP) → Jurnal (D-016) and Asesmen
```

The teacher can still open a single game from *Buat*; the playlist is the recommended path.

### 3.2 Activity templates (catalogue for SD/MI; build order in D-176)

All templates follow the hybrid rule: the runtime owns structure, AI fills content and allowed config only (D-015). Names are working names. Mechanics are generic classroom formats, not copies of the benchmark titles.

**Practice and check (reuse Bank Soal items):**

| Template | Mechanic on the IFP | Learning function | Item types | Grades |
|---|---|---|---|---|
| Quiz Battle (v1, D-014) | Teams answer in turn, timer, explanation | Practice, review | PG, Benar-Salah | 1–6 |
| **Lomba Tim** | Split screen, 2 teams answer at the same time; a correct answer moves the team marker | Fluency drill with feedback | PG, Benar-Salah, short number answer via keypad | 2–6 |
| **Papan Petak** | Board of tiles (grid or path); answering claims a tile, line of tiles wins | Mixed review, strategy | PG, Benar-Salah | 3–6 |
| **Kotak Kejutan** | Pick a numbered box, answer, reveal bonus or swap | Review with suspense, good for 3–6 groups | Any auto-gradable | 1–6 |
| **Pilah & Kelompokkan** | Drag cards into 2–4 bins | Classification, concept boundaries | Category items (maps to PGK Kategori) | 1–6 |
| **Pasangkan** | Draw lines or drag pairs | Vocabulary, term–definition, picture–word | Menjodohkan | 1–6 |
| **Urutkan** | Drag cards into order | Sequences: story events, life cycle, SKI timeline, steps of wudu | Ordering | 2–6 |
| **Tiket Keluar** | 3 items, class votes per item (see §6.2), no timer | Exit ticket, formative check | PG, Benar-Salah | 1–6 |
| **Pemanasan TKA** | Stimulus on screen, teams answer PG/PGK MCMA/PGK Kategori | Familiarity with TKA item forms | TKA forms from Paket Soal TKA | 5–6 |

**Manipulatives and simulations (no AI needed; teacher picks the setup):**

| Tool | Concept (subject) | Grades |
|---|---|---|
| Blok Puluhan | Place value, regrouping (Matematika) | 1–3 |
| Garis Bilangan | Addition, subtraction, negative numbers, fractions on a line | 1–6 |
| Pita Pecahan | Equal parts, equivalent fractions, comparing | 3–6 |
| Jam Analog | Reading time, elapsed time | 1–3 |
| Uang Rupiah | Buying, change, simple budget (Matematika, IPAS) | 1–4 |
| Bangun Datar & Jaring-jaring | Shapes, symmetry, area/perimeter on a grid, nets of solids (three.js, D-033) | 2–6 |
| Diagram Data | Build a pictogram or bar chart from class data (ApexCharts, D-033) | 2–6 |
| Peta & Skala | Indonesia map, provinces, distance with a scale bar (IPAS) | 4–6 |
| Huruf & Suku Kata | Syllable blocks, word building (B. Indonesia) | 1–2 |
| Hijaiyah & Harakat | Letter forms, harakat, reading practice (PAI, Al-Qur'an Hadis) | 1–4 |
| Aksara Jawa | Letter matching and tracing (Bahasa Jawa, muatan lokal) | 3–6 |

**Classroom helpers:** timer and stopwatch, random group maker from the class list, turn rotation ("who has not come to the board yet"), name picker, blank board with grid and lined backgrounds, traffic-light signal.

**Pricing (D-175):** manipulatives and classroom helpers use no AI and are part of **Sigrak Guru** for every paying teacher, so the value of the IFP mode is visible without the add-on. The game templates, the playlist, and the learning loop stay in the **Game kelas** add-on.

**Order (D-176):** after Quiz Battle is IFP-ready, the first new templates are **Lomba Tim**, **Pilah & Kelompokkan**, and **Pasangkan**. This learning-function list replaces the mechanic list "Drag & Drop, Word Puzzle, Maze, Flashcard" in D-014.

### 3.3 Coverage map by subject and phase

| Subject | Fase A (1–2) | Fase B (3–4) | Fase C (5–6) |
|---|---|---|---|
| Matematika | Blok Puluhan, Jam Analog, Uang Rupiah, Lomba Tim | Pita Pecahan, Garis Bilangan, Diagram Data | Bangun & Jaring-jaring, Peta & Skala, Pemanasan TKA |
| Bahasa Indonesia | Huruf & Suku Kata, Pasangkan (gambar–kata) | Urutkan (alur cerita), Pilah (jenis kata) | Pemanasan TKA (literasi teks informasi/fiksi), Pilah (fakta/opini) |
| IPAS | Pilah (makhluk hidup/benda mati) | Urutkan (daur hidup), Uang Rupiah | Peta & Skala, Pasangkan (organ–fungsi) |
| Pendidikan Pancasila | Pilah (sikap baik/kurang baik) | Pasangkan (simbol–sila) | Urutkan, Papan Petak |
| Bahasa Inggris | Pasangkan (picture–word) | Lomba Tim (vocabulary) | Urutkan (sentence building) |
| PJOK | Quiz before movement turns (rules, health) | Same | Same |
| Seni dan Budaya | Pasangkan (alat musik–daerah) | Urutkan (pola ritme) | Pilah |
| Bahasa Jawa (mulok) | Pasangkan (*tembung*) | Aksara Jawa | Aksara Jawa, Urutkan (*unggah-ungguh*) |
| Pendidikan Agama (six religions, SD) | Pasangkan, Urutkan (doa, tata ibadah) | Pilah, Kotak Kejutan | Papan Petak, Urutkan |
| MI: Al-Qur'an Hadis | Hijaiyah & Harakat | Pasangkan (surah–isi) | Urutkan (ayat), Lomba Tim |
| MI: Akidah Akhlak | Pilah (akhlak terpuji/tercela) | Pasangkan (Asmaul Husna–arti) | Kotak Kejutan |
| MI: Fikih | Urutkan (wudu, salat) | Pilah (najis, hadas) | Urutkan (zakat, puasa) |
| MI: SKI | — (starts at grade 3) | Urutkan (garis waktu) | Pasangkan (tokoh–peristiwa) |
| MI: Bahasa Arab | Pasangkan (mufradat–gambar) | Lomba Tim (mufradat) | Urutkan (*jumlah*) |

Content always comes from SIGRAK's curriculum data (`docs/data/curriculum/`) and the teacher's own TP, so the map grows with the data, not with hand-made game packs.

## 4. Integration with existing SIGRAK modules

| Module | How Game kelas connects |
|---|---|
| **Buat → RPM** | Each meeting step can hold a Game kelas activity. The RPM wizard's learning model "Game Based Learning" suggests one. The RPM PDF prints the activity name and goal, not the game |
| **Bank Soal** | Practice templates read items from a saved set (same rule as Quiz Battle: auto-gradable types). Distractor explanations come from the item's *pembahasan* |
| **LKPD** | An LKPD can end with "Lanjut di Game kelas" (group work on paper, check on the panel), and a manipulative can be printed as a worksheet image |
| **Paket Soal TKA** | Pemanasan TKA reuses items of a package without starting a CBT session |
| **Hari Ini** | Today's lessons show a "Buka Game kelas" button next to the RPM meeting |
| **Di Kelas → Jurnal** | After the session, the journal entry is pre-filled: activities run, time used, class result summary (D-016 offline sync applies to the journal, not to the game) |
| **Asesmen** | The class summary per TP is a formative record. It can be attached to the assessment plan as *asesmen formatif* evidence; per-student scores only when the teacher uses the response method in §6.2 |
| **Dokumen Saya** | Activities and playlists are documents with validation status and comments, like other content |
| **Portal Kepala Sekolah / Pengawas** | Usage report for both principal and supervisor (D-178): how often Game kelas runs per class and subject, and which TPs were practised. Useful for supervision and for reporting the school's IFP use |
| **Business (D-158, D-159, D-175)** | The IFP mode is the content of the **Game kelas** add-on. Manipulatives and classroom helpers are in Sigrak Guru. The 7-day teacher trial still applies |

## 5. Learning loop: from play to evidence

A game alone shows nothing to the teacher once the screen closes. Game kelas **always keeps** a small, **class-level** summary on the teacher's session (D-172); the teacher can delete it:

| Data kept | Granularity | Student data? |
|---|---|---|
| Items shown, correct/incorrect per attempt | Per item, per team | No |
| Most chosen wrong option per item | Per item | No |
| Result per TP (share correct) | Per TP, per class, per date | No |
| Time spent per activity | Per activity | No |

From this summary SIGRAK shows, after the lesson:

1. **"Perlu diulang"**: TPs or items below a threshold (for example under 60% correct), with the most common wrong answer and its explanation.
2. **Saran tindak lanjut** (AI, from TP and the wrong-answer pattern only, no student data): a short remedial activity for the next meeting, which the teacher can drop into the next RPM meeting with one tap.
3. **Jurnal** text, pre-filled.

This respects the present-mode rule that scores live in the teacher's session and no student data is stored (D-011): the summary is about items and teams. Per-student capture comes only from Kartu Jawab (§6.2, D-174), in a later step.

## 6. IFP-specific design requirements

### 6.1 Screen, touch, and device

| Requirement | Reason |
|---|---|
| 16:9 landscape layouts at 1920×1080 and 3840×2160; a portrait fallback only for phones | IFPs are landscape 65–86 inch panels |
| Touch targets at least 80 px at 1080p, text at least 32 px; key content in the lower two-thirds | Grade 1–2 students are short and stand close to the screen |
| Multi-touch: 2–4 independent zones (split screen), each with its own input | Teams play at the same time without stealing touches |
| No hover, no right-click, no long-press menus, no system keyboard (custom keypads) | The panel's on-screen keyboard covers the game |
| Works in the IFP's built-in Android browser/WebView, not only desktop Chrome | The Kemendikdasmen units run Android; the reference test device is that unit (D-179). Its Android and WebView versions are not published, so check them on a real unit in a pilot school before building |
| Sound through the panel speaker with a global mute; no sound needed to play | Classroom noise and hearing-impaired students |
| Offline: every template also works as *Unduh game* HTML (D-011) and can run from a USB drive | Panels can work without internet; school connections drop |
| No AI or network call during play | Play must not stall on Gemini quotas (D-012) or signal |

### 6.2 Shared device and privacy

The IFP is a shared screen in front of the class. Typing the teacher's password on it is a risk (students watch, and the browser may stay logged in).

- **Kode Tayang (D-173).** The teacher opens the `/pid` page on the app host (D-233) on the IFP, which shows a 6-digit code or QR. The teacher confirms it on their phone. The IFP then gets a **present-only** session: it can play today's Game kelas items and nothing else (no student list, no documents, no settings). It ends when the teacher taps *Selesai* on the phone or after a time limit.
- **Phone as remote (D-173).** The paired phone shows next/previous, reveal answer, the answer key, and the run sheet. The teacher can walk around the class, and the key never appears on the big screen.
- **Kartu Jawab (D-174, last step).** Per-student responses without student devices. Each student holds a printed card with a number and four sides (A–D). The teacher scans the raised cards with the phone camera. The app maps card numbers to the roster on the teacher's phone, so the exit ticket gives per-student results without any student touching a device or logging in. Names never show on the IFP. This is the only path from a game to per-student scores (§5), so it comes after the class-level loop is proven.
- On the IFP, only team names and team scores are shown; no public ranking of individual students.

### 6.3 Inclusion

- *Mode Tenang:* no timer, no elimination, no "kalah" screen; suitable for grade 1 and for students with special needs (D-155 covers inclusive schools).
- Read-aloud of items for early readers, using pre-generated audio or the device's speech engine when available (must still work silently offline).
- High-contrast theme and colour-blind safe team colours (design system tokens).
- Turn rotation from the class list so quiet students get a turn at the board.

## 7. Guardrails: learning, not only playing

Every Game kelas activity must pass these checks before it can be saved (template rules, checked by code):

1. It has at least one `kode_tp` inside the teacher's scope.
2. It states a lesson phase (pembuka, inti, penutup) and a duration (default 5–15 minutes; longer needs a reason).
3. Every auto-graded item has a key **and** a short explanation; distractors should reflect a real misconception.
4. It ends with a debrief prompt ("Kenapa jawaban B salah?") shown on screen.
5. Speed-based scoring is off by default for grades 1–3; accuracy first.
6. No AI images (D-030); pictures come from the bundled asset set.

## 8. Recommendations beyond the benchmark

Ideas the two sites do not have and that fit SIGRAK's strengths:

1. **Start from the RPM, not the catalogue.** The "Buka Game kelas" button on today's lesson removes the decision "which game?" and keeps the activity on the TP. This is SIGRAK's clearest difference.
2. **One question set, many formats** (as DigiEd's Excel quiz), but fed from Bank Soal with `kode_tp`, so practice results line up with assessment.
3. **Misconception-aware distractors.** When AI writes options, each wrong option names the misconception it tests, and the class summary reports misconceptions, not just scores.
4. **Kode Tayang + phone remote** (§6.2). Solves the shared-screen login problem that free sites avoid only by having no accounts.
5. **Kartu Jawab** (§6.2). Per-student formative data in classes where students have no devices, which is most SD/MI classes.
6. **Paket mingguan offline (D-181, with the core).** Download all of next week's Game kelas activities as one offline bundle (one HTML per activity, or one launcher page) for a USB drive.
7. **MI and religion packs from curriculum data.** Hijaiyah, tajwid basics, doa harian, wudu and salat steps, SKI timelines, mufradat; and equivalent packs for Kristen, Katolik, Hindu, Buddha, Khonghucu in SD. Neither benchmark serves these well.
8. **Bahasa Jawa and aksara Jawa** for the pilot region (Kudus, Jepara, Pati).
9. **Kokurikuler support.** Diagram Data and Peta & Skala let a class collect and show its own project data on the panel.
10. **Grade 6 TKA warm-ups** in game form, so the unfamiliar PGK formats are practised long before the Simulasi TKA session.
11. **Principal usage report** (§4). Shows the school uses its IFP for learning, which helps supervision and programme reporting.
12. **Teacher run sheet in every template.** A 5-line guide (goal, roles, time, how to rotate turns, debrief question) shown on the phone, so the activity is run well the first time.
13. **Sharing inside the school or KKG.** A teacher can share a Game kelas activity with colleagues in the same school (no public links, D-010 rules stay).
14. **Camera and movement activities later (D-180).** Papan Interaktif shows that body-movement games excite students, but they need camera permission and heavier processing. They come last, and only where the movement is part of the learning and tied to a TP (PJOK, counting jumps), never as decoration.

## 9. Phasing

| Step | Content | Depends on |
|---|---|---|
| 0 | Make Quiz Battle IFP-ready: §6.1 requirements, split-screen team mode, explanation and debrief screens | P5-05 |
| 1 | Game kelas shell: playlist from an RPM meeting, goal screen, exit ticket, class summary, journal pre-fill, classroom helpers, weekly offline bundle (D-181) | Step 0, P5-07 |
| 2 | Three new templates (D-176): Lomba Tim, Pilah & Kelompokkan, Pasangkan | Step 1 |
| 3 | Kode Tayang and phone remote | Step 1, security review |
| 4 | Manipulatives for Matematika Fase A–B (Blok Puluhan, Garis Bilangan, Pita Pecahan, Jam Analog, Uang Rupiah) | Step 1 |
| 5 | Asesmen link, "Perlu diulang" and AI follow-up suggestions; principal usage report | P5-08, P5-09 |
| 6 | Content packs, first MI (Al-Qur'an Hadis, Bahasa Arab, Fikih, Akidah Akhlak, SKI) and Bahasa Jawa with aksara Jawa (D-177); then the six SD religions. Also Pemanasan TKA, Urutkan, Papan Petak, Kotak Kejutan | Steps 2–5, curriculum data sign-off (D-113) |
| 7 | Kartu Jawab (D-174); camera activities tied to a TP (D-180) | Steps 1–6 |

**Success measures (pilot):** share of RPM meetings with a Game kelas activity; sessions per class per week; share of sessions that reach the exit ticket and debrief; teachers' rating of "membantu siswa paham" after use; time from opening the RPM to first activity on the panel (target under 1 minute).

## Decisions

Recorded in `docs/agent/DECISIONS.md` after the owner interview on 2026-11-04.

| ID | Decision |
|---|---|
| D-171 | The add-on keeps the name **Game kelas** and becomes the IFP learning mode described here |
| D-172 | The class-level, item-level result summary (no student data) is always kept and feeds Jurnal and Asesmen; the teacher can delete it |
| D-173 | Kode Tayang present-only session on the IFP, plus the phone as remote |
| D-174 | Kartu Jawab per-student responses, in the last step |
| D-175 | Manipulatives and classroom helpers are in Sigrak Guru; game templates stay in the add-on |
| D-176 | Template roadmap by learning function replaces D-014's later list; first Lomba Tim, Pilah & Kelompokkan, Pasangkan |
| D-177 | First content packs: MI subjects and Bahasa Jawa with aksara Jawa; SD six religions after |
| D-178 | Usage report for both principal and supervisor |
| D-179 | Reference test device is the Kemendikdasmen IFP unit |
| D-180 | Camera and movement activities only later and only when tied to a TP |
| D-181 | Weekly offline bundle ships with the Game kelas core |
| D-233 | Kode Tayang page at `/pid` on the app host (not a `papan.` subdomain, not `/papan`) |

## Open questions

1. Android and WebView version of the Kemendikdasmen IFP in pilot schools: to be checked on a real unit (D-179). Who arranges access to two or three panels?
2. ~~Hostname or path for the Kode Tayang page~~: resolved, `/pid` on the app host (D-233).
3. ~~Final docs still list the old D-014 game list~~: resolved, `product-concept.md` and `content-engine.md` follow D-176 (2026-11-08).
