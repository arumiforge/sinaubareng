<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;

// LOC-35: words the UI never uses (design system, D-302, D-311). "Absensi" waits for the
// owner's answer on a help-search allow-list.
it('keeps banned words out of lang/id', function (): void {
    $banned = ['siswa', 'teguran', 'tunggakan', 'kurang bayar', 'belum lunas', 'tanda tangan elektronik'];
    $hits = [];

    foreach (Finder::create()->files()->in(lang_path('id'))->name('*.php') as $file) {
        foreach (Arr::dot(require $file->getRealPath()) as $key => $text) {
            foreach ($banned as $word) {
                if (is_string($text) && str_contains(mb_strtolower($text), $word)) {
                    $hits[] = $file->getFilename().": {$key} ({$word})";
                }
            }
        }
    }

    expect($hits)->toBe([]);
});
