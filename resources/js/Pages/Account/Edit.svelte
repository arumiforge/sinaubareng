<script lang="ts">
    import { page, router, useForm } from '@inertiajs/svelte';
    import { route } from 'ziggy-js';
    import PrimaryButton from '@/Components/PrimaryButton.svelte';
    import TextField from '@/Components/TextField.svelte';
    import AppShell from '@/Layouts/AppShell.svelte';
    import { t } from '@/lib/i18n';

    let { profile }: { profile: { name: string; email: string; nip: string | null } } = $props();

    const memberships = $derived(page.props.auth?.memberships ?? []);

    const profileForm = useForm(() => ({ name: profile.name, nip: profile.nip ?? '' }));
    const passwordForm = useForm({ current_password: '', password: '', password_confirmation: '' });

    function saveProfile(event: SubmitEvent): void {
        event.preventDefault();
        profileForm.put(route('account.update'), { preserveScroll: true });
    }

    function savePassword(event: SubmitEvent): void {
        event.preventDefault();
        passwordForm.put(route('account.password.update'), {
            preserveScroll: true,
            onFinish: () => passwordForm.reset(),
        });
    }

    function switchTo(membership: string): void {
        router.put(route('role.update'), { membership });
    }
</script>

<AppShell title={t('account.titles.edit')}>
    <div class="mx-auto max-w-2xl space-y-6">
        <section class="rounded-card border border-line bg-surface p-6" aria-labelledby="profile-title">
            <h2 id="profile-title" class="mb-4 text-title font-semibold">{t('account.titles.profile')}</h2>
            <form class="space-y-4" onsubmit={saveProfile} novalidate>
                <TextField
                    id="name"
                    label={t('account.fields.name')}
                    autocomplete="name"
                    required
                    bind:value={profileForm.name}
                    error={profileForm.errors.name}
                />
                <TextField
                    id="email"
                    type="email"
                    label={t('account.fields.email')}
                    hint={t('account.hints.email')}
                    value={profile.email}
                    readonly
                />
                <TextField
                    id="nip"
                    label={t('account.fields.nip')}
                    hint={t('account.hints.nip')}
                    inputmode="numeric"
                    maxlength={18}
                    bind:value={profileForm.nip}
                    error={profileForm.errors.nip}
                />
                <PrimaryButton type="submit" disabled={profileForm.processing}>{t('account.actions.save_profile')}</PrimaryButton>
            </form>
        </section>

        <section class="rounded-card border border-line bg-surface p-6" aria-labelledby="password-title">
            <h2 id="password-title" class="mb-4 text-title font-semibold">{t('account.titles.password')}</h2>
            <form class="space-y-4" onsubmit={savePassword} novalidate>
                <TextField
                    id="current_password"
                    type="password"
                    label={t('account.fields.current_password')}
                    autocomplete="current-password"
                    required
                    bind:value={passwordForm.current_password}
                    error={passwordForm.errors.current_password}
                />
                <TextField
                    id="password"
                    type="password"
                    label={t('account.fields.password')}
                    hint={t('account.hints.password')}
                    autocomplete="new-password"
                    required
                    bind:value={passwordForm.password}
                    error={passwordForm.errors.password}
                />
                <TextField
                    id="password_confirmation"
                    type="password"
                    label={t('account.fields.password_confirmation')}
                    autocomplete="new-password"
                    required
                    bind:value={passwordForm.password_confirmation}
                    error={passwordForm.errors.password_confirmation}
                />
                <PrimaryButton type="submit" disabled={passwordForm.processing}>{t('account.actions.save_password')}</PrimaryButton>
            </form>
        </section>

        <section class="rounded-card border border-line bg-surface p-6" aria-labelledby="roles-title">
            <h2 id="roles-title" class="mb-1 text-title font-semibold">{t('account.titles.roles')}</h2>
            {#if memberships.length === 0}
                <p class="text-body text-muted">{t('account.empty.roles')}</p>
            {:else}
                <p class="mb-4 text-body text-muted">{t('account.hints.roles')}</p>
                <ul class="space-y-2">
                    {#each memberships as membership (membership.id)}
                        <li>
                            <button
                                type="button"
                                class="flex min-h-11 w-full items-center justify-between gap-3 rounded-input border px-4 py-2 text-left text-body"
                                class:border-brand={membership.current}
                                class:bg-brand-tint={membership.current}
                                class:border-line={!membership.current}
                                aria-pressed={membership.current}
                                onclick={() => switchTo(membership.id)}
                            >
                                <span>
                                    <span class="block font-semibold">{membership.role_label}</span>
                                    <span class="block text-label text-muted">{membership.school}</span>
                                </span>
                            </button>
                        </li>
                    {/each}
                </ul>
            {/if}
            {#if page.props.errors?.membership}
                <p class="mt-2 text-label text-danger" role="alert">{page.props.errors.membership}</p>
            {/if}
        </section>
    </div>
</AppShell>
