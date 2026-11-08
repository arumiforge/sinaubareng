<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\SchoolYear\AssignTeacher;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\AssignTeacherRequest;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;

/**
 * Kelas > a class > Guru per mata pelajaran: the operator reassigns any subject (D-032).
 */
final class TeachingAssignmentController extends Controller
{
    public function update(AssignTeacherRequest $request, SchoolClass $class, AssignTeacher $assign): RedirectResponse
    {
        /** @var Subject $subject validated in the request */
        $subject = $request->subject();
        $assign->handle($class, $subject, $request->teacherId());

        return to_route('classes.show', $class)->with('toast', __('classes.assignments.toasts.saved', ['subject' => $subject->name]));
    }
}
