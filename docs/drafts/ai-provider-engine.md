# AI Provider Engine

- **Status:** draft (owner review)
- **Task:** P1-04
- **Last updated:** 2026-10-30
- **Sources:** [`Konsep_Awal_Produk_SIGRAK.md`](../sources/Konsep_Awal_Produk_SIGRAK.md), [`Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md`](../sources/Catatan_Pengembang_EDUNEXA_Design_System_Data_dan_AI.md)
- **Facts checked on 2026-10-30** against Google's [rate limits](https://ai.google.dev/gemini-api/docs/rate-limits), [troubleshooting](https://ai.google.dev/gemini-api/docs/troubleshooting), [API errors](https://ai.google.dev/gemini-api/docs/api-errors), [Gemini API Additional Terms](https://ai.google.dev/gemini-api/terms), and [Google APIs Terms](https://developers.google.com/terms) pages, and Laravel's [AI SDK docs](https://laravel.com/docs/ai-sdk).

## Purpose

This document specifies the one layer that talks to AI providers: teachers' Gemini keys, key selection with round robin and failover, error handling, retries, duplicate-request safety, and usage logs. It is for developers. The content engine calls this layer through one interface, as described in [`content-engine.md`](content-engine.md).

## Scope: Gemini free tier only

SIGRAK supports **only Google Gemini API keys, and is designed for the free tier** (unpaid quota) (D-023). Keys from other AI providers are not accepted. Paid-tier Gemini keys work technically, but nothing is designed around them. The `AIProvider` interface stays, because it keeps the Gemini code isolated and lets tests use a fake. No second provider is planned.

Designing for the free tier means:

- **Low, per-project limits.** Every generation uses as few calls as possible. Steps are merged where output size allows. There are no background pings, no automatic regeneration, and no speculative calls.
- **Stable models only.** Preview and experimental models have tighter limits, and Google's terms forbid using them in production.
- **Data you send may be read by people.** See [Privacy rules](#privacy-rules).
- **Adults only.** Gemini's terms require API users to be 18+, and forbid using the API in a service directed to, or likely to be used by, people under 18. SIGRAK's AI users are teachers. The TKA exam site that students use never calls the AI (D-027).

## Goals

1. Every content feature uses the same AI path. Logging, retries, key rotation, and output checks live in one place.
2. If Google changes its API, only the Gemini adapter changes. Adding another provider later does not touch content code.
3. Teachers' keys never leave the server and never appear in logs.
4. Retries never run blindly. They never retry what cannot succeed, and they never silently double a request's cost.

## Key facts about Gemini quotas

- **Rate limits apply per Google Cloud project, not per API key.** Two keys from one project share one quota.
- Limits are measured as requests per minute (RPM), input tokens per minute (TPM), and requests per day (RPD), **per model**. They depend on the project's usage tier (Free, Tier 1–3).
- **The daily quota (RPD) resets at midnight Pacific time.** In WIB that is 14:00 while the US is on daylight time and 15:00 otherwise.
- Free-tier eligibility, billing, and restrictions are evaluated **per project**. A new key in a restricted project does not help.
- `models.list` only proves that a key exists. It does **not** prove the project can run inference (tier or billing problems still fail), so a key test must make a tiny generation call.
- Gemini 3.6 and later ignore `temperature`, `top_p`, and `top_k`, and Google says they will start returning 400 for them. Templates must not set them.

| Teacher's keys | Effective quota |
|---|---|
| 3 keys from 1 project | One shared quota |
| 3 keys from 3 projects | Three separate quotas, one per project |
| 3 keys from 3 accounts/projects | Separate quotas, each by its own tier |

**Terms of use and risk (D-023).** Google's API terms say users "will not attempt to circumvent" documented API limits. On the free tier, rotating several projects owned by one teacher effectively multiplies that teacher's free quota, so Google could treat it as circumvention and restrict or suspend those projects or accounts. The owner decided to keep multi-key rotation, **with the risk carried by the teacher**:

- Adding a second (or later) key requires ticking a consent box: "Saya memahami bahwa memakai beberapa project untuk menambah kuota gratis dapat melanggar ketentuan Google, dan Google dapat membatasi atau menangguhkan project saya."
- The key page and the help text repeat this plainly. Every key must come from a project the teacher personally owns.
- SIGRAK never creates Google projects or keys for teachers, never suggests making extra accounts, and never shares keys between teachers.

## Architecture

The owner's proposed tree is kept, with one change: education-specific parts (templates and the generation pipeline) move to `app/Content/` (see [`content-engine.md`](content-engine.md#template-anatomy)). That keeps the AI layer free of education logic. Class names follow Laravel conventions (`Contracts\AIProvider`, not `AIProviderInterface`).

```
app/AI/
├── Contracts/
│   └── AIProvider.php            # generate(AIRequest): AIResponse, throws AIException
├── Data/
│   ├── AIRequest.php             # purpose, system + content parts, JSON schema, limits, timeout, trace ids
│   ├── AIResponse.php            # parsed JSON/text, finish reason, tokens in/out, model, latency, credential id
│   └── AIErrorClass.php          # enum, see Error handling
├── Providers/Gemini/
│   ├── GeminiProvider.php        # AIRequest → generateContent body; response/error → AIResponse/AIErrorClass
│   └── GeminiClient.php          # thin client on Laravel's HTTP client; key passed per call
├── KeyManager/
│   ├── ApiKeyManager.php         # add, test, disable, delete; the encryption boundary
│   ├── KeySelector.php           # round robin + filters + sticky per generation
│   └── KeyHealthChecker.php      # state changes: cooldown, circuit breaker, invalid
├── Privacy/PromptDataGuard.php   # blocks student personal data from reaching a prompt
├── AIGateway.php                 # the only entry point used by app/Content: select key → call → classify → retry/failover → log
└── Usage/UsageLogger.php         # writes ai_usage_logs

app/Jobs/Generation/Generate{Rpm,BankSoal,Lkpd,QuizBattle,TkaPackage}.php, ReviseSection.php
                                  # one job per content type (D-024), all using GenerationRunner
```

**Why our own client and not the Laravel AI SDK** (accepted, D-116). Laravel ships a first-party AI SDK (`laravel/ai`, v1.0 released September 2026) with Gemini, structured output, and queueing. It is aimed at agents with one configured key per provider. Our core needs are to choose a different key for each call, to read raw HTTP status and error details for classification, and to control retries ourselves. A thin client on `Http::` makes those explicit and easy to test. The `AIProvider` interface keeps the choice reversible: the SDK can later become the transport inside `GeminiProvider` without touching callers.

## Keys

**Ownership (D-012, D-023).** Keys are Gemini free-tier keys that belong to a teacher (`user_id`), never to a school, and SIGRAK has no platform key. A principal needs no key unless they make content. Default limit: 5 keys per teacher (config). The second and later keys need the consent described above.

**Adding a key** (*Kunci AI Saya*, also asked inline the first time a teacher uses an AI feature):

1. The teacher pastes the key and enters a **project name** (required) and optionally the **Google account** it came from. Both are labels typed by the teacher, because a key does not reveal its project.
2. The server makes a tiny test generation. If it passes, the key is saved as `active`. If it fails, the teacher sees the reason in plain words and nothing is saved.
3. If the same key is already saved (same fingerprint), it is rejected.
4. If the project name matches an existing key, the teacher gets a warning: "Kunci dari project yang sama berbagi kuota, jadi tidak menambah batas pemakaian."
5. For the second and later keys, the consent box (Terms of use and risk) must be ticked, and the time of consent is stored.
6. A help page walks through making a key in Google AI Studio with a Gmail account (or belajar.id if the domain admin allows AI Studio). It explains free-tier limits, the daily reset time in WIB, and that Google may read free-tier prompts.

**Storage and transport.**

- The secret is stored in `ai_credentials.secret` with Laravel's `encrypted` cast (APP_KEY; rotate with `APP_PREVIOUS_KEYS`). A fingerprint (`HMAC-SHA256(secret, app key)`) detects duplicates, and `secret_last4` is kept for display.
- **Write-only.** The secret is never sent to the browser (Inertia props carry labels, last four characters, and status only) and never decrypted outside `ApiKeyManager`/`GeminiClient`.
- Sent as the `x-goog-api-key` header, **never** in the URL query string (EDUNEXA put it in the query string).
- It is never logged. HTTP client logging excludes headers, and Telescope hides `x-goog-api-key` (`Telescope::hideRequestHeaders`).
- Authorization: only the owner's generations may use a key. Principals, operators, supervisors, and staff can see that a teacher has keys and their status, never the secret.

**Key states**

| State | Meaning | Selectable | Leaves the state by |
|---|---|---|---|
| `active` | Healthy | Yes | Errors (below) |
| cooling down (per model) | Hit a rate limit on one model | Not for that model, until `until` | Time passes |
| circuit open | Repeated transient failures | No, until the window ends, then one probe | A successful probe closes it; a failed probe reopens it with double the window |
| `invalid` | Key rejected, project restricted, billing/credits issue | No | Teacher fixes it and presses "Uji lagi" |
| `disabled` | Teacher switched it off | No | Teacher switches it on |

Cooldowns are kept **per (key, model)** because quotas are per project per model: one model's daily limit does not block another. They live in Redis with a TTL and are mirrored to `ai_credential_cooldowns` so the key page can show "Kuota harian habis sampai pukul 14.00".

## Key selection

The algorithm combines round robin, health filtering, cooldown, failover, circuit breaker, and sticky selection:

```
select(user, model, generation):
    if generation.pinned_key is usable for model:          # sticky selection
        return generation.pinned_key
    candidates = user's keys ordered by id
                 where state = active
                   and not cooling down for model
                   and circuit closed (or half-open with its probe slot free)
    if candidates is empty:
        fail NoUsableKey(earliest_available_at)             # teacher sees when to retry
    i = INCR redis "ai:rr:{user_id}"  mod  len(candidates)  # round robin
    key = candidates[i]
    generation.pin(key)
    return key
```

- **Sticky selection.** All steps of one generation use the pinned key, so a multi-step RPM is made by one project and one model family. The pin changes only on failover.
- **Failover.** When an attempt fails with a failover-class error, the key's state is updated first. Then `select` runs again, excluding that key, and the new key is pinned.
- **Concurrency cap.** At most 2 running generations per teacher (`Redis::funnel`), so one teacher cannot burn all their own quota with parallel jobs.

## Error handling

The Gemini error is classified once in `GeminiProvider`, and the gateway acts on the class.

| Signal | Class | Retry same key | Fail over | Key state change | Teacher sees |
|---|---|---|---|---|---|
| 429, per-minute limit | `rate_limited` | Once, if the suggested retry delay is ≤ 20 s | Yes (otherwise) | Cool down (key, model) for the retry delay, else 60 s | Nothing, unless every key fails |
| 429, per-day limit | `rate_limited_daily` | No | Yes | Cool down (key, model) until next midnight Pacific | Key page: "Kuota harian project … habis sampai pukul …" |
| 400 `INVALID_ARGUMENT` | `bad_request` | No | **No** (another key will not fix a bad request) | None | "Belum berhasil dibuat. Tim Sigrak sudah diberi tahu." Error reported to the team |
| 400 `FAILED_PRECONDITION` (billing, free tier unavailable in region) | `project_unavailable` | No | Yes | `invalid` | Key page explains, with a link to AI Studio |
| 401 / 403 `PERMISSION_DENIED` (bad, leaked, or blocked key; restricted project) | `key_invalid` | No | Yes | `invalid` | Notification: "Kunci AI '…' tidak bisa dipakai lagi" |
| 402 (prepaid credits used up; paid projects only, should not occur on free tier) | `project_unavailable` | No | Yes | `invalid` | Key page explains |
| 404 model not found | `model_unavailable` | No | Next model in the purpose's list, same key | None | Nothing |
| 408, 500, 503 | `transient` | Up to 2 times, backoff 1 s → 2 s + jitter | Yes, after that | Counts toward the circuit breaker | Nothing, unless every key fails |
| 504 or client timeout | `timeout` | Once (see duplicates) | Yes, after that | Counts toward the circuit breaker | Nothing, unless every key fails |
| Blocked: `safety`, `prohibited_content`, `blocklist`, `recitation`, `spii`, … | `blocked` | No | No | None | "Isi yang diminta tidak bisa dibuat. Ubah topik atau catatan, lalu coba lagi." |
| Invalid JSON, schema mismatch, `MAX_TOKENS` | `malformed_output` | Goes to `OutputRepairer` | No | None | Only if repair fails |

To verify against real responses during implementation:

- How to tell per-minute from per-day 429s. The plan is to read the quota id in the error details (`QuotaFailure`) and the suggested delay (`RetryInfo.retryDelay`).
- The exact status names for `generateContent`.

**Budgets.** At most 4 attempts per step across all keys, and at most 5 minutes per step. When the budget is spent, the step fails with the most useful message seen. For example, if every key is cooling down, the message names the earliest time to retry.

**Circuit breaker** (per key; config values):

- It opens after 5 consecutive `transient`/`timeout` failures within 10 minutes and stays open for 5 minutes.
- After that one probe request is allowed. Success closes the circuit and resets the counter. Failure reopens it with double the window, up to 1 hour.

**Health checks.**

- Every key is tested on add, and when the teacher presses "Uji kunci" or "Uji lagi".
- Otherwise health comes from real traffic. There are no scheduled pings, because each ping is a real generation that spends the teacher's quota.

## Timeouts and duplicate requests

Gemini's `generateContent` has no idempotency key. A call that times out on our side may still have finished, and been billed, on Google's side. The rules:

1. **Client timeout per purpose** is generous (for example 180 s for long RPM steps, 90 s otherwise), and the job timeout and the queue's `retry_after` are set above it.
2. **On timeout, retry once on the same key before failing over.** Any duplicate cost stays on the same project, and the sticky selection holds.
3. **Every attempt is logged** with its attempt number, so duplicates are visible in usage history.
4. **A step's result is accepted exactly once.** `generation_steps` is unique on (`generation_id`, `step_key`) and moves `running → succeeded` with a compare-and-set, so a late duplicate cannot overwrite an accepted output.
5. **No queue-level retries.** Every generation job has `tries = 1` and implements `ShouldBeUnique` by generation id. Retries happen inside the gateway under the rules above. On resume, steps already marked `succeeded` are skipped.
6. **Double clicks.** The *Buat* button sends a client token (UUID). `generations` is unique on (`user_id`, `client_token`), so a double submit creates one generation.
7. **Crashed workers.** A scheduled sweeper finds steps stuck in `running` longer than timeout + margin, marks the attempt `unknown` in the log, and resumes the generation from that step. A possible duplicate cost is logged.

## Models and requests

- `config/ai.php` maps a **purpose** (for example `document_long`, `document_short`, `game_content`, `repair`) to an ordered list of models. Templates name a purpose, never a model. Model names change only in config/env. The default is **Gemini 3.8 Flash** (text-out; free tier 5 RPM, 250k TPM, 20 RPD per project, AI Studio 2026-10-31; D-117). Fallbacks and limits live in config and are re-checked at build time.
- Structured output: `responseMimeType: application/json` with the template's output schema in the JSON-schema field the current API version supports.
- Do not send `temperature`, `top_p`, or `top_k` (see Key facts).
- Teacher notes and curriculum text go inside clearly delimited data blocks. Instructions come only from the template. Output is accepted only through schema validation. These are the prompt-injection guards.

## Queues and progress

- Queues: `ai` (generation), `exports` (PDF/DOCX/HTML), `default`. Horizon gives `ai` its own supervisor, so long AI calls never block exports or mail.
- **One job class per content type** (D-024): `GenerateRpm`, `GenerateBankSoal`, `GenerateLkpd`, `GenerateQuizBattle`, `GenerateTkaPackage`, plus `ReviseSection`. Each declares its own step plan, timeouts, and progress labels, and delegates the mechanics to the shared `GenerationRunner`. A job runs one pending step, then re-dispatches itself until the generation is done, so each run stays short and resumable.
- Generation status: `queued → running (step i of n) → succeeded | failed | cancelled`.
- The UI polls every 2–3 s through an Inertia partial reload while the page is open, and a database notification arrives in *Kabar* when the generation finishes (*proposed*). WebSockets (Reverb) come later, only if polling proves too heavy.

## Privacy rules

Free-tier prompts and responses are used by Google to improve its products, and human reviewers may read them. Google's own terms say not to submit sensitive, confidential, or personal information to unpaid services. So:

1. **No student personal data ever goes into a prompt:** no names, NISN, birth dates, attendance, notes, or individual scores tied to an identity. `PromptDataGuard` checks every request built from school data, and generation code receives student data only in the forms allowed below.
2. **Report description drafts** (Asesmen > Rapor) send only per-TP mastery levels for an anonymous student ("Murid A"). The real name is merged in after generation, on our server.
3. **The teacher's note** field shows a hint: "Jangan tulis nama atau data pribadi siswa." Notes are free text, so this is guidance, not a guarantee.
4. **Curriculum data, teacher answers, and generated content** are fine to send. They hold no personal data.
5. The exam site (TKA) holds student credentials and answers and **never** calls the AI.

## Usage logs and visibility

`ai_usage_logs` gets one row per attempt: user, school, key id (never the key), generation, step, attempt number, model, purpose, error class, HTTP status, input/output tokens, latency, and time. Prompt and response text are **not** logged. The specification and accepted outputs on the generation are enough to rebuild and debug.

| Who | Sees |
|---|---|
| Teacher | *Kunci AI Saya*: each key's status, cooldowns, last success, daily counts, recent failures with plain reasons |
| Principal | Per-teacher totals: generations made, failures caused by key problems. No keys, no per-key detail |
| Tim SIGRAK | Errors by class and template in Filament, plus Horizon. Telescope on local/staging only |

Retention (D-118): detailed rows for 1 month, monthly aggregates kept.

## Testing

- A **fake provider** implementing `AIProvider`, with scripted responses and error sequences. Feature tests cover selection, sticky pinning, every row of the error table, cooldown expiry, the circuit breaker, and the duplicate rules.
- **Contract tests** for `GeminiProvider` against recorded Gemini responses (success, 429 per-minute and per-day, 400, 403, 503, blocked, malformed).
- One **opt-in live smoke test** with a real key, never in CI.

## Open questions

- [ ] Confirm the 429 detail fields that separate per-minute from per-day limits, using real responses.
- [x] Usage-log retention: 1 month (D-118).
- [x] Backup (*cadangan*) keys: yes, used only when every normal key is unavailable (D-119).
- [x] Default model Gemini 3.8 Flash with its free-tier limits (D-117); re-check at build time.
