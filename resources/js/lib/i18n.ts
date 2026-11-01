import { page } from '@inertiajs/svelte';

type Params = Record<string, string | number>;

/** Same semantics as Laravel's `__()`; text comes from the shared `translations` prop (LOC-13). */
export function t(key: string, params: Params = {}): string {
    const text = page.props.translations?.[key] ?? key; // a missing key shows the key; the lang test fails first
    return text.replace(/:(\w+)/g, (match, name: string) => (name in params ? String(params[name]) : match));
}
