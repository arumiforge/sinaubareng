<script lang="ts">
    import { Link, useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import CopyClassesPreview from '@/Components/CopyClassesPreview.svelte';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Dates {
        starts_on: string;
        even_semester_starts_on: string;
        ends_on: string;
    }

    interface CopySource {
        name: string;
        classes: { grade: number; name: string; new_intake: boolean }[];
    }

    let {
        year,
        defaults,
        copy_from: copyFrom = null,
    }: { year: (Dates & { id: string; name: string }) | null; defaults: Dates | null; copy_from?: CopySource | null } = $props();

    const initial = (): Dates => year ?? defaults ?? { starts_on: '', even_semester_starts_on: '', ends_on: '' };
    const form = useForm(() => {
        const dates = initial();
        return {
            starts_on: dates.starts_on,
            even_semester_starts_on: dates.even_semester_starts_on,
            ends_on: dates.ends_on,
            // D-242: on create, copy last year's classes unless the operator unticks it.
            copy_classes: true,
            with_grade_one: true,
        };
    });

    function save(event: SubmitEvent): void {
        event.preventDefault();
        if (year === null) {
            form.post(route('academic_years.store'));
        } else {
            form.put(route('academic_years.update', { academicYear: year.id }));
        }
    }
</script>

<AppShell title={year === null ? t('classes.years.titles.create') : t('classes.years.titles.edit', { name: year.name })}>
    <div class="mx-auto max-w-2xl">
        <section class="rounded-card border border-line bg-surface p-4 sm:p-6">
            <p class="mb-4 text-body text-muted">{t('classes.years.hints.form')}</p>
            <form class="space-y-4" onsubmit={save} novalidate>
                <TextField
                    id="starts_on"
                    type="date"
                    label={t('classes.years.fields.starts_on')}
                    hint={t('classes.years.hints.starts_on')}
                    required
                    bind:value={form.starts_on}
                    error={form.errors.starts_on}
                />
                <TextField
                    id="even_semester_starts_on"
                    type="date"
                    label={t('classes.years.fields.even_semester_starts_on')}
                    hint={t('classes.years.hints.even_semester_starts_on')}
                    required
                    bind:value={form.even_semester_starts_on}
                    error={form.errors.even_semester_starts_on}
                />
                <TextField
                    id="ends_on"
                    type="date"
                    label={t('classes.years.fields.ends_on')}
                    required
                    bind:value={form.ends_on}
                    error={form.errors.ends_on}
                />
                {#if year === null && copyFrom !== null}
                    <fieldset class="space-y-3 rounded-card border border-line p-4">
                        <label class="flex min-h-11 items-center gap-3 text-body font-semibold" for="copy_classes">
                            <input id="copy_classes" type="checkbox" class="size-5 shrink-0 accent-brand" bind:checked={form.copy_classes} />
                            {t('classes.copy.field', { name: copyFrom.name })}
                        </label>
                        {#if form.copy_classes}
                            <p class="text-body text-muted">{t('classes.copy.hint')}</p>
                            <CopyClassesPreview id="with_grade_one" classes={copyFrom.classes} bind:withGradeOne={form.with_grade_one} />
                        {/if}
                    </fieldset>
                {/if}
                <div class="flex flex-col gap-3 sm:flex-row">
                    <PrimaryButton type="submit" disabled={form.processing}>{t('classes.years.actions.save')}</PrimaryButton>
                    <Link
                        href={route('academic_years.index')}
                        class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold"
                    >
                        {t('common.actions.cancel')}
                    </Link>
                </div>
            </form>
        </section>
    </div>
</AppShell>
