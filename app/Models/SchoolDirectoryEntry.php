<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SchoolLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One SD/MI from the Kemendikdasmen reference site, used to fill in a new school's data (D-169).
 *
 * @property SchoolLevel $level
 */
#[Fillable(['npsn', 'name', 'level', 'address', 'village', 'district', 'region_id', 'fetched_on'])]
final class SchoolDirectoryEntry extends Model
{
    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * The address as one line for the school form, e.g. "JL. KADILANGU 549, Prambatan Kidul, Kaliwungu".
     */
    public function fullAddress(): string
    {
        return "{$this->address}, {$this->village}, {$this->district}";
    }

    protected function casts(): array
    {
        return [
            'level' => SchoolLevel::class,
            'fetched_on' => 'date',
        ];
    }
}
