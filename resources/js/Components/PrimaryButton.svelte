<script lang="ts">
    import type { Snippet } from 'svelte';
    import type { HTMLButtonAttributes } from 'svelte/elements';

    let { children, class: className = '', ...rest }: { children: Snippet; class?: string } & HTMLButtonAttributes = $props();
</script>

<button
    class="inline-flex min-h-11 items-center justify-center rounded-input bg-brand px-5 text-body font-semibold text-on-brand hover:bg-brand-strong disabled:opacity-60 {className}"
    {...rest}
>
    {@render children()}
</button>
