<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\SchoolYear\CopyClasses;
use App\Actions\SchoolYear\CopyClassesRefused;
use App\Actions\SchoolYear\FindCurrentAcademicYear;
use App\Actions\SchoolYear\SaveAcademicYear;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\SaveAcademicYearRequest;
use App\Models\AcademicYear;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Operator portal > Tahun Ajaran. Route binding goes through the school scope, so
 * another school's year is a 404 (TEN-4).
 */
final class AcademicYearController extends Controller
{
    public function index(FindCurrentAcademicYear $findCurrent, CopyClasses $copy): Response
    {
        Gate::authorize('viewAny', AcademicYear::class);

        $currentId = $findCurrent->handle()?->id;

        $years = AcademicYear::query()->withCount('classes')->orderByDesc('starts_on')->get();
        $years = $years->map(fn (AcademicYear $year, int $index): array => [
            'id' => $year->public_id,
            'name' => $year->name,
            'odd_semester' => $year->starts_on->format('d/m/Y').' – '.$year->even_semester_starts_on->subDay()->format('d/m/Y'),
            'even_semester' => $year->even_semester_starts_on->format('d/m/Y').' – '.$year->ends_on->format('d/m/Y'),
            'is_current' => $year->id === $currentId,
            'classes_count' => $year->classes_count,
            // An empty year can take the classes of the year before it (D-242, D-246).
            'copy_from' => $year->classes_count === 0 ? $this->copySource($copy, $years->get($index + 1)) : null,
        ]);

        return Inertia::render('School/AcademicYears/Index', ['years' => $years]);
    }

    public function create(CopyClasses $copy): Response
    {
        Gate::authorize('create', AcademicYear::class);

        // Suggest the year after the latest one, or the one that starts this July (data model: default start 1 July).
        $latest = AcademicYear::query()->orderByDesc('starts_on')->first();
        $start = $latest !== null
            ? $latest->starts_on->addYear()
            : CarbonImmutable::create(now()->month >= 7 ? now()->year : now()->year - 1, 7, 1);

        return Inertia::render('School/AcademicYears/Form', [
            'year' => null,
            'copy_from' => $this->copySource($copy, $latest),
            'defaults' => [
                'starts_on' => $start->toDateString(),
                'even_semester_starts_on' => $start->addMonths(6)->toDateString(),
                'ends_on' => $start->addYear()->subDay()->toDateString(),
            ],
        ]);
    }

    public function store(SaveAcademicYearRequest $request, SaveAcademicYear $save, CopyClasses $copy): RedirectResponse
    {
        /** @var array{starts_on: string, even_semester_starts_on: string, ends_on: string} $data */
        $data = $request->safe()->only(['starts_on', 'even_semester_starts_on', 'ends_on']);
        $year = $save->handle($data);
        $toast = __('classes.years.toasts.saved', ['name' => $year->name]);

        if ($request->boolean('copy_classes')) {
            try {
                $count = $copy->handle($year, $request->boolean('with_grade_one'))->count();
                $toast = __('classes.copy.toasts.created_with_classes', ['name' => $year->name, 'count' => $count]);
            } catch (CopyClassesRefused $refused) {
                $toast .= ' '.$refused->getMessage();
            }
        }

        return to_route('academic_years.index')->with('toast', $toast);
    }

    /** Operator portal > Tahun Ajaran > Salin kelas, for a year that has no classes yet (D-242…D-246). */
    public function copyClasses(Request $request, AcademicYear $academicYear, CopyClasses $copy): RedirectResponse
    {
        Gate::authorize('copyClasses', $academicYear);
        $request->validate(['with_grade_one' => ['sometimes', 'boolean']]);

        try {
            $count = $copy->handle($academicYear, $request->boolean('with_grade_one'))->count();
        } catch (CopyClassesRefused $refused) {
            return back()->with('toast', $refused->getMessage());
        }

        return to_route('classes.index', ['tahun' => $academicYear->public_id])
            ->with('toast', __('classes.copy.toasts.copied', ['name' => $academicYear->name, 'count' => $count]));
    }

    public function edit(AcademicYear $academicYear): Response
    {
        Gate::authorize('update', $academicYear);

        return Inertia::render('School/AcademicYears/Form', [
            'year' => $this->dates($academicYear),
            'defaults' => null,
        ]);
    }

    public function update(SaveAcademicYearRequest $request, AcademicYear $academicYear, SaveAcademicYear $save): RedirectResponse
    {
        /** @var array{starts_on: string, even_semester_starts_on: string, ends_on: string} $data */
        $data = $request->validated();
        $year = $save->handle($data, $academicYear);

        return to_route('academic_years.index')->with('toast', __('classes.years.toasts.saved', ['name' => $year->name]));
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        Gate::authorize('delete', $academicYear);

        if ($academicYear->classes()->exists()) {
            return back()->with('toast', __('classes.years.errors.has_classes', ['name' => $academicYear->name]));
        }

        $academicYear->delete();

        return to_route('academic_years.index')->with('toast', __('classes.years.toasts.deleted', ['name' => $academicYear->name]));
    }

    /**
     * What a copy from `$source` would make, for the dialog; null when there is nothing to copy.
     *
     * @return array{name: string, classes: list<array{grade: int, name: string, new_intake: bool}>}|null
     */
    private function copySource(CopyClasses $copy, ?AcademicYear $source): ?array
    {
        if ($source === null) {
            return null;
        }

        $plan = $copy->plan($source);

        return $plan === [] ? null : ['name' => $source->name, 'classes' => $plan];
    }

    /**
     * @return array{id: string, name: string, starts_on: string, even_semester_starts_on: string, ends_on: string}
     */
    private function dates(AcademicYear $year): array
    {
        return [
            'id' => $year->public_id,
            'name' => $year->name,
            'starts_on' => $year->starts_on->toDateString(),
            'even_semester_starts_on' => $year->even_semester_starts_on->toDateString(),
            'ends_on' => $year->ends_on->toDateString(),
        ];
    }
}
