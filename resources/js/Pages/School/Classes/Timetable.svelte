<script lang="ts">
    import { Link, router, useForm } from '@inertiajs/svelte';
    import { tick } from 'svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import SelectField from '@/Components/SelectField.svelte';
    import TextField from '@/Components/TextField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Row {
        id: string;
        weekday: number;
        period_no: number;
        starts_at: string;
        ends_at: string;
        kind: string;
        kind_label: string;
        label: string | null;
        subject: string | null;
        subject_name: string | null;
        teacher_name: string | null;
        jp: number;
        is_offered: boolean;
        clash: string | null;
    }

    interface Subject {
        code: string;
        name: string;
        teacher_name: string | null;
        jp_per_week: number | null;
        scheduled_jp: number;
        status: 'complete' | 'under' | 'over' | 'free';
    }

    let {
        school_class,
        rows,
        subjects,
        is_complete,
        kinds,
        minutes_per_jp,
    }: {
        school_class: { id: string; name: string; year: string };
        rows: Row[];
        subjects: Subject[];
        is_complete: boolean;
        kinds: { value: string; label: string }[];
        minutes_per_jp: number;
    } = $props();

    const days = [1, 2, 3, 4, 5, 6];
    const dayName = (day: number): string => t(`timetable.days.${day}`);

    // One form for adding and changing a row (D-247); `editing` is the row being changed.
    let editing = $state<string | null>(null);
    let formOpen = $state(false);
    const form = useForm({ weekday: '1', starts_at: '07:00', ends_at: '08:10', kind: 'lesson', subject: '', jp: '2', label: '' });

    const subjectOptions = $derived(
        subjects.map((subject) => ({
            value: subject.code,
            label:
                subject.teacher_name === null
                    ? `${subject.name} (${t('timetable.hints.subject_no_teacher')})`
                    : `${subject.name} · ${subject.teacher_name}`,
        })),
    );

    function minutes(time: string): number {
        const [hours, mins] = time.split(':').map(Number);
        return (hours ?? 0) * 60 + (mins ?? 0);
    }

    function clock(total: number): string {
        const hours = Math.floor(total / 60) % 24;
        return `${String(hours).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
    }

    // D-250: JP is suggested from the row's length; the operator may change it.
    function suggestJp(): void {
        const length = minutes(form.ends_at) - minutes(form.starts_at);
        if (form.kind === 'lesson' && length > 0) {
            form.jp = String(Math.min(4, Math.max(1, Math.round(length / minutes_per_jp))));
        }
    }

    function openAdd(day: number): void {
        const last = rows.filter((row) => row.weekday === day).at(-1);
        const start = last ? last.ends_at : '07:00';
        editing = null;
        form.clearErrors();
        form.weekday = String(day);
        form.starts_at = start;
        form.ends_at = clock(minutes(start) + 2 * minutes_per_jp);
        form.kind = 'lesson';
        form.subject = '';
        form.jp = '2';
        form.label = '';
        void show();
    }

    // The form sits above the days, so bring it into view on a phone.
    async function show(): Promise<void> {
        formOpen = true;
        await tick();
        document.getElementById('slot-form-title')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function openEdit(row: Row): void {
        editing = row.id;
        form.clearErrors();
        form.weekday = String(row.weekday);
        form.starts_at = row.starts_at;
        form.ends_at = row.ends_at;
        form.kind = row.kind;
        form.subject = row.subject ?? '';
        form.jp = String(row.jp || 1);
        form.label = row.label ?? '';
        void show();
    }

    function save(event: SubmitEvent): void {
        event.preventDefault();
        const lesson = form.kind === 'lesson';
        form.transform((data) => ({
            weekday: Number(data.weekday),
            starts_at: data.starts_at,
            ends_at: data.ends_at,
            kind: data.kind,
            subject: lesson ? data.subject : null,
            jp: lesson ? Number(data.jp) : null,
            label: lesson ? null : data.label,
        }));
        const options = { preserveScroll: true, onSuccess: () => (formOpen = false) };
        if (editing === null) {
            form.post(route('classes.timetable.store', { class: school_class.id }), options);
        } else {
            form.put(route('classes.timetable.update', { class: school_class.id, timetableSlot: editing }), options);
        }
    }

    function title(row: Row): string {
        return row.kind === 'lesson' ? (row.subject_name ?? '') : (row.label ?? row.kind_label);
    }

    function remove(row: Row): void {
        const message = t('timetable.dialogs.delete', { item: title(row), day: dayName(row.weekday), time: `${row.starts_at}–${row.ends_at}` });
        if (!confirm(message)) return;
        router.delete(route('classes.timetable.destroy', { class: school_class.id, timetableSlot: row.id }), { preserveScroll: true });
    }

    function statusLabel(subject: Subject): string {
        if (subject.status === 'free') return t('timetable.jp_free', { scheduled: subject.scheduled_jp });
        return t('timetable.jp_progress', { scheduled: subject.scheduled_jp, allocated: subject.jp_per_week ?? 0 });
    }
</script>

<AppShell title={t('timetable.titles.class', { name: school_class.name })}>
    <div class="mx-auto max-w-5xl space-y-6">
        <Link href={route('classes.show', { class: school_class.id })} class="inline-flex min-h-11 items-center text-body font-semibold text-brand-strong">
            ← {t('timetable.actions.back')}
        </Link>

        <section class="space-y-3 rounded-card border border-line bg-surface p-4 sm:p-6">
            <p class="text-body text-muted">{t('timetable.year', { name: school_class.year })} · {t('timetable.hints.page')}</p>
            <p class="rounded-input px-3 py-2 text-body font-semibold {is_complete ? 'bg-brand-tint text-brand-strong' : 'border border-line text-danger'}" role="status">
                {is_complete ? t('timetable.status.complete') : t('timetable.status.incomplete')}
            </p>
            <h2 class="text-title font-semibold">{t('timetable.summary')}</h2>
            <ul class="grid gap-2 sm:grid-cols-2">
                {#each subjects as subject (subject.code)}
                    <li class="flex items-center justify-between gap-3 rounded-input border border-line px-3 py-2 text-body">
                        <span class="min-w-0">
                            <span class="block font-semibold">{subject.name}</span>
                            <span class="block text-label text-muted">{subject.teacher_name ?? t('timetable.no_teacher')}</span>
                        </span>
                        <span class="shrink-0 text-right text-label">
                            <span class="block">{statusLabel(subject)}</span>
                            {#if subject.status === 'under'}
                                <span class="block font-semibold text-danger">{t('timetable.status.under')}</span>
                            {:else if subject.status === 'over'}
                                <span class="block font-semibold text-danger">{t('timetable.status.over')}</span>
                            {:else if subject.status === 'complete'}
                                <span class="block font-semibold text-brand-strong">{t('timetable.status.ok')}</span>
                            {/if}
                        </span>
                    </li>
                {/each}
            </ul>
        </section>

        {#if formOpen}
            <section class="rounded-card border border-brand bg-surface p-4 sm:p-6" aria-labelledby="slot-form-title">
                <h2 id="slot-form-title" class="mb-4 text-title font-semibold">
                    {editing === null ? t('timetable.actions.add') : t('timetable.actions.edit')}
                </h2>
                <form class="grid gap-4 sm:grid-cols-2" onsubmit={save} novalidate>
                    <SelectField
                        id="weekday"
                        label={t('timetable.fields.weekday')}
                        options={days.map((day) => ({ value: String(day), label: dayName(day) }))}
                        bind:value={form.weekday}
                        error={form.errors.weekday}
                    />
                    <SelectField id="kind" label={t('timetable.fields.kind')} options={kinds} bind:value={form.kind} error={form.errors.kind} onchange={suggestJp} />
                    <TextField id="starts_at" type="time" label={t('timetable.fields.starts_at')} bind:value={form.starts_at} error={form.errors.starts_at} onchange={suggestJp} />
                    <TextField id="ends_at" type="time" label={t('timetable.fields.ends_at')} bind:value={form.ends_at} error={form.errors.ends_at} onchange={suggestJp} />
                    {#if form.kind === 'lesson'}
                        <SelectField
                            id="subject"
                            label={t('timetable.fields.subject')}
                            options={subjectOptions}
                            placeholder={t('timetable.fields.subject')}
                            bind:value={form.subject}
                            error={form.errors.subject}
                        />
                        <SelectField
                            id="jp"
                            label={t('timetable.fields.jp')}
                            hint={t('timetable.hints.jp', { minutes: minutes_per_jp })}
                            options={[1, 2, 3, 4].map((jp) => ({ value: String(jp), label: t('timetable.jp_value', { jp }) }))}
                            bind:value={form.jp}
                            error={form.errors.jp}
                        />
                    {:else}
                        <TextField
                            id="label"
                            label={t('timetable.fields.label')}
                            hint={t('timetable.hints.label')}
                            maxlength={40}
                            bind:value={form.label}
                            error={form.errors.label}
                        />
                    {/if}
                    <div class="flex flex-col gap-3 sm:col-span-2 sm:flex-row">
                        <PrimaryButton type="submit" disabled={form.processing}>{t('timetable.actions.save')}</PrimaryButton>
                        <button
                            type="button"
                            class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold"
                            onclick={() => (formOpen = false)}
                        >
                            {t('common.actions.cancel')}
                        </button>
                    </div>
                </form>
            </section>
        {/if}

        <div class="grid gap-4 lg:grid-cols-2">
            {#each days as day (day)}
                {@const dayRows = rows.filter((row) => row.weekday === day)}
                <section class="rounded-card border border-line bg-surface p-4" aria-labelledby={'day-' + day}>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h2 id={'day-' + day} class="text-title font-semibold">{dayName(day)}</h2>
                        <button type="button" class="min-h-11 rounded-input border border-line px-4 text-body font-semibold" onclick={() => openAdd(day)}>
                            {t('timetable.actions.add')}
                        </button>
                    </div>
                    {#if dayRows.length === 0}
                        <p class="text-body text-muted">{t('timetable.empty_day')}</p>
                    {:else}
                        <ol class="divide-y divide-line">
                            {#each dayRows as row (row.id)}
                                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-label text-muted">{row.period_no}. {row.starts_at}–{row.ends_at}</p>
                                        <p class="font-semibold {row.kind === 'lesson' ? '' : 'text-muted'}">
                                            {title(row)}
                                            {#if row.kind === 'lesson'}
                                                <span class="text-label font-normal text-muted">· {t('timetable.jp_value', { jp: row.jp })}</span>
                                            {/if}
                                        </p>
                                        {#if row.kind === 'lesson'}
                                            <p class="text-label text-muted">{row.teacher_name ?? t('timetable.no_teacher')}</p>
                                        {/if}
                                        {#if !row.is_offered}
                                            <p class="text-label font-semibold text-danger">{t('timetable.not_offered')}</p>
                                        {/if}
                                        {#if row.clash}
                                            <p class="text-label font-semibold text-danger" role="alert">{row.clash}</p>
                                        {/if}
                                    </div>
                                    <div class="flex shrink-0 gap-2">
                                        <button type="button" class="min-h-11 rounded-input border border-line px-3 text-body font-semibold" onclick={() => openEdit(row)}>
                                            {t('timetable.actions.edit')}
                                        </button>
                                        <button type="button" class="min-h-11 rounded-input border border-line px-3 text-body font-semibold text-danger" onclick={() => remove(row)}>
                                            {t('timetable.actions.delete')}
                                        </button>
                                    </div>
                                </li>
                            {/each}
                        </ol>
                    {/if}
                </section>
            {/each}
        </div>
    </div>
</AppShell>
