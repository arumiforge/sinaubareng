# KM-SD-2026-R2 source files: check against the data model

- **Task:** P5-03g · **Status:** draft · **Date:** 2026-11-08
- **Files:** [`docs/data/curriculum/data_kurikulum_dengan_cp.json`](../data/curriculum/data_kurikulum_dengan_cp.json), [`docs/data/curriculum/struktur_alokasi_sd.json`](../data/curriculum/struktur_alokasi_sd.json) (provenance in the [README](../data/curriculum/README.md))
- **Checked against:** [`docs/final/data-model.md`](../final/data-model.md) (Curriculum), the mapping doc [`Pemetaan_Data_Materi_EDUNEXA…`](../sources/Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md), D-204, D-208

This note is input for P5-03c (`curriculum:import`) and for a later `docs/final/data-model.md` update. Nothing in `docs/final/` was changed.

## 1. Counts

| | Mapping doc (2026-10-29) | Files (2026-11-08) |
|---|---|---|
| Topics (rows) | 401 (203 S1, 198 S2) | **432** (219 S1, 213 S2) |
| JP total | 3,628 S1 / 3,478 S2 | 3,844 S1 / 3,696 S2 |
| Fokus (`Data_Fokus`) | 1,900 | **2,021** |
| TP (`kode_tp`) | 1,272 | **1,372** |
| Subject names | 11 | **12** (new: Seni Suara Daerah) |
| `alokasi[]` rows | 52 | **58** (+6 Seni Suara Daerah) |

The difference is the new Seni Suara Daerah subject (31 topics, grades 1–6) and the grade 5 renumbering (see 4.3).

Topics per subject: Seni dan Budaya 61, PAI dan Budi Pekerti 60, Bahasa Jawa 45, Bahasa Inggris 44, Matematika 40, Bahasa Indonesia 38, PJOK 34, Seni Suara Daerah 31, IPAS 30, Pendidikan Pancasila 27, Koding dan Kecerdasan Artifisial 14, Keterampilan Tata Boga 8. Grades 1–6, Fase A (1–2), B (3–4), C (5–6).

## 2. What matches

- Every field in the data model's "Source field (KM-SD-2026-R2)" column exists in every row: `ID_Kurikulum`, `Legacy_ID`, `Legacy_ID_Aliases`, `Versi_Kurikulum` (always `KM-SD-2026-R2`), `Status_Versi` (always `Aktif` → `active`), `Kelas`, `Fase`, `Semester` (`Ganjil (1)` / `Genap (2)`), `Mata Pelajaran`, `Status_Mapel`, `Elemen`, `Materi Pokok`, `Capaian Pembelajaran`, `Data_Tujuan`, `Data_ATP`, `Data_Fokus`, `Fokus_Materi`, `Referensi`, `Audit_Data`. Optional: `Jenis_Pembelajaran` (20 rows), `Cabang_Seni` (61), `Keterampilan_Proses` (24).
- `struktur_alokasi_sd.json` has `alokasi[]` and `kokurikuler_default[]` as the data model expects, plus `versi`, `jp_menit` (35), `default_minggu`, `catatan[]` (11), `generator_rpm`, `sinkronisasi`, `referensi_bahasa_jawa`, `referensi_pai`.
- No duplicate `ID_Kurikulum`, `kode_atp`, `kode_fokus`, or `kode_tp`. Every ID starts with `KM-SD-2026-R2-`.
- Every row has CP, at least one TP, and at least one fokus. `kode_tp` and `tujuan_pembelajaran` have the same length in every row. `Data_Tujuan.topik_materi` always equals `Materi Pokok`.
- Fokus `estimasi_jp` sums to `Data_ATP.alokasi_jp` in every row; `total_jp_fokus` equals `alokasi_jp`; `distribusi_mingguan` sums to `alokasi_jp` where present.
- `Fokus_Materi` equals the `fokus` names of `Data_Fokus` in every row, so dropping it loses nothing.
- Topic JP per subject, grade, and semester equals `alokasi[]` for every allocation row, and every allocation row has topics. PAI, Bahasa Jawa, and Seni Suara Daerah `distribusi_topik` lists match their topics exactly.
- **Matematika grade 4 semester 2 is now 90 JP** (was 80). The data-model issue "Matematika grade 4 semester 2 = 80 JP, target 90" is fixed in the source.
- No IPAS topics for grades 1–2, as D-204 expects.

## 3. Shape differences the import must handle

1. **JSON columns are real JSON, not strings.** `Data_Tujuan`, `Data_ATP`, `Referensi`, `Audit_Data` are objects; `Data_Fokus`, `Fokus_Materi`, `Legacy_ID_Aliases`, `Keterampilan_Proses` are arrays. The data model says "JSON-string columns parsed on import"; the import should accept both a string and a decoded value.
2. **No subject code field.** The subject code is only inside `ID_Kurikulum` (`KM-SD-2026-R2-<CODE>-K<grade>-<nn>`): `PP`, `BIN`, `MTK`, `IPAS`, `PJOK`, `BIG`, `BJW`, `PAI`, `KKA`, `KTB`, `SSD`, and five codes for Seni dan Budaya: `MUS` (grade 1 music), `SM` (music 2–4, 6), `SMU` (music 5), `SR` (art 1–4, 6), `SRP` (art 5). `struktur_alokasi_sd.json` uses names only. The import needs a fixed name → code map; it must not take Seni dan Budaya's code from the ID.
3. **Seni dan Budaya is one subject with two branches.** Each grade has Seni Rupa and Seni Musik topics (`Cabang_Seni`) that together fill the 54 JP per semester (e.g. grade 5: Musik 42 + Rupa 12). `urutan_semester` restarts per branch, so (subject, grade, semester, order) repeats 27 times. `order_in_semester` is unique only with the branch; keep `Cabang_Seni` in `extras` and sort by branch then order.
4. **`Status_Mapel` is free text with 10 values**, not an enum, and is not consistent inside one subject (PAI: `Wajib` vs `Wajib bagi peserta didik beragama Islam` in grade 5; Bahasa Inggris: `Wajib` vs `Wajib sesuai kebijakan satuan pendidikan` in grade 5; Koding: two wordings). `subjects.kind` and `default_teacher` should come from the import's own subject map, and `Status_Mapel` be kept as text only.
5. **Extra `Data_ATP` keys** beyond the table columns: `urutan_tahunan`, `durasi_minggu`, `minggu_efektif_semester`, `sumber_perhitungan`, `metode_alokasi_fokus`, `catatan` (348 rows), and in a few rows `alokasi_jp_buku`, `status_penyesuaian_jp` (14), `alokasi_jp_min`/`alokasi_jp_max` (13, grade 5 Bahasa Inggris, PJOK, Seni), `status_alokasi` (1). `distribusi_mingguan` is missing in the 20 Bahasa Inggris grade 1–2 rows. Proposal: keep them in `audit` or `extras`.
6. **Extra `Referensi` keys:** besides `sumber_cp`, `sumber_buku`, `halaman_buku`, `tanggal_penyesuaian` there are `sumber_tp_atp`, `status_verifikasi`, `halaman_cp_buku`, `halaman_bab_buku`, `halaman_atp_buku`, `halaman_skema_buku`, `sumber_struktur`, `regulasi_cp`, `tanggal_verifikasi`. Store the whole object in `references`.
7. **`Audit_Data.indeks_sumber_lama`** is `null` (361), an integer (26), or a list (45).
8. **Fokus `status_alokasi` is a sentence**, 7 distinct values (e.g. "Estimasi sementara sistem; … perlu diverifikasi guru."), not a short code. `focus_items.allocation_status` must be text, or the import maps the 7 sentences to codes.
9. **`alokasi[]` extra keys:** `jenis_pembelajaran` (always `Intrakurikuler`), `kategori` (`Muatan Lokal`, Bahasa Jawa and Seni Suara Daerah), `sumber_kurikulum` (an object), `distribusi_topik`, `catatan_penyesuaian` (PAI, Bahasa Jawa, Seni Suara Daerah). Allocation rows have no `s1_effective_weeks` key: it is `semester_1.minggu_efektif`. `kokurikuler_default[]` uses `nama_program` instead of `mata_pelajaran`.

## 4. Legacy IDs

1. `Legacy_ID` is empty in 73 rows (all 31 Seni Suara Daerah, 10 Koding, 9 Seni, 8 Tata Boga, and a few others).
2. Five non-empty `Legacy_ID` values are on more than one row (grade 5 Matematika and Seni: `CUR-2083366612`, `CUR-2023294460`, `CUR-1593100637`, `CUR-531056212` on two rows each, `CUR-1315798615` on three). The old topic was split.
3. `Legacy_ID_Aliases` (911 entries) holds old `KM-SD-2026-R2-…` IDs too. 16 aliases point to two or three rows, and 20 aliases are the current `ID_Kurikulum` of another row (grade 5 Bahasa Inggris, Koding, Matematika, PJOK, Pendidikan Pancasila were renumbered: e.g. the alias `KM-SD-2026-R2-BIG-K5-02` is on row `BIG-K5-01`, while `BIG-K5-02` is a different topic). In 238 rows `Legacy_ID` is not in the row's own alias list.
4. So `legacy_codes` cannot be a unique lookup. Proposal: (a) a lookup by code always tries `topics.code` first, and an alias only when no topic has that code; (b) an alias that maps to several topics is reported, not resolved; (c) `legacy_codes` stores `Legacy_ID` plus the aliases, minus the row's own ID.

## 5. Clashes with decisions

| # | Finding | Rule | Proposal |
|---|---|---|---|
| C1 | Bahasa Inggris grades 1–2: 20 topics with `Jenis_Pembelajaran: Kokurikuler`, allocation only in `kokurikuler_default[]` (2 JP/week, 36 per semester), no `alokasi[]` row | D-204: optional content, off by default; not a compulsory rapor subject | No clash with D-204's intent. Import the topics and the `kokurikuler_default` rows; the school switches the subject on in `school_subjects` per grade. Do not import them as `cocurricular` rows of the national kokurikuler allocation (which has no Bahasa Inggris) |
| C2 | IPAS grades 1–2: no topics, no allocation | D-204 | As expected. Topics still have to be written |
| C3 | Koding dan Kecerdasan Artifisial grades 5–6: 14 topics and two `alokasi[]` rows marked `Intrakurikuler`, 2 JP/week (36/36 in grade 5, 36/28 in grade 6) | D-208: not intrakurikuler; topics become kokurikuler content | The import skips the two `alokasi[]` rows (reports them) and loads the 14 topics as kokurikuler content. Needs a way to mark a topic or subject as kokurikuler (e.g. `subjects.kind = cocurricular`) |
| C4 | Keterampilan Tata Boga grades 3–6: 8 topics, 34 JP per semester at 2 JP/week, no allocation row. One topic per semester, numbered across two grades (K3-01, K3-02, K4-03, K4-04, K5-01, K5-02, K6-03, K6-04) | D-204: school-optional muatan lokal under the 72 JP cap | Import as `local_content`, off by default. Grade 6 semester 2 has 34 JP over weeks 1–17 with `minggu_efektif_semester` 18, but grade 6 semester 2 has 14 effective weeks (28 JP at 2 JP/week): **data fix needed**. Resolved by D-234: trimmed to 28 JP over weeks 1–14 at import; the source file stays as received |
| C5 | **New subject Seni Suara Daerah** (`SSD`), grades 1–6, 31 topics, 2 JP/week (36/36, grade 6 36/28), `alokasi[]` rows with `kategori: Muatan Lokal`, "muatan lokal tambahan … (keputusan sekolah)" for Jawa Tengah. CP is "Rumusan sistem … PERLU VERIFIKASI" (not an official CP), and JP is a system estimate | Not in the data model or any decision | **Owner decision needed.** Suggest: school-optional muatan lokal like Tata Boga (off by default), Jawa Tengah schools only via `region_subjects`, and Tim Validasi review of its CP (D-113) before teachers see it |
| C6 | Muatan lokal cap: Bahasa Jawa 72 + Seni Suara Daerah 72 + Tata Boga 68 JP per year in grades 3–5 (grade 6: 64 + 64 + 68) | D-204: SD muatan lokal cap 72 JP per year (64 in grade 6), checked per school | Bahasa Jawa alone fills the cap. A school that turns on Seni Suara Daerah or Tata Boga next to Bahasa Jawa exceeds it, so the per-school check must warn or block. Owner to confirm which |
| C7 | PAI: subject name `PAI dan Budi Pekerti`; CP from "CP 020/2026" (compared with 046/2025). `catatan` says the other five religions use the same allocation | D-034 (one religion subject per religion), CP-PABP-BKPDM-020-2026 | Map `PAI dan Budi Pekerti` to the Islam religion subject (`religion = islam`), the same subject as "Pendidikan Agama Islam dan Budi Pekerti" in `cp-pendidikan-agama-sd-bkpdm-020-2026.json`. Report when its CP text differs from that file. Reuse PAI's `alokasi[]` rows for the other five religions |
| C8 | Seni dan Budaya status "rencana integratif, sekolah dapat menyesuaikan cabang seni" with music and visual art in one allocation | Permendikdasmen 13/2025 lets a school pick the art branch | Keep one subject; the school's branch choice is out of scope for the import |

## 6. Proposed changes (for a later `docs/final/data-model.md` update; not applied)

1. Curriculum table, `topics`: "JSON-string columns parsed on import" → "JSON columns (objects in the current files; strings in older files)".
2. `topics.legacy_codes`: add that aliases are not unique and the lookup order in 4.4.
3. `topics`: `order_in_semester` is unique per (subject, grade, semester, art branch); keep `Cabang_Seni` in `extras`.
4. `focus_items.allocation_status`: text (the source has sentences).
5. Known data issues: remove "Matematika grade 4 semester 2 = 80 JP" (fixed in the source); add Tata Boga grade 6 semester 2 (34 JP in 14 weeks), the new Seni Suara Daerah subject (C5, C6), and the legacy alias collisions (4.3).
6. Mapping table, `subjects`: add Seni Suara Daerah and the name → code map (3.2).
