<?php

declare(strict_types=1);

namespace App\Enums;

/** What a proposed calendar change does to an event (D-380). */
enum CalendarChangeAction: string
{
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';

    public function label(): string
    {
        return __("enums.calendar_change_action.{$this->value}");
    }
}
