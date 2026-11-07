<script lang="ts">
    // Pages without a session: sign-in, password reset, invitation (D-010).
    import type { Snippet } from 'svelte';
    import Toast from '@/Components/Toast.svelte';
    import { t } from '@/lib/i18n';

    let { title, children }: { title: string; children: Snippet } = $props();
</script>

<svelte:head>
    <title>{title} · {t('common.app_name')}</title>
</svelte:head>

<main class="flex min-h-screen items-start justify-center px-4 py-10 sm:items-center">
    <div class="w-full max-w-md space-y-6">
        <p class="text-center text-title font-bold text-brand-strong">{t('common.app_name')}</p>
        <section class="rounded-card border border-line bg-surface p-6 sm:p-8">
            <h1 class="mb-4 text-heading font-semibold">{title}</h1>
            {@render children()}
        </section>
    </div>
</main>

<Toast />
