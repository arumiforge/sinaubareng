# P2-09 — TP and ATP for five SD religion subjects

- **Phase:** P2
- **Status:** review
- **Owner:** p2/p2-01-verify-religion-cp
- **Depends on:** P2-01 (verified CP)
- **Can run in parallel with:** other P2 tasks (touches only its own data file and this row)

## Goal

Draft topics (materi pokok/ATP), TP, fokus materi, and JP for Pendidikan Agama Kristen, Katolik, Hindu, Buddha, and Khonghucu dan Budi Pekerti, SD grades 1–6, semesters 1–2, from the verified CP, so the RPM wizard can offer them like PAI. Tim Validasi SIGRAK reviews the set afterwards (D-112, D-113).

## Read first

- `docs/drafts/religious-education.md` (SD section, Data work item 3)
- `docs/drafts/data-model.md` (Curriculum)
- `docs/sources/Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md` (KM-SD-2026-R2 shape and PAI allocation)
- `docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json`

## Output

- `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json`

## Done when

- [x] 5 topics per semester per grade for each religion (300 topics), each with TP and fokus
- [x] Every CP element/sub-element of each phase covered; JP matches the PAI allocation (54/54, grade 6 54/42)
- [x] Codes unique; fokus linked to TP; review status pending for Tim Validasi SIGRAK
- [x] JSON valid; Result filled in

## Coordination rules (parallel sessions)

- Edit only this file, the output file, and this task's row in `PROGRESS.md`.
- Do not edit `DECISIONS.md` or `CONTEXT.md`; list decisions under "Decisions to record" in Result.

## Result

Drafted `docs/data/curriculum/tp-atp-pendidikan-agama-sd-2026.json` (version `TPATP-PABP-SD-2026-R1`, `status: draft`).

**Contents:** for each of Kristen, Katolik, Hindu, Buddha, and Khonghucu there are 60 topics (5 per semester, grades 1–6), 153 TP, and 212 fokus. That is 300 topics, 765 TP, and 1,060 fokus in total.

**Checks (all scripted):**
- Every CP element or sub-element with text in each phase has at least one topic. Kristen "Allah Penyelamat" and "Allah Pembaru" are not used in Fase A, because they have no CP there.
- Semester JP matches PAI: 54/54, and 54/42 in grade 6.
- Each fokus is 3 JP (one week). The fokus JP add up to the topic JP, and the topics fill the semester weeks exactly.
- All codes are unique, and every fokus links to a TP (`objective_codes`).

**How it was made:** an AI-assisted draft from the verified CP text. Each topic takes its `cp_text` from the CP dataset, and religious terms follow the CP's spelling (Sanskrit diacritics, pinyin with characters). `authoring.review` is `pending` for Tim Validasi SIGRAK (D-112, D-113).

**Open points for the reviewer:**
- **Fokus–TP links:** these were assigned by order. Check them per topic.
- **Book references:** `references.book` names the Kemendikbudristek student book for each grade, and `book_pages` stays `null` until someone checks the edition.
- **Hindu local terms:** examples such as pura/sanggah, Galungan, and Mpu Kuturan are Bali-centred. Add examples from other regions where schools need them, since the CP says "sesuai kearifan lokal".
- **Khonghucu names and characters:** check the names and characters of historical figures (e.g. 叔梁纥, 颜徵在, 孟母三迁, 精忠报国, 福德正神) and the list of delapan keimanan.
- **Fokus titles:** these name one week of content and are not exact lesson titles. Teachers' RPM still splits them into meetings.

**Follow-ups:**
- `docs/drafts/religious-education.md`, Data work item 3, and `docs/drafts/data-model.md` ("built by the team") should link this file once it is approved.
- The importer needs to accept this file. Its shape is the data model's (`topics`, `learning_objectives`, `focus_items`, `focus_item_objectives`), not the EDUNEXA JSON-string columns.

**Decisions to record:**
- New curriculum version `TPATP-PABP-SD-2026-R1` for the five non-Islam SD religion subjects, with subject codes PAKR, PAKT, PAH, PAB, and PAKH.
- Religion TP/ATP data is stored in the data model's English-keyed shape, not in the EDUNEXA row shape.
- Allocation follows PAI (3 JP/week, 5 topics per semester), with one fokus per week of 3 JP.
