<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LearningType;
use App\Enums\Phase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * JP for one subject and grade in one curriculum version, intrakurikuler or kokurikuler.
 *
 * @property int $subject_id
 * @property int $grade
 * @property Phase $phase
 * @property LearningType $learning_type
 * @property int $jp_per_week
 * @property int $weeks_per_year
 * @property int $s1_jp
 * @property int $s2_jp
 */
#[Fillable(['curriculum_version_id', 'subject_id', 'grade', 'phase', 'learning_type', 'jp_per_week', 'weeks_per_year', 's1_jp', 's1_effective_weeks', 's2_jp', 's2_effective_weeks'])]
final class SubjectAllocation extends Model
{
    /**
     * @return BelongsTo<CurriculumVersion, $this>
     */
    public function curriculumVersion(): BelongsTo
    {
        return $this->belongsTo(CurriculumVersion::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    protected function casts(): array
    {
        return [
            'phase' => Phase::class,
            'learning_type' => LearningType::class,
        ];
    }
}
