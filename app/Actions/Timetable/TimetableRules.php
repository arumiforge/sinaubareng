<?php

declare(strict_types=1);

namespace App\Actions\Timetable;

use App\Actions\SchoolYear\ClassSubjects;
use App\Curriculum\SubjectOffer;
use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;

/**
 * The rules every timetable row passes, typed by hand or saved from a draft
 * (D-247…D-251, D-262):
 *
 * - rows of a class may not overlap on a day, except lessons of different religions (D-251);
 * - a lesson needs a subject the class takes that already has a teacher (D-249);
 * - a subject may not go over its weekly JP allocation (D-250);
 * - the teacher may not have a lesson in another class at the same time (D-248).
 */
final readonly class TimetableRules
{
    public function __construct(
        private ClassSubjects $classSubjects,
        private TimetableClashes $clashes,
    ) {}

    /**
     * The first broken rule as [field, message], or null with the subject offer of a lesson.
     *
     * @param  array{weekday: int, starts_at: string, ends_at: string, kind: string, subject: string|null, jp: int}  $row  times as H:i:s
     * @param  ?SubjectOffer  $offer  set to the lesson's subject offer when the row passes
     * @return array{0: string, 1: string}|null
     */
    public function check(SchoolClass $class, array $row, ?TimetableSlot $ignore = null, ?SubjectOffer &$offer = null): ?array
    {
        $overlapping = $class->timetableSlots()
            ->when($ignore, fn ($query, TimetableSlot $slot) => $query->whereKeyNot($slot->id))
            ->where('weekday', $row['weekday'])
            ->where('starts_at', '<', $row['ends_at'])
            ->where('ends_at', '>', $row['starts_at'])
            ->with('subject:id,code,religion')
            ->get();

        $isLesson = $row['kind'] === TimetableSlotKind::Lesson->value;
        $code = $isLesson ? (string) $row['subject'] : null;
        $religionOnly = $code !== null
            && Subject::query()->where('code', $code)->whereNotNull('religion')->exists()
            && $overlapping->every(fn (TimetableSlot $other): bool => $other->subject?->religion !== null && $other->subject->code !== $code);

        if ($overlapping->isNotEmpty() && ! $religionOnly) {
            return ['starts_at', __('timetable.errors.overlap')];
        }

        if (! $isLesson) {
            return null;
        }

        $offer = $this->classSubjects->handle($class)->first(fn (SubjectOffer $item): bool => $item->subject->code === $code);

        if ($offer === null) {
            return ['subject', __('classes.assignments.errors.subject_missing')];
        }

        $subject = $offer->subject;
        $assignment = TeachingAssignment::query()->where('class_id', $class->id)->where('subject_id', $subject->id)->with('teacher:id,name')->first();

        if ($assignment === null) {
            return ['subject', __('timetable.errors.no_teacher', ['subject' => $subject->name])];
        }

        if ($offer->jpPerWeek !== null) {
            $scheduled = (int) $class->timetableSlots()
                ->when($ignore, fn ($query, TimetableSlot $slot) => $query->whereKeyNot($slot->id))
                ->where('subject_id', $subject->id)
                ->sum('jp');

            if ($scheduled + $row['jp'] > $offer->jpPerWeek) {
                return ['jp', __('timetable.errors.over_allocation', [
                    'subject' => $subject->name,
                    'allocated' => $offer->jpPerWeek,
                    'left' => max(0, $offer->jpPerWeek - $scheduled),
                ])];
            }
        }

        $clash = $this->clashes->find($assignment->teacher_id, $class, $row['weekday'], $row['starts_at'], $row['ends_at']);

        return $clash === null ? null : ['starts_at', $this->clashes->message($clash, $assignment->teacher->name)];
    }
}
