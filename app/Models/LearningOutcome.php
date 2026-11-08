<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Phase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One CP row (capaian pembelajaran) of a CP-only version, per element and phase (D-226).
 * `element` NULL is the phase summary.
 *
 * @property string $code
 * @property Phase $phase
 * @property string|null $element
 * @property string|null $sub_element
 * @property string $text
 * @property array<string, mixed>|null $extras
 */
#[Fillable(['curriculum_version_id', 'subject_id', 'phase', 'code', 'order', 'element', 'sub_element', 'text', 'extras'])]
final class LearningOutcome extends Model
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
            'extras' => 'array',
        ];
    }
}
