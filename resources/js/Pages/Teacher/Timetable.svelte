<script lang="ts">
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Lesson {
        id: string;
        weekday: number;
        starts_at: string;
        ends_at: string;
        class_name: string;
        subject_name: string | null;
        jp: number;
    }

    interface Row {
        id: string;
        weekday: number;
        period_no: number;
        starts_at: string;
        ends_at: string;
        kind: string;
        kind_label: string;
        label: string | null;
        subject_name: string | null;
        teacher_name: string | null;
        jp: number;
    }

    let {
        year,
        lessons,
        total_jp,
        homeroom_classes,
    }: { year: string | null; lessons: Lesson[]; total_jp: number; homeroom_classes: { id: string; name: string; rows: Row[] }[] } = $props();

    const days = [1, 2, 3, 4, 5, 6];
    const dayName = (day: number): string => t(`timetable.days.${day}`);
</script>

<AppShell title={t('timetable.titles.mine')}>
    <div class="mx-auto max-w-5xl space-y-6">
        {#if year === null}
            <p class="rounded-card border border-line bg-surface p-6 text-body text-muted">{t('timetable.no_year')}</p>
        {:else}
            <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby="my-lessons">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 id="my-lessons" class="text-title font-semibold">{t('timetable.my_lessons')}</h2>
                    <p class="text-body text-muted">{t('timetable.year', { name: year })} · {t('timetable.total_jp', { jp: total_jp })}</p>
                </div>
                {#if lessons.length === 0}
                    <p class="text-body text-muted">{t('timetable.empty_mine')}</p>
                {:else}
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {#each days as day (day)}
                            {@const dayLessons = lessons.filter((lesson) => lesson.weekday === day)}
                            {#if dayLessons.length > 0}
                                <div>
                                    <h3 class="mb-2 font-semibold">{dayName(day)}</h3>
                                    <ul class="space-y-2">
                                        {#each dayLessons as lesson (lesson.id)}
                                            <li class="rounded-input border border-line px-3 py-2 text-body">
                                                <span class="block text-label text-muted">{lesson.starts_at}–{lesson.ends_at} · {t('timetable.jp_value', { jp: lesson.jp })}</span>
                                                <span class="block font-semibold">{lesson.subject_name}</span>
                                                <span class="block text-label">{t('timetable.columns.class')} {lesson.class_name}</span>
                                            </li>
                                        {/each}
                                    </ul>
                                </div>
                            {/if}
                        {/each}
                    </div>
                {/if}
            </section>

            {#each homeroom_classes as schoolClass (schoolClass.id)}
                <section class="space-y-4 rounded-card border border-line bg-surface p-4 sm:p-6" aria-labelledby={'homeroom-' + schoolClass.id}>
                    <h2 id={'homeroom-' + schoolClass.id} class="text-title font-semibold">{t('timetable.homeroom', { name: schoolClass.name })}</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {#each days as day (day)}
                            {@const dayRows = schoolClass.rows.filter((row) => row.weekday === day)}
                            {#if dayRows.length > 0}
                                <div>
                                    <h3 class="mb-2 font-semibold">{dayName(day)}</h3>
                                    <ol class="divide-y divide-line text-body">
                                        {#each dayRows as row (row.id)}
                                            <li class="py-2">
                                                <span class="block text-label text-muted">{row.period_no}. {row.starts_at}–{row.ends_at}</span>
                                                {#if row.kind === 'lesson'}
                                                    <span class="block font-semibold">{row.subject_name}</span>
                                                    <span class="block text-label">{row.teacher_name ?? t('timetable.no_teacher')}</span>
                                                {:else}
                                                    <span class="block text-muted">{row.label ?? row.kind_label}</span>
                                                {/if}
                                            </li>
                                        {/each}
                                    </ol>
                                </div>
                            {/if}
                        {/each}
                    </div>
                </section>
            {/each}
        {/if}
    </div>
</AppShell>
