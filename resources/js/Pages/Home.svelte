<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    const hasRole = $derived((page.props.auth?.memberships.length ?? 0) > 0);
</script>

<AppShell title={t('common.app_name')}>
    <div class="rounded-card border border-line bg-surface p-6">
        <p class="text-muted">{hasRole ? t('common.empty.page') : t('common.empty.no_role')}</p>
    </div>
</AppShell>
