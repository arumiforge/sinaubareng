<?php

declare(strict_types=1);

namespace App\Calendar;

/** One line from the conflict checker: refused, or a warning the user may save past (D-260). */
final readonly class Conflict
{
    public const string ERROR = 'error';

    public const string WARNING = 'warning';

    public function __construct(public string $level, public string $message) {}

    public function isError(): bool
    {
        return $this->level === self::ERROR;
    }

    /**
     * @return array{level: string, message: string}
     */
    public function toArray(): array
    {
        return ['level' => $this->level, 'message' => $this->message];
    }
}
