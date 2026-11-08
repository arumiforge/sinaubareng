<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Fokus materi: a unit of a topic with its estimated JP.
 *
 * @property string $code
 * @property int $topic_id
 * @property int $estimated_jp
 */
#[Fillable(['topic_id', 'code', 'order', 'title', 'estimated_jp', 'allocation_status'])]
final class FocusItem extends Model
{
    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @return BelongsToMany<LearningObjective, $this>
     */
    public function learningObjectives(): BelongsToMany
    {
        return $this->belongsToMany(LearningObjective::class, 'focus_item_objectives');
    }
}
