<script lang="ts">
    import { Link, useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import { t } from '@/lib/i18n';

    let { status = null }: { status?: string | null } = $props();

    const form = useForm({ email: '', password: '', remember: false });

    function submit(event: SubmitEvent): void {
        event.preventDefault();
        form.post(route('login.store'), { onFinish: () => form.reset('password') });
    }
</script>

<GuestLayout title={t('auth.titles.login')}>
    {#if status}
        <p role="status" class="mb-4 rounded-input bg-brand-tint px-3 py-2 text-body">{status}</p>
    {/if}
    <p class="mb-6 text-body text-muted">{t('auth.intro.login')}</p>

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
        <TextField
            id="password"
            type="password"
            label={t('auth.fields.password')}
            autocomplete="current-password"
            required
            bind:value={form.password}
            error={form.errors.password}
        />
        <label class="flex min-h-11 items-center gap-3 text-body">
            <input type="checkbox" class="size-5 accent-brand" bind:checked={form.remember} />
            {t('auth.fields.remember')}
        </label>
        <PrimaryButton type="submit" class="w-full" disabled={form.processing}>{t('auth.actions.login')}</PrimaryButton>
    </form>

    <div class="mt-6 space-y-2 text-body">
        <Link href={route('password.request')} class="font-semibold text-brand-strong underline">{t('auth.actions.forgot_password')}</Link>
        <p class="text-muted">{t('auth.intro.invite_only')}</p>
    </div>
</GuestLayout>
