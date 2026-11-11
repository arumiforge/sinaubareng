<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

use App\Actions\SchoolYear\ClassSubjects;
use App\Curriculum\SubjectOffer;
use App\Enums\TimetableSlotKind;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;
use Illuminate\Support\Collection;

/**
 * Reads what the generator needs from the school (D-262): for each class, the frame
 * cells its locked rows leave free, the JP still to place per subject, and the times
 * each teacher already teaches in classes outside the run. Current school only.
 */
final readonly class BuildDraftPlans
{
    public function __construct(private ClassSubjects $classSubjects) {}

    /**
     * @param  Collection<int, SchoolClass>  $classes
     * @return array{plans: list<ClassPlan>, busy: array<int, list<array{0: int, 1: string, 2: string}>>}
     */
    public function handle(AcademicYear $year, Collection $classes, DayFrame $frame): array
    {
        $classIds = $classes->modelKeys();
        $busy = [];

        // Lessons that stay: every lesson of the year's other classes, and locked lessons of the classes in the run.
        $staying = TimetableSlot::query()
            ->select('timetable_slots.*', 'teaching_assignments.teacher_id')
            ->join('classes', 'classes.id', '=', 'timetable_slots.class_id')
            ->join('teaching_assignments', function ($join): void {
                $join->on('teaching_assignments.class_id', '=', 'timetable_slots.class_id')
                    ->on('teaching_assignments.subject_id', '=', 'timetable_slots.subject_id');
            })
            ->where('classes.academic_year_id', $year->id)
            ->where('timetable_slots.kind', TimetableSlotKind::Lesson->value)
            ->where(fn ($query) => $query->whereNotIn('timetable_slots.class_id', $classIds)->orWhere('timetable_slots.is_locked', true))
            ->get();

        foreach ($staying as $slot) {
            $busy[(int) $slot->getAttribute('teacher_id')][] = [$slot->weekday, $slot->starts_at, $slot->ends_at];
        }

        $plans = $classes->map(fn (SchoolClass $class): ClassPlan => $this->plan($class, $frame))->values()->all();

        return ['plans' => $plans, 'busy' => $busy];
    }

    private function plan(SchoolClass $class, DayFrame $frame): ClassPlan
    {
        $locked = $class->timetableSlots()->where('is_locked', true)->with('subject:id,code,name')->orderBy('weekday')->orderBy('starts_at')->get();
        $assignments = TeachingAssignment::query()->where('class_id', $class->id)->with('teacher:id,name')->get()->keyBy('subject_id');
        $overlapsLocked = fn (int $weekday, string $startsAt, string $endsAt): bool => $locked->contains(
            fn (TimetableSlot $slot): bool => $slot->weekday === $weekday && $slot->starts_at < $endsAt && $slot->ends_at > $startsAt,
        );

        $cells = array_values(array_filter($frame->cells(), fn (Cell $cell): bool => ! $overlapsLocked($cell->weekday, $cell->startsAt, $cell->endsAt)));

        $fixed = $locked->map(fn (TimetableSlot $slot): array => [
            'weekday' => $slot->weekday,
            'starts_at' => $slot->starts_at,
            'ends_at' => $slot->ends_at,
            'kind' => $slot->kind->value,
            'subject' => $slot->subject?->code,
            'subject_name' => $slot->subject?->name,
            'teacher_name' => $slot->subject_id === null ? null : $assignments->get($slot->subject_id)?->teacher->name,
            'label' => $slot->label,
            'jp' => $slot->jp,
            'is_locked' => true,
        ])->values()->all();

        foreach ($frame->breakRows() as $break) {
            if (! $overlapsLocked($break['weekday'], $break['starts_at'], $break['ends_at'])) {
                $fixed[] = [...$break, 'kind' => TimetableSlotKind::Break->value, 'subject' => null, 'subject_name' => null, 'teacher_name' => null, 'jp' => 0, 'is_locked' => false];
            }
        }

        $lockedJp = $locked->whereNotNull('subject_id')->groupBy('subject_id')->map(fn ($rows): int => (int) $rows->sum('jp'));
        $demands = [];
        $religions = [];
        $unplaceable = [];
        $pjok = (array) config('timetable.subjects.pjok', []);
        $core = (array) config('timetable.subjects.core', []);

        foreach ($this->classSubjects->handle($class) as $offer) {
            /** @var SubjectOffer $offer */
            if ($offer->jpPerWeek === null) {
                continue; // optional content has no allocation to place (D-250)
            }

            $subject = $offer->subject;
            $needed = $offer->jpPerWeek - $lockedJp->get($subject->id, 0);
            $assignment = $assignments->get($subject->id);

            if ($needed <= 0) {
                continue;
            }

            if ($assignment === null) {
                // D-251: a religion the class has no teacher for is not taught there.
                if ($subject->religion === null) {
                    $unplaceable[] = ['subject' => $subject->name, 'jp' => $needed, 'reason' => __('timetable.draft.reasons.no_teacher')];
                }

                continue;
            }

            $entry = ['subject_id' => $subject->id, 'code' => $subject->code, 'name' => $subject->name, 'teacher_id' => $assignment->teacher_id, 'teacher_name' => $assignment->teacher->name];

            if ($subject->religion !== null) {
                $religions[] = [$entry, $needed];

                continue;
            }

            $demands[] = new Demand($subject->code, [$entry], $needed, in_array($subject->code, $pjok, true), in_array($subject->code, $core, true));
        }

        if ($religions !== []) {
            // The religions share their time (D-251); any JP one needs beyond the others is placed alone.
            $shared = min(array_column($religions, 1));
            $demands[] = new Demand('religion', array_column($religions, 0), $shared);

            foreach ($religions as [$entry, $needed]) {
                if ($needed > $shared) {
                    $demands[] = new Demand($entry['code'], [$entry], $needed - $shared);
                }
            }
        }

        return new ClassPlan($class->id, $class->public_id, $class->name, $cells, $demands, $fixed, $unplaceable);
    }
}
