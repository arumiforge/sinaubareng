<?php

declare(strict_types=1);

namespace App\Curriculum;

use App\Models\Subject;

/**
 * One subject a class of a school takes, and why.
 */
final readonly class SubjectOffer
{
    public const FROM_STRUCTURE = 'structure';

    public const FROM_REGION = 'region';

    public const FROM_SCHOOL = 'school';

    /**
     * @param  self::FROM_*  $source
     * @param  int|null  $jpPerWeek  NULL when no allocation sets it (optional content)
     */
    public function __construct(
        public Subject $subject,
        public string $source,
        public bool $inReport,
        public ?int $jpPerWeek,
    ) {}
}
