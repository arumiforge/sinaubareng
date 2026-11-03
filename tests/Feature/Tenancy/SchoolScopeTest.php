<?php

declare(strict_types=1);

use App\Models\School;
use App\Models\SchoolMembership;
use App\Tenancy\SchoolContext;
use Illuminate\Database\QueryException;

beforeEach(function (): void {
    $this->schoolA = School::factory()->create();
    $this->schoolB = School::factory()->create();
    SchoolMembership::factory()->count(2)->for($this->schoolA)->create();
    SchoolMembership::factory()->for($this->schoolB)->create();
});

it('returns nothing without a school in the context (fail-closed)', function (): void {
    expect(SchoolMembership::query()->count())->toBe(0)
        ->and(SchoolMembership::query()->find(SchoolMembership::withoutSchoolScope()->value('id')))->toBeNull();
});

it('returns only the current school\'s rows', function (): void {
    app(SchoolContext::class)->set($this->schoolA->id);

    expect(SchoolMembership::query()->pluck('school_id')->unique()->all())->toBe([$this->schoolA->id])
        ->and(SchoolMembership::query()->count())->toBe(2);
});

it('hides another school\'s row by id', function (): void {
    $foreign = SchoolMembership::withoutSchoolScope()->whereBelongsTo($this->schoolB)->firstOrFail();
    app(SchoolContext::class)->set($this->schoolA->id);

    expect(SchoolMembership::query()->find($foreign->id))->toBeNull();
});

it('fills the current school on create and refuses another school', function (): void {
    app(SchoolContext::class)->set($this->schoolA->id);

    $membership = SchoolMembership::factory()->make(['school_id' => null]);
    $membership->save();
    expect($membership->school_id)->toBe($this->schoolA->id);

    expect(fn () => SchoolMembership::factory()->for($this->schoolB)->create())->toThrow(LogicException::class);
});

it('cannot create a row without any school', function (): void {
    expect(fn () => SchoolMembership::factory()->make(['school_id' => null])->save())
        ->toThrow(QueryException::class);
});

it('restores the previous context after runFor', function (): void {
    $context = app(SchoolContext::class);
    $context->set($this->schoolA->id);

    $count = $context->runFor($this->schoolB->id, fn (): int => SchoolMembership::query()->count());

    expect($count)->toBe(1)->and($context->schoolId())->toBe($this->schoolA->id);
});
