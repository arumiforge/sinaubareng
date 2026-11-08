<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use RuntimeException;

/** A class copy that cannot run; the message is shown to the operator (D-246). */
final class CopyClassesRefused extends RuntimeException {}
