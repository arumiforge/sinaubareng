<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DefaultTeacher;
use App\Enums\Religion;
use App\Enums\SubjectKind;
use App\Enums\SubjectSchoolLevel;
use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A subject from the catalogue in docs/data/curriculum/subjects.json (D-227).
 *
 * @property string $code
 * @property string $name
 * @property SubjectKind $kind
 * @property DefaultTeacher $default_teacher
 * @property Religion|null $religion
 * @property SubjectSchoolLevel $school_level
 * @property list<string> $aliases
 */
#[Fillable(['code', 'name', 'kind', 'default_teacher', 'religion', 'school_level', 'aliases'])]
final class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;

    /**
     * @return HasMany<SubjectAllocation, $this>
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(SubjectAllocation::class);
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
            'kind' => SubjectKind::class,
            'default_teacher' => DefaultTeacher::class,
            'religion' => Religion::class,
            'school_level' => SubjectSchoolLevel::class,
            'aliases' => 'array',
        ];
    }
}
