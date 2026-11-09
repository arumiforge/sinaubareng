# Internal Roles for Tim SIGRAK

- **Status:** draft (needs owner review)
- **Task:** P4-17
- **Last updated:** 2026-11-09
- **Builds on:** D-167 (staff roles admin, support, content), D-314 (fixed roles, read-only matrix), D-113 (Tim Validasi), D-309 (pengawas requests), D-315…D-320 (console features), D-211 (plan mapping)

## 1. Summary

School-side roles stay as they are: teacher, principal, operator, supervisor (D-010). Most SD/MI schools have no finer split, and the owner wants it kept simple.

The growth is on the **maker's side**. More templates, LKPD patterns, curated data, regional content, and a sales network need people with clear, limited powers, so SIGRAK stays credible. The proposal has four parts:

1. **Role catalogue in code.** Grow the catalogue from 3 roles to 11, still fixed in code (keeps D-314).
2. **Editable scope per person.** The admin sets each staff member's area: region (province, kabupaten, kecamatan), level (SD/MI), field (*bidang*: subject group or religion), and partner. Scope is data; powers stay in code.
3. **Several roles per person.** A small team means one person holds several roles. `staff_users.role` becomes a list of role assignments, each with its own scope.
4. **Four rules for every role:**
   - **Least access:** no role sees student data in the console by default.
   - **Separation of duty:** whoever writes content cannot sign it off; whoever records a payment cannot confirm it.
   - **Audit log** of every staff action on school data.
   - **2FA for all staff.**

## 2. Role catalogue

| # | Role (UI label) | Key | Does | Scope set by admin | Must not |
|---|---|---|---|---|---|
| R1 | **Pemilik** | `owner` | Everything an admin does, plus staff accounts with high powers, AI models per purpose (D-315), plan mapping core/add-on (D-211), prices | — | — |
| R2 | **Admin** | `admin` | Staff accounts, roles and scopes, console settings, schools, subscriptions, Kabar for everyone, approvals that need an admin | Optional region | Change prices or AI models (owner only) |
| R3 | **Validator** (Tim Validasi SIGRAK) | `validator` | Reviews and signs off curated data: curriculum and religion data (D-113), catalogs (D-305), local seed lists, Game kelas content packs (D-177), LKPD patterns, sample outputs of a new template version before it goes live | Field (e.g. Matematika, Bahasa, IPAS, PAI/MI, Kristen, Katolik, Hindu, Buddha, Khonghucu, Muatan lokal), level, region (for local content) | Sign off their own work |
| R4 | **Penyusun Konten** | `content` | Prepares curated data and template material: prompt overrides (D-013), curriculum drafts in the curriculum console (D-317), LKPD patterns and copy, local seed lists, pictograms and 3D models, the embed allowlist, help articles | Field, level | Publish without a validator's sign-off |
| R5 | **Kurator Kurikulum** | `curriculum` | Publishes and rolls back curriculum versions after sign-off; runs the JP audit and impact report (D-317) | Level (SD/MI) | Publish a version that has no sign-off |
| R6 | **Support** | `support` | Help requests (D-320), school setup help, resend invitations, account recovery, pengawas requests (D-309), school list fixes | Optional region | Read teachers' documents or student data without the school's consent (§4) |
| R7 | **Pemasaran** | `marketing` | Targeted Kabar for campaigns and webinars (D-319), Mode Contoh and trial content, aggregated usage reports per region (counts only), referral codes | Region | See school-level user data or any student data |
| R8 | **Penjualan** | `sales` | Their schools in their area: pipeline status (prospek, trial, aktif, perpanjang, berhenti), contacts of the operator and principal, trial extensions within a limit, quotes, recording a payment claim | Region (kabupaten/kecamatan), or a named list of schools | Confirm payments, see teachers' content or student data, change prices |
| R9 | **Koordinator Wilayah** | `regional_lead` | Everything a sales role sees for several areas; moves schools between sales people; sets targets; area reports | Several regions | Confirm payments |
| R10 | **Keuangan** | `finance` | Confirms manual payments, issues invoices and receipts, handles refunds, sees revenue reports | — | Change school data or content |
| R11 | **Teknis** | `engineer` | Kesehatan Sistem (D-316), Horizon, failed jobs, AI error overview, backups | — | Read school data in the console (uses logs, not school records) |

**Later, outside the console:** *Mitra Wilayah*, external partners such as a teacher community, a KKG or KKM, or a reseller. They get a referral code and a partner report, not a console account. This needs its own decision once there is a partner programme.

**Not a role:** read-only "observer" access for investors or auditors is a report export, not an account.

## 3. Access matrix (proposed)

● full · ◐ within scope · ○ read only · – none.

| Area | Owner | Admin | Validator | Konten | Kurikulum | Support | Pemasaran | Penjualan | Koord. | Keuangan | Teknis |
|---|---|---|---|---|---|---|---|---|---|---|---|
| Staff accounts, roles, scopes | ● | ● | – | – | – | – | – | – | – | – | – |
| Prices, plan mapping, AI models | ● | ○ | – | – | – | – | – | – | – | ○ | ○ |
| Schools and regions (list, profile) | ● | ● | – | – | – | ● | ○ counts | ◐ | ◐ | ○ | – |
| Subscriptions and trials | ● | ● | – | – | – | ○ | – | ◐ trial extension | ◐ | ● | – |
| Payment confirmation | ● | ○ | – | – | – | – | – | record claim ◐ | ○ ◐ | ● | – |
| Template prompts and LKPD patterns | ● | ○ | sign-off ◐ | prepare ◐ | – | – | – | – | – | – | – |
| Curriculum versions | ● | ○ | sign-off ◐ | draft ◐ | publish ◐ | – | – | – | – | – | – |
| Catalogs, seed lists, content packs, assets | ● | ○ | sign-off ◐ | prepare ◐ | – | – | – | – | – | – | – |
| Embed allowlist (D-330) | ● | ● | sign-off | prepare | – | – | – | – | – | – | ○ |
| Kabar to schools | ● | ● | – | – | ◐ curriculum notices | ◐ | ◐ | ◐ own schools | ◐ | – | ◐ maintenance |
| Help requests | ● | ● | – | ○ content tickets | – | ● | – | ○ own schools | ○ | ○ billing tickets | ○ |
| Pengawas requests (D-309) | ● | ● | – | – | – | ● | – | – | – | – | – |
| Usage reports | ● | ● | ○ quality | ○ | ○ | ○ | ◐ aggregated | ◐ own schools | ◐ | ○ revenue | ○ |
| System health, Horizon | ● | ○ | – | – | – | – | – | – | – | – | ● |
| Audit log | ● | ● | – | – | – | – | – | – | – | – | ○ |
| Student data | – | – | – | – | – | – | – | – | – | – | – |
| Teachers' documents | – | – | – | – | – | with consent (§4) | – | – | – | – | – |

Student data never shows in the console. This follows the tenancy and privacy rules (D-023, D-036, D-215): student data belongs to the school.

## 4. Support access to a school ("Lihat sebagai")

Support sometimes needs to see what a teacher sees. Rules:

- The school's principal or operator grants access for a set time (default 24 hours) from Pengaturan Sekolah, or the teacher grants it from Minta Bantuan.
- Access is read-only, every page view is logged, and the school sees the log.
- Student names are masked unless the request is about student data.

## 5. Scopes

| Scope | Values | Used by |
|---|---|---|
| Region | Province → kabupaten → kecamatan (from the NPSN directory, D-169, D-319) | Penjualan, Koordinator, Pemasaran, Support, Validator (local content) |
| School list | Named schools, for exceptions such as a foundation with schools in several areas | Penjualan, Koordinator |
| Level | SD, MI | Validator, Konten, Kurikulum |
| Field (*bidang*) | Subject groups and the six religions; Muatan lokal per region | Validator, Konten |

The admin changes scopes in the console, and every change is logged. One school belongs to exactly one sales person at a time; moving it keeps the history.

## 6. Clash check

| # | Idea | Existing rule | Clash? | Proposed way |
|---|---|---|---|---|
| K1 | More staff roles | D-167 names three roles | Changes it | New catalogue of 11 roles; `support` and `content` keep their meaning, `admin` loses prices and AI models to `owner` |
| K2 | Editable matrix | D-314: fixed roles, read-only matrix, editable matrix rejected | **Yes, if powers become editable** | Recommended: powers stay in code, and only **scopes** become editable data. If the owner wants editable powers, D-314 must be superseded |
| K3 | Several roles per person | `staff_users.role` is one value (data model) | Schema change | Table `staff_role_assignments` (`staff_user_id`, `role`, `scope`); policies check any matching assignment |
| K4 | Sales sees schools | Tenancy: the console bypasses school scope (architecture) | No | The console filters by the staff member's scope; schools outside it are hidden |
| K5 | Support sees a teacher's work | Login-only, school data owned by the school (D-010) | Partial | Time-limited, school-granted, read-only, logged (§4) |
| K6 | Tim Validasi as a role | D-113 names Tim Validasi as a group, not an account type | No | The validator role makes sign-off a tracked action (who, when, which version) |

## 7. Start small

With a small team, start with **six roles**: Pemilik, Admin, Validator, Penyusun Konten, Support, Penjualan (with region scope). Add Kurikulum, Koordinator, Pemasaran, Keuangan, and Teknis when someone is hired for them. Until then, the owner or admin holds those powers. The catalogue and scopes should still be built for 11 from the start, so adding a role is a code release with no data change.

## 8. Owner questions

Answered on 2026-11-09:

- **RQ1** powers in code, scopes editable (D-338).
- **RQ2** all 11 roles active now (D-339), so §7 "start small" is replaced.
- **RQ3** Keuangan or the owner confirms payments (D-340).
- **RQ4** support may view school data under the school MoU: read-only, health and religion hidden, one internal row per support session, no log shown to schools (D-341). This replaces §4.
- **RQ5** partner programme for teacher communities (incl. KKG/KKM) and resellers, designed later (D-342).
- **RQ6** 2FA with an authenticator app (TOTP), required for owner and admin, optional for others and later for teachers; no recovery codes; a SIGRAK admin resets it (D-343).


| # | Question | Options |
|---|---|---|
| RQ1 | Keep powers fixed in code with only scopes editable (keeps D-314)? | Yes (recommended) / Editable powers (supersedes D-314) |
| RQ2 | Accept the 11-role catalogue, starting with six? | Yes / Change the list |
| RQ3 | Should payment confirmation be separate from sales (Keuangan, or owner until hired)? | Yes (recommended) / Sales may confirm |
| RQ4 | Should support access to a school be granted by the school, time-limited and logged (§4)? | Yes (recommended) / Support sees school data freely |
| RQ5 | External partners (Mitra Wilayah) with referral codes later? | Yes, later / No |
| RQ6 | 2FA required for all staff from the first release? | Yes (recommended) / Admin and owner only |
