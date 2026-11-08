<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

/**
 * One kind of curriculum file the import understands.
 */
interface CurriculumFormat
{
    /**
     * @param  array<array-key, mixed>  $json
     */
    public function supports(array $json): bool;

    public function import(ImportedFile $file, ImportReport $report): void;
}
