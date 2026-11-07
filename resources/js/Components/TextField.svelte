<script lang="ts">
    // Labelled input with its hint and inline server error (SV-6, SV-10).
    import type { HTMLInputAttributes } from 'svelte/elements';

    let {
        id,
        label,
        value = $bindable(''),
        error = undefined,
        hint = undefined,
        ...rest
    }: {
        id: string;
        label: string;
        value?: string | null;
        error?: string;
        hint?: string;
    } & Omit<HTMLInputAttributes, 'value' | 'id'> = $props();

    const describedBy = $derived([error ? `${id}-error` : null, hint ? `${id}-hint` : null].filter(Boolean).join(' ') || undefined);
</script>

<div class="space-y-1.5">
    <label for={id} class="block text-label font-semibold">{label}</label>
    <input
        {id}
        bind:value
        class="block min-h-11 w-full rounded-input border bg-surface px-3 text-body text-text outline-none focus:border-brand focus:ring-2 focus:ring-brand/30"
        class:border-danger={error}
        class:border-line={!error}
        aria-invalid={error ? 'true' : undefined}
        aria-describedby={describedBy}
        {...rest}
    />
    {#if hint}
        <p id="{id}-hint" class="text-label text-muted">{hint}</p>
    {/if}
    {#if error}
        <p id="{id}-error" class="text-label text-danger">{error}</p>
    {/if}
</div>
