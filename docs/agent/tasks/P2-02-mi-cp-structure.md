# P2-02 — Structure the MI CP data and retype Arabic

- **Phase:** P2
- **Status:** done
- **Owner:** p2/p2-02-mi-cp-structure
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-01, P2-03…P2-06

## Goal

Turn the raw MI CP text (Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, Bahasa Arab; Fase A–C) into structured element/sub-element rows like the SD religion data, and retype every Arabic passage correctly.

## Read first

- `docs/drafts/religious-education.md` (MI section)
- `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` (raw text; see its `extraction.note`)
- `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` (target shape)
- `docs/sources/SK_Dirjen_Pendis_No_9941_Tahun_2025_CP_PAI_dan_Bahasa_Arab_pada_Madrasah.pdf` PDF pages 13–16, 44–48, 70–73, 90–92, 99–101 (has a text layer; Arabic must be read from the page images)

## Output

- `docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` rewritten: per subject `elements` (with sub-elements, e.g. Pemahaman Konsep → Tajwid / Al-Qur'an / Hadis; Keterampilan Proses → Mengamati …), per phase `outcomes[] {element, sub_element, text}`; keep `raw_text` per phase for traceability; `extraction.structured: true`.
- Arabic passages retyped in Unicode with harakat, right-to-left order checked; list them in **Result**.

## Done when

- [x] All five subjects, all MI phases (SKI: B–C only) structured
- [x] Every Arabic passage retyped and checked against the page image
- [x] JSON valid; no text lost compared with `raw_text`

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-02-mi-cp-structure`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

**Done (2026-10-30).** `cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json` is rewritten with `extraction.structured: true` (still `verified: false`).

- **Shape.** Per subject, `elements[] {name, sub_elements[]}`. Per phase, `lead_in`, optional `element_intros[]`, `outcomes[] {element, sub_element, text}`, and the untouched `raw_text`. 14 phases, 99 outcome rows. Conventions are in `extraction.conventions`.
- **Sub-elements.** Al-Qur'an Hadis: Tajwid / Al-Qur'an / Hadis. Akidah Akhlak: Akidah / Akhlak / Adab / Kisah Keteladanan. Fikih: Ibadah (A–C) and Muamalah (C only). No phase has Ushul Fikih. SKI Pemahaman Konsep and both Bahasa Arab elements have no sub-heading, so `sub_element` is null. Keterampilan Proses sub-elements use the names printed in each phase, and these differ between subjects and phases.
- **Text check.** A script compared the Latin words of each phase with `raw_text`. Only the phase headings, the stray text-layer "s" (Akidah Akhlak A) and "Mengomunikasikanhasil" (renamed) are missing, all on purpose.
- **Arabic.** Only Bahasa Arab has Arabic script. The six passages (Fase A, B, C × 2, PDF pp. 99–101) were retyped from the page images with full harakat in logical order. Each is inline in `text` and repeated in `arabic_passages[]`:
  - A1 (p. 99): مَا اسْمُكَ؟، مَا اسْمُكِ؟، اِسْمِيْ...، … هٰذِهِ مَدْرَسَةٌ
  - A2 (p. 99): مَا هِوَايَتُكَ؟; … ذٰلِكَ بَحْرٌ
  - B1 (p. 100): مَا دَرْسُنَا الْآنَ؟; … ذٰلِكَ أَنَانَاسٌ وَتِلْكَ زَهْرَةُ الْوَرْدَةِ
  - B2 (p. 100): مِنْ أَيْنَ أَنْتِ؟ … هَلْ أَنْتَ إِنْدُوْنِيْسِيٌّ؟
  - C1 (p. 101): هٰذَا أُذُنٌ، أَنَا أَسْمَعُ بِالْأُذُنِ; … نَعَمْ، أَنَا جَوْعَانُ
  - C2 (p. 101): كَمْ السَّاعَةُ الْآن؟; … ذَهَبْتُ إِلَى شَاطِئِ الْبَحْرِ

  Per phase, the letter counts (harakat ignored) match the text layer. The only gaps are letters the text layer drops inside ligatures, and the page images show them (`extraction.arabic_check`).

**Open questions / follow-ups**

- Possible source errors: the owner decided (D-127, replacing D-115) that the app shows the printed text. The 9 affected rows carry `review_notes[]` for the reviewers: خَالِيْ (should be عَمِّيْ), كَمْ (should be كَمِ السَّاعَةُ), مَالَوْنُ spacing, the repeated "berupa kritik sosial" (Fikih B/C), madarasah, peyelidikan (SKI B/C), kongkrit.
- Review by a guru of each subject and an Arabic reader (D-037) is still needed before import.
- `docs/drafts/religious-education.md` still says the MI CP "is not yet split into element rows". I did not edit it (P1-07 draft, also near P1-08). The integration pass should update that line.

**Decisions to record**

- MI CP rows follow the SD shape (`element`, `sub_element`, `text`) and add `lead_in`, `element_intros`, and, for Bahasa Arab, `arabic_passages[] {pdf_page, text}`. Arabic stays inline in `text` so no text is lost. UIs and PDFs render `text` with bidi isolation, or show `arabic_passages` as separate RTL blocks.
- Outcome text keeps source typos as printed. Only sub-element names are normalised, because they act as keys.
