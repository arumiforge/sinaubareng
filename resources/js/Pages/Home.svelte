<script lang="ts">
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';
</script>

<AppShell title={t('common.app_name')}>
    <div class="rounded-card border border-line bg-surface p-6">
        <p class="text-muted">{t('common.empty.page')}</p>
    </div>
</AppShell>
