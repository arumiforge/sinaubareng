<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

/**
 * What one file import did: row counts, notes, and data mismatches. Mismatches do not
 * stop the import; they are the data issues a curriculum owner has to look at.
 */
final class ImportReport
{
    /** @var array<string, array{created: int, updated: int, unchanged: int}> */
    private array $counts = [];

    /** @var list<string> */
    private array $notes = [];

    /** @var list<string> */
    private array $mismatches = [];

    public function __construct(public readonly string $file) {}

    public function count(string $table, string $outcome): void
    {
        $this->counts[$table] ??= ['created' => 0, 'updated' => 0, 'unchanged' => 0];
        $this->counts[$table][$outcome]++;
    }

    public function note(string $message): void
    {
        $this->notes[] = $message;
    }

    public function mismatch(string $message): void
    {
        $this->mismatches[] = $message;
    }

    /**
     * @return array<string, array{created: int, updated: int, unchanged: int}>
     */
    public function counts(): array
    {
        return $this->counts;
    }

    /**
     * @return list<string>
     */
    public function notes(): array
    {
        return $this->notes;
    }

    /**
     * @return list<string>
     */
    public function mismatches(): array
    {
        return $this->mismatches;
    }

    public function changedRows(): int
    {
        return array_sum(array_map(fn (array $count): int => $count['created'] + $count['updated'], $this->counts));
    }
}
