<?php

declare(strict_types=1);

namespace App\Jobs\Timetable;

use App\Actions\Timetable\Draft\BuildDraftPlans;
use App\Actions\Timetable\Draft\DraftGenerator;
use App\Models\SchoolClass;
use App\Models\TimetableDraft;
use App\Models\TimetableSetting;
use App\Tenancy\SchoolContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Makes a draft timetable on the `default` queue (D-262), inside the draft's school.
 */
final class GenerateTimetableDraft implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 120;

    public function __construct(public int $draftId, public int $schoolId)
    {
        $this->onQueue('default');
    }

    public function handle(SchoolContext $context, BuildDraftPlans $build, DraftGenerator $generator): void
    {
        $context->runFor($this->schoolId, function () use ($build, $generator): void {
            $draft = TimetableDraft::query()->with('academicYear')->find($this->draftId);

            if ($draft === null || $draft->status !== TimetableDraft::QUEUED) {
                return;
            }

            $draft->update(['status' => TimetableDraft::RUNNING]);

            try {
                $classes = SchoolClass::query()->whereBelongsTo($draft->academicYear)
                    ->when($draft->class_id, fn ($query, int $id) => $query->whereKey($id))
                    ->orderBy('grade')->orderBy('name')
                    ->get();
                $settings = TimetableSetting::current();
                ['plans' => $plans, 'busy' => $busy] = $build->handle($draft->academicYear, $classes, $settings->frame());

                $result = $generator->generate($plans, $busy, $settings->switches(), $draft->seed, (int) config('timetable.attempts', 30));
                $draft->update(['status' => TimetableDraft::READY, 'result' => $result]);
            } catch (Throwable $exception) {
                $draft->update(['status' => TimetableDraft::FAILED]);

                throw $exception;
            }
        });
    }
}
