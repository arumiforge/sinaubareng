<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use RuntimeException;

/**
 * The file cannot be imported as it is. Nothing from it is written.
 */
final class InvalidCurriculumFile extends RuntimeException {}
