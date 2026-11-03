<?php

declare(strict_types=1);

namespace App\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Limits school-owned rows to the current school. Without a school in the context
 * the query returns nothing (fail-closed).
 *
 * @implements Scope<Model>
 */
final class SchoolScope implements Scope
{
    /**
     * @param  Builder<covariant Model>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        $schoolId = app(SchoolContext::class)->schoolId();

        if ($schoolId === null) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where($model->qualifyColumn('school_id'), $schoolId);
    }
}
