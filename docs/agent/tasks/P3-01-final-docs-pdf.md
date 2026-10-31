# P3-01 — Move approved drafts to final and build Indonesian PDFs

- **Phase:** P3
- **Status:** todo
- **Owner:** —
- **Depends on:** owner approval of all drafts (D-123)
- **Can run in parallel with:** P2-08 (it writes a new draft and does not touch these files)

## Goal

Move the ten approved drafts to `docs/final/` and build one Indonesian PDF per final doc in `docs/pdf/`, following the docs pipeline in `docs/agent/WORKFLOW.md`.

## Read first

- `CLAUDE.md` (language policy), `docs/agent/WORKFLOW.md` (docs pipeline)
- The ten drafts listed below (each one before you translate it)

## Output

- **Move with `git mv`:** `docs/drafts/{product-concept, content-engine, ai-provider-engine, data-model, design-system, religious-education, architecture, server-setup, coding-standards, export-localization}.md` → `docs/final/`. In each moved file, set the status line to `final (approved 2026-10-31, D-123)`.
- **`docs/drafts/open-questions.md` stays in drafts.** It is a living list, not an approved doc. Update its links to the moved files.
- **Fix every link** to the moved files across the repo:
  - `docs/agent/*.md` (CONTEXT, PROGRESS, DECISIONS paths in prose; do not change decision wording), task files, `docs/sources/README.md`, `docs/data/**` (JSON `source`/notes fields that name draft paths), and links between the final docs.
  - Check with `grep -rn "drafts/<name>.md"` for each name. Relative links inside `docs/final/` stay `name.md`.
- **PDFs:** `docs/pdf/<name>.pdf` for each of the ten. Method:
  1. Write the Indonesian translation in `docs/.build/<name>.md`. This folder is git-ignored and never committed.
  2. Render it with Pandoc to HTML with a print stylesheet, then to PDF with headless Chromium. Pandoc and Chromium are in the container. Use A4, an embedded Noto Naskh Arabic font for Arabic text, and Mermaid blocks shown as code or pre-rendered.
- **Translation rules (WORKFLOW):**
  - Translate prose, headings, and table text into plain, natural Indonesian.
  - Keep unchanged: code blocks, identifiers, commands, file paths, decision IDs, URLs, and Indonesian/Arabic terms that are already in the source.
  - Put a short line under the title: "Diterjemahkan dari `docs/final/<name>.md` pada <tanggal>".
- **PROGRESS.md:** only your own row.

## Done when

- [ ] Ten files are in `docs/final/` (moved with git history), and `open-questions.md` stays in `docs/drafts/`
- [ ] `grep -rn "docs/drafts/\|\.\./drafts/\|drafts/" docs CLAUDE.md` lists only `open-questions.md`, P2-08's `server-monitoring.md`, the folder description in WORKFLOW/CLAUDE, and historical text inside DECISIONS.md
- [ ] Ten PDFs in `docs/pdf/`, all in Indonesian, opened and spot-checked: tables fit the page, Arabic renders right to left, and no English prose is left
- [ ] Nothing from `docs/.build/` is committed

## Coordination rules

- Work on branch `p3/p3-01-final-docs-pdf`, created from `main`. Open a ready PR to `main` when done, with **no AI attribution** anywhere (see `CLAUDE.md`). Splitting into two PRs is fine (1: move + links, 2: PDFs).
- Do **not** edit `docs/agent/DECISIONS.md` or `docs/agent/CONTEXT.md` content, except to fix draft paths to final paths. List anything new under "Decisions to record" in **Result**.
- One owner question is still open: the print font choice (D-109 says one font; the owner later wrote "font cukup 2"). Do not change the font text in `export-localization.md`; the coordinator fixes it in `docs/final/` after the owner answers.

## Result

Filled in by the agent on finish: what was done, open questions, follow-ups.
