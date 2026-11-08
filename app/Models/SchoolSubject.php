<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\BelongsToSchool;
use Database\Factories\SchoolSubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A school's switch for local or optional content (D-204). `grade` NULL covers every
 * grade; a row for one grade wins over it.
 *
 * @property int $school_id
 * @property int $subject_id
 * @property int|null $grade
 * @property bool $enabled
 * @property bool $in_report
 */
#[Fillable(['school_id', 'subject_id', 'grade', 'enabled', 'in_report'])]
final class SchoolSubject extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<SchoolSubjectFactory> */
    use HasFactory;

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
            'enabled' => 'boolean',
            'in_report' => 'boolean',
        ];
    }
}
