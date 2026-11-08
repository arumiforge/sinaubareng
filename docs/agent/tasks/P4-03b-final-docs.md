# P4-03b — Apply P4-03 findings to the final docs

- **Phase:** P4
- **Status:** done
- **Owner:** p4/p4-03b-final-docs
- **Depends on:** P4-03, D-203…D-209
- **Can run in parallel with:** P4-04

## Goal

Update `docs/final/` and the matching Indonesian PDFs from §10 of `docs/drafts/dokumen-resmi-sd-mi.md`, after the owner's answers (D-204…D-209).

## Read first

- `docs/drafts/dokumen-resmi-sd-mi.md` (§2–§10)
- `docs/agent/DECISIONS.md` D-203…D-209

## Output

- `docs/final/content-engine.md`, `data-model.md`, `product-concept.md`, `religious-education.md`
- `docs/pdf/src/` translations and `docs/pdf/*.pdf` for the same four docs

## Done when

- [x] The four final docs reflect §10 and D-204…D-209
- [x] Translations updated line by line; PDFs rebuilt; `check.cjs` reports no English prose

## Result

- **content-engine:** RPM uses the eight dimensi profil lulusan (DPL1–DPL8), splits INTI into Memahami/Mengaplikasi/Merefleksi with principle tags, and uses the official section terms. The kokurikuler plan follows Panduan Kokurikuler (sections A–H; forms Lintas Disiplin, 7 KAIH, Cara Lainnya; "Tidur Cepat"; KKA runs here). Bank Soal adds a Fase A note and keeps written items optional.
- **data-model:** new version `STRUKTUR-SD-PERMENDIKDASMEN-13-2025` holds SD kokurikuler JP. `school_subjects.grade` and `in_report` are added for optional content. Data issues resolved: Tata Boga, Bahasa Inggris and IPAS in grades 1–2, Koding. `assessments.kktp_approach` is added, plus new `report_cards` and `report_extracurriculars` tables.
- **product-concept:** level line, kokurikuler forms, rapor content, KSP starter template (Kepala Sekolah portal > Program Sekolah).
- **religious-education:** D-141 JP confirmed by 13/2025. MI keeps KMA 1503. Koding follows D-208.
- **Open:** the placement of the KSP template is a proposal. Its screen is designed with P5-09 (portals).
