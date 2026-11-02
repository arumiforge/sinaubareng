<?php

declare(strict_types=1);

namespace App\Http\Middleware;

/**
 * Exam site: own root view and Vite entry, and only `common` and `exam` text (LOC-12).
 */
final class HandleExamInertiaRequests extends HandleInertiaRequests
{
    protected $rootView = 'exam';

    /** @var list<string> */
    protected array $translationGroups = ['common', 'exam'];
}
