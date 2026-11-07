<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    interface Teacher {
        id: string;
        name: string;
        email: string;
        status: string;
        is_active: boolean;
    }

    interface PendingInvitation {
        id: string;
        email: string;
        status: 'pending' | 'expired';
        status_label: string;
        expires_at: string;
    }

    let { teachers, invitations }: { teachers: Teacher[]; invitations: PendingInvitation[] } = $props();

    const form = useForm({ email: '' });

    function invite(event: SubmitEvent): void {
        event.preventDefault();
        form.post(route('invitations.store'), { preserveScroll: true, onSuccess: () => form.reset() });
    }

    function resend(invitation: PendingInvitation): void {
        router.post(route('invitations.resend', { invitation: invitation.id }), {}, { preserveScroll: true });
    }

    function cancel(invitation: PendingInvitation): void {
        // The confirmation names the item and the consequence (design system, writing rule 5).
        if (!confirm(t('school.dialogs.cancel_invitation.body', { email: invitation.email }))) return;
        router.delete(route('invitations.destroy', { invitation: invitation.id }), { preserveScroll: true });
    }
</script>

<AppShell title={t('school.titles.teachers')}>
    <div class="mx-auto max-w-4xl space-y-6">
        <section class="rounded-card border border-line bg-surface p-6" aria-labelledby="invite-title">
            <h2 id="invite-title" class="mb-1 text-title font-semibold">{t('school.titles.invite')}</h2>
            <p class="mb-4 text-body text-muted">{t('school.hints.invite')}</p>
            <form class="flex flex-col gap-3 sm:flex-row sm:items-start" onsubmit={invite} novalidate>
                <div class="flex-1">
                    <TextField
                        id="email"
                        type="email"
                        label={t('school.fields.email')}
                        autocomplete="off"
                        required
                        bind:value={form.email}
                        error={form.errors.email}
                    />
                </div>
                <PrimaryButton type="submit" class="sm:mt-7" disabled={form.processing}>{t('school.actions.invite')}</PrimaryButton>
            </form>
        </section>

        <section class="rounded-card border border-line bg-surface p-6" aria-labelledby="invitations-title">
            <h2 id="invitations-title" class="mb-4 text-title font-semibold">{t('school.titles.invitations')}</h2>
            {#if invitations.length === 0}
                <p class="text-body text-muted">{t('school.empty.invitations')}</p>
            {:else}
                <ul class="divide-y divide-line">
                    {#each invitations as invitation (invitation.id)}
                        <li class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{invitation.email}</p>
                                <p class="text-label text-muted">
                                    <span class:text-warn={invitation.status === 'expired'}>{invitation.status_label}</span>
                                    · {t('school.columns.expires_at')} {invitation.expires_at}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    class="min-h-11 rounded-input border border-line px-4 text-body font-semibold"
                                    onclick={() => resend(invitation)}
                                >
                                    {t('school.actions.resend')}
                                </button>
                                <button
                                    type="button"
                                    class="min-h-11 rounded-input border border-line px-4 text-body font-semibold text-danger"
                                    onclick={() => cancel(invitation)}
                                >
                                    {t('school.actions.cancel_invitation')}
                                </button>
                            </div>
                        </li>
                    {/each}
                </ul>
            {/if}
        </section>

        <section class="rounded-card border border-line bg-surface p-6" aria-labelledby="teachers-title">
            <h2 id="teachers-title" class="mb-4 text-title font-semibold">{t('school.titles.teachers')}</h2>
            {#if teachers.length === 0}
                <p class="text-body text-muted">{t('school.empty.teachers')}</p>
            {:else}
                <!-- A table on wide screens, a card list below 640 px (SV-9). -->
                <table class="hidden w-full text-left text-body sm:table">
                    <thead class="text-label text-muted">
                        <tr>
                            <th class="py-2 font-semibold">{t('school.columns.name')}</th>
                            <th class="py-2 font-semibold">{t('school.columns.email')}</th>
                            <th class="py-2 font-semibold">{t('school.columns.status')}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        {#each teachers as teacher (teacher.id)}
                            <tr>
                                <td class="py-2 font-semibold">{teacher.name}</td>
                                <td class="py-2">{teacher.email}</td>
                                <td class="py-2" class:text-muted={!teacher.is_active}>{teacher.status}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
                <ul class="divide-y divide-line sm:hidden">
                    {#each teachers as teacher (teacher.id)}
                        <li class="py-3">
                            <p class="font-semibold">{teacher.name}</p>
                            <p class="text-label text-muted">{teacher.email} · {teacher.status}</p>
                        </li>
                    {/each}
                </ul>
            {/if}
        </section>
    </div>
</AppShell>
