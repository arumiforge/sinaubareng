# P4-32 — Add the four remaining regulations

- **Phase:** P4
- **Status:** done
- **Owner:** `p4/p4-32-remaining-regulations`
- **Depends on:** P4-23
- **Can run in parallel with:** docs consolidation (other `docs/final/` files and `DECISIONS.md` are edited elsewhere)

## Goal

Store the four regulation PDFs that P4-23 could not fetch (the owner supplied them from an Indonesian connection), extract their SD/MI parts as draft data, and fill the regulation registry and the docs that still listed them as missing.

## Read first

- `docs/agent/CONTEXT.md`, `docs/agent/tasks/P4-23-missing-regulations.md` (pattern: names, sha256, verification, extract shape)
- `docs/data/regulations/README.md`, `docs/sources/README.md`, `docs/data/taxonomy/README.md`
- `docs/drafts/policy-innovation-layers.md` §11, `docs/final/research-digest.md` §1–2, §16–17

## Output

- Four PDFs in `docs/sources/` and their rows in `docs/sources/README.md`
- Four extracts in `docs/data/regulations/` and rows in its `README.md`
- `docs/data/taxonomy/regulations.json` (four entries, three relations) and the pending note in `docs/data/taxonomy/README.md`
- `docs/drafts/policy-innovation-layers.md` §11, `docs/drafts/open-questions.md` (fetch row removed)
- `docs/final/research-digest.md`, `docs/pdf/src/research-digest.md`, `docs/pdf/research-digest.pdf`

## Done when

- [x] Each PDF is in `docs/sources/` with sha256, dates, and an honest `verification`
- [x] SD/MI parts extracted with PDF page numbers, `status: draft`, `extraction.verified: false`
- [x] Registry entries filled, `pending_p4_23: false`, sha256 matches the file
- [x] §11, research digest (English and Indonesian PDF) and open questions updated

## Result

All four PDFs were supplied by the owner on 2026-11-11 without a download URL. Three are the e-signed promulgated originals (the Minister's and the Kementerian Hukum / Kemenkumham signatures validate with `pdfsig`, Berita Negara number stamped on the signature page), so they are `official_copy`. The Kepmendikdasmen 17/2026 file is the JDIH salinan (Biro Hukum certification, jdih footer) but was re-saved by a non-government community site that added an invisible watermark text layer on every page, so it is a `mirror`.

| Code | File in `docs/sources/` | sha256 | Verification | Set / promulgated (in force) | Extract |
|---|---|---|---|---|---|
| `PERMENDIKDASMEN-1-2026` | `Permendikdasmen_No_1_Tahun_2026_Standar_Proses.pdf` (9 pp.) | `4438bf42fe4c5624cea5514284fb08a68fbd44b91860b82fff102ebbd452b857` | official_copy (e-signed original) | 2026-01-02 / 2026-01-05 (5 Jan 2026), BN 2026/1 | `process-standard-permendikdasmen-1-2026.json` |
| `PERMENDIKDASMEN-9-2026` | `Permendikdasmen_No_9_Tahun_2026_Perubahan_Permendikbudristek_17_2021_Asesmen_Nasional.pdf` (5 pp.) | `737f5f07af7893d77fb33ab75e36798ff507d43a0eadd1271ea906221be4f716` | official_copy (e-signed original) | 2026-02-11 / 2026-02-12 (12 Feb 2026), BN 2026/98 | `national-assessment-permendikdasmen-9-2026.json` |
| `KEPMENDIKDASMEN-17-2026` | `Kepmendikdasmen_No_17_Tahun_2026_Pedoman_Penyelenggaraan_Budaya_Sekolah_Aman_dan_Nyaman.pdf` (80 pp.) | `e5e71f11b49bb27997e964066110488ef68345c67c88b6931950aa60e1f0aab1` | **mirror** (JDIH salinan altered by a third-party site) | 2026-02-06 / — (in force on setting, 6 Feb 2026) | `safe-school-culture-guide-kepmendikdasmen-17-2026.json` |
| `PERMENDIKBUDRISTEK-12-2024` | `Permendikbudristek_No_12_Tahun_2024_Kurikulum.pdf` (71 pp.) | `fb2f3ee0fe3af14a348c0c3c274095d6aaeab0d3896188cb0c507e8df1617c3f` | official_copy (e-signed original) | 2024-03-25 / 2024-03-26 (26 Mar 2024), BN 2024/172 | `curriculum-base-permendikbudristek-12-2024.json` (unamended articles only) |

Registry relations also updated: Kepmendikdasmen 56/2026 now `implements` 9/2026 Pasal 12 (its konsiderans cites it); Kepmendikdasmen 17/2026 implements 6/2026 Pasal 27; article numbers added to the 1/2026 and 12/2024 relations. The taxonomy README note on `pending_p4_23` now says every entry is false.

Everything in the four PDFs that the extracts cover was read in the text layer; the signature pages, page 1 of each file, and the SD role table of the BSAN guide (p. 33) and the referral question table (p. 53) were checked on the page image. Skipped parts are listed in each extract's `extraction.skipped`. Nothing was unreadable.

**Findings that change other docs** (not edited here; files owned elsewhere):

- **Guru kelas / wali kelas.** The BSAN guide (Kepmendikdasmen 17/2026, Tabel 3.5, PDF p. 33) names the SD and PAUD class role **"Guru Kelas atau Wali Kelas"**, with the same duty as 6/2026 Pasal 18(3) gives the guru kelas (main responsibility for class conditions, daily welcome and check, early detection, handling violations between murid). This softens the P4-23 finding that "wali kelas" is named only for SMP/SMA/SMK: that is true of the Permen, not of the guide. Affects the open question on SD "wali kelas" wording in `docs/drafts/open-questions.md` (Regulations and terms), `administration-features.md`, `teacher-supervision-instruments.md`, and the 6/2026 extract's open point and registry note. The guide's handling steps (pp. 50–51) assign interviews to the wali kelas, guru BK, guru wali, or another competent teacher; SD has no guru BK or guru wali in Tabel 3.5.
- **Kesepakatan kelas** (`administration-features.md` A8): the guide makes three contents mandatory ("setidaknya memuat": nilai kebajikan, interaksi yang saling menghargai dan menghormati, tindakan pembinaan yang mendidik) and gives four optional stages (persiapan, eksplorasi, perumusan, penyepakatan dan implementasi), signing by the whole class, display, weekly reminder, sharing with parents (pp. 35–37). Tata tertib/kode etik minimum content and yearly review (pp. 22–23), POS minimum content (p. 24), complaint channel first response within 24 hours recommended (p. 20).
- **Planning document minimum** stays Permendikdasmen 1/2026 **Pasal 5** (tujuan, langkah, asesmen). Pasal 9, unreadable before, is the learning atmosphere and the teacher roles keteladanan, pendampingan, fasilitasi; docs that point to Pasal 9 for the plan should point to Pasal 5. `policy-innovation-layers.md` §3 table "Check" column for Standar Proses (and Kurikulum 12/2024) can move from pasal.id to the official copy.
- **Process review** (1/2026): only the teacher's self-reflection is required (Pasal 15, at least once a semester after teaching); peer, principal and murid reviews are optional (Pasal 16 "dapat"), each at least once a semester when done, murid per subject. `teacher-supervision-instruments.md` S-11 cites Pasal 16 for self-reflection; it should be Pasal 15. 1/2026 does not contain "disiplin positif" (`administration-features.md` §1 lists it as 1/2026 vocabulary; the term is in the BSAN guide).
- **AN in SD/MI** (9/2026 Pasal 5(3)): participants are murid in the final grade (SD/MI kelas VI), every pendidik, and the principal; survei karakter now uses the 8 dimensi profil lulusan; Pasal 9A lets literacy and numeracy be measured inside the academic-achievement test of certain subjects (the TKA link). Matches "AN in the final grade" in `administration-features.md`; no AN simulator change needed. The base Permendikbudristek 17/2021 is still not in the repo.
- **Bahasa Inggris from 2027/2028**: 12/2024 Pasal 33 was not amended by 13/2025, so Bahasa Inggris in SD/MI is elective until 2026/2027 and compulsory from 2027/2028, while the 13/2025 SD tables (Tabel 3–5) already list it under Mata Pelajaran Wajib in kelas III–VI. `policy-innovation-layers.md` §6.3 example (optional in 2026/2027, mandatory from 2027/2028) stands, but Tim Validasi should confirm the 2026/2027 label; added as a research gap in the digest §16.
- 12/2024 Pasal 31 (unamended): Kurikulum 2013 only until 2025/2026 (3T regions 2026/2027). Pasal 24 (unamended): ekstrakurikuler participation stays voluntary even though Pramuka must be offered (13/2025 Pasal 22).
- **Mirror list**: Kepmendikdasmen 17/2026 joins 26/2025 and 21/2022 as a mirror to replace with the JDIH file; the open-questions mirror-replacement row (left unchanged here) lists only the first two.
- Personal-data rows in the BSAN guide (Tabel 2.15, Tabel 4.1 recording without consent, Pokja reports without murid names) agree with D-023, D-347, D-350; worth citing in the child-data docs.
