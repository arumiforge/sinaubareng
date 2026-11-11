<?php

declare(strict_types=1);

return [
    /*
     * Files `php artisan curriculum:import` loads when no file is named, in this order:
     * the subject catalogue first, then CP and structures, then the SIGRAK topic files (D-363).
     * A file marked optional is skipped with a note when it is missing.
     */
    'files' => [
        ['path' => 'docs/data/curriculum/subjects.json', 'optional' => false],
        ['path' => 'docs/data/curriculum/cp-pendidikan-agama-sd-bkpdm-020-2026.json', 'optional' => false],
        ['path' => 'docs/data/curriculum/cp-pai-bahasa-arab-mi-sk-dirjen-9941-2025.json', 'optional' => false],
        ['path' => 'docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json', 'optional' => false],
        ['path' => 'docs/data/curriculum/mi-allocation-kma-1503-2025.json', 'optional' => false],
        ['path' => 'docs/data/curriculum/sigrak-sd-2026-r1-umum.json', 'optional' => false],
        ['path' => 'docs/data/curriculum/sigrak-sd-2026-r1-agama.json', 'optional' => false],
        ['path' => 'docs/data/curriculum/sigrak-mi-2026-r1.json', 'optional' => false],
    ],

    /*
     * The Profil Jenjang files, one per school level (D-356). The policy in force,
     * including the structure version that decides a class's compulsory subjects, is
     * resolved from them per school year (App\Curriculum\Policy\PolicyProfileResolver,
     * D-355); this replaces the old `allocation_versions` pointer.
     */
    'profiles' => 'docs/data/profiles',
];
