# Coding Standards

- **Status:** draft
- **Task:** P2-06
- **Last updated:** 2026-10-31

## Purpose

These are the rules every developer and agent follows once the SIGRAK build starts. Each rule is short and can be checked in review or by a tool. Where a rule comes from an earlier decision or draft, it links there and does not repeat it. Items marked *proposed* are listed under [Decisions to record](#decisions-to-record) and need owner approval. Items marked *owner-approved* were approved by the owner on 2026-10-31 and only need a decision number.

Sources: [`CLAUDE.md`](../../CLAUDE.md), [`CONTEXT.md`](../agent/CONTEXT.md), [`architecture.md`](architecture.md), [`data-model.md`](data-model.md), [`ai-provider-engine.md`](ai-provider-engine.md), [`content-engine.md`](content-engine.md), [`design-system.md`](design-system.md).

## 1. PHP and Laravel

| # | Rule | Checked by |
|---|---|---|
| PHP-1 | Code style is PSR-12 through **Laravel Pint** with the repo's `pint.json` (Laravel preset + `declare_strict_types`). No hand formatting debates. | `vendor/bin/pint --test` in CI |
| PHP-2 | Every PHP file in `app/`, `database/`, `routes/`, `tests/`, and `config/` starts with `declare(strict_types=1);`. | Pint rule |
| PHP-3 | Static analysis with **Larastan**, level 6 at start, raised over time (*proposed*). New code adds no baseline entries. | `vendor/bin/phpstan` in CI |
| PHP-4 | Parameters, return types, and properties are typed. `mixed` and untyped arrays only with a PHPDoc shape (`array<int, Topic>`, `array{id: int, name: string}`). | Larastan |
| PHP-5 | Fixed sets are **PHP backed string enums** in `app/Enums/`, cast on the model, mirroring the column's `CHECK` (see [data model](data-model.md#conventions)). Enum values are the English snake_case DB values; labels come from `lang/id/enums.php`. No magic strings for statuses. | Review, arch test |
| PHP-6 | Input is validated in a **Form Request** (`app/Http/Requests/<Area>/<Action>Request.php`). Never `$request->all()` into a model; use `$request->validated()`. | Review, arch test |
| PHP-7 | Every controller action is authorized through a **Policy** (`authorize()` in the Form Request or `Gate`/`can` middleware). An action without authorization fails review. | Review + IDOR tests |
| PHP-8 | **No business logic in controllers.** A controller validates (Form Request), authorizes (Policy), calls one Action or service, and returns an Inertia response or redirect. About 15 lines per method is a smell, not a limit. | Review |
| PHP-9 | Business operations live in single-purpose **Action** classes with one public `handle()` method (*proposed*): in the module folder when one exists (`app/Classroom/Actions/RecordAttendance.php`), otherwise in `app/Actions/<Domain>/`. `app/AI` and `app/Content` keep their own layers. | Review |
| PHP-10 | Models declare `$fillable` explicitly. `$guarded = []` and `Model::unguard()` are not allowed. Sensitive attributes (`secret`, `password`, `token_secret`) are in `$hidden`. | Arch test |
| PHP-11 | Read config through `config()`. `env()` is called only inside `config/*.php`. | Larastan rule |
| PHP-12 | No raw SQL with interpolated values. `DB::raw`/`whereRaw` only with bindings. | Review |
| PHP-13 | Prevent N+1: `Model::preventLazyLoading()` outside production; eager load in queries that feed lists. | Runtime exception in dev/tests |
| PHP-14 | Jobs carry ids, not models with loaded relations, and re-check state on run. Generation jobs follow [AI engine queue rules](ai-provider-engine.md#timeouts-and-duplicate-requests) (`tries = 1`), with `ShouldBeUniqueUntilProcessing` + `WithoutOverlapping` by generation id so a job can re-dispatch itself ([architecture.md](architecture.md#decisions-to-record), item 3). | Review |
| PHP-15 | Dates come from `now()`/`CarbonImmutable` (set as the default date class), never `date()`/`time()`, so tests can freeze time. | Review |

Example of a thin controller:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Classroom\Actions\CreateJournal;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreJournalRequest;
use Illuminate\Http\RedirectResponse;

final class JournalController extends Controller
{
    public function store(StoreJournalRequest $request, CreateJournal $createJournal): RedirectResponse
    {
        $journal = $createJournal->handle($request->user(), $request->validated());

        return to_route('journals.show', $journal)->with('toast', __('journals.saved'));
    }
}
```

## 2. Folder layout

The module folders and their allowed dependencies are defined in [architecture.md](architecture.md#module-boundaries) (`app/AI`, `app/Content`, `app/Tenancy`, `app/Curriculum`, `app/Classroom`, `app/Exam`, `app/Filament`, `app/Jobs/Generation`). This section does not repeat or change them. It only adds the Laravel folders around them and the rules for where code goes.

| Path | Holds |
|---|---|
| Module folders | As in [architecture.md](architecture.md#module-boundaries). `BelongsToSchool` and the school context live in `app/Tenancy/` |
| `app/Actions/<Domain>/` | Single-purpose business operations for features **without** a module folder (assessment, report cards, portals, finance, inventory). Inside a module, its actions live in the module (`app/Classroom/Actions/RecordAttendance.php`) |
| `app/Enums/` | Backed enums for every fixed set |
| `app/Models/` | Eloquent models |
| `app/Policies/` | One policy per model exposed to users |
| `app/Http/Controllers/{Teacher,School,Supervisor,Exam}/` | Thin controllers grouped by area. Exam site controllers live only in `Exam/` and call `app/Exam` |
| `app/Http/Requests/<Area>/` | Form Requests |
| `app/Http/Resources/` | Shapes for Inertia props (see SV-4) |
| `app/Jobs/` | Queued jobs (`Generation/` per [D-024](../agent/DECISIONS.md)) |
| `app/Exports/`, `app/Imports/` | Laravel Excel classes for administrative data (leger, attendance, student import). Template document export stays in `app/Content/Export/` |
| `app/Filament/` | Internal console, `staff` guard only |
| `app/Support/` | Small framework-free helpers, e.g. `IndonesianFormat` |
| `routes/app.php`, `routes/exam.php`, `routes/admin.php` | One file per hostname group (`app.`, `ujian.`, `admin.<domain>`, registered with `Route::domain(...)`), each with its own guard ([architecture.md](architecture.md#hostnames-proposed)) |
| `resources/js/Pages/<Area>/` | Inertia pages |
| `resources/js/Components/` | Shared Svelte components (ported TailAdmin parts), `resources/js/Templates/<Key>/` per template |
| `resources/game-runtime/`, `resources/exam-client/` | Separate bundles (D-011, D-027) |
| `resources/views/exports/<key>/` | Blade views for PDF/DOCX |
| `lang/id/` | All UI and export text |
| `python/<tool>/` | Python tools, only when Laravel/Node cannot do the job well, each with its own pinned `requirements.txt` and README |

Rules:

- **LAY-1** Every dependency follows the "May use / Must not use" table in [architecture.md](architecture.md#module-boundaries). Each row of that table has one Pest architecture test.
- **LAY-2** Outside `app/AI`, code reaches AI only through `App\AI\AIGateway`, never `App\AI\Providers\*` or `KeyManager` internals.
- **LAY-3** `app/AI` never imports `App\Content`, `App\Exam`, or student models.
- **LAY-4** `App\Exam`, `App\Classroom`, `App\Http\Controllers\Exam`, and `resources/exam-client` never import `App\AI\*` (D-027; offline data never touches AI).
- **LAY-5** Controllers and `app/Actions` may call into modules; modules never import `App\Http\*`.

Example: `arch()->expect('App\Exam')->not->toUse('App\AI');`

## 3. Naming

| Thing | Convention | Example |
|---|---|---|
| Classes, enums, Svelte components | StudlyCase / PascalCase | `KeySelector`, `GenerationStatus`, `KeyRow.svelte` |
| Methods, variables, props | camelCase | `selectKey()`, `$pinnedCredential`, `secretLast4` |
| Enum cases | StudlyCase, value snake_case | `case RateLimitedDaily = 'rate_limited_daily';` |
| Tables, columns | plural snake_case tables, snake_case columns | `teaching_assignments.teacher_id` |
| Models | singular StudlyCase | `TeachingAssignment` |
| Pivot tables | as named in the data model; new ones use Laravel's alphabetical singular form | `focus_item_objectives`, `role_user` |
| Booleans | `is_`/`has_` prefix | `is_active`, `is_flagged` |
| Timestamps / dates | `_at` (timestamp), `_on` (date) | `submitted_at`, `starts_on` |
| Actions, jobs | verb first | `CreateJournal`, `GenerateRpm` |
| Events | past tense | `GenerationSucceeded` |
| URLs | kebab-case, English, plural resources | `/ai-keys/{credential}`, `/documents/{document}` |
| Route names | dot.snake | `ai_keys.store`, `exam.sessions.show` |
| Config keys, translation keys | snake_case, dot-separated | `ai.purposes.document_long`, `journals.saved` |
| Branches | `<phase>/<task-id>-<slug>` | `p3/p3-04-journals` |

Indonesian school terms that have no exact English equivalent keep a short English identifier plus a comment or doc link, never Indonesian identifiers (`homeroom_teacher_id`, not `wali_kelas_id`). Exception: curriculum codes and enum values already fixed in the data model (`kurang`, `memadai`, `pgk_mcma`, …).

## 4. Migrations and database

| # | Rule |
|---|---|
| DB-1 | One table per migration file (or one change per file for alters). |
| DB-2 | Never edit a migration that is on `main`. Add a new one. |
| DB-3 | Every foreign key is declared with `constrained()` and an explicit `cascadeOnDelete()` / `restrictOnDelete()` / `nullOnDelete()`, matching the data model. |
| DB-4 | Every school-owned table has `school_id` `NOT NULL`, indexed, and composite indexes start with it. |
| DB-5 | Columns are `NOT NULL` unless the data model states what `NULL` means. Add that meaning as a column comment. |
| DB-6 | Fixed sets: `string` column + `CHECK (col IN (…))` added with `DB::statement` in the same migration, generated from the enum's cases so they cannot drift. |
| DB-7 | Uniqueness and range invariants from the data model are DB constraints (unique indexes, `CHECK`), not only validation. |
| DB-8 | **No soft deletes.** The `SoftDeletes` trait and `softDeletes()` column are banned. Lifecycle uses an explicit `status`. |
| DB-9 | Money is `bigInteger` rupiah. Scores use `decimal(5,2)`. No `float`/`double` for either. |
| DB-10 | Timestamps are stored in UTC (`APP_TIMEZONE=UTC`). Conversion to `Asia/Jakarta` happens only when displaying or exporting. |
| DB-11 | Rows exposed in URLs use `public_id` (UUIDv7) for route model binding (`getRouteKeyName()`); the bigint id never appears in a URL or prop. |
| DB-12 | JSON columns only where the data model allows them. Never filter or sort on a JSON path in a hot query; promote the field to a column instead. |
| DB-13 | Each migration has a working `down()` for local use. Production is forward-only. |
| DB-14 | Seeders for reference data (curriculum, regions) are idempotent upserts by natural key. Demo data lives only in factories and `DemoSeeder`, never runs in production. |

```php
Schema::create('journals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('school_id')->constrained()->restrictOnDelete();
    $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
    $table->string('status');
    $table->uuid('client_uuid')->unique();
    $table->timestamps();
    $table->index(['school_id', 'class_id']);
});

$statuses = implode("','", array_column(JournalStatus::cases(), 'value'));
DB::statement("ALTER TABLE journals ADD CONSTRAINT journals_status_check CHECK (status IN ('{$statuses}'))");
```

## 5. Tenancy and access tests

The tenancy model is in [data-model.md](data-model.md#tenancy-and-access). Code rules:

- **TEN-1** Every school-owned model uses the `App\Tenancy\BelongsToSchool` trait. A model with `school_id` and without the trait fails an arch test.
- **TEN-2** `withoutSchoolScope()` is allowed only in `app/Filament/` and in jobs or commands that receive and name the school explicitly. Each call has a one-line comment saying why.
- **TEN-3** Jobs that touch school data take `schoolId` in the constructor and set the tenancy context first. They never rely on the request's session.
- **TEN-4** Route model binding resolves through the scoped query, so a foreign `public_id` gives 404, not 403 (no existence leak).
- **TEN-5** **One IDOR test per resource and action** that reads or changes data: a user of school A requests school B's row and gets 404; a user without the role gets 403. Pattern:

```php
it('hides another school\'s journal', function () {
    $journal = Journal::factory()->for(School::factory())->create();
    $teacher = User::factory()->teacherAt(School::factory()->create())->create();

    actingAs($teacher)->get(route('journals.show', $journal))->assertNotFound();
});
```

- **TEN-6** Student-level data, counselling notes, and religion follow the visibility rules in the data model (D-036). Each rule has a policy test, including "supervisor sees no student rows".
- **TEN-7** Inertia props for one school's page never include another school's data, even counts.

## 6. Svelte and Inertia

| # | Rule |
|---|---|
| SV-1 | Plain Svelte with `@inertiajs/svelte`, no SvelteKit (D-008). Svelte 5 runes syntax (*owner-approved*). |
| SV-2 | TypeScript in `<script lang="ts">` for the school app and the exam client (*owner-approved*). Game runtime stays vanilla JS (D-011), with JSDoc types and `// @ts-check`. |
| SV-3 | Pages in `resources/js/Pages/<Area>/<Name>.svelte`, one per Inertia route. Shared parts in `resources/js/Components/`. Components stay presentational; data comes from page props. |
| SV-4 | Props are built server-side with API Resources or explicit arrays. **Never pass a whole model** (`toArray()` leaks columns). Secrets never reach a prop (AI keys show label, last 4, status only). |
| SV-5 | Ids in props and URLs are `public_id`s. Route URLs come from the generated route helper, never hand-built strings. |
| SV-6 | Forms use Inertia `useForm`; server validation errors show inline under the field. The *Buat* buttons send a client token (UUID) to stop double submits. |
| SV-7 | No `{@html}` on user or AI text. Allowed only for output of our own trusted renderers (KaTeX, bundled SVG figures), each marked with a comment. |
| SV-8 | Styling only through Tailwind classes mapped to the tokens in `resources/css/tokens.css` ([design system](design-system.md#stack-mapping)). No hex colours or arbitrary values (`text-[9px]`) in components. |
| SV-9 | Every screen is checked at 360 px and 1280 px wide. Tables switch to card lists below 640 px; touch targets ≥ 44 px. |
| SV-10 | Icon-only buttons have `aria-label`; form fields have a `<label>`; status is never colour alone. |
| SV-11 | Libraries (Lucide, KaTeX, three.js, ApexCharts, fonts) are imported from `node_modules` and bundled by Vite. No CDN `<script>` or `<link>` (D-033). Heavy libraries (three.js, ApexCharts) are lazy-loaded with `import()` on the pages that need them. |
| SV-12 | Long-running state (generation progress) uses Inertia partial reloads (`only: [...]`) on a poll, as in the [AI engine](ai-provider-engine.md#queues-and-progress). |

## 7. Translations and UI text

| # | Rule |
|---|---|
| TR-1 | All user-visible text lives in `lang/id/*.php`. No Indonesian (or English) UI string literals in PHP, Blade, or Svelte. |
| TR-2 | Keys are English snake_case, grouped by area file: `common.php`, `nav.php`, `journals.php`, `ai_keys.php`, `enums.php`, `export.php`, `validation.php`. Key = meaning, not the text (`journals.saved`, not `journals.jurnal_tersimpan`). |
| TR-3 | Placeholders use Laravel `:name` syntax. Plurals use `trans_choice`. Never build a sentence by concatenating translated parts. |
| TR-4 | Copy follows the [voice, writing rules, and terms](design-system.md#ui-writing-rules) (e.g. "Unduh", "Kunci AI", never "generate"/"prompt"). |
| TR-5 | Validation messages and attribute names are Indonesian, in `lang/id/validation.php` (`attributes` array). |
| TR-6 | Svelte gets strings through one `t('key', params)` helper fed from the server (shared Inertia prop per page group, *proposed*). Same keys as PHP. |
| TR-7 | A test fails when a key used in PHP, Blade, or Svelte is missing from `lang/id`. |
| TR-8 | The app locale and fallback locale are both `id`. There is no `lang/en`. |

## 8. Exports, dates, and numbers

| # | Rule |
|---|---|
| EX-1 | Every export (Excel, CSV, Word, PDF) uses Indonesian headers, labels, sheet names, and file names, through translation keys: `__('export.columns.created_at')` → "Tanggal Dibuat". |
| EX-2 | Enum values in exports go through their labels (`present` → "Hadir"), never the raw value. |
| EX-3 | Export file names are Indonesian, lowercase, hyphenated, with the date: `jurnal-kelas-4a-2026-10-30.xlsx`. |
| EX-4 | Excel cells keep real types: numbers as numbers, dates as dates with an Indonesian display format (`dd/mm/yyyy`), so teachers can sort and sum. |
| EX-5 | PDFs are one Blade view per document through Gotenberg (D-028, D-031); the print font comes from the school's print settings. |
| EX-6 | All formatting goes through `App\Support\IndonesianFormat` (PHP) and `resources/js/lib/format.ts` (JS). No ad-hoc `number_format()` or `toLocaleString()` in views. |

| Value | Format | PHP | JS |
|---|---|---|---|
| Date | `30/10/2026` | `$date->timezone('Asia/Jakarta')->format('d/m/Y')` | `Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta' })` |
| Date in prose | `30 Oktober 2026` | `->locale('id')->translatedFormat('j F Y')` | `{ dateStyle: 'long' }` |
| Time | `14.00` (WIB) | `->format('H.i')` | `{ hour: '2-digit', minute: '2-digit' }` |
| Number | `1.234.567,89` | `Number::format($n, 2, locale: 'id')` | `Intl.NumberFormat('id-ID')` |
| Money | `Rp1.234.567` | `Number::currency($n, 'IDR', 'id', 0)` | `{ style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }` |

The display timezone is `Asia/Jakarta` unless a school setting says otherwise later.

## 9. Tests

| # | Rule |
|---|---|
| TS-1 | Test framework: **Pest** (*owner-approved*), including its architecture tests for the layering and model rules above. |
| TS-2 | Tests run against **MySQL 8** in CI and locally (*proposed*), not SQLite, because the schema relies on `CHECK` constraints, JSON columns, and generated columns. |
| TS-3 | Required per feature: a feature test for each route (happy path + validation failure), the IDOR/role tests from TEN-5, and policy tests. Unit tests for Actions with real rules, for `app/AI` key selection and error classification, and for formatters. |
| TS-4 | **No real AI calls in tests.** Bind the fake `AIProvider` with scripted responses and error sequences. `Http::preventStrayRequests()` is on in the base `TestCase`. The one live Gemini smoke test is opt-in and never runs in CI ([AI testing](ai-provider-engine.md#testing)). |
| TS-5 | Gemini contract tests use recorded responses in `tests/Fixtures/gemini/`, scrubbed of keys. |
| TS-6 | Exports have a test that checks headers are Indonesian and number/date cells have the right types. PDFs are tested at the HTML-view level; Gotenberg is faked in CI. |
| TS-7 | Factories exist for every model and produce valid rows within one school by default. Fake student names come from `fake('id_ID')`; never use real people's data in fixtures. |
| TS-8 | A bug fix comes with a test that fails without the fix. |
| TS-9 | Frontend: `svelte-check` and ESLint in CI. Component tests (Vitest) for the content editors and the exam client's answer saving (*proposed*). |

## 10. AI privacy rules for code

These turn the [privacy rules](ai-provider-engine.md#privacy-rules) into code checks:

- **AI-1** Only `App\AI\Providers\Gemini\GeminiClient` calls Google's API. No other `Http::` call to `*.googleapis.com`.
- **AI-2** Every request built from school data goes through `PromptBuilder` and `PromptDataGuard`. Code never puts a `Student` model, name, NISN, birth date, attendance, note, religion, or identifiable score into an `AIRequest`.
- **AI-3** Report description drafts use an anonymous label ("Murid A"); the real name is merged after generation, on the server.
- **AI-4** Instructions come only from the template. Teacher notes and curriculum text go into delimited data blocks; AI output is accepted only after schema validation.
- **AI-5** Templates name a purpose, never a model id; model ids live in `config/ai.php`. Never send `temperature`, `top_p`, or `top_k`.
- **AI-6** The key is sent only in the `x-goog-api-key` header, decrypted only in `ApiKeyManager`/`GeminiClient`, and never put in a URL, prop, log, exception message, or queue payload (jobs carry the credential id).
- **AI-7** AI text shown in the UI is escaped (SV-7).
- **AI-8** Code under the exam site never uses `App\AI` (LAY-4).

Review checklist for any PR touching `app/Content` or `app/AI`: "Which data reaches the prompt, and could any of it identify a student?"

## 11. Logging and secrets

| # | Rule |
|---|---|
| LOG-1 | Secrets live only in `.env` on the server (never committed). `.env.example` lists every key with an empty or dummy value. |
| LOG-2 | Logs and exception context carry ids (`user_id`, `school_id`, `generation_id`, `public_id`), never secrets, passwords, tokens, prompt or response text, or student personal data. |
| LOG-3 | Parameters holding secrets are marked `#[\SensitiveParameter]` so they are hidden in stack traces. |
| LOG-4 | Telescope runs only in local/staging, behind the `staff` gate, and hides `x-goog-api-key`, `authorization`, `cookie`, `password`, `secret`, and `token` fields. |
| LOG-5 | Encrypted columns use the `encrypted` cast; never decrypt to log or compare. Duplicates are found by fingerprint. |
| LOG-6 | User-facing errors use translated messages (what happened + what to do). Technical details go to the log and behind "Lihat detail", never raw exception text. |
| LOG-7 | Uploads (letterheads, imports) are validated for type and size, stored on a private disk, and served through authorized or signed routes. |
| LOG-8 | Exam participant passwords and tokens never appear in logs; proctor actions go to `exam_events`, not the app log. |

## 12. Commits and pull requests

| # | Rule |
|---|---|
| GIT-1 | **No AI attribution anywhere**: no `Co-Authored-By` or session lines in commits, nothing in PR titles, descriptions, comments, code, or docs ([`CLAUDE.md`](../../CLAUDE.md)). |
| GIT-2 | Commit messages in English, imperative, subject ≤ 72 characters, no trailing period: `Add daily attendance sync endpoint`. A body explains *why* when it is not obvious. |
| GIT-3 | One task per branch (`<phase>/<task-id>-<slug>`) and per PR, following [`WORKFLOW.md`](../agent/WORKFLOW.md). Unrelated fixes go in their own PR. |
| GIT-4 | Before opening a PR: `pint --test`, `phpstan`, `pest`, `npm run lint`, `npm run check`, `npm run build` all pass. CI runs the same commands. |
| GIT-5 | PR description: what changed, why (task id, decision ids), how it was tested, and screenshots at phone and laptop width for UI changes. |
| GIT-6 | Never commit `.env`, keys, real student data, dumps, or build output (`public/build`, `docs/.build/`). |
| GIT-7 | A PR that adds a migration, a new dependency, or a new env key says so in its description. |

## 13. Dependencies

| # | Rule |
|---|---|
| DEP-1 | `composer.lock` and `package-lock.json` are committed. CI installs with `composer install` and `npm ci`, never `update`. |
| DEP-2 | Front-end runtime libraries that affect output (KaTeX, three.js, ApexCharts, Lucide, fonts) use exact versions in `package.json` (no `^`/`~`), so documents and figures render the same after a reinstall. |
| DEP-3 | No runtime CDN for scripts, styles, or fonts (D-033). Everything is bundled or self-hosted. |
| DEP-4 | Docker images (Gotenberg) are pinned to a version tag, never `latest` (D-028). Python packages are pinned with `==` in `requirements.txt`. |
| DEP-5 | Adding a dependency needs a reason in the PR: what it does, why Laravel or an existing package cannot, its licence, and its maintenance state. Prefer first-party Laravel packages. |
| DEP-6 | Dependency upgrades go in their own PR, with the changelog read and the full test suite passing. `composer audit` and `npm audit --omit=dev` run in CI. |

## Decisions to record

New choices made in this document. They get numbered in `DECISIONS.md` during integration.

1. **Pest** as the test framework (*owner-approved 2026-10-31*), with Pest architecture tests enforcing layering (LAY-1…5, TEN-1, PHP-10). Reason: readable tests and built-in arch tests make several rules here machine-checkable. PHPUnit stays underneath.
2. **TypeScript** in Svelte (*owner-approved 2026-10-31*) (`lang="ts"`) for the school app and exam client; the game runtime stays vanilla JS with JSDoc + `@ts-check` (keeps D-011). Reason: typed Inertia props catch prop/serialization mistakes; `svelte-check` runs in CI.
3. **Svelte 5 (runes)** as the component syntax (*owner-approved 2026-10-31*). Reason: current Svelte major, supported by `@inertiajs/svelte`.
4. **Larastan** static analysis, starting at level 6.
5. **Action classes** hold business operations; controllers stay thin. They live inside the module folders proposed in [architecture.md](architecture.md#decisions-to-record) (item 2), and in `app/Actions/<Domain>/` only for features without a module. This adds to that decision and does not change it.
6. **Tests run on MySQL 8**, not SQLite, because the schema depends on MySQL 8 features (D-007).
7. **Frontend translations** come from `lang/id` through a shared Inertia prop and one `t()` helper; no separate JS translation files.
8. **Typed route helper** for the frontend (Laravel Wayfinder, or Ziggy if Wayfinder does not fit plain Svelte), chosen at scaffold time.
9. **App timezone is UTC**, display and export in `Asia/Jakarta` (makes the data model's "stored in UTC" explicit in config).
10. **Exact version pins** for output-affecting front-end libraries (DEP-2).

## Open questions

- [x] Confirm decisions 1–3 (Pest, TypeScript, Svelte 5) before the project is scaffolded. Approved by the owner on 2026-10-31.
- [ ] Is a CI service (e.g. GitHub Actions) already chosen? GIT-4 and DEP-6 assume CI exists.
- [ ] Should `down()` methods be required (DB-13) or dropped to save time, given production is forward-only?
