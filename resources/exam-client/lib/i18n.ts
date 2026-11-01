import { page } from '@inertiajs/svelte';

type Params = Record<string, string | number>;

// Own copy: the exam client imports nothing from resources/js (D-094).
export function t(key: string, params: Params = {}): string {
    const text = page.props.translations?.[key] ?? key;
    return text.replace(/:(\w+)/g, (match, name: string) => (name in params ? String(params[name]) : match));
}
