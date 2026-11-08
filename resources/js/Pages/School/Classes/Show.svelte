<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Option {
        id: string;
        name: string;
    }

    interface SubjectRow {
        code: string;
        name: string;
        teacher: string | null;
        teacher_name: string | null;
        teacher_is_active: boolean;
        default_teacher: string;
        jp_per_week: number | null;
        is_optional: boolean;
        in_report: boolean;
        is_offered: boolean;
    }

    interface ClassData {
        id: string;
        name: string;
        grade: number;
        year: string;
        year_id: string;
        homeroom_teacher: string | null;
    }

    let {
        school_class,
        subjects,
        teachers,
    }: { school_class: ClassData; subjects: SubjectRow[]; teachers: Option[] } = $props();

    let saving = $state<string | null>(null);

    // A refused change (e.g. a timetable clash, D-248) comes back as a validation error.
    const assignError = $derived(page.props.errors?.teacher ?? page.props.errors?.subject);

    function assign(subject: SubjectRow, teacher: string): void {
        saving = subject.code;
        router.put(
            route('classes.assignments.update', { class: school_class.id }),
            { subject: subject.code, teacher: teacher === '' ? null : teacher },
            { preserveScroll: true, onFinish: () => (saving = null) },
        );
    }

    function remove(): void {
        if (!confirm(t('classes.classes.dialogs.delete', { name: school_class.name }))) return;
        router.delete(route('classes.destroy', { class: school_class.id }));
    }
</script>

<AppShell title={t('classes.classes.titles.show', { name: school_class.name })}>
    <div class="mx-auto max-w-4xl space-y-6">
        <Link href={route('classes.index', { tahun: school_class.year_id })} class="inline-flex min-h-11 items-center text-body font-semibold text-brand-strong">
            ← {t('classes.classes.actions.back')}
        </Link>

        {#if assignError}
            <p class="rounded-card border border-danger bg-surface p-4 text-body text-danger" role="alert">{assignError}</p>
        {/if}

        <section class="flex flex-col gap-4 rounded-card border border-line bg-surface p-4 sm:flex-row sm:items-start sm:justify-between sm:p-6">
            <dl class="grid gap-x-6 gap-y-1 text-body sm:grid-cols-[auto_1fr]">
                <dt class="text-muted">{t('classes.classes.fields.academic_year')}</dt>
                <dd>{school_class.year}</dd>
                <dt class="text-muted">{t('classes.classes.fields.grade')}</dt>
                <dd>{t('classes.classes.grade_option', { grade: school_class.grade })}</dd>
                <dt class="text-muted">{t('classes.classes.fields.homeroom_teacher')}</dt>
                <dd>{school_class.homeroom_teacher ?? t('classes.classes.no_homeroom')}</dd>
            </dl>
            <div class="flex flex-wrap gap-2">
                <Link
                    href={route('classes.timetable.show', { class: school_class.id })}
                    class="inline-flex min-h-11 items-center rounded-input border border-line px-4 text-body font-semibold"
                >
                    {t('classes.classes.actions.timetable')}
                </Link>
                <Link
                    href={route('classes.edit', { class: school_class.id })}
                    class="inline-flex min-h-11 items-center rounded-input border border-line px-4 text-body font-semibold"
                >
                    {t('classes.classes.actions.edit')}
                </Link>
                <button type="button" class="min-h-11 rounded-input border border-line px-4 text-body font-semibold text-danger" onclick={remove}>
                    {t('classes.classes.actions.delete')}
                </button>
            </div>
        </section>

        <section class="rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="assignments-title">
            <h2 id="assignments-title" class="mb-1 text-title font-semibold">{t('classes.assignments.title')}</h2>
            <p class="mb-4 text-body text-muted">{t('classes.assignments.hint')}</p>
            {#if teachers.length === 0}
                <p class="mb-4 text-body text-warn">{t('classes.classes.hints.no_teachers')}</p>
            {/if}
            {#if subjects.length === 0}
                <p class="text-body text-muted">{t('classes.assignments.empty')}</p>
            {:else}
                <!-- One row per subject; stacks into a card below 640 px (SV-9). -->
                <ul class="divide-y divide-line">
                    {#each subjects as subject (subject.code)}
                        <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                            <div class="min-w-0">
                                <p class="font-semibold">{subject.name}</p>
                                <p class="flex flex-wrap gap-x-2 text-label text-muted">
                                    {#if subject.jp_per_week !== null}
                                        <span>{t('classes.assignments.jp_per_week', { jp: subject.jp_per_week })}</span>
                                    {/if}
                                    <span>{t('classes.assignments.default', { teacher: subject.default_teacher })}</span>
                                    {#if subject.is_optional}
                                        <span>· {t('classes.assignments.badges.optional')}</span>
                                    {/if}
                                    {#if subject.is_offered && !subject.in_report}
                                        <span>· {t('classes.assignments.badges.not_in_report')}</span>
                                    {/if}
                                    {#if !subject.is_offered}
                                        <span class="text-warn">· {t('classes.assignments.badges.not_offered')}</span>
                                    {/if}
                                </p>
                                {#if subject.teacher_name !== null && !subject.teacher_is_active}
                                    <p class="text-label text-warn">{t('classes.assignments.inactive', { name: subject.teacher_name })}</p>
                                {/if}
                            </div>
                            <div class="sm:w-72">
                                {#if subject.is_offered}
                                    <label class="sr-only" for="teacher-{subject.code}">{t('classes.assignments.columns.teacher')} {subject.name}</label>
                                    <select
                                        id="teacher-{subject.code}"
                                        class="block min-h-11 w-full rounded-input border border-line bg-surface px-3 text-body text-text outline-none focus:border-brand focus:ring-2 focus:ring-brand/30"
                                        value={subject.teacher ?? ''}
                                        disabled={saving === subject.code}
                                        onchange={(event) => assign(subject, event.currentTarget.value)}
                                    >
                                        <option value="">{t('classes.assignments.no_teacher')}</option>
                                        {#each teachers as teacher (teacher.id)}
                                            <option value={teacher.id}>{teacher.name}</option>
                                        {/each}
                                    </select>
                                {:else}
                                    <button
                                        type="button"
                                        class="min-h-11 w-full rounded-input border border-line px-4 text-body font-semibold"
                                        disabled={saving === subject.code}
                                        onclick={() => assign(subject, '')}
                                    >
                                        {t('classes.assignments.actions.clear')}
                                    </button>
                                {/if}
                            </div>
                        </li>
                    {/each}
                </ul>
            {/if}
        </section>
    </div>
</AppShell>
