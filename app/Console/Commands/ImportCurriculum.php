<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Curriculum\Import\CurriculumImporter;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Loads curriculum reference data, idempotent by code (data model, Curriculum). With no
 * file named it loads config('curriculum.files') in order. Data mismatches are listed
 * and do not stop the import unless --strict is given.
 */
#[Signature('curriculum:import {files?* : JSON files; default: every file in config/curriculum.php} {--strict : Fail when a file has data mismatches}')]
#[Description('Import curriculum versions, subjects, allocations, CP, topics, TP, and fokus')]
final class ImportCurriculum extends Command
{
    public function handle(CurriculumImporter $importer): int
    {
        /** @var list<string> $files */
        $files = (array) $this->argument('files');
        $mismatches = 0;

        foreach ($files === [] ? $this->defaultFiles() : $files as $path) {
            try {
                $report = $importer->import($path);
            } catch (InvalidCurriculumFile $exception) {
                $this->error($exception->getMessage());
                $this->error('Nothing from this file was imported.');

                return self::FAILURE;
            }

            $this->print($report);
            $mismatches += count($report->mismatches());
        }

        if ($mismatches > 0) {
            $this->warn("{$mismatches} data mismatch(es) to review.");

            return $this->option('strict') ? self::FAILURE : self::SUCCESS;
        }

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function defaultFiles(): array
    {
        $files = [];

        /** @var list<array{path: string, optional: bool}> $configured */
        $configured = config('curriculum.files');

        foreach ($configured as $file) {
            if ($file['optional'] && ! is_file(base_path($file['path']))) {
                $this->line("<comment>Skipped</comment> {$file['path']}: not in the repository yet.");

                continue;
            }

            $files[] = base_path($file['path']);
        }

        return $files;
    }

    private function print(ImportReport $report): void
    {
        $this->info($report->file);

        foreach ($report->counts() as $table => $count) {
            $this->line("  {$table}: {$count['created']} created, {$count['updated']} updated, {$count['unchanged']} unchanged");
        }

        foreach ($report->notes() as $note) {
            $this->line("  <comment>Note</comment> {$note}");
        }

        foreach ($report->mismatches() as $mismatch) {
            $this->line("  <error>Mismatch</error> {$mismatch}");
        }
    }
}
