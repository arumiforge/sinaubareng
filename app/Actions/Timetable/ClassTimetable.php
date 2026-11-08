<?php

declare(strict_types=1);

namespace App\Actions\Timetable;

use App\Actions\SchoolYear\ClassSubjects;
use App\Curriculum\SubjectOffer;
use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;

/**
 * A class's timetable for the pages: rows per day with the teacher from the teaching
 * assignments (D-249), JP per subject against the allocation (D-250), and clashes left
 * by later teacher changes (D-248).
 */
final readonly class ClassTimetable
{
    public function __construct(
        private ClassSubjects $classSubjects,
        private TimetableClashes $clashes,
    ) {}

    /**
     * @return array{
     *     rows: list<array{id: string, weekday: int, period_no: int, starts_at: string, ends_at: string, kind: string, kind_label: string, label: string|null, subject: string|null, subject_name: string|null, teacher_name: string|null, jp: int, is_offered: bool, clash: string|null}>,
     *     subjects: list<array{code: string, name: string, teacher_name: string|null, jp_per_week: int|null, scheduled_jp: int, status: string}>,
     *     is_complete: bool,
     * }
     */
    public function handle(SchoolClass $class, bool $withClashes = true): array
    {
        $offers = $this->classSubjects->handle($class);
        $assignments = TeachingAssignment::query()->where('class_id', $class->id)->with('teacher:id,name')->get()->keyBy('subject_id');
        $slots = $class->timetableSlots()->with('subject:id,code,name')->orderBy('weekday')->orderBy('period_no')->get();

        $rows = $slots->map(function (TimetableSlot $slot) use ($offers, $assignments, $class, $withClashes): array {
            $assignment = $slot->subject_id === null ? null : $assignments->get($slot->subject_id);
            $clash = $withClashes && $assignment !== null
                ? $this->clashes->find($assignment->teacher_id, $class, $slot->weekday, $slot->starts_at, $slot->ends_at)
                : null;

            return [
                'id' => $slot->public_id,
                'weekday' => $slot->weekday,
                'period_no' => $slot->period_no,
                'starts_at' => substr($slot->starts_at, 0, 5),
                'ends_at' => substr($slot->ends_at, 0, 5),
                'kind' => $slot->kind->value,
                'kind_label' => $slot->kind->label(),
                'label' => $slot->label,
                'subject' => $slot->subject?->code,
                'subject_name' => $slot->subject?->name,
                'teacher_name' => $assignment?->teacher->name,
                'jp' => $slot->jp,
                'is_offered' => $slot->kind !== TimetableSlotKind::Lesson || $offers->has((int) $slot->subject_id),
                'clash' => $clash === null ? null : $this->clashes->message($clash, $assignment->teacher->name),
            ];
        })->values()->all();

        $scheduled = $slots->whereNotNull('subject_id')->groupBy('subject_id')->map(fn ($group): int => (int) $group->sum('jp'));

        $subjects = $offers->map(function (SubjectOffer $offer) use ($assignments, $scheduled): array {
            $jp = $scheduled->get($offer->subject->id, 0);

            return [
                'code' => $offer->subject->code,
                'name' => $offer->subject->name,
                'teacher_name' => $assignments->get($offer->subject->id)?->teacher->name,
                'jp_per_week' => $offer->jpPerWeek,
                'scheduled_jp' => $jp,
                'status' => match (true) {
                    $offer->jpPerWeek === null => 'free',
                    // D-251: a religion the class has no teacher for is not taught there.
                    $offer->subject->religion !== null && ! $assignments->has($offer->subject->id) && $jp === 0 => 'not_counted',
                    $jp === $offer->jpPerWeek => 'complete',
                    $jp < $offer->jpPerWeek => 'under',
                    default => 'over',
                },
            ];
        })->values()->all();

        return [
            'rows' => $rows,
            'subjects' => $subjects,
            'is_complete' => collect($subjects)->every(fn (array $subject): bool => in_array($subject['status'], ['complete', 'free', 'not_counted'], true)),
        ];
    }
}
