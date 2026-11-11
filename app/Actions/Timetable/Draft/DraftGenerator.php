<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * "Susun draf jadwal" (D-262): places each class's JP into the free cells of the day
 * frame. Hard limits are never broken: no two lessons of a class at once except the
 * religion group (D-251), no teacher in two classes at once (D-248), only subjects with a
 * teacher (D-249), never more JP than the allocation (D-250), locked rows untouched.
 * Soft preferences only change the score. Several shuffled attempts run and the best
 * draft wins: fewest unplaced JP first, then the lowest score. Pure: the same input and
 * seed give the same draft.
 */
final class DraftGenerator
{
    private const int UNPLACED = 1000;

    private Randomizer $random;

    /**
     * @param  list<ClassPlan>  $plans
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy  teacher id => [weekday, starts_at, ends_at] taken outside the plans
     * @return array{classes: list<array{class: string, name: string, rows: list<array<string, mixed>>, unplaced: list<array{subject: string, jp: int, reason: string}>}>, unplaced_jp: int, score: int}
     */
    public function generate(array $plans, array $busy, Preferences $preferences, int $seed, int $attempts): array
    {
        $this->random = new Randomizer(new Mt19937($seed));
        $best = null;

        for ($attempt = 0; $attempt < max(1, $attempts); $attempt++) {
            $result = $this->attempt($plans, $busy, $preferences);

            if ($best === null || [$result['unplaced_jp'], $result['score']] < [$best['unplaced_jp'], $best['score']]) {
                $best = $result;
            }

            if ($best['unplaced_jp'] === 0 && $best['score'] === 0) {
                break;
            }
        }

        return $best;
    }

    /**
     * @param  list<ClassPlan>  $plans
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy
     * @return array{classes: list<array{class: string, name: string, rows: list<array<string, mixed>>, unplaced: list<array{subject: string, jp: int, reason: string}>}>, unplaced_jp: int, score: int}
     */
    private function attempt(array $plans, array $busy, Preferences $preferences): array
    {
        $order = $this->random->shuffleArray(array_keys($plans));
        $classes = [];
        $unplacedJp = 0;
        $score = 0;

        foreach ($order as $i) {
            $plan = $plans[$i];
            [$rows, $unplaced, $penalty] = $this->placeClass($plan, $busy, $preferences);
            $classes[$i] = ['class' => $plan->publicId, 'name' => $plan->name, 'rows' => $rows, 'unplaced' => [...$plan->unplaceable, ...$unplaced]];
            $unplacedJp += array_sum(array_column($unplaced, 'jp'));
            $score += $penalty;
        }

        ksort($classes);

        return ['classes' => array_values($classes), 'unplaced_jp' => $unplacedJp, 'score' => $unplacedJp * self::UNPLACED + $score];
    }

    /**
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy  extended with this class's lessons
     * @return array{0: list<array<string, mixed>>, 1: list<array{subject: string, jp: int, reason: string}>, 2: int}
     */
    private function placeClass(ClassPlan $plan, array &$busy, Preferences $preferences): array
    {
        /** @var array<string, Cell> $free */
        $free = [];
        foreach ($plan->cells as $cell) {
            $free[$cell->key()] = $cell;
        }

        /** @var array<int, list<Cell>> $days */
        $days = [];
        foreach ($plan->cells as $cell) {
            $days[$cell->weekday][] = $cell;
        }

        $taken = []; // cell key => demand key
        $rows = [];
        $unplaced = [];
        $penalty = 0;

        $blocks = [];
        foreach ($plan->demands as $demand) {
            foreach ($this->blocks($demand, $preferences) as $size) {
                $blocks[] = [$demand, $size, $this->random->getInt(0, 999)];
            }
        }

        // Religion groups and big blocks first; the random key breaks ties differently per attempt.
        usort($blocks, fn (array $a, array $b): int => [count($b[0]->subjects), $b[1], $a[2]] <=> [count($a[0]->subjects), $a[1], $b[2]]);

        foreach ($blocks as [$demand, $size]) {
            $placed = $this->placeBlock($demand, $size, $days, $free, $taken, $busy, $preferences);

            if ($placed !== null) {
                [$cells, $cost] = $placed;
                $penalty += $cost;
                $rows = [...$rows, ...$this->rowsFor($demand, $cells)];

                continue;
            }

            // Too big for any gap: try one JP at a time before giving up.
            for ($jp = 0; $jp < $size; $jp++) {
                $single = $size > 1 ? $this->placeBlock($demand, 1, $days, $free, $taken, $busy, $preferences) : null;

                if ($single !== null) {
                    $penalty += $single[1];
                    $rows = [...$rows, ...$this->rowsFor($demand, $single[0])];

                    continue;
                }

                $unplaced[] = ['subject' => $demand->name(), 'jp' => 1, 'reason' => $this->reason($demand, $free, $taken, $busy)];
            }
        }

        return [[...$plan->fixedRows, ...$rows], $this->mergeUnplaced($unplaced), $penalty];
    }

    /**
     * The block sizes for a demand: balanced, at most 2 JP (3 for PJOK) with
     * `max_consecutive`, else up to 4 (the timetable's row limit).
     *
     * @return list<int>
     */
    private function blocks(Demand $demand, Preferences $preferences): array
    {
        $max = $preferences->on('max_consecutive') ? ($demand->isPjok ? 3 : 2) : 4;
        $count = (int) ceil($demand->jp / $max);
        $sizes = [];

        for ($i = 0; $i < $count; $i++) {
            $sizes[] = intdiv($demand->jp, $count) + ($i < $demand->jp % $count ? 1 : 0);
        }

        return $sizes;
    }

    /**
     * The cheapest place for a block of `$size` consecutive cells in one part of a day.
     *
     * @param  array<int, list<Cell>>  $days
     * @param  array<string, Cell>  $free
     * @param  array<string, string>  $taken
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy
     * @return array{0: list<Cell>, 1: int}|null
     */
    private function placeBlock(Demand $demand, int $size, array $days, array &$free, array &$taken, array &$busy, Preferences $preferences): ?array
    {
        $options = [];

        foreach ($days as $weekday => $cells) {
            $count = count($cells);

            for ($start = 0; $start + $size <= $count; $start++) {
                $run = array_slice($cells, $start, $size);

                if (! $this->fits($run, $demand, $free, $busy)) {
                    continue;
                }

                $options[] = [$run, $this->cost($demand, $run, $cells, $taken, $preferences), $this->random->getInt(0, 999)];
            }
        }

        if ($options === []) {
            return null;
        }

        usort($options, fn (array $a, array $b): int => [$a[1], $a[2]] <=> [$b[1], $b[2]]);
        [$run, $cost] = $options[0];

        foreach ($run as $cell) {
            unset($free[$cell->key()]);
            $taken[$cell->key()] = $demand->key;

            foreach ($demand->teacherIds() as $teacherId) {
                $busy[$teacherId][] = [$cell->weekday, $cell->startsAt, $cell->endsAt];
            }
        }

        return [$run, $cost];
    }

    /**
     * @param  list<Cell>  $run
     * @param  array<string, Cell>  $free
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy
     */
    private function fits(array $run, Demand $demand, array $free, array $busy): bool
    {
        $segment = $run[0]->segment;

        foreach ($run as $cell) {
            if (! isset($free[$cell->key()]) || $cell->segment !== $segment || ! $this->teachersFree($demand, $cell, $busy)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy
     */
    private function teachersFree(Demand $demand, Cell $cell, array $busy): bool
    {
        foreach ($demand->teacherIds() as $teacherId) {
            foreach ($busy[$teacherId] ?? [] as [$weekday, $startsAt, $endsAt]) {
                if ($weekday === $cell->weekday && $cell->overlaps($startsAt, $endsAt)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Soft preferences as a score; 0 is a perfect place (D-262).
     *
     * @param  list<Cell>  $run
     * @param  list<Cell>  $day
     * @param  array<string, string>  $taken
     */
    private function cost(Demand $demand, array $run, array $day, array $taken, Preferences $preferences): int
    {
        $cost = 0;
        $sameDay = count(array_filter($day, fn (Cell $cell): bool => ($taken[$cell->key()] ?? null) === $demand->key));

        if ($preferences->on('spread_week') && $sameDay > 0) {
            $cost += 10;
        }

        if ($preferences->on('max_consecutive') && $sameDay + count($run) > ($demand->isPjok ? 3 : 2)) {
            $cost += 20;
        }

        $last = $run[count($run) - 1];

        if ($preferences->on('pjok_before_10') && $demand->isPjok && substr($last->endsAt, 0, 5) > (string) config('timetable.pjok_ends_by', '10:00')) {
            $cost += 15;
        }

        if ($preferences->on('core_early') && $demand->isCore) {
            $cost += $run[0]->index - 1;
        }

        if ($preferences->on('no_split_break')) {
            $position = array_search($run[0], $day, true);
            $before = is_int($position) ? ($day[$position - 1] ?? null) : null;
            $after = is_int($position) ? ($day[$position + count($run)] ?? null) : null;

            foreach ([$before, $after] as $neighbour) {
                if ($neighbour !== null && $neighbour->segment !== $run[0]->segment && ($taken[$neighbour->key()] ?? null) === $demand->key) {
                    $cost += 8;
                }
            }
        }

        return $cost;
    }

    /**
     * Why a JP could not be placed, in the operator's words.
     *
     * @param  array<string, Cell>  $free
     * @param  array<string, string>  $taken
     * @param  array<int, list<array{0: int, 1: string, 2: string}>>  $busy
     */
    private function reason(Demand $demand, array $free, array $taken, array $busy): string
    {
        if ($free === []) {
            return __('timetable.draft.reasons.no_free_cells');
        }

        foreach ($free as $cell) {
            if ($this->teachersFree($demand, $cell, $busy)) {
                return __('timetable.draft.reasons.no_fit');
            }
        }

        return __('timetable.draft.reasons.teacher_busy', ['teachers' => implode(', ', array_unique(array_column($demand->subjects, 'teacher_name')))]);
    }

    /**
     * One lesson row per subject of the demand (a religion group gives parallel rows).
     *
     * @param  list<Cell>  $cells
     * @return list<array<string, mixed>>
     */
    private function rowsFor(Demand $demand, array $cells): array
    {
        $first = $cells[0];
        $last = $cells[count($cells) - 1];

        return array_map(fn (array $subject): array => [
            'weekday' => $first->weekday,
            'starts_at' => $first->startsAt,
            'ends_at' => $last->endsAt,
            'kind' => 'lesson',
            'subject' => $subject['code'],
            'subject_name' => $subject['name'],
            'teacher_name' => $subject['teacher_name'],
            'label' => null,
            'jp' => count($cells),
            'is_locked' => false,
        ], $demand->subjects);
    }

    /**
     * @param  list<array{subject: string, jp: int, reason: string}>  $unplaced
     * @return list<array{subject: string, jp: int, reason: string}>
     */
    private function mergeUnplaced(array $unplaced): array
    {
        $merged = [];

        foreach ($unplaced as $item) {
            $key = $item['subject'].'|'.$item['reason'];
            $merged[$key] = isset($merged[$key]) ? [...$item, 'jp' => $merged[$key]['jp'] + $item['jp']] : $item;
        }

        return array_values($merged);
    }
}
