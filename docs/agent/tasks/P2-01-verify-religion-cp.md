# P2-01 — Verify the SD religion CP transcription

- **Phase:** P2
- **Status:** done
- **Owner:** p2/p2-01-verify-religion-cp
- **Depends on:** —
- **Can run in parallel with:** P1-08, P2-02…P2-06

## Goal

Check `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json` word by word against the scanned decree and fix every difference, so the data can be imported. (Content sign-off by a guru agama of each religion is a separate later step, D-037.)

## Read first

- `docs/drafts/religious-education.md` (SD section)
- `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json`
- `docs/sources/Salinan_Kepka_BKPDM_No._020_Tahun_2026_tentang_Perubahan_atas_Keputusan_Kepala_BSKAP_Kemendikdasmen_No._046_H_KR_2025.pdf` pages 6–8 (Islam), 12–14 (Kristen), 20–23 (Katolik), 29 (Hindu), 34–36 (Buddha), 40–42 (Khonghucu); scanned, read the page images

## Output

- The same JSON, corrected; `transcription.verified: true`, `transcription.verified_on`, and `transcription.verification` describing the method.
- In **Result** below: a list of every correction (religion, phase, element, old → new).

## Done when

- [x] Every outcome text, phase summary, element and sub-element name checked against the scan (96 outcomes, 6 summaries)
- [x] Diacritics (Arabic transliteration, Sanskrit, pinyin) and Chinese characters checked
- [x] JSON still valid; corrections listed in Result

## Coordination rules (parallel sessions)

- Work on branch `p2/p2-01-verify-religion-cp`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`).
- In `docs/agent/PROGRESS.md`, edit **only your own row** (status `review` when the PR is open). Do not add handoff notes there; write them in **Result** below.
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md`. If your work implies a decision, list it under a "Decisions to record" heading in your output document; it gets numbered when the parallel work is integrated.
- Do not edit files owned by other running tasks (see the Phase 2 table in `PROGRESS.md`). If you must, note it in Result instead.

## Result

Checked all six religions on pages 6–8, 11–14, 19–23, 28–29, 34–36 and 39–42 (element tables and CP sections). Running text was read at 150 dpi; every diacritic and Chinese character was checked on 300–400 dpi crops. The section page ranges were checked against the section headings.

**Corrections (religion, phase, element, old → new):** none. All 96 outcomes, 6 summaries, and the element and sub-element names match the scan word for word. The only change is the `transcription` metadata: `verified: true`, `verified_on`, `verification`, and clearer conventions.

**Normalisations documented in `transcription.conventions`** (the text matches the scan; only the encoding differs):

- Curly apostrophes stored as ASCII `'`. Italics not kept.
- Pinyin third tone: the scan prints a breve (Kŏngzĭ); it is stored with a caron (Kǒngzǐ).
- Element names follow the section C tables. The CP section writes Kristen "Terhadap Alam" with a capital T and Khonghucu "Perilaku Junzi" without tone marks.

**Source anomaly:** Khonghucu Fase A, Kitab Suci ends "Nabi Kǒngzǐ (孔)", one character, as printed. Kept as printed and listed in `transcription.source_anomalies` for the guru agama review (D-037).

**Follow-ups:**

- `docs/drafts/religious-education.md` (line 38 and open item 1) still says `verified: false`. That file is being edited by P1-08, so it was left alone. Update it in the integration pass.
- The 2026-10-30 handoff note in `PROGRESS.md` saying "still unverified" is now out of date.

**Decisions to record:** none.
