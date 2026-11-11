<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import { onDestroy, untrack } from 'svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import SelectField from '@/Components/SelectField.svelte';
    import TextField from '@/Components/TextField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Option {
        value: string;
        label: string;
    }

    interface Row {
        weekday: number;
        starts_at: string;
        ends_at: string;
        kind: string;
        subject: string | null;
        subject_name: string | null;
        teacher_name: string | null;
        label: string | null;
        jp: number;
        is_locked?: boolean;
    }

    interface DraftClass {
        class: string;
        name: string;
        rows: Row[];
        unplaced: { subject: string; jp: number; reason: string }[];
    }

    interface Frame {
        days: Record<string, { starts_at: string; periods: number }>;
        breaks: { after_period: number; minutes: number; label: string | null }[];
    }

    let {
        year,
        years = [],
        classes = [],
        frame,
        preferences,
        draft = null,
        current = {},
    }: {
        year: { id: string; name: string } | null;
        years?: Option[];
        classes?: Option[];
        frame: Frame;
        preferences: Record<string, boolean>;
        draft?: { id: string; status: string; class: string | null; unplaced_jp: number; classes: DraftClass[] } | null;
        current?: Record<string, Row[]>;
    } = $props();

    const days = [1, 2, 3, 4, 5, 6];
    const dayName = (day: number): string => t(`timetable.days.${day}`);
    const preferenceKeys = ['max_consecutive', 'pjok_before_10', 'core_early', 'spread_week', 'no_split_break'];

    // The form starts from the saved settings; later prop reloads must not reset what the operator types.
    const settings = useForm(
        untrack(() => ({
            frame: {
                days: Object.fromEntries(days.map((day) => [day, { starts_at: frame.days[day]?.starts_at ?? '07:00', periods: String(frame.days[day]?.periods ?? 0) }])),
                breaks: frame.breaks.map((item) => ({ after_period: String(item.after_period), minutes: String(item.minutes), label: item.label ?? '' })),
            },
            preferences: { ...preferences },
        })),
    );

    let target = $state('');

    function saveSettings(event: SubmitEvent): void {
        event.preventDefault();
        settings.transform((data) => ({
            frame: {
                days: Object.fromEntries(Object.entries(data.frame.days).map(([day, value]) => [day, { starts_at: value.starts_at, periods: Number(value.periods) }])),
                breaks: data.frame.breaks.map((item) => ({ after_period: Number(item.after_period), minutes: Number(item.minutes), label: item.label || null })),
            },
            preferences: data.preferences,
        }));
        settings.put(route('timetable.draft.settings'), { preserveScroll: true });
    }

    function addBreak(): void {
        settings.frame.breaks = [...settings.frame.breaks, { after_period: '3', minutes: '15', label: '' }];
    }

    function removeBreak(index: number): void {
        settings.frame.breaks = settings.frame.breaks.filter((_, i) => i !== index);
    }

    function run(kelas: string | null): void {
        if (year === null) return;
        router.post(route('timetable.draft.store'), { tahun: year.id, kelas }, { preserveScroll: true });
    }

    let saveError = $state<string | null>(null);

    function save(classIds: string[] | null): void {
        if (draft === null || !confirm(t('timetable.draft.dialogs.save'))) return;
        saveError = null;
        router.post(route('timetable.draft.save', { draft: draft.id }), { classes: classIds }, {
            preserveScroll: true,
            onError: (errors) => (saveError = errors.draft ?? null),
        });
    }

    // The job runs on the queue (D-262); reload the draft until it is ready.
    const busy = $derived(draft !== null && (draft.status === 'queued' || draft.status === 'running'));
    let timer: ReturnType<typeof setInterval> | null = null;
    $effect(() => {
        if (busy && timer === null) {
            timer = setInterval(() => router.reload({ only: ['draft', 'current'] }), 2000);
        } else if (!busy && timer !== null) {
            clearInterval(timer);
            timer = null;
        }
    });
    onDestroy(() => {
        if (timer !== null) clearInterval(timer);
    });

    function rowTitle(row: Row): string {
        if (row.kind === 'lesson') return `${row.subject_name ?? ''} · ${t('timetable.jp_value', { jp: row.jp })}`;
        return row.label ?? (row.kind === 'break' ? t('timetable.draft.break') : t(`timetable.fields.label`));
    }

    function dayRows(rows: Row[], day: number): Row[] {
        return rows.filter((row) => row.weekday === day).sort((a, b) => a.starts_at.localeCompare(b.starts_at));
    }
</script>

<AppShell title={t('timetable.draft.title')}>
    <div class="mx-auto max-w-6xl space-y-6">
        {#if year === null}
            <p class="rounded-card border border-line bg-surface p-6 text-body text-muted">{t('timetable.no_year')}</p>
        {:else}
            <section class="space-y-3 rounded-card border border-line bg-surface p-4 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-title font-semibold">{t('timetable.year', { name: year.name })}</h2>
                        <p class="text-body text-muted">{t('timetable.draft.intro')}</p>
                    </div>
                    {#if years.length > 1}
                        <div class="sm:w-48">
                            <SelectField
                                id="year"
                                label={t('timetable.draft.fields.year')}
                                options={years}
                                value={year.id}
                                onchange={(e) => router.get(route('timetable.draft.show'), { tahun: e.currentTarget.value })}
                            />
                        </div>
                    {/if}
                </div>
            </section>

            <form class="space-y-6" onsubmit={saveSettings} novalidate>
                <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="frame">
                    <h2 id="frame" class="text-title font-semibold">{t('timetable.draft.sections.frame')}</h2>
                    <p class="text-body text-muted">{t('timetable.draft.hints.frame')}</p>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {#each days as day (day)}
                            <fieldset class="grid grid-cols-2 gap-3 rounded-input border border-line p-3">
                                <legend class="px-1 text-body font-semibold">{dayName(day)}</legend>
                                <TextField id={`starts-${day}`} type="time" label={t('timetable.draft.fields.starts_at')} bind:value={settings.frame.days[day]!.starts_at} />
                                <TextField
                                    id={`periods-${day}`}
                                    type="number"
                                    min="0"
                                    max="12"
                                    label={t('timetable.draft.fields.periods')}
                                    bind:value={settings.frame.days[day]!.periods}
                                    error={settings.errors[`frame.days.${day}.periods`]}
                                />
                            </fieldset>
                        {/each}
                    </div>
                    <div class="space-y-3">
                        {#each settings.frame.breaks as item, index (index)}
                            <div class="grid grid-cols-2 items-end gap-3 sm:grid-cols-4">
                                <TextField id={`break-after-${index}`} type="number" min="1" max="11" label={t('timetable.draft.fields.after_period')} bind:value={item.after_period} />
                                <TextField id={`break-minutes-${index}`} type="number" min="5" max="60" label={t('timetable.draft.fields.minutes')} bind:value={item.minutes} />
                                <TextField id={`break-label-${index}`} label={t('timetable.draft.fields.label')} maxlength={40} bind:value={item.label} />
                                <button type="button" class="min-h-11 rounded-input border border-line px-3 text-body font-semibold text-danger" onclick={() => removeBreak(index)}>
                                    {t('timetable.draft.remove_break')}
                                </button>
                            </div>
                        {/each}
                        <button type="button" class="min-h-11 rounded-input border border-line px-4 text-body font-semibold" onclick={addBreak}>{t('timetable.draft.add_break')}</button>
                    </div>
                </section>

                <section class="space-y-3 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="preferences">
                    <h2 id="preferences" class="text-title font-semibold">{t('timetable.draft.sections.preferences')}</h2>
                    <p class="text-body text-muted">{t('timetable.draft.hints.preferences')}</p>
                    {#each preferenceKeys as key (key)}
                        <label class="flex min-h-11 items-center gap-3 text-body">
                            <input type="checkbox" class="size-5 rounded border-line" bind:checked={settings.preferences[key]} />
                            {t(`timetable.draft.preferences.${key}`)}
                        </label>
                    {/each}
                    <PrimaryButton type="submit" disabled={settings.processing}>{t('timetable.draft.actions.save_settings')}</PrimaryButton>
                </section>
            </form>

            <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="run">
                <h2 id="run" class="text-title font-semibold">{t('timetable.draft.sections.run')}</h2>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="sm:w-64">
                        <SelectField id="target" label={t('timetable.draft.fields.class')} placeholder={t('timetable.draft.hints.all_classes')} options={classes} bind:value={target} />
                    </div>
                    <PrimaryButton type="button" disabled={busy} onclick={() => run(target || null)}>{t('timetable.draft.actions.run')}</PrimaryButton>
                </div>
            </section>

            {#if draft !== null}
                <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="result">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 id="result" class="text-title font-semibold">{t('timetable.draft.sections.result')}</h2>
                        {#if draft.status === 'ready'}
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <button type="button" class="min-h-11 rounded-input border border-line px-4 text-body font-semibold" onclick={() => run(draft?.class ?? null)}>
                                    {t('timetable.draft.actions.reshuffle')}
                                </button>
                                <PrimaryButton type="button" onclick={() => save(null)}>{t('timetable.draft.actions.save')}</PrimaryButton>
                            </div>
                        {/if}
                    </div>
                    <p class="text-body {draft.status === 'failed' ? 'text-danger' : 'text-muted'}" role="status">
                        {#if busy}
                            {t('timetable.draft.hints.running')}
                        {:else if draft.status === 'failed'}
                            {t('timetable.draft.hints.failed')}
                        {:else if draft.status === 'saved'}
                            {t('timetable.draft.hints.saved')}
                        {:else if draft.unplaced_jp === 0}
                            {t('timetable.draft.hints.complete')}
                        {:else}
                            {t('timetable.draft.hints.unplaced', { jp: draft.unplaced_jp })}
                        {/if}
                    </p>
                    {#if saveError}
                        <p class="text-body font-semibold text-danger" role="alert">{saveError}</p>
                    {/if}

                    {#each draft.classes as item (item.class)}
                        <article class="space-y-3 border-t border-line pt-4" aria-labelledby={`class-${item.class}`}>
                            <div class="flex items-center justify-between gap-3">
                                <h3 id={`class-${item.class}`} class="text-body font-semibold">{item.name}</h3>
                                {#if draft.status === 'ready' && draft.classes.length > 1}
                                    <button type="button" class="min-h-11 rounded-input border border-line px-3 text-body font-semibold" onclick={() => save([item.class])}>
                                        {t('timetable.draft.actions.save_class')}
                                    </button>
                                {/if}
                            </div>
                            {#if item.unplaced.length > 0}
                                <ul class="list-disc space-y-1 pl-5 text-body text-danger">
                                    {#each item.unplaced as missing, index (index)}
                                        <li>{t('timetable.draft.unplaced_item', { subject: missing.subject, jp: missing.jp, reason: missing.reason })}</li>
                                    {/each}
                                </ul>
                            {/if}
                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                {#each days as day (day)}
                                    {@const draftRows = dayRows(item.rows, day)}
                                    {@const currentRows = dayRows(current[item.class] ?? [], day)}
                                    {#if draftRows.length > 0 || currentRows.length > 0}
                                        <div class="rounded-input border border-line p-3">
                                            <p class="mb-2 font-semibold">{dayName(day)}</p>
                                            <div class="grid grid-cols-2 gap-2 text-label">
                                                <div>
                                                    <p class="mb-1 font-semibold text-muted">{t('timetable.draft.columns.current')}</p>
                                                    {#each currentRows as row, index (index)}
                                                        <p class="text-muted">{row.starts_at} {rowTitle(row)}</p>
                                                    {/each}
                                                </div>
                                                <div>
                                                    <p class="mb-1 font-semibold text-brand-strong">{t('timetable.draft.columns.draft')}</p>
                                                    {#each draftRows as row, index (index)}
                                                        <p class={row.kind === 'lesson' ? '' : 'text-muted'}>
                                                            {row.starts_at} {rowTitle(row)}{row.is_locked ? ` (${t('timetable.locked')})` : ''}
                                                        </p>
                                                    {/each}
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                {/each}
                            </div>
                        </article>
                    {/each}
                </section>
            {/if}
        {/if}
    </div>
</AppShell>
