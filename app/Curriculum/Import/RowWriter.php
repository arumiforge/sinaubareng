<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use Illuminate\Database\Eloquent\Model;

/**
 * Upserts one row by its natural key and counts whether it was created, updated, or
 * already the same, so a second run of the same file reports no changes.
 */
final class RowWriter
{
    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $model
     * @param  array<string, mixed>  $key
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function upsert(string $model, array $key, array $values, ImportReport $report): Model
    {
        $row = $model::query()->firstOrNew($key);
        $row->fill($values);
        $this->ignoreKeyOrder($row);
        $outcome = ! $row->exists ? 'created' : ($row->isDirty() ? 'updated' : 'unchanged');
        $row->save();
        $report->count($row->getTable(), $outcome);

        return $row;
    }

    /**
     * MySQL stores JSON objects with its own key order, so a JSON column read back
     * differs from the file in key order only. Such a column is not a change.
     */
    public function ignoreKeyOrder(Model $row): void
    {
        foreach (array_keys($row->getDirty()) as $key) {
            $original = $row->getRawOriginal($key);
            $current = $row->getAttributes()[$key] ?? null;

            if (! is_string($original) || ! is_string($current) || ! $row->hasCast($key, ['array', 'json'])) {
                continue;
            }

            if (self::sorted(json_decode($original, true)) === self::sorted(json_decode($current, true))) {
                $row->syncOriginalAttribute($key);
            }
        }
    }

    private static function sorted(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (! array_is_list($value)) {
            ksort($value);
        }

        return array_map(self::sorted(...), $value);
    }
}
