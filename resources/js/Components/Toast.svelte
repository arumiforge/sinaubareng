<script lang="ts">
    // One-line success message that hides by itself (design system, writing rule 6).
    import { page } from '@inertiajs/svelte';

    let visible = $state<string | null>(null);

    $effect(() => {
        const message = page.props.flash?.toast ?? null;
        visible = message;
        if (message === null) return;
        const timer = setTimeout(() => (visible = null), 4000);
        return () => clearTimeout(timer);
    });
</script>

{#if visible}
    <div
        role="status"
        class="fixed inset-x-4 bottom-4 z-50 mx-auto max-w-md rounded-card border border-line bg-surface px-4 py-3 text-body shadow-lg"
    >
        {visible}
    </div>
{/if}
