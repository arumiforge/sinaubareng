# P3-02 — PDF build script

- **Phase:** P3
- **Status:** in-progress
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

- [ ] `node scripts/docs-pdf/build.mjs <name>` rebuilds a PDF from `docs/.build/<name>.md`
- [ ] The page check reports no table or code block wider than the page
- [ ] The three outdated PDFs match their final docs
- [ ] Nothing from `docs/.build/` is committed

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
