<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Year {
        id: string;
        name: string;
        odd_semester: string;
        even_semester: string;
        is_current: boolean;
        classes_count: number;
    }

    let { years }: { years: Year[] } = $props();

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
                    </li>
                {/each}
            </ul>
        {/if}
    </div>
</AppShell>
