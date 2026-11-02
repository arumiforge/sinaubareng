<?php

declare(strict_types=1);

// Route lists sent to each front end through `@routes('<group>')` (D-081).
// The exam site sees only its own routes; staff and vendor routes never reach a browser.

return [
    'groups' => [
        'app' => ['!exam.*', '!admin.*', '!filament.*', '!horizon.*', '!telescope.*', '!livewire.*', '!storage.*', '!inertia.*'],
        'exam' => ['exam.*'],
    ],
];
