<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\BelongsToSchool;
use Database\Factories\TeachingAssignmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Who teaches one subject in one class (D-032). A class subject without a row has no
 * teacher yet (D-240).
 *
 * @property int $school_id
 * @property int $class_id
 * @property int $subject_id
 * @property int $teacher_id
 */
#[Fillable(['school_id', 'class_id', 'subject_id', 'teacher_id'])]
final class TeachingAssignment extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<TeachingAssignmentFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
