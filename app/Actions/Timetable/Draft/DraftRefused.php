<?php

declare(strict_types=1);

namespace App\Actions\Timetable\Draft;

use RuntimeException;

/** A draft row broke a timetable rule when it was saved; the message is for the operator. */
final class DraftRefused extends RuntimeException {}
