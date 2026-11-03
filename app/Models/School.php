<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SchoolLevel;
use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * One school, one tenant (data model, Accounts and schools).
 *
 * @property SchoolLevel $level
 */
#[Fillable(['name', 'npsn', 'level', 'region_id', 'address'])]
final class School extends Model
{
    /** @use HasFactory<SchoolFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return HasMany<SchoolMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(SchoolMembership::class);
    }

    /**
     * The reference-site entry with the same NPSN, when there is one (D-169).
     *
     * @return HasOne<SchoolDirectoryEntry, $this>
     */
    public function directoryEntry(): HasOne
    {
        return $this->hasOne(SchoolDirectoryEntry::class, 'npsn', 'npsn');
    }

    protected function casts(): array
    {
        return [
            'level' => SchoolLevel::class,
        ];
    }
}
