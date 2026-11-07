<script lang="ts">
    import { Link, useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import { t } from '@/lib/i18n';

    const form = useForm({ email: '' });

    function submit(event: SubmitEvent): void {
        event.preventDefault();
        form.post(route('password.email'));
    }
</script>

<GuestLayout title={t('auth.titles.forgot_password')}>
    <p class="mb-6 text-body text-muted">{t('auth.intro.forgot_password')}</p>

    <form class="space-y-4" onsubmit={submit} novalidate>
        <TextField
            id="email"
            type="email"
            label={t('auth.fields.email')}
            autocomplete="username"
            required
            bind:value={form.email}
            error={form.errors.email}
        />
        <PrimaryButton type="submit" class="w-full" disabled={form.processing}>{t('auth.actions.send_reset_link')}</PrimaryButton>
    </form>

    <Link href={route('login')} class="mt-6 inline-block text-body font-semibold text-brand-strong underline">{t('auth.actions.back_to_login')}</Link>
</GuestLayout>
