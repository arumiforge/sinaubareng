<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\SchoolYear\FindCurrentAcademicYear;
use App\Actions\SchoolYear\SaveAcademicYear;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\SaveAcademicYearRequest;
use App\Models\AcademicYear;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Operator portal > Tahun Ajaran. Route binding goes through the school scope, so
 * another school's year is a 404 (TEN-4).
 */
final class AcademicYearController extends Controller
{
    public function index(FindCurrentAcademicYear $findCurrent): Response
    {
        Gate::authorize('viewAny', AcademicYear::class);

        $currentId = $findCurrent->handle()?->id;

        $years = AcademicYear::query()->withCount('classes')->orderByDesc('starts_on')->get()
            ->map(fn (AcademicYear $year): array => [
                'id' => $year->public_id,
                'name' => $year->name,
                'odd_semester' => $year->starts_on->format('d/m/Y').' – '.$year->even_semester_starts_on->subDay()->format('d/m/Y'),
                'even_semester' => $year->even_semester_starts_on->format('d/m/Y').' – '.$year->ends_on->format('d/m/Y'),
                'is_current' => $year->id === $currentId,
                'classes_count' => $year->classes_count,
            ]);

        return Inertia::render('School/AcademicYears/Index', ['years' => $years]);
    }

    public function create(): Response
    {
        Gate::authorize('create', AcademicYear::class);

        // Suggest the year after the latest one, or the one that starts this July (data model: default start 1 July).
        $latest = AcademicYear::query()->orderByDesc('starts_on')->first();
        $start = $latest !== null
            ? $latest->starts_on->addYear()
            : CarbonImmutable::create(now()->month >= 7 ? now()->year : now()->year - 1, 7, 1);

        return Inertia::render('School/AcademicYears/Form', [
            'year' => null,
            'defaults' => [
                'starts_on' => $start->toDateString(),
                'even_semester_starts_on' => $start->addMonths(6)->toDateString(),
                'ends_on' => $start->addYear()->subDay()->toDateString(),
            ],
        ]);
    }

    public function store(SaveAcademicYearRequest $request, SaveAcademicYear $save): RedirectResponse
    {
        /** @var array{starts_on: string, even_semester_starts_on: string, ends_on: string} $data */
        $data = $request->validated();
        $year = $save->handle($data);

        return to_route('academic_years.index')->with('toast', __('classes.years.toasts.saved', ['name' => $year->name]));
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
