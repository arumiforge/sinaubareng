# Design System and UI Writing

- **Status:** final (approved 2026-10-31, D-123)
- **Task:** P1-06
- **Last updated:** 2026-10-30
- **Sources:** [`Rebranding_dan_Perombakan_Fitur_EDUNEXA.md`](../sources/Rebranding_dan_Perombakan_Fitur_EDUNEXA.md), [`Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md`](../sources/Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md), [`Parafrase_Teks_UI_Sigrak.xlsx`](../sources/Parafrase_Teks_UI_Sigrak.xlsx)

## Purpose

This document defines how SIGRAK looks and speaks: tokens, components, mobile rules, voice, and terms. The tokens and rules come from the EDUNEXA notes, adapted to the repo stack (Tailwind + TailAdmin, ported to Svelte). It is for anyone building screens or writing UI copy. UI copy itself is Indonesian and lives in `lang/id`. This doc is English, with the Indonesian copy quoted as data.

## Direction

The app should look like a tidy teacher's workbook: plain background, one brand colour, thin lines, and type that is easy to read on a phone. It is a calm work tool, not a poster.

| Remove (seen in EDUNEXA) | Use instead |
|---|---|
| Glassmorphism, gradients, glow, blur | Plain surfaces: very light grey page, white cards, 1 px borders |
| A different accent colour per card or icon | One brand colour + status colours only |
| Tiny uppercase labels (`text-[9px] uppercase`) | Sentence-case labels, min 13 px, weight 600 |
| Icon-only buttons | Icon + text. Icon alone only for universal actions (close, search, menu) with `aria-label` |
| A "Kurang 232" badge on every row | Thin progress bar + number. Colour only when late |
| `user-scalable=no` | Zoom allowed (accessibility) |
| Tailwind CDN, `lucide@latest` | Tailwind through the Vite build. Icons pinned and bundled |

## Stack mapping

- **Tokens are CSS custom properties**, defined once in `resources/css/tokens.css` for light and dark, and mapped into Tailwind's theme (`@theme` in Tailwind v4) so classes like `bg-surface`, `text-muted`, and `border-line` use them.
- **TailAdmin is the component reference, not the look.** Its layout patterns (sidebar, header, tables, forms, modals) are ported to Svelte components. Its decorative styles (gradients, multi-colour stat cards, heavy shadows) are dropped in favour of the tokens below.
- **Filament (internal console)** uses its own theme with the brand colour set to `--brand`. It is staff-only, so it does not have to match the school app pixel for pixel.
- **Icons:** Lucide for Svelte, version pinned in `package.json`, 1.5 px stroke, 20 px (24 px in the bottom nav).
- **Font:** Plus Jakarta Sans, self-hosted through the build (no runtime CDN), with `font-variant-numeric: tabular-nums` in tables.

## Colour tokens

Every text pair meets WCAG AA (≥ 4.5:1). Contrast is measured against the pair's own background.

| Token | Light | Dark | Use | Contrast |
|---|---|---|---|---|
| `--brand` | #0F766E | #2DD4BF | Primary button, links, active tab | 5.47 on white; text #0B1F1C on #2DD4BF 9.19 |
| `--brand-strong` | #115E59 | #5EEAD4 | Hover, brand text | 7.58 on white; 11.8 on #151B19 |
| `--brand-tint` | #F0FDFA | #12302C | Selected row, active page marker | — |
| `--bg` | #F7F8F7 | #151B19 | Page background | — |
| `--surface` | #FFFFFF | #1C2321 | Cards, tables, forms | — |
| `--border` | #E3E6E4 | #2C3532 | 1 px lines | — |
| `--text` | #1C2420 | #E6ECE9 | Main text | 14.91 / 14.58 |
| `--text-muted` | #5B6660 | #9AA7A1 | Secondary text | 5.98 / 6.42 |
| `--ok` / `--warn` / `--danger` / `--info` | #15803D / #B45309 / #B91C1C / #1D4ED8 | Lighter versions from the dark set | Status only, never decoration | 5.02 / 5.02 / 6.47 / 6.7 |

Rules:

- One brand colour per screen.
- Status colours appear only when there is a status (late, failed, incomplete).
- Menu cards and icons never get different colours.
- Dark mode is its own set, not an inversion.

## Type, space, shape

- **Type scale:** 13 / 15 / 17 / 20 / 24 / 30 px. Reading text is 16 px on phones. Labels are at least 13 px, weight 600, sentence case.
- **Print documents** use one fixed print font, Arial (D-109), for uniform output. The letterhead is an uploaded image (D-031), so its fonts and logo are whatever the school or teacher designed.
- **Math and Arabic:** KaTeX for formulas and fractions; Noto Naskh Arabic (or Amiri) for Arabic text in PAI material.
- **Space:** multiples of 4 px. **Radius:** 6 (inputs), 10 (cards). **Shadow:** floating menus and dialogs only.

## Components

| Group | Components |
|---|---|
| Actions | Button (primary, secondary, text), icon button (close/search/menu only) |
| Forms | Input, Select, Combobox (curriculum pickers), Checkbox, Radio cards (wizard choices), Textarea with counter (the optional AI note) |
| Structure | Tabs, Table + card list for phones, Stepper (wizards), Bottom sheet, Dialog |
| Feedback | Toast (one line, auto-hide), inline error, Empty state, Skeleton, Progress ("Langkah 2 dari 4") |
| Content engine | Template card (gallery), Section editor with "Perbaiki bagian ini", Revision compare (old/new), Generation status panel |
| AI keys | Key row (label, project, last 4, status chip, cooldown time), Add-key sheet with test result and the multi-key consent box |
| TKA exam | Session card, exam card sheet (print), proctor panel (current token with countdown, participant list with status), result table |
| Print | Letterhead upload with A4/F4 preview and resolution warning, print settings form |

## Mobile rules

- Touch targets are at least 44 px.
- On phones the five teacher menus are a bottom nav. On laptops the same order is a sidebar.
- Tables become card lists below 640 px.
- Forms are one column, and the main action button sticks to the bottom of the screen.
- Wizards show one step per screen, with "Kembali" and the next action always visible.
- Di Kelas screens must work one-handed: large rows and an "all present" default.

## Game runtime style

Games (Quiz Battle and later ones) are played by children on a projector or a phone. They get **their own style**, separate from the calm app shell:

- Brighter colours, bigger type, and motion are allowed.
- Still accessible: AA contrast for all text, no flashing above 3 Hz, a timer that is not conveyed by colour alone, and a sound toggle.
- The runtime bundle carries its own CSS, so a downloaded game looks the same offline.

## Exam site style (TKA practice)

The exam site is a separate front end for students (D-027). It should feel like the national TKA exam client, so the practice carries over:

- Plain and distraction-free: the question on the left, stimulus text or figure where the question needs it, answer options as large touch targets.
- A number grid for jumping between items. Items are coloured *answered*, *ragu-ragu* (flagged), or *not answered*, with a text label too, never colour alone.
- A visible countdown, with a warning 5 minutes before the end.
- Font size control (A−, A, A+) and full keyboard use.
- No brand promotion, no links to the teacher app, no AI wording.
- Copy is short and child-friendly: "Masukkan token dari pengawas", "Kirim jawaban", "Waktu tersisa 5 menit".

## Voice and tone

Sigrak speaks like a quick, polite fellow teacher: straight to the point, courteous, never showing off technology.

| Sigrak is | Not |
|---|---|
| Quick: short sentences, verb first | Hasty: cutting important information |
| Friendly: addresses "Anda", uses "silakan" when needed | Over-familiar: "Yuk, Bapak/Ibu kece!" |
| Clear: school terms teachers use | Technical: Cloud, Firestore, sinkronisasi, inject, generate, prompt |
| Calm: errors explained with a way out | Blaming: "Input tidak valid!" |
| Helpful: offers the next step | Preachy: "Anda harus…" without a reason |

## UI writing rules

1. Titles and buttons use verbs: "Buat RPM", "Simpan jurnal", "Unduh PDF", "Tayangkan".
2. Capitals only at the start of a sentence and in proper nouns. No ALL-CAPS buttons.
3. One term per thing (list below).
4. Error = what happened + what to do. Technical codes go behind "Lihat detail".
5. A delete confirmation names the item and the consequence, and its button names the action ("Hapus jurnal"), never "Ya".
6. A success message is one line that disappears by itself, never a dialog to close.
7. No emoji, no stacked exclamation marks, no "cerdas", "canggih", or "AI" as decoration. When the AI features need a name, use "Kunci AI" and plain verbs ("Buat", "Perbaiki").
8. "Anda" for every role. "Bapak/Ibu" only in broadcasts from the principal.
9. **Never ask the teacher for a prompt.** Wizard questions are pedagogical and come with defaults (see [`content-engine.md`](content-engine.md#wizard-rules)).

| Situation | Before | After |
|---|---|---|
| Main button | GENERATE MODUL | Buat RPM |
| Error | Gagal memproses AI: {pesan} | RPM belum berhasil dibuat. Periksa kunci AI Anda, lalu coba lagi. |
| All keys at limit | — | Semua kunci AI Anda sedang mencapai batas. Coba lagi pukul {jam}. |
| Confirmation | Hapus jurnal? / Ya, Hapus | Hapus jurnal 12 Okt? Jurnal ini tidak bisa dikembalikan. / Hapus jurnal |
| Empty | Data belum tersedia | Belum ada siswa. Tambahkan satu per satu atau impor dari Excel. |

## Terms

These are the standard terms from the spreadsheet's *Istilah* sheet (37 entries; the full list stays in the spreadsheet), updated for the new concept. New entries are marked **new**.

| Old / avoid | Use | Note |
|---|---|---|
| Download | Unduh | |
| Generate / GENERATE MODUL | Buat (Buat RPM, Buat soal, Buat LKPD) | |
| Modul Ajar / Modul | RPM | One name for the planning document |
| Modul mandiri / Mode Manual | RPM lepas jadwal | |
| Bank Modul / Arsip / Penyimpanan Lokal | Dokumen Saya | |
| Worksheet | LKPD | Official curriculum term |
| Bank Soal (menu) | Bank Soal template under Buat; "Buat soal" shortcut in Asesmen | **Updated**: soal now live in the template gallery |
| Rencana (menu) | Buat | **New** (D-021) |
| Template | Templat | **New**. KBBI spelling |
| Game mode / Play | Tayangkan (present), Unduh game (file) | **New** (D-011) |
| CBT / ujian online TKA | Simulasi TKA | **New** (D-026). Never "TKA" alone, so it is not confused with the official test |
| Username/password ujian | Kartu login | **New**. Same name as in TKA |
| Token ujian | Token | **New**. "Token dari pengawas" in student copy |
| Proktor | Pengawas (to students), Proktor (to teachers) | **New** |
| Kop surat / letterhead | Kop | **New**. "Unggah kop", "Kop sekolah", "Kop saya" |
| Ragu-ragu | Ragu-ragu | **New**. Same as TKA |
| Prompt, generate, AI generator | (never shown) | **New**. Use "Buat" and pedagogical questions |
| API Key / API Key Gemini | Kunci AI | Plural "kunci AI", one per Google project |
| Google Cloud Project | Project Google | **New**. The label teachers type for each key |
| Penilaian (menu) | Asesmen | |
| Administrasi Kelas | Kelas Saya (+ Di Kelas for absensi, jurnal, catatan) | |
| Pemberitahuan / broadcast board | Kabar | School to teacher |
| Pemberitahuan (principal to teacher) | Pengingat | |
| Tutorial | Bantuan | |
| Pengaturan / Pengaturan Portal | Akun (personal) / Pengaturan Sekolah (school) | |
| Dashboard | Ringkasan (portal) / Hari Ini (teacher) | |
| Password / Username / Login | Kata sandi / Nama pengguna / Masuk | |
| Instansi | Sekolah | |
| Super Admin (to teachers) | Tim Sigrak / admin sekolah | Whoever really decides |
| Role / Hak Akses | Peran | |
| Cloud / Firestore / Firebase / sinkronisasi | tersimpan di akun | Technology names never shown |
| Edit / Reset / Timpa | Ubah / Kembalikan, Atur ulang / Ganti | |
| Presensi / Kehadiran / Daftar Hadir | Absensi | |
| Mutasi siswa | Pindah dan keluar | |
| Master EDUNEXA | Kurikulum bawaan | |
| Gagal … | … belum berhasil | Calmer, still clear |
| `...` / `&` | `…` (one character) / "dan" | |

## Copy source and localization

- `Parafrase_Teks_UI_Sigrak.xlsx` holds 1,499 old → new strings across 10 EDUNEXA pages (Administrasi Kelas 608, Beranda & akun 272, Portal Kepala Sekolah 199, Generator RPM 112, shared 70, Super Admin 65, Tutorial 62, Bank Soal 58, Worksheet/LKPD 27, Portal Pengawas 26).
- When a screen is built, its strings move into `lang/id/*.php` with English keys (repo convention). The spreadsheet's "Teks baru" column is the starting copy, checked against the terms above.
- `{…}` placeholders in the spreadsheet become Laravel `:placeholder` parameters.
- Strings for pages that no longer exist (Super Admin → internal console, Tutorial → Bantuan) are used only where the new screen still needs them.

## Open questions

- [ ] Confirm "Templat" (KBBI) or keep the familiar "template" in UI copy.
- [ ] Game runtime palette: designer task, or derived from the brand colour?
