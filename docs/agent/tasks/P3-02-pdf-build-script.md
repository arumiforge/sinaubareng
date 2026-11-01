# P3-02 — PDF build script

- **Phase:** P3
- **Status:** review
- **Owner:** p3/p3-02-pdf-build-script
- **Depends on:** P3-01
- **Can run in parallel with:** —

## Goal

Keep the Indonesian PDF build from P3-01 in the repo, so anyone can regenerate `docs/pdf/` the same way. Use it to rebuild the PDFs that fell behind their final docs.

## Read first

- `docs/agent/WORKFLOW.md` (docs pipeline), `docs/agent/tasks/P3-01-final-docs-pdf.md` (method)

## Output

- `scripts/docs-pdf/`: build script, page check, print stylesheet, bundled Arabic font with its licence, pinned `package.json`, README.
- `docs/agent/WORKFLOW.md`: the PDF line points to the script.
- Rebuilt `docs/pdf/` files whose final doc changed after P3-01: data-model, religious-education, server-setup (D-136 heartbeat, TP/ATP links).

## Done when

- [x] `node scripts/docs-pdf/build.mjs <name>` rebuilds a PDF from `docs/.build/<name>.md`
- [x] The page check reports no table or code block wider than the page
- [x] The three outdated PDFs match their final docs
- [x] Nothing from `docs/.build/` is committed

## Result

- Added `scripts/docs-pdf/`:
  - `build.cjs`: Pandoc to self-contained HTML, page-width check, then Chromium to PDF.
  - `check.cjs`: the page-width check, plus a scan for leftover English prose.
  - `print.css`, and Noto Naskh Arabic with its OFL licence.
  - `package.json` with Playwright pinned to 1.63.0, and a README with the requirements and translation rules.
- `WORKFLOW.md` now points to the script.
- Rebuilt data-model, religious-education, and server-setup PDFs, so they include the TP/ATP draft links and the D-136 backup heartbeat. A full build of all ten docs passes the page check and the English scan. The other seven PDFs are unchanged.
- Translations stay in the git-ignored `docs/.build/`, as WORKFLOW requires. Rebuilding a PDF still needs its translation file. A fresh clone has none, so the next person translates from `docs/final/` first.
