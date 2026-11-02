<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

// Every literal key passed to t(), tc(), __(), trans() or trans_choice() exists in lang/id (TR-7).
it('finds every used translation key in lang/id', function (): void {
    $files = Finder::create()->files()
        ->in([app_path(), resource_path(), base_path('routes')])
        ->name(['*.php', '*.svelte', '*.ts']);

    $missing = [];

    foreach ($files as $file) {
        preg_match_all('/\b(?:tc|t|__|trans|trans_choice)\(\s*[\'"]([a-z0-9_]+\.[a-z0-9_.]+)[\'"]/', $file->getContents(), $matches);

        foreach ($matches[1] as $key) {
            if (! trans()->hasForLocale($key, 'id')) {
                $missing[] = $file->getRelativePathname().': '.$key;
            }
        }
    }

    expect($missing)->toBe([]);
});
