<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Enums\CalendarEventKind;

/**
 * Why a school day is not effective, in the columns of the effective-days table
 * (D-259): Libur, Asesmen, Kegiatan. A day in several categories counts once, in the
 * first of this order.
 */
enum DayCategory: string
{
    case Holiday = 'holiday';
    case Assessment = 'assessment';
    case Activity = 'activity';

    public static function forEvent(CalendarEventKind $kind): self
    {
        return match ($kind) {
            CalendarEventKind::Holiday => self::Holiday,
            CalendarEventKind::Exam => self::Assessment,
            CalendarEventKind::SchoolEvent, CalendarEventKind::NoLessons => self::Activity,
        };
    }

    public function rank(): int
    {
        return match ($this) {
            self::Holiday => 0,
            self::Assessment => 1,
            self::Activity => 2,
        };
    }
}
