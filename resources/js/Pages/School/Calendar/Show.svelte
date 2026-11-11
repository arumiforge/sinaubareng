<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import { tick } from 'svelte';
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

    interface CalendarEvent {
        id: string;
        title: string;
        kind: string;
        kind_label: string;
        exam_type: string | null;
        exam_type_label: string | null;
        starts_on: string;
        ends_on: string;
        class: string | null;
        class_name: string | null;
        can_edit: boolean;
    }

    interface Holiday {
        date: string;
        name: string;
        kind: string;
        kind_label: string;
        is_provisional: boolean;
        is_school_day: boolean;
    }

    interface Row {
        school_days: number;
        holidays: number;
        assessments: number;
        activities: number;
        effective_days: number;
        effective_weeks: number;
    }

    interface EffectiveDays {
        months: (Row & { key: string; month: string; semester: number })[];
        semesters: Record<string, Row>;
        year: Row;
        days_per_week: number;
        from_timetable: boolean;
    }

    let {
        year,
        years,
        calendar = null,
        region_calendar = null,
        can_manage = false,
        events = [],
        holidays = [],
        classes = [],
        selected_class = null,
        effective_days = null,
        kinds = [],
        exam_types = [],
    }: {
        year: { id: string; name: string; starts_on: string; ends_on: string } | null;
        years: Option[];
        calendar?: { id: string; source: string | null; region_changes: { type: string; message: string }[] } | null;
        region_calendar?: string | null;
        can_manage?: boolean;
        events?: CalendarEvent[];
        holidays?: Holiday[];
        classes?: (Option & { editable: boolean })[];
        selected_class?: string | null;
        effective_days?: EffectiveDays | null;
        kinds?: Option[];
        exam_types?: Option[];
    } = $props();

    const editableClasses = $derived(classes.filter((item) => item.editable));
    const canAdd = $derived(calendar !== null && (can_manage || editableClasses.length > 0));

    // Holidays and events in one list by date, grouped per month.
    type Item = { date: string; holiday?: Holiday; event?: CalendarEvent };
    const items = $derived(
        [
            ...holidays.map((holiday): Item => ({ date: holiday.date, holiday })),
            ...events.map((event): Item => ({ date: event.starts_on, event })),
        ].sort((a, b) => a.date.localeCompare(b.date)),
    );
    const months = $derived([...new Set(items.map((item) => item.date.slice(0, 7)))]);

    const dateFormat = new Intl.DateTimeFormat('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    const monthFormat = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });
    const day = (date: string): string => dateFormat.format(new Date(`${date}T00:00:00`));
    const monthName = (month: string): string => monthFormat.format(new Date(`${month.slice(0, 7)}-01T00:00:00`));
    const span = (from: string, to: string): string => (from === to ? day(from) : `${day(from)} – ${day(to)}`);
    const weeks = (value: number): string => value.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

    let editing = $state<string | null>(null);
    let formOpen = $state(false);
    const form = useForm({ title: '', kind: 'school_event', exam_type: 'sts', starts_on: '', ends_on: '', class: '', confirmed: false });

    async function show(): Promise<void> {
        formOpen = true;
        await tick();
        document.getElementById('event-form-title')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function openAdd(): void {
        editing = null;
        form.clearErrors();
        form.title = '';
        form.kind = 'school_event';
        form.exam_type = 'sts';
        form.starts_on = year?.starts_on ?? '';
        form.ends_on = year?.starts_on ?? '';
        form.class = can_manage ? '' : (editableClasses[0]?.value ?? '');
        void show();
    }

    function openEdit(event: CalendarEvent): void {
        editing = event.id;
        form.clearErrors();
        form.title = event.title;
        form.kind = event.kind;
        form.exam_type = event.exam_type ?? 'sts';
        form.starts_on = event.starts_on;
        form.ends_on = event.ends_on;
        form.class = event.class ?? '';
        void show();
    }

    // D-261: when the change would move RPM meetings, the server asks first; send again once confirmed.
    function submit(confirmed: boolean): void {
        if (calendar === null) return;
        const exam = form.kind === 'exam';
        form.transform((data) => ({ ...data, exam_type: exam ? data.exam_type : null, class: data.class || null, confirmed }));
        const options = {
            preserveScroll: true,
            onSuccess: () => (formOpen = false),
            onError: (errors: Record<string, string>) => {
                if (errors.confirm_impact && confirm(`${errors.confirm_impact} ${t('calendar.impact.confirm')}`)) submit(true);
            },
        };
        if (editing === null) {
            form.post(route('calendar.events.store', { calendar: calendar.id }), options);
        } else {
            form.put(route('calendar.events.update', { calendar: calendar.id, event: editing }), options);
        }
    }

    function save(event: SubmitEvent): void {
        event.preventDefault();
        submit(false);
    }

    function remove(event: CalendarEvent, confirmed = false): void {
        if (calendar === null) return;
        if (!confirmed && !confirm(t('calendar.dialogs.delete', { title: event.title }))) return;
        router.delete(route('calendar.events.destroy', { calendar: calendar.id, event: event.id }), {
            data: { confirmed },
            preserveScroll: true,
            onError: (errors) => {
                if (errors.confirm_impact && confirm(`${errors.confirm_impact} ${t('calendar.impact.confirm')}`)) remove(event, true);
            },
        });
    }

    function createCalendar(copy: boolean): void {
        if (year === null) return;
        router.post(route('calendar.store'), { tahun: year.id, copy }, { preserveScroll: true });
    }

    function regionChanges(action: 'apply' | 'skip', confirmed = false): void {
        if (calendar === null) return;
        router.post(route('calendar.region_changes', { calendar: calendar.id }), { action, confirmed }, {
            preserveScroll: true,
            onError: (errors) => {
                if (errors.confirm_impact && confirm(`${errors.confirm_impact} ${t('calendar.impact.confirm')}`)) regionChanges(action, true);
            },
        });
    }

    function toggleSchoolDay(holiday: Holiday, confirmed = false): void {
        if (calendar === null) return;
        router.put(route('calendar.joint_leave', { calendar: calendar.id }), { date: holiday.date, school_day: !holiday.is_school_day, confirmed }, {
            preserveScroll: true,
            onError: (errors) => {
                if (errors.confirm_impact && confirm(`${errors.confirm_impact} ${t('calendar.impact.confirm')}`)) toggleSchoolDay(holiday, true);
            },
        });
    }

    function pick(params: { tahun?: string; kelas?: string }): void {
        router.get(route('calendar.show'), { tahun: year?.id, kelas: selected_class ?? undefined, ...params }, { preserveScroll: true, preserveState: true });
    }
</script>

<AppShell title={t('calendar.title')}>
    <div class="mx-auto max-w-5xl space-y-6">
        {#if year === null}
            <p class="rounded-card border border-line bg-surface p-6 text-body text-muted">{t('calendar.no_year')}</p>
        {:else}
            <section class="space-y-3 rounded-card border border-line bg-surface p-4 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-title font-semibold">{t('calendar.year', { name: year.name })}</h2>
                        <p class="text-body text-muted">{can_manage ? t('calendar.hints.page') : t('calendar.hints.read_only')}</p>
                        {#if calendar?.source}
                            <p class="text-label text-muted">{t('calendar.hints.source', { name: calendar.source })}</p>
                        {/if}
                    </div>
                    {#if years.length > 1}
                        <div class="sm:w-48">
                            <SelectField id="year" label={t('calendar.fields.year')} options={years} value={year.id} onchange={(e) => pick({ tahun: e.currentTarget.value, kelas: undefined })} />
                        </div>
                    {/if}
                </div>

                {#if calendar === null}
                    <p class="text-body text-muted">{t('calendar.hints.no_calendar')}</p>
                    {#if can_manage}
                        <p class="text-body">{region_calendar ? t('calendar.hints.copy', { name: region_calendar }) : t('calendar.hints.no_region')}</p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            {#if region_calendar}
                                <PrimaryButton type="button" onclick={() => createCalendar(true)}>{t('calendar.actions.copy')}</PrimaryButton>
                            {/if}
                            <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold" onclick={() => createCalendar(false)}>
                                {t('calendar.actions.create_empty')}
                            </button>
                        </div>
                    {/if}
                {/if}
            </section>

            {#if calendar !== null && calendar.region_changes.length > 0}
                <section class="space-y-3 rounded-card border border-brand bg-surface p-4 sm:p-6" aria-labelledby="region-changes">
                    <h2 id="region-changes" class="text-title font-semibold">{t('calendar.sections.region_changes')}</h2>
                    <p class="text-body text-muted">{t('calendar.region_changes.intro')}</p>
                    <ul class="list-disc space-y-1 pl-5 text-body">
                        {#each calendar.region_changes as change, index (index)}
                            <li>{change.message}</li>
                        {/each}
                    </ul>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <PrimaryButton type="button" onclick={() => regionChanges('apply')}>{t('calendar.actions.apply')}</PrimaryButton>
                        <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold" onclick={() => regionChanges('skip')}>
                            {t('calendar.actions.skip')}
                        </button>
                    </div>
                </section>
            {/if}

            {#if formOpen}
                <section class="rounded-card border border-brand bg-surface p-4 sm:p-6" aria-labelledby="event-form-title">
                    <h2 id="event-form-title" class="mb-4 text-title font-semibold">{editing === null ? t('calendar.actions.add') : t('calendar.actions.edit')}</h2>
                    <form class="grid gap-4 sm:grid-cols-2" onsubmit={save} novalidate>
                        <div class="sm:col-span-2">
                            <TextField id="title" label={t('calendar.fields.title')} maxlength={120} bind:value={form.title} error={form.errors.title} />
                        </div>
                        <SelectField id="kind" label={t('calendar.fields.kind')} options={kinds} bind:value={form.kind} error={form.errors.kind} />
                        {#if form.kind === 'exam'}
                            <SelectField id="exam_type" label={t('calendar.fields.exam_type')} options={exam_types} bind:value={form.exam_type} error={form.errors.exam_type} />
                        {/if}
                        <TextField id="starts_on" type="date" label={t('calendar.fields.starts_on')} bind:value={form.starts_on} error={form.errors.starts_on} />
                        <TextField id="ends_on" type="date" label={t('calendar.fields.ends_on')} bind:value={form.ends_on} error={form.errors.ends_on} />
                        <SelectField
                            id="class"
                            label={t('calendar.fields.class')}
                            hint={can_manage ? t('calendar.hints.class') : undefined}
                            placeholder={can_manage ? t('calendar.whole_school') : undefined}
                            options={can_manage ? classes : editableClasses}
                            bind:value={form.class}
                            error={form.errors.class}
                        />
                        <div class="flex flex-col gap-3 sm:col-span-2 sm:flex-row">
                            <PrimaryButton type="submit" disabled={form.processing}>{t('calendar.actions.save')}</PrimaryButton>
                            <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold" onclick={() => (formOpen = false)}>
                                {t('common.actions.cancel')}
                            </button>
                        </div>
                    </form>
                </section>
            {/if}

            <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="events">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="events" class="text-title font-semibold">{t('calendar.sections.events')}</h2>
                    {#if canAdd}
                        <button type="button" class="min-h-11 rounded-input border border-line px-4 text-body font-semibold" onclick={openAdd}>{t('calendar.actions.add')}</button>
                    {/if}
                </div>
                {#if items.length === 0}
                    <p class="text-body text-muted">{t('calendar.empty')}</p>
                {/if}
                {#each months as month (month)}
                    <div>
                        <h3 class="mb-1 text-body font-semibold capitalize">{monthName(month)}</h3>
                        <ul class="divide-y divide-line">
                            {#each items.filter((item) => item.date.startsWith(month)) as item, index (item.event?.id ?? `${item.date}-${index}`)}
                                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    {#if item.holiday}
                                        {@const holiday = item.holiday}
                                        <div class="min-w-0">
                                            <p class="text-label text-muted">{day(holiday.date)} · {holiday.kind_label}{holiday.is_provisional ? ` (${t('calendar.hints.provisional')})` : ''}</p>
                                            <p class="font-semibold {holiday.is_school_day ? 'text-muted line-through' : 'text-danger'}">{holiday.name}</p>
                                            {#if holiday.is_school_day}
                                                <p class="text-label font-semibold text-brand-strong">{t('calendar.hints.joint_leave_school_day')}</p>
                                            {/if}
                                        </div>
                                        {#if can_manage && calendar !== null && holiday.kind === 'joint_leave'}
                                            <button type="button" class="min-h-11 shrink-0 rounded-input border border-line px-3 text-body font-semibold" onclick={() => toggleSchoolDay(holiday)}>
                                                {holiday.is_school_day ? t('calendar.actions.holiday_again') : t('calendar.actions.school_day')}
                                            </button>
                                        {/if}
                                    {:else if item.event}
                                        {@const event = item.event}
                                        <div class="min-w-0">
                                            <p class="text-label text-muted">{span(event.starts_on, event.ends_on)} · {event.exam_type_label ?? event.kind_label}</p>
                                            <p class="font-semibold">{event.title}</p>
                                            <p class="text-label text-muted">{event.class_name ?? t('calendar.whole_school')}</p>
                                        </div>
                                        {#if event.can_edit}
                                            <div class="flex shrink-0 gap-2">
                                                <button type="button" class="min-h-11 rounded-input border border-line px-3 text-body font-semibold" onclick={() => openEdit(event)}>{t('calendar.actions.edit')}</button>
                                                <button type="button" class="min-h-11 rounded-input border border-line px-3 text-body font-semibold text-danger" onclick={() => remove(event)}>{t('calendar.actions.delete')}</button>
                                            </div>
                                        {/if}
                                    {/if}
                                </li>
                            {/each}
                        </ul>
                    </div>
                {/each}
            </section>

            {#if classes.length > 0}
                <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="effective-days">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <h2 id="effective-days" class="text-title font-semibold">{t('calendar.sections.effective_days')}</h2>
                        <div class="sm:w-48">
                            <SelectField id="effective-class" label={t('calendar.fields.class')} options={classes} value={selected_class ?? ''} onchange={(e) => pick({ kelas: e.currentTarget.value })} />
                        </div>
                    </div>
                    {#if effective_days}
                        {#if !effective_days.from_timetable}
                            <p class="text-label text-muted">{t('calendar.hints.effective_from_default', { days: effective_days.days_per_week })}</p>
                        {/if}
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[36rem] text-left text-body">
                                <thead class="text-label text-muted">
                                    <tr>
                                        <th class="py-2 pr-2">{t('calendar.columns.month')}</th>
                                        <th class="px-2 text-right">{t('calendar.columns.school_days')}</th>
                                        <th class="px-2 text-right">{t('calendar.columns.holidays')}</th>
                                        <th class="px-2 text-right">{t('calendar.columns.assessments')}</th>
                                        <th class="px-2 text-right">{t('calendar.columns.activities')}</th>
                                        <th class="px-2 text-right">{t('calendar.columns.effective_days')}</th>
                                        <th class="pl-2 text-right">{t('calendar.columns.effective_weeks')}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line">
                                    {#each [1, 2] as semester (semester)}
                                        {#each effective_days.months.filter((row) => row.semester === semester) as row (row.key)}
                                            <tr>
                                                <td class="py-2 pr-2 capitalize">{monthName(row.month)}</td>
                                                <td class="px-2 text-right">{row.school_days}</td>
                                                <td class="px-2 text-right">{row.holidays}</td>
                                                <td class="px-2 text-right">{row.assessments}</td>
                                                <td class="px-2 text-right">{row.activities}</td>
                                                <td class="px-2 text-right font-semibold">{row.effective_days}</td>
                                                <td class="pl-2 text-right">{weeks(row.effective_weeks)}</td>
                                            </tr>
                                        {/each}
                                        {@const total = effective_days.semesters[semester]}
                                        {#if total}
                                            <tr class="bg-brand-tint font-semibold">
                                                <td class="py-2 pr-2">{t('calendar.columns.semester', { number: semester })}</td>
                                                <td class="px-2 text-right">{total.school_days}</td>
                                                <td class="px-2 text-right">{total.holidays}</td>
                                                <td class="px-2 text-right">{total.assessments}</td>
                                                <td class="px-2 text-right">{total.activities}</td>
                                                <td class="px-2 text-right">{total.effective_days}</td>
                                                <td class="pl-2 text-right">{weeks(total.effective_weeks)}</td>
                                            </tr>
                                        {/if}
                                    {/each}
                                    <tr class="font-bold">
                                        <td class="py-2 pr-2">{t('calendar.columns.year_total')}</td>
                                        <td class="px-2 text-right">{effective_days.year.school_days}</td>
                                        <td class="px-2 text-right">{effective_days.year.holidays}</td>
                                        <td class="px-2 text-right">{effective_days.year.assessments}</td>
                                        <td class="px-2 text-right">{effective_days.year.activities}</td>
                                        <td class="px-2 text-right">{effective_days.year.effective_days}</td>
                                        <td class="pl-2 text-right">{weeks(effective_days.year.effective_weeks)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    {/if}
                </section>
            {/if}
        {/if}
    </div>
</AppShell>
