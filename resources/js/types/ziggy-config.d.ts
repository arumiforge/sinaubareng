import 'ziggy-js';

declare module 'ziggy-js' {
    // An unknown route name fails `npm run check` (SV-5).
    interface TypeConfig {
        strictRouteNames: true;
    }
}
