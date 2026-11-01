<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A TKA practice participant on the exam site's `exam` guard (D-027, D-094).
 * The `exam_participants` table and its sign-in come with the exam slice (P5-06).
 */
#[Fillable(['username', 'password'])]
#[Hidden(['password'])]
final class ExamParticipant extends Authenticatable {}
