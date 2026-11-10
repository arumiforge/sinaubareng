<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CurriculumVersionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A global, versioned curriculum default, e.g. SIGRAK-SD-2026-R1 (data model, Curriculum).
 * Rows come only from `curriculum:import`.
 *
 * @property string $code
 * @property CurriculumVersionStatus $status
 * @property array{files?: array<string, array<string, mixed>>} $meta
 * @property CarbonImmutable|null $imported_at NULL only before the first save
 */
#[Fillable(['code', 'name', 'status', 'meta', 'imported_at'])]
final class CurriculumVersion extends Model
{
    /**
     * @return HasMany<SubjectAllocation, $this>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(SubjectAllocation::class);
    }

    /**
     * @return HasMany<LearningOutcome, $this>
     */
    public function learningOutcomes(): HasMany
    {
        return $this->hasMany(LearningOutcome::class);
    }

    /**
     * @return HasMany<Topic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * A value one of the version's source files recorded, e.g. the muatan lokal limits.
     */
    public function fileMeta(string $key): mixed
    {
        foreach ($this->meta['files'] ?? [] as $file) {
            if (array_key_exists($key, $file)) {
                return $file[$key];
            }
        }

        return null;
    }

    protected function casts(): array
    {
        return [
            'status' => CurriculumVersionStatus::class,
            'meta' => 'array',
            'imported_at' => 'datetime',
        ];
    }
}
