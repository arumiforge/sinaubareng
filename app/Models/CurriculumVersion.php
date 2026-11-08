<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CurriculumVersionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A global, versioned curriculum default, e.g. KM-SD-2026-R2 (data model, Curriculum).
 * Rows come only from `curriculum:import`.
 *
 * @property string $code
 * @property CurriculumVersionStatus $status
 * @property array<string, mixed>|null $meta
 * @property CarbonImmutable|null $imported_at NULL only before the first save
 */
#[Fillable(['code', 'name', 'status', 'source_file', 'source_sha256', 'meta', 'imported_at'])]
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

    protected function casts(): array
    {
        return [
            'status' => CurriculumVersionStatus::class,
            'meta' => 'array',
            'imported_at' => 'datetime',
        ];
    }
}
