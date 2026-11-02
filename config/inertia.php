<?php

declare(strict_types=1);

// Only what differs from the package defaults.

return [
    'pages' => [
        'ensure_pages_exist' => false,
        'paths' => [
            resource_path('js/Pages'),
            resource_path('exam-client/Pages'),
        ],
        'extensions' => ['svelte'],
    ],
];
