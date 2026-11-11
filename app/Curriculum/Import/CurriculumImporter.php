<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Curriculum\Import\Formats\DataModelTopics;
use App\Curriculum\Import\Formats\LearningOutcomes;
use App\Curriculum\Import\Formats\MiAllocation;
use App\Curriculum\Import\Formats\SdStructure;
use App\Curriculum\Import\Formats\SubjectCatalog;
use App\Curriculum\Policy\PolicyProfileResolver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Imports one curriculum file (D-020, D-226). The format is read from the file itself.
 * Each file is one transaction: an invalid file writes nothing.
 */
final readonly class CurriculumImporter
{
    /** @var list<class-string<CurriculumFormat>> */
    private const FORMATS = [
        SubjectCatalog::class,
        LearningOutcomes::class,
        SdStructure::class,
        MiAllocation::class,
        DataModelTopics::class,
    ];

    public function __construct(private Container $container) {}

    public function import(string $path): ImportReport
    {
        $file = $this->read($path);
        $report = new ImportReport($file->relativePath);
        $format = $this->formatFor($file);

        try {
            DB::transaction(fn () => $format->import($file, $report));
        } finally {
            // A rolled-back catalogue must not stay cached; resolved policies may change too (D-355).
            $this->container->make(SubjectLookup::class)->refresh();
            $this->container->make(PolicyProfileResolver::class)->flush();
        }

        return $report;
    }

    private function read(string $path): ImportedFile
    {
        $real = realpath($path);

        if ($real === false || ! is_file($real)) {
            throw new InvalidCurriculumFile("File not found: {$path}");
        }

        $contents = (string) file_get_contents($real);

        try {
            $json = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new InvalidCurriculumFile("{$path} is not valid JSON: {$exception->getMessage()}");
        }

        if (! is_array($json)) {
            throw new InvalidCurriculumFile("{$path} must hold a JSON object or list.");
        }

        $base = rtrim(base_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $relative = str_starts_with($real, $base) ? substr($real, strlen($base)) : basename($real);

        return new ImportedFile($real, $relative, hash('sha256', $contents), $json);
    }

    private function formatFor(ImportedFile $file): CurriculumFormat
    {
        foreach (self::FORMATS as $class) {
            $format = $this->container->make($class);

            if ($format->supports($file->json)) {
                return $format;
            }
        }

        if ($this->isLegacyShape($file->json)) {
            throw new InvalidCurriculumFile("{$file->relativePath}: the legacy app format is no longer imported; topics come only from SIGRAK curriculum files (D-363).");
        }

        throw new InvalidCurriculumFile("{$file->relativePath}: not a curriculum file this import knows.");
    }

    /**
     * The legacy app's row-per-topic export or its SD allocation table.
     *
     * @param  array<array-key, mixed>  $json
     */
    private function isLegacyShape(array $json): bool
    {
        $rows = array_is_list($json) ? $json : ($json['data'] ?? null);

        return isset($json['alokasi']) || (is_array($rows) && is_array($rows[0] ?? null) && isset($rows[0]['ID_Kurikulum']));
    }
}
