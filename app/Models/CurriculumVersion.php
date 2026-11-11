<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CurriculumVersionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A global, versioned curriculum default, e.g. SIGRAK-SD-2026-R1 (data model, Curriculum).
 * Rows come only from `curriculum:import`.
 *
 * @property string $code
 * @property CurriculumVersionStatus $status
 * @property array{files?: array<string, array<string, mixed>>} $meta
 * @property CarbonImmutable|null $imported_at NULL only before the first save
 * @property CarbonImmutable|null $effective_on
 * @property string|null $applies_from_school_year e.g. 2026/2027 (D-355)
 * @property int|null $supersedes_id
 */
#[Fillable(['code', 'name', 'status', 'effective_on', 'applies_from_school_year', 'supersedes_id', 'meta', 'imported_at'])]
final class CurriculumVersion extends Model
{
    /**
     * The version this one replaces (D-355).
     *
     * @return BelongsTo<self, $this>
     */
    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supersedes_id');
    }

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
            'effective_on' => 'immutable_date',
        ];
    }
}
