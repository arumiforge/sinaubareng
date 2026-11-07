<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import { t } from '@/lib/i18n';

    let { token, email }: { token: string; email: string } = $props();

    const form = useForm(() => ({ token, email, password: '', password_confirmation: '' }));

    function submit(event: SubmitEvent): void {
        event.preventDefault();
        form.post(route('password.store'), { onFinish: () => form.reset('password', 'password_confirmation') });
    }
</script>

<GuestLayout title={t('auth.titles.reset_password')}>
    <p class="mb-6 text-body text-muted">{t('auth.intro.reset_password', { email })}</p>

    <form class="space-y-4" onsubmit={submit} novalidate>
        {#if form.errors.email}
            <p class="text-label text-danger" role="alert">{form.errors.email}</p>
        {/if}
        <TextField
            id="password"
            type="password"
            label={t('auth.fields.new_password')}
            hint={t('auth.hints.password_rules')}
            autocomplete="new-password"
            required
            bind:value={form.password}
            error={form.errors.password}
        />
        <TextField
            id="password_confirmation"
            type="password"
            label={t('auth.fields.password_confirmation')}
            autocomplete="new-password"
            required
            bind:value={form.password_confirmation}
            error={form.errors.password_confirmation}
        />
        <PrimaryButton type="submit" class="w-full" disabled={form.processing}>{t('auth.actions.save_password')}</PrimaryButton>
    </form>
</GuestLayout>
