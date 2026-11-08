<script lang="ts">
    import { Link, useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import SelectField from '@/Components/SelectField.svelte';
    import TextField from '@/Components/TextField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Option {
        id: string;
        name: string;
    }

    interface ClassData {
        id: string;
        name: string;
        grade: number;
        year: string;
        homeroom_teacher: string | null;
    }

    let {
        school_class: schoolClass,
        years,
        default_year,
        teachers,
    }: { school_class: ClassData | null; years: Option[]; default_year: string | null; teachers: Option[] } = $props();

    const form = useForm(() => ({
        academic_year: default_year ?? '',
        grade: schoolClass === null ? '' : String(schoolClass.grade),
        name: schoolClass?.name ?? '',
        homeroom_teacher: schoolClass?.homeroom_teacher ?? '',
    }));

    const grades = [1, 2, 3, 4, 5, 6].map((grade) => ({ value: String(grade), label: t('classes.classes.grade_option', { grade }) }));

    function save(event: SubmitEvent): void {
        event.preventDefault();
        if (schoolClass === null) {
            form.post(route('classes.store'));
        } else {
            const current = schoolClass;
            form.transform((data) => ({ grade: data.grade, name: data.name, homeroom_teacher: data.homeroom_teacher })).put(route('classes.update', { class: current.id }));
        }
    }
</script>

<AppShell title={schoolClass === null ? t('classes.classes.titles.create') : t('classes.classes.titles.edit', { name: schoolClass.name })}>
    <div class="mx-auto max-w-2xl">
        <section class="rounded-card border border-line bg-surface p-4 sm:p-6">
            <form class="space-y-4" onsubmit={save} novalidate>
                {#if schoolClass === null}
                    <SelectField
                        id="academic_year"
                        label={t('classes.classes.fields.academic_year')}
                        options={years.map((year) => ({ value: year.id, label: year.name }))}
                        required
                        bind:value={form.academic_year}
                        error={form.errors.academic_year}
                    />
                {:else}
                    <p class="text-body text-muted">{t('classes.classes.year', { name: schoolClass.year })}</p>
                {/if}
                <SelectField
                    id="grade"
                    label={t('classes.classes.fields.grade')}
                    options={grades}
                    placeholder=""
                    required
                    bind:value={form.grade}
                    error={form.errors.grade}
                />
                <TextField
                    id="name"
                    label={t('classes.classes.fields.name')}
                    hint={t('classes.classes.hints.name')}
                    maxlength={20}
                    required
                    bind:value={form.name}
                    error={form.errors.name}
                />
                <SelectField
                    id="homeroom_teacher"
                    label={t('classes.classes.fields.homeroom_teacher')}
                    options={teachers.map((teacher) => ({ value: teacher.id, label: teacher.name }))}
                    placeholder={t('classes.classes.no_homeroom')}
                    hint={teachers.length === 0 ? t('classes.classes.hints.no_teachers') : t('classes.classes.hints.homeroom_teacher')}
                    bind:value={form.homeroom_teacher}
                    error={form.errors.homeroom_teacher}
                />
                <div class="flex flex-col gap-3 sm:flex-row">
                    <PrimaryButton type="submit" disabled={form.processing}>{t('classes.classes.actions.save')}</PrimaryButton>
                    <Link
                        href={schoolClass === null ? route('classes.index') : route('classes.show', { class: schoolClass.id })}
                        class="inline-flex min-h-11 items-center justify-center rounded-input border border-line px-5 text-body font-semibold"
                    >
                        {t('common.actions.cancel')}
                    </Link>
                </div>
            </form>
        </section>
    </div>
</AppShell>
