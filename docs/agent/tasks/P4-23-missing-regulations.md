# P4-23 — Get and extract missing regulations

- **Phase:** P4
- **Status:** done (merged 2026-11-11, PR #73; the four regulations not reached moved to P4-32)
- **Owner:** `p4/p4-23-missing-regulations`
- **Depends on:** —
- **Can run in parallel with:** P4-22 (taxonomy and regulation registry), P5-03i/j, docs consolidation

## Goal

Put the official PDFs of the regulations listed in `policy-innovation-layers.md` §11 into `docs/sources/` with sha256 and a verification level, extract the SD/MI parts to `docs/data/regulations/` (draft, D-203), re-check KMA 512/2026, and update the research digest (D-362).

## Read first

- `docs/agent/CONTEXT.md`, `docs/agent/PROGRESS.md`
- `docs/final/research-digest.md` §2, §16
- `docs/drafts/policy-innovation-layers.md` §6.4 (workflow), §9.2 (codes), §11 (sources still needed)
- `docs/data/regulations/README.md` (file shape)

## Output

- New PDFs in `docs/sources/` (descriptive names, D-203, no legacy brand, D-360) and their rows in `docs/sources/README.md`
- New extracts in `docs/data/regulations/` and rows in its `README.md`
- `docs/final/research-digest.md`, `docs/pdf/src/research-digest.md`, `docs/pdf/research-digest.pdf`

## Done when

- [x] Every regulation that could be reached is in `docs/sources/` with sha256, download URL, date, and `verification`
- [x] SD/MI parts extracted with PDF page numbers, `status: draft`, `extraction.verified: false`
- [x] KMA 512/2026 re-checked
- [x] Research digest §2, §16, §17 updated, Indonesian PDF rebuilt
- [x] What could not be got is listed here and in the PR, with nothing made up

## Result

**Network.** From the work environment, jdih.kemendikdasmen.go.id, peraturan.go.id, jdih.kemdikbud.go.id, kurikulum.kemendikdasmen.go.id and internal-portal.kemdikbud.go.id did not answer (proxy: tunnel closed or gateway could not connect), and peraturan.bpk.go.id returned a bot challenge (403). These look like the sites refusing foreign traffic, not the environment's policy. www.kemendikdasmen.go.id and some ministry-unit and regional-office (.go.id) sites did answer, so the PDFs below come from there. pasal.id was used only as a lead, never as a source.

**Got (2026-11-10/11).** Codes follow `policy-innovation-layers.md` §9.2.

| Code | File in `docs/sources/` | sha256 | Verification | Set / promulgated (in force) | Relations | Extract |
|---|---|---|---|---|---|---|
| `PERMENDIKDASMEN-10-2025` | `Permendikdasmen_No_10_Tahun_2025_Standar_Kompetensi_Lulusan.pdf` | `207a66b76105aa3a180b6e50213be8ef44df15ff040efbcdb6305be86f7ae5b4` | official_copy (www.kemendikdasmen.go.id) | 2025-06-10 / 2025-06-13 (13 Jun 2025), BN 2025/410 | revokes PERMENDIKBUDRISTEK-5-2022 | `graduate-competency-standard-permendikdasmen-10-2025.json` |
| `PERMENDIKDASMEN-12-2025` | `Permendikdasmen_No_12_Tahun_2025_Standar_Isi.pdf` | `184497add24f2baaf72e94d1fddfd842d1e3656d8bb37bb222bd41eaa7dbdd16` | official_copy (www.kemendikdasmen.go.id) | 2025-07-10 / 2025-07-15 (15 Jul 2025), BN 2025/502 | revokes PERMENDIKBUDRISTEK-8-2024 | `content-standard-permendikdasmen-12-2025.json` |
| `PERMENDIKDASMEN-26-2025` | `Permendikdasmen_No_26_Tahun_2025_Standar_Pengelolaan.pdf` | `1f992433483d543c5d1e051518b082555f9e8932818fa13674cdb79ca0074500` | **mirror** (JDIH salinan re-hosted by Disdikbud Kab. Musi Banyuasin) | 2025-12-16 / 2025-12-17 (17 Dec 2025), BN 2025/1079 | revokes PERMENDIKBUDRISTEK-47-2023 | `management-standard-permendikdasmen-26-2025.json` |
| `PERMENDIKDASMEN-6-2026` | `Permendikdasmen_No_6_Tahun_2026_Budaya_Sekolah_Aman_dan_Nyaman.pdf` | `3e6d3deab3fdad8f6dd0d57b5e22eebc396596bb45051ed7c14b66b5c4a7d36f` | official_copy (www.kemendikdasmen.go.id, "BSAN (JDIH)") | 2026-01-08 / 2026-01-09 (9 Jan 2026), BN 2026/11 | revokes PERMENDIKBUDRISTEK-46-2023; implemented by KEPMENDIKDASMEN-17-2026 (not got) | `safe-school-culture-permendikdasmen-6-2026.json` |
| `PERMENDIKBUDRISTEK-21-2022` | `Permendikbudristek_No_21_Tahun_2022_Standar_Penilaian_Pendidikan.pdf` | `cd412d083cf5dbfe7dc0b05c41f536d378d7fcaa9fc2421b92f2124e7b732fc9` | **mirror** (JDIH salinan re-hosted by Disdikbud Kab. Batang) | 2022-04-26 / 2022-04-28 (28 Apr 2022), BN 2022/460 | revokes Permendikbud 23/2016, 53/2015 and parts of 137/2014, 34/2018, 43/2019 | `assessment-standard-permendikbudristek-21-2022.json` |
| `KEPKA-BSKAP-046-H-KR-2025` | `Kepka_BSKAP_No_046_H_KR_2025_Capaian_Pembelajaran.pdf` (1,691 pp., 15 MB) | `45809d2c88a612f90ee5adbd60d71a8952527d6924c626d126ea6926ae3d3394` | official_copy (ministry unit BPMP Babel, not JDIH) | 2025-07-16 (on setting) | revokes Kepka BSKAP 32/H/KR/2024; implements PERMENDIKBUDRISTEK-12-2024 Pasal 11(1) as amended by PERMENDIKDASMEN-13-2025; amended by BKPDM 020/2026 | `cp-decree-sd-index-kepka-bskap-046-h-kr-2025.json` (page index only) |

Check: the copy of Permendikdasmen 13/2025 served by www.kemendikdasmen.go.id (`/download/file/1392`) has the same sha256 as the owner-supplied file already in the repo (`2fab2e91…`), so that site serves the JDIH salinan unchanged.

**KMA 512/2026.** The file in `docs/sources/` matches the sha256 recorded in `docs/sources/README.md` (`e637aad3…`); the decree page (PDF p. 3, OCR layer) confirms "Ditetapkan … 31 Maret 2026" and "mulai berlaku pada tanggal ditetapkan". No official-channel copy could be fetched (jdih.kemenag.go.id unreachable), so it stays the owner-supplied scan. Its SD scope question is settled by D-368.

**Not got (still needed).**

| Code | What | Why not | Lead |
|---|---|---|---|
| `PERMENDIKDASMEN-1-2026` | Standar Proses | Only on peraturan.go.id / JDIH / BPK, all unreachable; not on www.kemendikdasmen.go.id (file ids 1380–2250 scanned) | peraturan.go.id/files/Permendikdasmen-no-1-tahun-2026.pdf; BPK Details/344196 |
| `PERMENDIKDASMEN-9-2026` | Asesmen Nasional (amends Permendikbudristek 17/2021) | same | peraturan.go.id/files/Permendikdasmen-no-9-tahun-2026.pdf; BPK Details/345735 |
| `KEPMENDIKDASMEN-17-2026` | Pedoman Penyelenggaraan BSAN | JDIH unreachable; the one .go.id copy found (Disdikbud Kab. Sarolangun) answered 403 | JDIH produk-hukum page linked from cerdasberkarakter.kemendikdasmen.go.id |
| `PERMENDIKBUDRISTEK-12-2024` | Kurikulum, base text | BPK and the kemdikbud.go.id unit copies unreachable | peraturan.bpk.go.id Download/340776; bpmpkaltara.kemdikbud.go.id upload |

No content of these four was written into the repo; the digest rows for 1/2026 stay `mirror` (pasal.id).

**Findings that change other docs** (for the coordinator; files owned by other sessions):

- `policy-innovation-layers.md` §11: move 10/2025, 12/2025, 26/2025, 6/2026, 21/2022 and BSKAP 046 from "still needed" to "in `docs/sources/`"; keep 1/2026, 9/2026, Kepmen 17/2026, base 12/2024; mark 26/2025 and 21/2022 as mirror copies to replace.
- P4-22 regulation registry: fill `source_file`, `sha256`, `verification`, dates, and relations from the table above.
- `administration-features.md` / `teacher-supervision-instruments.md`: 6/2026 class duties are in **Pasal 18(3)**, not Pasal 13; in SD the *guru kelas* holds them, "wali kelas" is named only for SMP/SMA/SMK. 26/2025 adds **one learning session per day** (Pasal 17, 3-year transition in Pasal 26) and **max 24 rombel per SD** (Pasal 9).
- Standar Isi 12/2025 puts **Teknologi Digital** (computational thinking, digital literacy, basic AI) under the Matematika muatan for SD; relevant to KKA placement and P4-30.
- 21/2022: **SD graduation considers kelas V and VI** reports (Pasal 10(2)).
- CONTEXT.md line "Official 2025 curriculum rules and guides … `docs/data/regulations/`" could mention the national standards (SKL, Isi, Pengelolaan, Penilaian, BSAN) now there.

**Owner answers (2026-11-11):** Q1 keep the two mirror copies (26/2025, 21/2022) until JDIH copies can be fetched; Q2 keep the full CP decree file unchanged in the repo. Q3 is still open.

**Open questions for the owner** (as asked):

1. Keep the two **mirror** copies (26/2025, 21/2022) until a JDIH copy can be fetched, or should the owner download the JDIH files and upload them? *Suggested: keep, replace later and re-hash.*
2. The CP decree PDF is 15 MB (1,691 pages). Keep the full file in the repo (it is the CP source for P4-29/P4-30), or keep only the SD/MI pages? *Suggested: keep the full official file unchanged.*
3. Can the owner fetch the four missing PDFs from an Indonesian connection (JDIH or peraturan.go.id) and add them to `docs/sources/`?
