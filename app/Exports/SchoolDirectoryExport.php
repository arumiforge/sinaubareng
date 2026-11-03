<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\SchoolDirectoryEntry;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * The Sebaran sekolah list as filtered on screen (XP-4), for marketing (D-170).
 *
 * @implements WithMapping<SchoolDirectoryEntry>
 */
final class SchoolDirectoryExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithCustomValueBinder, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private const COLUMNS = ['npsn', 'school_name', 'level', 'regency', 'district', 'village', 'address', 'is_customer', 'latitude', 'longitude'];

    /**
     * @param  Builder<SchoolDirectoryEntry>  $query  with `region` loaded and `school_exists` selected
     */
    public function __construct(private readonly Builder $query) {}

    /**
     * @return Builder<SchoolDirectoryEntry>
     */
    public function query(): Builder
    {
        return $this->query;
    }

    public function title(): string
    {
        return __('export.sheets.school_directory');
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return array_map(fn (string $column): string => __("export.columns.{$column}"), self::COLUMNS);
    }

    /**
     * @param  SchoolDirectoryEntry  $row
     * @return list<string|float|null>
     */
    public function map($row): array
    {
        return [
            $row->npsn,
            $row->name,
            $row->level->label(),
            $row->region->regency,
            $row->district,
            $row->village,
            $row->address,
            __($row->getAttribute('school_exists') ? 'export.values.yes' : 'export.values.no'),
            $row->latitude,
            $row->longitude,
        ];
    }

    /**
     * Text stays text (NPSN keeps its digits, a name starting with "=" is no formula: XP-21, XP-23).
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * @return array<int, array<string, array<string, bool>>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    /**
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
            },
        ];
    }
}
