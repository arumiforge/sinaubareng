<script lang="ts">
    // School app shell, ported from TailAdmin's layout (sidebar, header, content), drawn with our tokens.
    import type { Snippet } from 'svelte';
    import { route } from 'ziggy-js';
    import CalendarDays from '@lucide/svelte/icons/calendar-days';
    import ClipboardCheck from '@lucide/svelte/icons/clipboard-check';
    import Menu from '@lucide/svelte/icons/menu';
    import Presentation from '@lucide/svelte/icons/presentation';
    import SquarePen from '@lucide/svelte/icons/square-pen';
    import Users from '@lucide/svelte/icons/users';
    import X from '@lucide/svelte/icons/x';
    import { t } from '@/lib/i18n';

    let { title, children }: { title: string; children: Snippet } = $props();

    let sidebarOpen = $state(false);

    // Menu from the product concept; each item gets its route when its slice is built.
    const items = [
        { label: t('nav.items.today'), icon: CalendarDays },
        { label: t('nav.items.in_class'), icon: Presentation },
        { label: t('nav.items.create'), icon: SquarePen },
        { label: t('nav.items.assessments'), icon: ClipboardCheck },
        { label: t('nav.items.my_classes'), icon: Users },
    ];
</script>

<div class="flex min-h-screen">
    {#if sidebarOpen}
        <button
            type="button"
            class="fixed inset-0 z-30 bg-text/40 lg:hidden"
            aria-label={t('common.actions.close_menu')}
            onclick={() => (sidebarOpen = false)}
        ></button>
    {/if}

    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-line bg-surface transition-transform lg:static lg:translate-x-0"
        class:-translate-x-full={!sidebarOpen}
    >
        <div class="flex h-16 items-center justify-between px-6">
            <a href={route('home')} class="text-title font-bold text-brand-strong">{t('common.app_name')}</a>
            <button
                type="button"
                class="flex size-11 items-center justify-center rounded-input text-muted lg:hidden"
                aria-label={t('common.actions.close_menu')}
                onclick={() => (sidebarOpen = false)}
            >
                <X size={20} strokeWidth={1.5} />
            </button>
        </div>

        <nav class="flex-1 px-4 py-2" aria-label={t('nav.label')}>
            <ul class="space-y-1">
                {#each items as item (item.label)}
                    <li>
                        <span class="flex min-h-11 items-center gap-3 rounded-input px-3 text-body font-semibold text-muted">
                            <item.icon size={20} strokeWidth={1.5} />
                            {item.label}
                        </span>
                    </li>
                {/each}
            </ul>
        </nav>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-surface px-4 lg:px-8">
            <button
                type="button"
                class="flex size-11 items-center justify-center rounded-input text-muted lg:hidden"
                aria-label={t('common.actions.open_menu')}
                aria-expanded={sidebarOpen}
                onclick={() => (sidebarOpen = true)}
            >
                <Menu size={20} strokeWidth={1.5} />
            </button>
            <h1 class="text-lead font-semibold">{title}</h1>
        </header>

        <main class="flex-1 p-4 lg:p-8">
            {@render children()}
        </main>
    </div>
</div>
