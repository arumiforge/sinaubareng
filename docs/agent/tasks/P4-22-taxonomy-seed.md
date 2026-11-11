# P4-22 — Taxonomy seed (facets A–H) and regulation registry

- **Phase:** P4
- **Status:** review (waiting for Tim Validasi)
- **Owner:** p4/p4-22-taxonomy-seed
- **Depends on:** P4-21 answers (D-352…D-359), D-367 catalogs
- **Can run in parallel with:** P4-23 (regulation copies), P4-19 (Peta Asesmen, uses facet E)

## Goal

Seed `docs/data/taxonomy/` with the master taxonomy (facets A–H, stable codes, strengths, crosswalks) and a regulation registry with relations, as in `docs/drafts/policy-innovation-layers.md` §6 and §9 (D-354, D-357). Link to `docs/data/catalogs/` and the curriculum data instead of copying them; give P4-19 the core assessment codes.

## Read first

- `docs/drafts/policy-innovation-layers.md` §2.1, §6, §8, §9
- `docs/agent/DECISIONS.md` D-344…D-367
- `docs/data/catalogs/README.md`, `docs/data/regulations/README.md`, `docs/drafts/assessment-landscape.md`

## Output

- `docs/data/taxonomy/README.md`
- `docs/data/taxonomy/facet-a-policy.json` … `facet-h-administration.json`
- `docs/data/taxonomy/regulations.json`
- `tests/Feature/Data/TaxonomyDataTest.php`

## Done when

- [x] Eight facet files with nodes in the §9.2 shape (`TAX-2026-R1`, `status: draft`)
- [x] Crosswalks: P3 → 8 DPL, P5/P5RA → kokurikuler, RPP/Modul Ajar → RPM, peserta didik → murid
- [x] Catalog and curriculum lists linked by code (`members_from`), not copied
- [x] Facet E core codes and the P4-19 reuse rule written in the README
- [x] Regulation registry with §6.2 fields and relations, codes aligned with `docs/data/` codes; P4-23 entries marked `pending_p4_23` with null `source_file`/`sha256`
- [x] Validator test (unique codes, parents, facets, strengths, sources, links, sha256)
- [ ] Tim Validasi review

## Result

326 nodes (A 41, B 43, C 35, D 34, E 83, F 36, G 13, H 41; 12 retired nodes for crosswalks), 38 regulations, 28 relations. Conventions and open points: `docs/data/taxonomy/README.md`.

Choices made inside the task (data conventions, no owner decision needed):
- Catalog-backed lists are one node with `members_from`; tags store node code + member code.
- Registry adds `layer`, `year`, `data_files`, `pending_p4_23`; kinds `perka` and `perdirjen`; verification `unverified`.
- Unchecked legal bases are `references`, not `implements` (Kepmendikdasmen 56/2026, Perka BSKAP 047/H/AN/2025).

Open questions (for Tim Validasi, none for the owner):
- `approximate` P3 → DPL links (berkebinekaan global → kewargaan, bergotong royong → kolaborasi).
- DPL sub-dimensions, CP element codes, region code list, missing articles: after P4-23.

Follow-ups:
- Coordinator, after P4-23 merges: fill `source_file`, `sha256`, `verification`, full titles and dates of the ten `pending_p4_23` entries.
- P4-19: build `docs/data/assessment/` on facet E codes (README rule 4).
- Docs consolidation (P4-24): mention `docs/data/taxonomy/` in `docs/final/` data docs and CONTEXT.md.
