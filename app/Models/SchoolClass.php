<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\BelongsToSchool;
use Database\Factories\SchoolClassFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A class (rombel) of one grade in one academic year. `Class` is a reserved word in
 * PHP, so the model is SchoolClass on the data model's `classes` table.
 *
 * @property int $id
 * @property int $school_id
 * @property string $public_id
 * @property int $academic_year_id
 * @property int $grade
 * @property string $name
 * @property int|null $homeroom_teacher_id
 */
#[Fillable(['school_id', 'academic_year_id', 'grade', 'name', 'homeroom_teacher_id'])]
final class SchoolClass extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<SchoolClassFactory> */
    use HasFactory;

    use HasUuids;

    protected $table = 'classes';

    /**
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * @return HasMany<TeachingAssignment, $this>
     */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class, 'class_id');
    }

    /**
     * @return HasMany<TimetableSlot, $this>
     */
    public function timetableSlots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class, 'class_id');
    }

    protected function casts(): array
    {
        return [
            'grade' => 'integer',
        ];
    }
}
