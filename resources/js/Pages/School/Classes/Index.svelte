<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import SelectField from '@/Components/SelectField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface YearOption {
        id: string;
        name: string;
    }

    interface ClassRow {
        id: string;
        name: string;
        grade: number;
        homeroom_teacher: string | null;
        assigned_count: number;
        subjects_count: number;
    }

    let { years, selected_year, classes }: { years: YearOption[]; selected_year: string | null; classes: ClassRow[] } = $props();

    let year = $derived(selected_year ?? '');

    const grades = $derived([...new Set(classes.map((row) => row.grade))]);

    function chooseYear(): void {
        router.get(route('classes.index'), { tahun: year }, { preserveState: true });
    }
</script>

<AppShell title={t('classes.classes.titles.index')}>
    <div class="mx-auto max-w-4xl space-y-6">
        {#if years.length === 0}
            <section class="space-y-4 rounded-card border border-line bg-surface p-6">
                <p class="text-body text-muted">{t('classes.classes.empty.no_year')}</p>
                <Link
                    href={route('academic_years.create')}
                    class="inline-flex min-h-11 items-center justify-center rounded-input bg-brand px-5 text-body font-semibold text-on-brand hover:bg-brand-strong"
                >
                    {t('classes.years.actions.create')}
                </Link>
            </section>
        {:else}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="sm:w-64">
                    <SelectField
                        id="year"
                        label={t('classes.classes.fields.academic_year')}
                        options={years.map((option) => ({ value: option.id, label: option.name }))}
                        bind:value={year}
                        onchange={chooseYear}
                    />
                </div>
                <Link
                    href={route('classes.create', { tahun: year })}
                    class="inline-flex min-h-11 items-center justify-center rounded-input bg-brand px-5 text-body font-semibold text-on-brand hover:bg-brand-strong"
                >
                    {t('classes.classes.actions.create')}
                </Link>
            </div>

            {#if classes.length === 0}
                <p class="rounded-card border border-line bg-surface p-6 text-body text-muted">{t('classes.classes.empty.classes')}</p>
            {:else}
                {#each grades as grade (grade)}
                    <section class="rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="grade-{grade}">
                        <h2 id="grade-{grade}" class="mb-2 text-title font-semibold">{t('classes.classes.grade_option', { grade })}</h2>
                        <ul class="divide-y divide-line">
                            {#each classes.filter((row) => row.grade === grade) as row (row.id)}
                                <li>
                                    <Link
                                        href={route('classes.show', { class: row.id })}
                                        class="flex min-h-11 flex-col gap-1 py-3 hover:text-brand-strong sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <span class="font-semibold">{row.name}</span>
                                        <span class="text-label text-muted sm:text-right">
                                            {row.homeroom_teacher ?? t('classes.classes.no_homeroom')}
                                            · {t('classes.classes.assigned', { assigned: row.assigned_count, total: row.subjects_count })}
                                        </span>
                                    </Link>
                                </li>
                            {/each}
                        </ul>
                    </section>
                {/each}
            {/if}
        {/if}
    </div>
</AppShell>
