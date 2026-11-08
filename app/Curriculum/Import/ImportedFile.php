<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

/**
 * A decoded curriculum file and where it came from.
 */
final readonly class ImportedFile
{
    /**
     * @param  array<array-key, mixed>  $json
     */
    public function __construct(
        public string $path,
        public string $relativePath,
        public string $sha256,
        public array $json,
    ) {}

    public function data(): Data
    {
        return new Data($this->json);
    }
}
