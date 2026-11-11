# School calendar data (draft)

## National holidays

`national-holidays-2026-2027.json` lists the national holidays (`national_holiday`) and joint leave days (`joint_leave`, cuti bersama) for academic year 2026/2027 (July 2026 to June 2027) and for 2027/2028 up to 31 December 2027. Names are in Indonesian, worded as in the SKB; each day names its decree (`decree`) and whether it is provisional (`is_provisional`).

- **Provenance:** converted from `docs/sources/usulan-01/data/libur-nasional-cuti-bersama.json` (sha256 `82e7ed33d8614269e1a2bcccd7adf75a9d0a92e75b751c9ddbe67d75e1aefab6`), which was copied from the legacy app and was unverified.
- **Check (D-254, 2026-11-10):** every day was compared with the official annexes of the SKB Menteri Agama, Menteri Ketenagakerjaan, dan Menteri PANRB No. 1497/2/5 Tahun 2025 (2026 holidays, set 19 September 2025) and No. 1205/3/2 Tahun 2026 (2027 holidays, set 15 September 2026), both taken from JDIH Kemnaker and cross-checked against Kemensetneg, Kemenko PMK and Kemenag pages. Decree numbers, set dates, all dates and all kinds match; no official day was missing. Only the names differed and now follow the SKB. The `check` block lists the sources, each difference, and the open points (Idul Fitri and Idul Adha dates depend on the Minister of Religious Affairs' decision; January to June 2028 waits for the 2028 SKB).
- **Status:** `draft`. The data is not imported or seeded until Tim Validasi SIGRAK signs it off (D-113, D-254).

## Jawa Tengah reference calendar

`region-jawa-tengah-2026-2027.json` (code `KALDIK-JATENG-2026-2027-6H`) is the province-level reference calendar for Jawa Tengah, academic year 2026/2027, 6-day school week (D-323). Every regency of the province uses it unless it gets its own calendar; schools copy it and add their own events (D-256).

- **Source:** Peraturan Kepala Dinas Pendidikan Provinsi Jawa Tengah 400.3/23862/2026 (`docs/sources/Pedoman_Kalender_Pendidikan_Prov_Jawa_Tengah_2026_2027.pdf`, sha256 in the file): Pasal 5-29, Lampiran I (SD/MI), Lampiran X (SD/MI calendar, 6 days) and Lampiran XIII (SD/MI effective days, 6 days). The 5-day dates are not imported.
- **Dates:** first day 13 July 2026, semester 2 starts 2 January 2027, last day 19 June 2027. Where the guide disagrees with itself, D-323 applies: Maulid Nabi on 25 August 2026, semester-1 break 21 December 2026 to 1 January 2027.
- **Fields:** `events` use the app kinds (`holiday`, `exam` with `exam_type`, `school_event`, `no_lessons`); every event is subtracted from effective days. National holidays and cuti bersama are not repeated here; they come from `national-holidays-2026-2027.json`. `joint_leave_school_days` lists the cuti bersama dates the guide keeps as school days (5 Feb, 15 Mar, 25 Mar, 18 May, 19 May 2027); these must be switched off for this calendar. `assessment_windows` (SAS and Sumatif Akhir Tahun, approximate weeks from Pasal 19) and `milestones` (MPLS, upacara, report cards) are for information only and are not imported as events, because Lampiran XIII counts them as effective days.
- **Check:** with the app rule (Monday to Saturday from the first to the last day, minus Sundays, national holidays, active cuti bersama and events) the file gives 254 effective days, 134 in semester 1 and 120 in semester 2, as in Lampiran XIII. Per month it matches the Lampiran XIII column "Hari Belajar Efektif" except May 2027 (22, guide 21) and June 2027 (15, guide 16): the guide puts the graduation-announcement day in May, which for SD/MI falls on 7 June 2027. The `notes` block explains each choice and lists the guide's inner inconsistencies.
- **Status:** `draft`. Not imported or seeded until Tim Validasi SIGRAK signs it off (D-113).
