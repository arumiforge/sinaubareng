<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import { t } from '@/lib/i18n';

    interface InvitationProps {
        id: string;
        token: string;
        email: string;
        school: string;
        role: string;
        has_account: boolean;
    }

    let { invitation, signed_in_as = null }: { invitation: InvitationProps; signed_in_as?: string | null } = $props();

    const form = useForm(() => ({ token: invitation.token, name: '', password: '', password_confirmation: '' }));

    function submit(event: SubmitEvent): void {
        event.preventDefault();
        form.post(route('invitations.accept.store', { invitation: invitation.id }), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    }
</script>

<GuestLayout title={t('auth.titles.accept_invitation')}>
    <p class="mb-2 text-lead font-semibold">{t('auth.invitation.intro', { school: invitation.school, role: invitation.role })}</p>
    <p class="mb-6 text-body text-muted">
        {invitation.has_account
            ? t('auth.invitation.existing_account', { email: invitation.email })
            : t('auth.invitation.new_account', { email: invitation.email })}
    </p>
    {#if signed_in_as}
        <p role="note" class="mb-4 rounded-input border border-warn px-3 py-2 text-body">{t('auth.invitation.signed_in_as', { email: signed_in_as })}</p>
    {/if}

    <form class="space-y-4" onsubmit={submit} novalidate>
        {#if form.errors.token}
            <p class="text-label text-danger" role="alert">{form.errors.token}</p>
        {/if}
        {#if !invitation.has_account}
            <TextField
                id="name"
                label={t('auth.fields.name')}
                hint={t('auth.hints.name')}
                autocomplete="name"
                required
                bind:value={form.name}
                error={form.errors.name}
            />
        {/if}
        <TextField
            id="password"
            type="password"
            label={t('auth.fields.password')}
            hint={invitation.has_account ? undefined : t('auth.hints.password_rules')}
            autocomplete={invitation.has_account ? 'current-password' : 'new-password'}
            required
            bind:value={form.password}
            error={form.errors.password}
        />
        {#if !invitation.has_account}
            <TextField
                id="password_confirmation"
                type="password"
                label={t('auth.fields.password_confirmation')}
                autocomplete="new-password"
                required
                bind:value={form.password_confirmation}
                error={form.errors.password_confirmation}
            />
        {/if}
        <PrimaryButton type="submit" class="w-full" disabled={form.processing}>{t('auth.actions.accept_invitation')}</PrimaryButton>
    </form>
</GuestLayout>
