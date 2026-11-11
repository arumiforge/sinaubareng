<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

use App\Actions\Timetable\SaveTimetableSlot;
use App\Actions\Timetable\TimetableRules;
use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TimetableDraft;
use Illuminate\Support\Facades\DB;

/**
 * Puts a ready draft into the timetable (D-262): the unlocked rows of each class in the
 * draft go, the draft rows come in, and every row passes the same rules as a row typed
 * by hand (TimetableRules, D-248…D-251). One broken row stops the whole save.
 */
final readonly class SaveDraft
{
    public function __construct(
        private TimetableRules $rules,
        private SaveTimetableSlot $save,
    ) {}

    /**
     * @param  list<string>|null  $classIds  public ids to save, or all classes in the draft
     *
     * @throws DraftRefused
     */
    public function handle(TimetableDraft $draft, ?array $classIds = null): void
    {
        $results = collect($draft->result['classes'] ?? [])
            ->when($classIds !== null, fn ($items) => $items->whereIn('class', $classIds));

        DB::transaction(function () use ($draft, $results): void {
            $classes = SchoolClass::query()->where('academic_year_id', $draft->academic_year_id)->whereIn('public_id', $results->pluck('class'))->get()->keyBy('public_id');

            foreach ($classes as $class) {
                $class->timetableSlots()->where('is_locked', false)->delete();
            }

            $subjects = Subject::query()->pluck('id', 'code');

            foreach ($results as $result) {
                $class = $classes->get($result['class']);

                if ($class === null) {
                    continue;
                }

                // Breaks first, so lessons are checked against them; locked rows are already there.
                $rows = collect($result['rows'])->reject(fn (array $row): bool => (bool) $row['is_locked'])
                    ->sortBy(fn (array $row): int => $row['kind'] === TimetableSlotKind::Lesson->value ? 1 : 0);

                foreach ($rows as $row) {
                    $data = [
                        'weekday' => (int) $row['weekday'],
                        'starts_at' => (string) $row['starts_at'],
                        'ends_at' => (string) $row['ends_at'],
                        'kind' => (string) $row['kind'],
                        'subject' => $row['subject'],
                        'jp' => (int) $row['jp'],
                    ];
                    $broken = $this->rules->check($class, $data);

                    if ($broken !== null) {
                        throw new DraftRefused(__('timetable.draft.errors.row_refused', ['class' => $class->name, 'message' => $broken[1]]));
                    }

                    $this->save->handle($class, [
                        'weekday' => $data['weekday'],
                        'starts_at' => $data['starts_at'],
                        'ends_at' => $data['ends_at'],
                        'kind' => $data['kind'],
                        'subject_id' => $data['subject'] === null ? null : $subjects->get($data['subject']),
                        'label' => $row['label'] ?? null,
                        'jp' => $data['jp'],
                    ]);
                }
            }

            $draft->update(['status' => TimetableDraft::SAVED]);
        });
    }
}
