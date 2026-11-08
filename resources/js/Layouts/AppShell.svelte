<script lang="ts">
    // School app shell, ported from TailAdmin's layout (sidebar, header, content), drawn with our tokens.
    import type { Snippet } from 'svelte';
    import { Link, page } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import CalendarDays from '@lucide/svelte/icons/calendar-days';
    import CalendarRange from '@lucide/svelte/icons/calendar-range';
    import CircleUser from '@lucide/svelte/icons/circle-user';
    import ClipboardCheck from '@lucide/svelte/icons/clipboard-check';
    import GraduationCap from '@lucide/svelte/icons/graduation-cap';
    import LogOut from '@lucide/svelte/icons/log-out';
    import Menu from '@lucide/svelte/icons/menu';
    import Presentation from '@lucide/svelte/icons/presentation';
    import School from '@lucide/svelte/icons/school';
    import SquarePen from '@lucide/svelte/icons/square-pen';
    import Users from '@lucide/svelte/icons/users';
    import X from '@lucide/svelte/icons/x';
    import RoleSwitch from '@/Components/RoleSwitch.svelte';
    import Toast from '@/Components/Toast.svelte';
    import { t } from '@/lib/i18n';

    let { title, children }: { title: string; children: Snippet } = $props();

    let sidebarOpen = $state(false);

    const role = $derived(page.props.auth?.memberships.find((membership) => membership.current)?.role);

    // Menu from the product concept; each item gets its route when its slice is built.
    const items = [
        { label: t('nav.items.today'), icon: CalendarDays },
        { label: t('nav.items.in_class'), icon: Presentation },
        { label: t('nav.items.create'), icon: SquarePen },
        { label: t('nav.items.assessments'), icon: ClipboardCheck },
        { label: t('nav.items.my_classes'), icon: Users },
    ];

    // Operator portal (product concept: Tahun Ajaran, Kelas, Guru, …).
    const operatorItems = [
        { label: t('nav.items.academic_years'), icon: CalendarRange, route: 'academic_years.index' },
        { label: t('nav.items.classes'), icon: School, route: 'classes.index' },
        { label: t('nav.items.teachers'), icon: GraduationCap, route: 'teachers.index' },
    ] as const;
</script>

<svelte:head>
    <title>{title} · {t('common.app_name')}</title>
</svelte:head>

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
                {#if role === 'operator'}
                    {#each operatorItems as item (item.route)}
                        <li>
                            <Link
                                href={route(item.route)}
                                class="flex min-h-11 items-center gap-3 rounded-input px-3 text-body font-semibold text-text hover:bg-brand-tint"
                            >
                                <item.icon size={20} strokeWidth={1.5} />
                                {item.label}
                            </Link>
                        </li>
                    {/each}
                {/if}
            </ul>
        </nav>

        <div class="space-y-1 border-t border-line px-4 py-3">
            <Link
                href={route('account.edit')}
                class="flex min-h-11 items-center gap-3 rounded-input px-3 text-body font-semibold text-text hover:bg-brand-tint"
            >
                <CircleUser size={20} strokeWidth={1.5} />
                {t('nav.account')}
            </Link>
            <Link
                href={route('logout')}
                method="post"
                as="button"
                class="flex min-h-11 w-full items-center gap-3 rounded-input px-3 text-body font-semibold text-text hover:bg-brand-tint"
            >
                <LogOut size={20} strokeWidth={1.5} />
                {t('nav.sign_out')}
            </Link>
        </div>
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
            <h1 class="min-w-0 flex-1 truncate text-lead font-semibold">{title}</h1>
            <RoleSwitch />
        </header>

        <main class="flex-1 p-4 lg:p-8">
            {@render children()}
        </main>
    </div>
</div>

<Toast />
