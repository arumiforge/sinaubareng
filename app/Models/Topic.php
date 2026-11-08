<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Phase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Materi pokok / ATP: one topic of a grade, subject, and semester.
 *
 * @property string $code
 * @property int $subject_id
 * @property int $grade
 * @property Phase $phase
 * @property int $semester
 * @property int $jp_total
 * @property list<string> $legacy_codes
 * @property list<int> $weekly_distribution
 * @property array<string, mixed> $references
 * @property array<string, mixed>|null $audit
 * @property array<string, mixed>|null $extras
 */
#[Fillable([
    'curriculum_version_id', 'code', 'legacy_codes', 'subject_id', 'grade', 'phase', 'semester', 'elements', 'title',
    'cp_text', 'atp_code', 'order_in_semester', 'jp_total', 'jp_per_week', 'week_start', 'week_end',
    'weekly_distribution', 'references', 'audit', 'extras',
])]
final class Topic extends Model
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

    /**
     * @return HasMany<FocusItem, $this>
     */
    public function focusItems(): HasMany
    {
        return $this->hasMany(FocusItem::class)->orderBy('order');
    }

    /**
     * @return HasMany<LearningObjective, $this>
     */
    public function learningObjectives(): HasMany
    {
        return $this->hasMany(LearningObjective::class)->orderBy('order');
    }

    protected function casts(): array
    {
        return [
            'phase' => Phase::class,
            'legacy_codes' => 'array',
            'weekly_distribution' => 'array',
            'references' => 'array',
            'audit' => 'array',
            'extras' => 'array',
        ];
    }
}
