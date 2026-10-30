# Workflow

How phased, parallel work is done so agents stay consistent, save tokens, and can pick up where others left off.

## Phases and tasks

- Work is split into **phases** (`P1`, `P2`, …). A phase finishes before the next one starts, unless a task in the next phase has no dependency on unfinished work.
- Each phase is split into **tasks** (`P1-01`, `P1-02`, …), each listed in `PROGRESS.md` and described in `tasks/<ID>-<slug>.md` (copy `docs/templates/TASK.md`).
- A task must be small enough for one agent/session, with clear inputs, outputs, and dependencies. Tasks without mutual dependencies can run in parallel.

## Running in parallel

- **Sub-agents** (inside one session): the coordinating agent gives each sub-agent one task file path, nothing else. The sub-agent reads `CLAUDE.md`, `CONTEXT.md`, and its task file only.
- **Parallel sessions**: each session works on its own branch, named `<phase>/<task-id>-<slug>` (e.g. `p1/p1-02-architecture`) unless the session was given a branch.
- Two tasks must not edit the same file. If they would, mark one as depending on the other, or split the file.
- Shared files (`PROGRESS.md`, `DECISIONS.md`, `CONTEXT.md`) get small, line-level edits only, to keep merges easy.

## Task lifecycle

1. **Claim:** in `PROGRESS.md`, set the task to `in-progress` with your owner label. Commit and push this first, so others see the claim.
2. **Work:** read only what the task file lists under *Read first*. Write drafts in `docs/drafts/` (docs) or the code paths listed in the task.
3. **Decide:** a new or changed decision goes in `DECISIONS.md`. If it changes the stack or a convention, update `CONTEXT.md` too.
4. **Finish:** set status to `review` (needs owner approval) or `done`; fill *Result* in the task file; add a short handoff note in `PROGRESS.md`.
5. **Blocked:** set `blocked`, write the reason and what is needed in the task file and the handoff notes.

## Docs pipeline

| Stage | Folder | Language | Rule |
|---|---|---|---|
| Draft | `docs/drafts/` | English | Work in progress; may be incomplete. |
| Final | `docs/final/` | English | Approved by owner; moved here from drafts (`git mv`). |
| PDF | `docs/pdf/` | Indonesian | Generated from a final doc for developers to read. Same file name, `.pdf`. |

PDF generation: translate the final markdown into Indonesian in `docs/.build/` (git-ignored, never committed), then render to `docs/pdf/` (e.g. with Pandoc). Keep code blocks, identifiers, commands, and file paths unchanged; translate prose, headings, and table text. Regenerate the PDF whenever its final doc changes.

## Saving tokens

- Read only the files a task needs. Don't scan the whole repo to "get context" — `CONTEXT.md` is the context.
- Link to existing docs instead of repeating them.
- Keep agent files short. Trim stale handoff notes; move finished phases to `docs/agent/archive/`.
- Give sub-agents a task file path, not a long prompt.
