<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Models\School;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * For every school-owned model (TEN-1): reads are scoped to the current school,
 * and new rows get the current school's id.
 *
 * @mixin Model
 */
trait BelongsToSchool
{
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new SchoolScope);

        static::creating(function (Model $model): void {
            $schoolId = app(SchoolContext::class)->schoolId();

            if ($model->getAttribute('school_id') === null) {
                $model->setAttribute('school_id', $schoolId);
            } elseif ($schoolId !== null && (int) $model->getAttribute('school_id') !== $schoolId) {
                throw new LogicException('A row cannot be created for another school than the current one.');
            }
        });
    }

    /**
     * Skips the school scope: `Model::withoutSchoolScope()`. Allowed only where TEN-2
     * allows it; say why at each call.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function withoutSchoolScope(Builder $query): void
    {
        $query->withoutGlobalScope(SchoolScope::class);
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
