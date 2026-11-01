import '@inertiajs/core';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            // Flattened `lang/id` groups shared by HandleInertiaRequests (LOC-9).
            translations: Record<string, string>;
        };
    }
}
