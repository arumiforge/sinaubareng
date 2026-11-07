<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import GuestLayout from '@/Layouts/GuestLayout.svelte';
    import { t } from '@/lib/i18n';
</script>

<GuestLayout title={t('auth.titles.invitation_unavailable')}>
    <p class="text-body">{t('auth.invitation.unavailable')}</p>
    <Link href={route('login')} class="mt-6 inline-block text-body font-semibold text-brand-strong underline">{t('auth.actions.back_to_login')}</Link>
</GuestLayout>
