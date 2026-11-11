<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CalendarEventKind;
use App\Enums\CalendarStatus;
use App\Enums\ExamType;
use App\Models\CalendarEvent;
use App\Models\Region;
use App\Models\RegionCalendar;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Loads a region reference calendar such as
 * docs/data/calendar/region-jawa-tengah-2026-2027.json (D-256, D-257, D-323).
 * Idempotent by code: unchanged events keep their id, so schools that copied the
 * calendar only see real changes; any change raises the calendar's version
 * (RegionCalendar::TRACKED, CalendarEvent::booted).
 * A new calendar is `draft` until Tim Validasi signs it off in the console.
 */
#[Signature('calendar:import-region {file : JSON file, e.g. docs/data/calendar/region-jawa-tengah-2026-2027.json}')]
#[Description('Import a region reference calendar')]
final class ImportRegionCalendar extends Command
{
    public function handle(): int
    {
        $path = (string) $this->argument('file');
        $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;

        // A school day may come as a date or as {date, title, source_ref}.
        if (is_array($data) && is_array($data['joint_leave_school_days'] ?? null)) {
            $data['joint_leave_school_days'] = array_map(fn ($day) => is_array($day) ? ($day['date'] ?? null) : $day, $data['joint_leave_school_days']);
        }

        $validator = Validator::make(is_array($data) ? $data : [], [
            'dataset' => ['required', Rule::in(['region_calendar'])],
            'code' => ['required', 'string', 'max:64'],
            'name' => ['nullable', 'string', 'max:255'],
            'region.province' => ['required', 'string', 'max:255'],
            'region.regency' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'school_days_per_week' => ['required', 'integer', 'between:5,6'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'even_semester_starts_on' => ['required', 'date_format:Y-m-d', 'after:starts_on'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after:even_semester_starts_on'],
            'events' => ['present', 'array'],
            'events.*.kind' => ['required', Rule::enum(CalendarEventKind::class)],
            'events.*.exam_type' => ['nullable', Rule::enum(ExamType::class)],
            'events.*.title' => ['required', 'string', 'max:120'],
            'events.*.starts_on' => ['required', 'date_format:Y-m-d'],
            'events.*.ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:events.*.starts_on'],
            'joint_leave_school_days' => ['sometimes', 'array'],
            'joint_leave_school_days.*' => ['date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            foreach (array_slice($validator->errors()->all(), 0, 10) as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        /** @var array<string, mixed> $data */
        $calendar = DB::transaction(fn (): RegionCalendar => $this->store($data));

        $this->info("Kalender {$calendar->code}: versi {$calendar->version}, {$calendar->events()->count()} acara, status {$calendar->status->value}.");

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function store(array $data): RegionCalendar
    {
        /** @var array{province: string, regency?: string|null} $owner */
        $owner = $data['region'];
        $region = Region::query()->firstOrCreate(['province' => $owner['province'], 'regency' => $owner['regency'] ?? null]);

        $calendar = RegionCalendar::query()->firstOrNew(['code' => $data['code']]);
        $calendar->fill([
            'region_id' => $region->id,
            'academic_year' => $data['academic_year'],
            'name' => $data['name'] ?? $data['code'],
            'school_days_per_week' => $data['school_days_per_week'],
            'starts_on' => $data['starts_on'],
            'even_semester_starts_on' => $data['even_semester_starts_on'],
            'ends_on' => $data['ends_on'],
            'joint_leave_school_days' => array_values($data['joint_leave_school_days'] ?? []),
            'meta' => ['source' => $data['source'] ?? null, 'expected' => $data['expected'] ?? null],
        ]);
        $calendar->status ??= CalendarStatus::Draft;
        $calendar->save();

        $kept = [];

        /** @var list<array{kind: string, exam_type?: string|null, title: string, starts_on: string, ends_on: string}> $events */
        $events = $data['events'];

        foreach ($events as $row) {
            $examType = $row['kind'] === CalendarEventKind::Exam->value ? ($row['exam_type'] ?? ExamType::Other->value) : null;
            $same = CalendarEvent::query()
                ->where('calendar_id', $calendar->id)
                ->where('kind', $row['kind'])
                ->where('title', $row['title'])
                ->whereDate('starts_on', $row['starts_on'])
                ->whereDate('ends_on', $row['ends_on'])
                ->when($examType, fn ($query, string $type) => $query->where('exam_type', $type), fn ($query) => $query->whereNull('exam_type'))
                ->whereNotIn('id', $kept)
                ->value('id');

            // Saving or deleting a region event raises the calendar's version (CalendarEvent::booted).
            $kept[] = $same ?? $calendar->events()->create([
                'kind' => $row['kind'],
                'exam_type' => $examType,
                'title' => $row['title'],
                'starts_on' => $row['starts_on'],
                'ends_on' => $row['ends_on'],
            ])->id;
        }

        CalendarEvent::query()->where('calendar_id', $calendar->id)->whereNotIn('id', $kept)->get()->each(fn (CalendarEvent $event) => $event->delete());

        return $calendar->refresh();
    }
}
