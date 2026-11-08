<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Actions\SchoolYear\FindCurrentAcademicYear;
use App\Actions\Timetable\ClassTimetable;
use App\Enums\TimetableSlotKind;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Kelas Saya > Jadwal: the teacher's own lessons this academic year, from the class
 * timetables and their teaching assignments (D-249), plus the full timetable of each
 * class they are homeroom teacher of. Reads the current school only (school scope).
 */
final class MyTimetableController extends Controller
{
    public function __invoke(Request $request, FindCurrentAcademicYear $findCurrent, ClassTimetable $timetable): Response
    {
        Gate::authorize('viewOwnTimetable', SchoolClass::class);

        $userId = (int) $request->user()?->id;
        $year = $findCurrent->handle();

        $lessons = $year === null ? collect() : TimetableSlot::query()
            ->select('timetable_slots.*')
            ->join('classes', 'classes.id', '=', 'timetable_slots.class_id')
            ->join('teaching_assignments', function ($join): void {
                $join->on('teaching_assignments.class_id', '=', 'timetable_slots.class_id')
                    ->on('teaching_assignments.subject_id', '=', 'timetable_slots.subject_id');
            })
            ->where('teaching_assignments.teacher_id', $userId)
            ->where('classes.academic_year_id', $year->id)
            ->where('timetable_slots.kind', TimetableSlotKind::Lesson->value)
            ->with(['schoolClass:id,name', 'subject:id,code,name'])
            ->orderBy('timetable_slots.weekday')->orderBy('timetable_slots.starts_at')
            ->get();

        $homeroom = $year === null ? collect() : SchoolClass::query()
            ->whereBelongsTo($year)
            ->where('homeroom_teacher_id', $userId)
            ->orderBy('grade')->orderBy('name')
            ->get();

        return Inertia::render('Teacher/Timetable', [
            'year' => $year?->name,
            'lessons' => $lessons->map(fn (TimetableSlot $slot): array => [
                'id' => $slot->public_id,
                'weekday' => $slot->weekday,
                'starts_at' => substr($slot->starts_at, 0, 5),
                'ends_at' => substr($slot->ends_at, 0, 5),
                'class_name' => $slot->schoolClass->name,
                'subject_name' => $slot->subject?->name,
                'jp' => $slot->jp,
            ])->values(),
            'total_jp' => (int) $lessons->sum('jp'),
            'homeroom_classes' => $homeroom->map(fn (SchoolClass $class): array => [
                'id' => $class->public_id,
                'name' => $class->name,
                'rows' => $timetable->handle($class, withClashes: false)['rows'],
            ])->values(),
        ]);
    }
}
