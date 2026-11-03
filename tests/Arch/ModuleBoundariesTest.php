<?php

declare(strict_types=1);

// One test per row of the module table in docs/final/architecture.md (LAY-1).

arch('AI knows nothing about education')
    ->expect('App\AI')
    ->not->toUse(['App\Content', 'App\Exam', 'App\Models\Student']);

arch('Content reaches AI only through AIGateway')
    ->expect('App\Content')
    ->not->toUse(['App\AI\Providers', 'App\AI\ApiKeyManager']);

arch('Curriculum is reference data only')
    ->expect('App\Curriculum')
    ->not->toUse(['App\AI', 'App\Content']);

arch('Classroom data never touches AI')
    ->expect('App\Classroom')
    ->not->toUse('App\AI');

arch('Exam site never touches AI', function (): void {
    expect('App\Exam')->not->toUse('App\AI');
    expect('App\Http\Controllers\Exam')->not->toUse('App\AI');
});

arch('Filament is staff tooling only')
    ->expect('App\Filament')
    ->toOnlyBeUsedIn(['App\Providers\Filament', 'App\Filament']);

// Pest's array form of expect() can pass silently when a namespace is missing, so one call each.
arch('modules never import the HTTP layer (LAY-5)', function (): void {
    foreach (['App\AI', 'App\Content', 'App\Tenancy', 'App\Curriculum', 'App\Classroom', 'App\Exam'] as $module) {
        expect($module)->not->toUse('App\Http');
    }
});
