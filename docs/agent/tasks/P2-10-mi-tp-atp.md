# P2-10 — TP and ATP for the five MI Kemenag subjects

- **Phase:** P2
- **Status:** review
- **Owner:** p2/p2-01-verify-religion-cp
- **Depends on:** P2-02 (structured MI CP), P1-08 (MI allocation), P2-09 (same data shape)
- **Can run in parallel with:** other tasks (touches only its own data file, this file, and this row)

## Goal

Draft topics (materi pokok/ATP), TP, fokus materi, and JP for Al-Qur'an Hadis, Akidah Akhlak, Fikih, Sejarah Kebudayaan Islam, and Bahasa Arab at MI, grades 1–6 (SKI 3–6), semesters 1–2, from the structured CP and the KMA 1503/2025 allocation. Tim Validasi SIGRAK reviews the set afterwards (D-112, D-113).

## Read first

- `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json`
- `docs/data/curriculum/mi-allocation-kma-1503-2025.json`
- `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json` (shape)

## Output

- `docs/data/curriculum/tp-atp-pai-bahasa-arab-mi-2026.json`

## Done when

- [x] 4 topics per semester per grade for each subject (224 topics), each with TP and fokus
- [x] Every Pemahaman Konsep sub-element / Komponen Bahasa CP of each phase covered; JP matches the intrakurikuler allocation (36/36, grade 6 36/28)
- [x] Codes unique; fokus linked to TP; review status pending for Tim Validasi SIGRAK
- [x] JSON valid; Result filled in

## Coordination rules (parallel sessions)

- Edit only this file, the output file, and this task's row in `PROGRESS.md`. Do not touch `docs/drafts/*` or `docs/final/*` while P3-01 runs.
- Do not edit `DECISIONS.md` or `CONTEXT.md`; list decisions under "Decisions to record" in Result.

## Result

Drafted `docs/data/curriculum/tp-atp-pai-bahasa-arab-mi-2026.json` (version `TPATP-PAI-BA-MI-2026-R1`, `status: draft`).

**Contents:**

| Subject | Grades | Topics | TP | Fokus |
|---|---|---|---|---|
| Al-Qur'an Hadis | 1–6 | 48 | 126 | 212 |
| Akidah Akhlak | 1–6 | 48 | 118 | 212 |
| Fikih | 1–6 | 48 | 118 | 212 |
| Sejarah Kebudayaan Islam | 3–6 | 32 | 78 | 140 |
| Bahasa Arab | 1–6 | 48 | 118 | 212 |
| **Total** | | **224** | **558** | **988** |

**Allocation:** intrakurikuler only, 2 JP per week, 4 topics per semester. Each fokus is one week of 2 JP. A semester is 18 weeks (36 JP), and grade 6 semester 2 is 14 weeks (28 JP). The kokurikuler JP from KMA 1503/2025 (QH 1–2, Fikih 3–4, AA 5–6) are recorded per grade, but they belong to the Kokurikuler module.

**Checks (all scripted):**
- Every CP in Pemahaman Konsep (Bahasa Arab: Komponen Bahasa) has at least one topic in its phase.
- The fokus JP add up to the topic JP, and the topics fill the semester exactly.
- All codes are unique, and every fokus links to a TP.
- The Arabic sentence patterns quoted in the Bahasa Arab fokus come from the CP's printed patterns. They keep the CP's vocalisation, including its feminine forms. The patterns flagged in the CP's `review_notes` (خَالِيْ for a father's brother; كَمْ السَّاعَةُ) are not reused.

**How it was made:** an AI-assisted draft from the structured CP. Keterampilan Proses and Keterampilan Bahasa have no topics of their own; they are worked into the TP and fokus (observe, ask, present; listen, speak, read, write). `authoring.review` is `pending` for Tim Validasi SIGRAK.

**Open points for the reviewer:**
- **Surah choice:** the short surahs for Al-Qur'an Hadis Fase A–B are a proposed order, because the CP only says "surah pendek pilihan". Check it against the BTU.
- **Verses and hadiths:** no verse numbers or hadith texts are given. The fokus name only the theme from the CP.
- **Fikih in grades 1–2:** this phase has CP for Ibadah only, so all 16 topics are Ibadah. Fikih Muamalah starts in Fase C, as the CP sets.
- **SKI topics beyond the CP list:** Fathu Makkah, the letters to kings, and Hudaibiyah support "dakwah kepada kelompok nonmuslim" and "menjelang akhir hayat". Remove them if the team wants to stay strictly within the CP wording.
- **Fokus–TP links** are drafted by order, and `book_pages` stays `null`.

**Follow-ups:**
- Link this file from the religious-education doc and the data model once P3-01 has moved them to `docs/final/`.
- The importer needs to accept this file (same shape as P2-09).

**Decisions to record:**
- New curriculum version `TPATP-PAI-BA-MI-2026-R1`, with subject codes QH, AA, FIK, SKI, and BAR.
- MI topics count intrakurikuler JP only (2 JP/week, 4 topics per semester, one fokus per week). Kokurikuler JP stay with the Kokurikuler module.
