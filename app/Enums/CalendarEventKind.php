<?php

declare(strict_types=1);

namespace App\Enums;

/** What a calendar event does to the school days it covers (data model, Calendar; D-259). */
enum CalendarEventKind: string
{
    case Holiday = 'holiday';
    case Exam = 'exam';
    case SchoolEvent = 'school_event';
    case NoLessons = 'no_lessons';

    public function label(): string
    {
        return __("enums.calendar_event_kind.{$this->value}");
    }
}
