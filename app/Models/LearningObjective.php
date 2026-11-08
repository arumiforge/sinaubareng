<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Tujuan pembelajaran (TP) of a topic.
 *
 * @property string $code
 * @property int $topic_id
 * @property string $text
 */
#[Fillable(['topic_id', 'code', 'order', 'text'])]
final class LearningObjective extends Model
{
    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @return BelongsToMany<FocusItem, $this>
     */
    public function focusItems(): BelongsToMany
    {
        return $this->belongsToMany(FocusItem::class, 'focus_item_objectives');
    }
}
