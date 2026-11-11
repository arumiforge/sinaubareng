<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Enums\NationalHolidayKind;

/** One national holiday or cuti bersama date as every calendar view shows it (D-253). */
final readonly class HolidayDay
{
    public function __construct(
        public string $date,
        public string $name,
        public NationalHolidayKind $kind,
        public bool $isProvisional,
    ) {}

    /**
     * @return array{date: string, name: string, kind: string, kind_label: string, is_provisional: bool}
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'name' => $this->name,
            'kind' => $this->kind->value,
            'kind_label' => $this->kind->label(),
            'is_provisional' => $this->isProvisional,
        ];
    }
}
