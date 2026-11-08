<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import CopyClassesPreview from '@/Components/CopyClassesPreview.svelte';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Year {
        id: string;
        name: string;
        odd_semester: string;
        even_semester: string;
        is_current: boolean;
        classes_count: number;
        copy_from: { name: string; classes: { grade: number; name: string; new_intake: boolean }[] } | null;
    }

    let { years }: { years: Year[] } = $props();

    // Salin kelas on an empty year (D-242…D-246): one open panel at a time.
    let copying = $state<string | null>(null);
    let withGradeOne = $state(true);
    let processing = $state(false);

    function toggleCopy(year: Year): void {
        copying = copying === year.id ? null : year.id;
        withGradeOne = true;
    }

    function copy(year: Year): void {
        router.post(
            route('academic_years.copy_classes', { academicYear: year.id }),
            { with_grade_one: withGradeOne },
            { onStart: () => (processing = true), onFinish: () => (processing = false) },
        );
    }

    function plannedCount(year: Year): number {
        return (year.copy_from?.classes ?? []).filter((row) => withGradeOne || !row.new_intake).length;
    }

    function remove(year: Year): void {
        // The confirmation names the item and the consequence (design system, writing rule 5).
        if (!confirm(t('classes.years.dialogs.delete', { name: year.name }))) return;
        router.delete(route('academic_years.destroy', { academicYear: year.id }), { preserveScroll: true });
    }
</script>

<AppShell title={t('classes.years.titles.index')}>
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex justify-end">
            <Link
                href={route('academic_years.create')}
                class="inline-flex min-h-11 items-center justify-center rounded-input bg-brand px-5 text-body font-semibold text-on-brand hover:bg-brand-strong"
            >
                {t('classes.years.actions.create')}
            </Link>
        </div>

        {#if years.length === 0}
            <p class="rounded-card border border-line bg-surface p-6 text-body text-muted">{t('classes.years.empty')}</p>
        {:else}
            <ul class="space-y-4">
                {#each years as year (year.id)}
                    <li class="rounded-card border border-line bg-surface p-4 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 space-y-1">
                                <h2 class="flex flex-wrap items-center gap-2 text-title font-semibold">
                                    {year.name}
                                    {#if year.is_current}
                                        <span class="rounded-input bg-brand-tint px-2 py-0.5 text-label font-semibold text-brand-strong">
                                            {t('classes.years.badges.current')}
                                        </span>
                                    {/if}
                                </h2>
                                <dl class="grid gap-x-6 gap-y-1 text-body sm:grid-cols-[auto_1fr]">
                                    <dt class="text-muted">{t('classes.years.columns.odd_semester')}</dt>
                                    <dd>{year.odd_semester}</dd>
                                    <dt class="text-muted">{t('classes.years.columns.even_semester')}</dt>
                                    <dd>{year.even_semester}</dd>
                                    <dt class="text-muted">{t('classes.years.columns.classes')}</dt>
                                    <dd>{t('classes.years.classes_count', { count: year.classes_count })}</dd>
                                </dl>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    href={route('classes.index', { tahun: year.id })}
                                    class="inline-flex min-h-11 items-center rounded-input border border-line px-4 text-body font-semibold"
                                >
                                    {t('classes.years.actions.classes')}
                                </Link>
                                <Link
                                    href={route('academic_years.edit', { academicYear: year.id })}
                                    class="inline-flex min-h-11 items-center rounded-input border border-line px-4 text-body font-semibold"
                                >
                                    {t('classes.years.actions.edit')}
                                </Link>
                                {#if year.copy_from !== null}
                                    <button
                                        type="button"
                                        class="min-h-11 rounded-input border border-line px-4 text-body font-semibold"
                                        aria-expanded={copying === year.id}
                                        aria-controls={'copy-' + year.id}
                                        onclick={() => toggleCopy(year)}
                                    >
                                        {t('classes.copy.actions.open')}
                                    </button>
                                {/if}
                                {#if year.classes_count === 0}
                                    <button
                                        type="button"
                                        class="min-h-11 rounded-input border border-line px-4 text-body font-semibold text-danger"
                                        onclick={() => remove(year)}
                                    >
                                        {t('classes.years.actions.delete')}
                                    </button>
                                {/if}
                            </div>
                        </div>
                        {#if year.copy_from !== null && copying === year.id}
                            <section id={'copy-' + year.id} class="mt-4 space-y-3 border-t border-line pt-4">
                                <h3 class="text-body font-semibold">{t('classes.copy.title', { name: year.copy_from.name })}</h3>
                                <p class="text-body text-muted">{t('classes.copy.hint')}</p>
                                <CopyClassesPreview id={'grade-one-' + year.id} classes={year.copy_from.classes} bind:withGradeOne />
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <PrimaryButton type="button" disabled={processing} onclick={() => copy(year)}>
                                        {t('classes.copy.actions.confirm', { count: plannedCount(year) })}
                                    </PrimaryButton>
                                    <button
                                        type="button"
                                        class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold"
                                        onclick={() => (copying = null)}
                                    >
                                        {t('common.actions.cancel')}
                                    </button>
                                </div>
                            </section>
                        {/if}
                    </li>
                {/each}
            </ul>
        {/if}
    </div>
</AppShell>
