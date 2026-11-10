# Curriculum scripts

- `convert_legacy_topics.py` — one-time conversion (2026-11-09) of the legacy app curriculum data and the two earlier SIGRAK religion drafts into the SIGRAK R1 dataset (D-363…D-366): `sigrak-sd-2026-r1-umum.json`, `sigrak-sd-2026-r1-agama.json`, `sigrak-mi-2026-r1.json`, and the team-only `crosswalk-legacy-2026.json` in `docs/data/curriculum/`. It recodes topics, TP, and fokus, renames keys to English, adds a `curation` block per subject, applies the D-234 Tata Boga trim, rewords notes that named dropped files or legacy internals, and checks that counts and JP match before and after.

The input files were removed after the conversion, so the script no longer runs on the current tree; it is kept to show how R1 was made (inputs are in the git history before 2026-11-10). From now on the SIGRAK files are edited directly, subject by subject, during curation (D-364). Run Python only from the repository root with `python3 -I`.
