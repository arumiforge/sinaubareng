<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Models\Subject;
use Illuminate\Support\Str;

/**
 * Finds a catalogue subject by code, name, or alias. Matching ignores case,
 * punctuation, and spaces, so "PJOK", "pjok", and "Pendidikan Jasmani, Olahraga,
 * dan Kesehatan" all find the same subject.
 */
final class SubjectLookup
{
    /** @var array<string, Subject>|null */
    private ?array $index = null;

    public static function normalize(string $name): string
    {
        return (string) preg_replace('/[^a-z0-9]+/', '', Str::lower(Str::ascii($name)));
    }

    public function find(string $codeOrName): ?Subject
    {
        return $this->index()[self::normalize($codeOrName)] ?? null;
    }

    public function get(string $codeOrName, string $where): Subject
    {
        return $this->find($codeOrName)
            ?? throw new InvalidCurriculumFile("{$where}: unknown subject '{$codeOrName}'. Add it, or an alias for it, to docs/data/curriculum/subjects.json and import that file first.");
    }

    public function refresh(): void
    {
        $this->index = null;
    }

    /**
     * @return array<string, Subject>
     */
    private function index(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        $this->index = [];

        foreach (Subject::query()->get() as $subject) {
            foreach ([$subject->code, $subject->name, ...$subject->aliases] as $name) {
                $this->index[self::normalize($name)] ??= $subject;
            }
        }

        return $this->index;
    }
}
