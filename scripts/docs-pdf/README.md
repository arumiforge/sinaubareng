# Docs PDF build

Builds the Indonesian developer PDFs in `docs/pdf/` from the final docs, as described in the docs pipeline in [`docs/agent/WORKFLOW.md`](../../docs/agent/WORKFLOW.md).

## How it works

1. A person or agent translates `docs/final/<name>.md` into Indonesian and saves it as `docs/.build/<name>.md`. This folder is git-ignored, so translations are never committed.
2. `build.cjs` turns each translation into one self-contained HTML file with Pandoc. The HTML embeds `print.css` and the Arabic font.
3. Headless Chromium (Playwright) prints that HTML to `docs/pdf/<name>.pdf`. The PDF is A4 and its footer reads "Halaman X dari Y".
4. Before printing, the build checks that no table or code block is wider than the page. If one is, the build exits with an error.

## Requirements

- Pandoc 3.x
- Node.js 24 LTS
- Playwright with Chromium. `npm ci` in this folder installs the pinned version; then run `npx playwright install chromium` once.
- The Liberation fonts (`fonts-liberation` on Ubuntu)

The Arabic font, Noto Naskh Arabic, is bundled in `fonts/` under the SIL Open Font License (`fonts/OFL.txt`).

## Usage

From the repository root:

```bash
node scripts/docs-pdf/build.cjs data-model server-setup   # rebuild named docs
node scripts/docs-pdf/build.cjs                           # rebuild every doc in docs/final/
node scripts/docs-pdf/check.cjs                           # list leftover English prose in the translations
```

## Translation rules

- Translate prose, headings, and table text into plain, natural Indonesian.
- Keep these unchanged: code blocks, identifiers, commands, file paths, decision IDs, URLs, and Indonesian or Arabic terms already in the source.
- Under the title, add `<p class="source-note">Diterjemahkan dari <code>docs/final/<name>.md</code> pada <tanggal>.</p>`.
- Diagrams in code blocks stay as they are. If a diagram has English labels, follow it with a short Indonesian summary.
- Rebuild the PDF whenever its final doc changes.
