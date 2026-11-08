<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\Timetable\ClassTimetable;
use App\Actions\Timetable\SaveTimetableSlot;
use App\Enums\TimetableSlotKind;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\SaveTimetableSlotRequest;
use App\Models\SchoolClass;
use App\Models\TimetableSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Operator portal > Kelas > a class > Jadwal (D-247…D-250). Binding goes through the
 * school scope and rows are scoped to their class, so another school's class or row is a 404.
 */
final class TimetableController extends Controller
{
    public function show(SchoolClass $class, ClassTimetable $timetable): Response
    {
        Gate::authorize('view', $class);

        $class->load('academicYear');

        return Inertia::render('School/Classes/Timetable', [
            'school_class' => [
                'id' => $class->public_id,
                'name' => $class->name,
                'year' => $class->academicYear->name,
            ],
            ...$timetable->handle($class),
            'kinds' => collect(TimetableSlotKind::cases())->map(fn (TimetableSlotKind $kind): array => ['value' => $kind->value, 'label' => $kind->label()])->all(),
            'minutes_per_jp' => TimetableSlot::MINUTES_PER_JP,
        ]);
    }

    public function store(SaveTimetableSlotRequest $request, SchoolClass $class, SaveTimetableSlot $save): RedirectResponse
    {
        $save->handle($class, $request->slotData());

        return to_route('classes.timetable.show', $class)->with('toast', __('timetable.toasts.saved'));
    }

    public function update(SaveTimetableSlotRequest $request, SchoolClass $class, TimetableSlot $timetableSlot, SaveTimetableSlot $save): RedirectResponse
    {
        $save->handle($class, $request->slotData(), $timetableSlot);

        return to_route('classes.timetable.show', $class)->with('toast', __('timetable.toasts.saved'));
    }

    public function destroy(SchoolClass $class, TimetableSlot $timetableSlot, SaveTimetableSlot $save): RedirectResponse
    {
        Gate::authorize('update', $class);

        $save->delete($timetableSlot);

        return to_route('classes.timetable.show', $class)->with('toast', __('timetable.toasts.deleted'));
    }
}
