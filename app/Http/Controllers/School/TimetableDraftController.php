<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\SchoolYear\FindCurrentAcademicYear;
use App\Actions\Timetable\ClassTimetable;
use App\Actions\Timetable\Draft\DayFrame;
use App\Actions\Timetable\Draft\DraftRefused;
use App\Actions\Timetable\Draft\Preferences;
use App\Actions\Timetable\Draft\SaveDraft;
use App\Http\Controllers\Controller;
use App\Jobs\Timetable\GenerateTimetableDraft;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\TimetableDraft;
use App\Models\TimetableSetting;
use App\Tenancy\SchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Operator portal > Kelas > Susun draf jadwal (D-262): the day frame and preference
 * switches, a draft for one class or all made on the queue, a comparison with the
 * current timetable, the reasons for unplaced JP, Acak ulang, and saving.
 */
final class TimetableDraftController extends Controller
{
    public function show(Request $request, FindCurrentAcademicYear $findCurrent, ClassTimetable $timetable): Response
    {
        Gate::authorize('arrangeTimetable', SchoolClass::class);

        $years = AcademicYear::query()->orderByDesc('starts_on')->get();
        $year = $request->filled('tahun')
            ? $years->firstWhere('public_id', $request->string('tahun')->toString()) ?? abort(404)
            : $findCurrent->handle();
        $settings = TimetableSetting::current();
        $classes = $year === null ? collect() : SchoolClass::query()->whereBelongsTo($year)->orderBy('grade')->orderBy('name')->get();
        $draft = $year === null ? null : TimetableDraft::query()->whereBelongsTo($year)->latest('id')->with('schoolClass:id,public_id')->first();
        $draftClasses = collect($draft?->result['classes'] ?? [])->pluck('class')->all();

        return Inertia::render('School/Timetable/Draft', [
            'year' => $year === null ? null : ['id' => $year->public_id, 'name' => $year->name],
            'years' => $years->map(fn (AcademicYear $item): array => ['value' => $item->public_id, 'label' => $item->name])->values()->all(),
            'classes' => $classes->map(fn (SchoolClass $class): array => ['value' => $class->public_id, 'label' => $class->name])->values()->all(),
            'frame' => $settings->frame()->toArray(),
            'preferences' => $settings->switches()->switches,
            'draft' => $draft === null ? null : [
                'id' => $draft->public_id,
                'status' => $draft->status,
                'class' => $draft->schoolClass?->public_id,
                'unplaced_jp' => $draft->result['unplaced_jp'] ?? 0,
                'classes' => collect($draft->result['classes'] ?? [])->map(fn (array $item): array => [
                    ...$item,
                    'rows' => array_map(fn (array $row): array => [...$row, 'starts_at' => substr($row['starts_at'], 0, 5), 'ends_at' => substr($row['ends_at'], 0, 5)], $item['rows']),
                ])->all(),
            ],
            // Comparison: the current timetable of each class in the draft.
            'current' => $classes->whereIn('public_id', $draftClasses)
                ->mapWithKeys(fn (SchoolClass $class): array => [$class->public_id => $timetable->handle($class, false)['rows']])
                ->all(),
        ]);
    }

    public function settings(Request $request): RedirectResponse
    {
        Gate::authorize('arrangeTimetable', SchoolClass::class);

        $valid = $request->validate([
            'frame.days' => ['required', 'array'],
            'frame.days.*.starts_at' => ['required', 'date_format:H:i'],
            'frame.days.*.periods' => ['required', 'integer', 'between:0,12'],
            'frame.breaks' => ['present', 'array', 'max:4'],
            'frame.breaks.*.after_period' => ['required', 'integer', 'between:1,11'],
            'frame.breaks.*.minutes' => ['required', 'integer', 'between:5,60'],
            'frame.breaks.*.label' => ['nullable', 'string', 'max:40'],
            'preferences' => ['present', 'array'],
            'preferences.*' => ['boolean'],
        ], [], ['frame.days.*.periods' => __('timetable.draft.fields.periods')]);

        foreach (array_keys($valid['frame']['days']) as $weekday) {
            if (! in_array((int) $weekday, range(1, 6), true)) {
                throw ValidationException::withMessages(['frame.days' => __('timetable.draft.errors.weekday')]);
            }
        }

        TimetableSetting::query()->updateOrCreate([], [
            'day_frame' => DayFrame::fromArray($valid['frame'])->toArray(),
            'preferences' => Preferences::fromArray($valid['preferences'])->switches,
        ]);

        return back()->with('toast', __('timetable.draft.toasts.settings_saved'));
    }

    /** "Susun draf jadwal" and "Acak ulang": a new seed each time. */
    public function store(Request $request, SchoolContext $context): RedirectResponse
    {
        Gate::authorize('arrangeTimetable', SchoolClass::class);

        $request->validate(['tahun' => ['required', 'string'], 'kelas' => ['nullable', 'string']]);
        $year = AcademicYear::query()->where('public_id', $request->string('tahun')->toString())->firstOrFail();
        $class = $request->filled('kelas')
            ? SchoolClass::query()->whereBelongsTo($year)->where('public_id', $request->string('kelas')->toString())->firstOrFail()
            : null;

        $draft = TimetableDraft::query()->create([
            'academic_year_id' => $year->id,
            'class_id' => $class?->id,
            'created_by' => $request->user()?->id,
            'seed' => random_int(1, 2_000_000_000),
            'status' => TimetableDraft::QUEUED,
        ]);

        GenerateTimetableDraft::dispatch($draft->id, (int) $context->schoolId());

        return to_route('timetable.draft.show', ['tahun' => $year->public_id])->with('toast', __('timetable.draft.toasts.queued'));
    }

    public function save(Request $request, TimetableDraft $draft, SaveDraft $save): RedirectResponse
    {
        Gate::authorize('arrangeTimetable', SchoolClass::class);

        $request->validate(['classes' => ['nullable', 'array'], 'classes.*' => ['string']]);

        if ($draft->status !== TimetableDraft::READY) {
            throw ValidationException::withMessages(['draft' => __('timetable.draft.errors.not_ready')]);
        }

        try {
            $save->handle($draft, $request->filled('classes') ? array_values($request->input('classes')) : null);
        } catch (DraftRefused $refused) {
            throw ValidationException::withMessages(['draft' => $refused->getMessage()]);
        }

        return to_route('timetable.draft.show', ['tahun' => $draft->loadMissing('academicYear')->academicYear->public_id])->with('toast', __('timetable.draft.toasts.saved'));
    }
}
