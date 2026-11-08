<script lang="ts">
    // Labelled select with its hint and inline server error (SV-6, SV-10). An empty
    // string value stands for "nothing chosen".
    import type { HTMLSelectAttributes } from 'svelte/elements';

    interface Option {
        value: string;
        label: string;
    }

    let {
        id,
        label,
        options,
        placeholder = undefined,
        value = $bindable(''),
        error = undefined,
        hint = undefined,
        ...rest
    }: {
        id: string;
        label: string;
        options: Option[];
        placeholder?: string;
        value?: string;
        error?: string;
        hint?: string;
    } & Omit<HTMLSelectAttributes, 'value' | 'id'> = $props();

    const describedBy = $derived([error ? `${id}-error` : null, hint ? `${id}-hint` : null].filter(Boolean).join(' ') || undefined);
</script>

<div class="space-y-1.5">
    <label for={id} class="block text-label font-semibold">{label}</label>
    <select
        {id}
        bind:value
        class="block min-h-11 w-full rounded-input border bg-surface px-3 text-body text-text outline-none focus:border-brand focus:ring-2 focus:ring-brand/30"
        class:border-danger={error}
        class:border-line={!error}
        aria-invalid={error ? 'true' : undefined}
        aria-describedby={describedBy}
        {...rest}
    >
        {#if placeholder !== undefined}
            <option value="">{placeholder}</option>
        {/if}
        {#each options as option (option.value)}
            <option value={option.value}>{option.label}</option>
        {/each}
    </select>
    {#if hint}
        <p id="{id}-hint" class="text-label text-muted">{hint}</p>
    {/if}
    {#if error}
        <p id="{id}-error" class="text-label text-danger">{error}</p>
    {/if}
</div>
