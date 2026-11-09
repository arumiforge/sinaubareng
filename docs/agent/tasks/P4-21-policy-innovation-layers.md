# P4-21 — Policy foundation and innovation layers

- **Phase:** P4
- **Status:** review
- **Owner:** sky/optimistic-thompson-2qmlfz
- **Depends on:** P4-03, P4-04, P4-18, D-113, D-203…D-210, D-317
- **Can run in parallel with:** P4-14, P4-15, P4-19, P4-20

## Goal

Set SIGRAK's position: national curriculum and policy as the foundation, the widest room for innovation on top. Separate the policy and innovation layers, classify every component, design regulation versioning, the SD/MI configuration, and a master taxonomy.

## Read first

- `docs/agent/CONTEXT.md`
- `docs/drafts/dokumen-resmi-sd-mi.md`, `docs/drafts/administration-features.md` §1
- `docs/data/regulations/README.md`, `docs/final/data-model.md` (curriculum part)

## Output

- `docs/drafts/policy-innovation-layers.md`

## Done when

- [x] Layer model and rule strengths (§2)
- [x] What the government fixes; whether standards limit methods (§3)
- [x] How the app accommodates policy (§4)
- [x] Classification of all components (§5)
- [x] Regulation versioning (§6)
- [x] Room for innovation (§7)
- [x] SD and MI configuration (§8)
- [x] Master taxonomy (§9)
- [x] Clash check with earlier decisions (§10)
- [ ] Owner answers PQ1–PQ8, recorded as decisions

## Result

Draft written 2026-11-09 from the repo and a web check of the 2025–2026 rules (official JDIH sites were down; mirrors and secondary sources are marked). No decision is overturned; the only behaviour change proposed is moving the `config/curriculum.php` version pointer into data. Open: owner answers PQ1–PQ8; KMA 512/2026 (KBC) is unverified. Follow-ups proposed: P4-22 (taxonomy and regulation registry data), P4-23 (missing regulation sources), P4-24 (apply to final docs).
