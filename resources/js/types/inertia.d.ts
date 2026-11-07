import '@inertiajs/core';

export interface MembershipOption {
    id: string;
    school: string;
    role: 'principal' | 'operator' | 'teacher';
    role_label: string;
    current: boolean;
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            // Flattened `lang/id` groups shared by HandleInertiaRequests (LOC-9).
            translations: Record<string, string>;
            // The signed-in user and the roles the role switch offers; null for guests.
            auth: {
                user: { name: string; email: string };
                memberships: MembershipOption[];
            } | null;
            flash: { toast: string | null };
        };
    }
}
