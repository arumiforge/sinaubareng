<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Default local content for a region, e.g. Bahasa Jawa for Kudus (D-018). Reference
 * data, not school-owned: every school in the region gets it unless it switches it off.
 *
 * @property int $region_id
 * @property int $subject_id
 * @property int $allocation JP per week
 */
#[Fillable(['region_id', 'subject_id', 'allocation'])]
final class RegionSubject extends Model
{
    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
