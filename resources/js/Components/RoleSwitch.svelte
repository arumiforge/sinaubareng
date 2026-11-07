<script lang="ts">
    // Role switch: picks the school and role the user works as. The server keeps the
    // choice in the session (SetSchoolContext).
    import { page, router } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import { t } from '@/lib/i18n';

    const memberships = $derived(page.props.auth?.memberships ?? []);
    const current = $derived(memberships.find((membership) => membership.current));

    function change(event: Event): void {
        const membership = (event.currentTarget as HTMLSelectElement).value;
        router.put(route('role.update'), { membership });
    }
</script>

{#if memberships.length > 1}
    <label class="flex items-center gap-2 text-label">
        <span class="sr-only">{t('nav.switch_role')}</span>
        <select
            class="min-h-11 max-w-56 rounded-input border border-line bg-surface px-2 text-label font-semibold sm:max-w-72"
            value={current?.id}
            onchange={change}
        >
            {#each memberships as membership (membership.id)}
                <option value={membership.id}>{t('nav.current_role', { role: membership.role_label, school: membership.school })}</option>
            {/each}
        </select>
    </label>
{:else if current}
    <p class="hidden text-label text-muted sm:block">{t('nav.current_role', { role: current.role_label, school: current.school })}</p>
{/if}
