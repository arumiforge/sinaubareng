<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\SchoolYear\ClassSubjects;
use App\Actions\SchoolYear\FindCurrentAcademicYear;
use App\Actions\SchoolYear\SaveClass;
use App\Actions\SchoolYear\TeacherOptions;
use App\Curriculum\SubjectOffer;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\SaveClassRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\SchoolMembership;
use App\Models\TeachingAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Operator portal > Kelas: the classes of one academic year (`?tahun=<year>`, the
 * current year by default), and per class who teaches what (D-032, D-240).
 */
final class SchoolClassController extends Controller
{
    public function index(Request $request, FindCurrentAcademicYear $findCurrent, ClassSubjects $classSubjects): Response
    {
        Gate::authorize('viewAny', SchoolClass::class);

        $years = AcademicYear::query()->orderByDesc('starts_on')->get();
        $selected = $years->firstWhere('public_id', $request->query('tahun')) ?? $findCurrent->handle();

        $classes = $selected === null ? collect() : SchoolClass::query()
            ->whereBelongsTo($selected)
            ->with(['homeroomTeacher:id,name', 'teachingAssignments:id,class_id,subject_id'])
            ->orderBy('grade')->orderBy('name')
            ->get();

        /** @var array<int, list<int>> $subjectIds subject ids per grade */
        $subjectIds = [];

        return Inertia::render('School/Classes/Index', [
            'years' => $years->map(fn (AcademicYear $year): array => ['id' => $year->public_id, 'name' => $year->name])->values(),
            'selected_year' => $selected?->public_id,
            'classes' => $classes->map(function (SchoolClass $class) use ($classSubjects, &$subjectIds): array {
                $subjectIds[$class->grade] ??= $classSubjects->handle($class)->keys()->all();

                return [
                    'id' => $class->public_id,
                    'name' => $class->name,
                    'grade' => $class->grade,
                    'homeroom_teacher' => $class->homeroomTeacher?->name,
                    'assigned_count' => $class->teachingAssignments->whereIn('subject_id', $subjectIds[$class->grade])->count(),
                    'subjects_count' => count($subjectIds[$class->grade]),
                ];
            })->values(),
        ]);
    }

    public function create(Request $request, TeacherOptions $teachers, FindCurrentAcademicYear $findCurrent): Response
    {
        Gate::authorize('create', SchoolClass::class);

        $years = AcademicYear::query()->orderByDesc('starts_on')->get();
        $year = $years->firstWhere('public_id', $request->query('tahun')) ?? $findCurrent->handle();

        return Inertia::render('School/Classes/Form', [
            'school_class' => null,
            'years' => $years->map(fn (AcademicYear $year): array => ['id' => $year->public_id, 'name' => $year->name])->values(),
            'default_year' => $year?->public_id,
            'teachers' => $this->teacherOptions($teachers->handle()),
        ]);
    }

    public function store(SaveClassRequest $request, SaveClass $save): RedirectResponse
    {
        $class = $save->handle($request->classData());

        return to_route('classes.show', $class)->with('toast', __('classes.classes.toasts.saved', ['name' => $class->name]));
    }

    public function show(SchoolClass $class, ClassSubjects $classSubjects, TeacherOptions $teachers): Response
    {
        Gate::authorize('view', $class);

        $class->load(['academicYear', 'homeroomTeacher:id,name']);
        $options = $teachers->handle();
        $assignments = $class->teachingAssignments()->with(['subject', 'teacher:id,name'])->get()->keyBy('subject_id');
        $offers = $classSubjects->handle($class);

        $subjects = $offers->map(fn (SubjectOffer $offer): array => [
            ...$this->subjectRow($offer->subject->code, $offer->subject->name, $assignments->get($offer->subject->id), $options),
            'default_teacher' => $offer->subject->default_teacher->label(),
            'jp_per_week' => $offer->jpPerWeek,
            'is_optional' => $offer->source === SubjectOffer::FROM_SCHOOL,
            'in_report' => $offer->inReport,
            'is_offered' => true,
        ])->values();

        // Rows for subjects the class no longer takes (the school switched them off): shown so the operator can clear them.
        $stale = $assignments->reject(fn (TeachingAssignment $assignment): bool => $offers->has($assignment->subject_id))
            ->map(fn (TeachingAssignment $assignment): array => [
                ...$this->subjectRow($assignment->subject->code, $assignment->subject->name, $assignment, $options),
                'default_teacher' => $assignment->subject->default_teacher->label(),
                'jp_per_week' => null,
                'is_optional' => false,
                'in_report' => false,
                'is_offered' => false,
            ])->values();

        return Inertia::render('School/Classes/Show', [
            'school_class' => [
                'id' => $class->public_id,
                'name' => $class->name,
                'grade' => $class->grade,
                'year' => $class->academicYear->name,
                'year_id' => $class->academicYear->public_id,
                'homeroom_teacher' => $class->homeroomTeacher?->name,
            ],
            'subjects' => [...$subjects, ...$stale],
            'teachers' => $this->teacherOptions($options),
        ]);
    }

    public function edit(SchoolClass $class, TeacherOptions $teachers): Response
    {
        Gate::authorize('update', $class);

        $class->load('academicYear');
        $options = $teachers->handle();

        return Inertia::render('School/Classes/Form', [
            'school_class' => [
                'id' => $class->public_id,
                'name' => $class->name,
                'grade' => $class->grade,
                'year' => $class->academicYear->name,
                'homeroom_teacher' => $class->homeroom_teacher_id === null ? null : $options->get($class->homeroom_teacher_id)?->public_id,
            ],
            'years' => [],
            'default_year' => null,
            'teachers' => $this->teacherOptions($options),
        ]);
    }

    public function update(SaveClassRequest $request, SchoolClass $class, SaveClass $save): RedirectResponse
    {
        $save->handle($request->classData(), $class);

        return to_route('classes.show', $class)->with('toast', __('classes.classes.toasts.saved', ['name' => $class->name]));
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        Gate::authorize('delete', $class);

        $class->load('academicYear');
        $class->delete();

        return to_route('classes.index', ['tahun' => $class->academicYear->public_id])
            ->with('toast', __('classes.classes.toasts.deleted', ['name' => $class->name]));
    }

    /**
     * @param  Collection<int, SchoolMembership>  $options
     * @return list<array{id: string, name: string}>
     */
    private function teacherOptions(Collection $options): array
    {
        return $options->map(fn (SchoolMembership $membership): array => ['id' => $membership->public_id, 'name' => $membership->user->name])->values()->all();
    }

    /**
     * @param  Collection<int, SchoolMembership>  $options
     * @return array{code: string, name: string, teacher: string|null, teacher_name: string|null, teacher_is_active: bool}
     */
    private function subjectRow(string $code, string $name, ?TeachingAssignment $assignment, Collection $options): array
    {
        $membership = $assignment === null ? null : $options->get($assignment->teacher_id);

        return [
            'code' => $code,
            'name' => $name,
            'teacher' => $membership?->public_id,
            'teacher_name' => $assignment?->teacher->name,
            'teacher_is_active' => $assignment === null || $membership !== null,
        ];
    }
}
