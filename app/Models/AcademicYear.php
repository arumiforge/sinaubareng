<?php

declare(strict_types=1);

namespace App\Models;

use App\Tenancy\BelongsToSchool;
use Carbon\CarbonImmutable;
use Database\Factories\AcademicYearFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A school's academic year: semester 1 (ganjil) from `starts_on`, semester 2 (genap)
 * from `even_semester_starts_on` to `ends_on` (data model, School year).
 *
 * @property int $id
 * @property int $school_id
 * @property string $public_id
 * @property string $name
 * @property CarbonImmutable $starts_on
 * @property CarbonImmutable $even_semester_starts_on
 * @property CarbonImmutable $ends_on
 */
#[Fillable(['school_id', 'name', 'starts_on', 'even_semester_starts_on', 'ends_on'])]
final class AcademicYear extends Model
{
    use BelongsToSchool;

    /** @use HasFactory<AcademicYearFactory> */
    use HasFactory;

    use HasUuids;

    /** The name from the year the academic year starts in: 2026 gives `2026/2027`. */
    public static function nameFor(CarbonImmutable $startsOn): string
    {
        return $startsOn->year.'/'.($startsOn->year + 1);
    }

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
     * @return HasMany<SchoolClass, $this>
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function includes(CarbonImmutable $day): bool
    {
        return $day->betweenIncluded($this->starts_on, $this->ends_on);
    }

    protected function casts(): array
    {
        return [
            'starts_on' => 'immutable_date',
            'even_semester_starts_on' => 'immutable_date',
            'ends_on' => 'immutable_date',
        ];
    }
}
