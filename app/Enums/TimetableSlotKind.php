<?php

declare(strict_types=1);

namespace App\Enums;

/** What a timetable row holds: a lesson, or a break, ceremony, or other activity (D-247). */
enum TimetableSlotKind: string
{
    case Lesson = 'lesson';
    case Break = 'break';
    case Ceremony = 'ceremony';
    case Activity = 'activity';

    public function label(): string
    {
        return __("enums.timetable_slot_kind.{$this->value}");
    }
}
