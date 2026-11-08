<script lang="ts">
    import { t } from '@/lib/i18n';

    interface PlannedClass {
        grade: number;
        name: string;
        new_intake: boolean;
    }

    // The new classes of a class copy (D-242…D-246); grade 1 for the new intake only when chosen (D-244).
    let { classes, withGradeOne = $bindable(true), id }: { classes: PlannedClass[]; withGradeOne: boolean; id: string } = $props();

    const hasGradeOne = $derived(classes.some((row) => row.new_intake));
    const shown = $derived(classes.filter((row) => withGradeOne || !row.new_intake));
</script>

<div class="space-y-3">
    {#if hasGradeOne}
        <label class="flex min-h-11 items-center gap-3 text-body" for={id}>
            <input {id} type="checkbox" class="size-5 shrink-0 accent-brand" bind:checked={withGradeOne} />
            {t('classes.copy.grade_one')}
        </label>
    {/if}
    <p class="text-label font-semibold text-muted">{t('classes.copy.preview', { count: shown.length })}</p>
    <ul class="flex flex-wrap gap-2">
        {#each shown as row (row.grade + '|' + row.name)}
            <li class="rounded-input border border-line px-2 py-1 text-body">
                {row.name}
                {#if row.new_intake}
                    <span class="text-label text-muted">({t('classes.copy.new_intake')})</span>
                {/if}
            </li>
        {/each}
    </ul>
    <p class="text-label text-muted">{t('classes.copy.rename_hint')}</p>
</div>
