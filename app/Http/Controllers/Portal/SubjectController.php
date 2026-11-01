<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Subjects, managed from both portals (/admin/subjects and /tech/subjects). Each is taught by a
 * department in one semester.
 */
class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.subjects', [
            'portal' => $request->user()->role->value,
            'subjects' => Subject::with('department')->orderBy('code')->get(),
            'departments' => Department::orderBy('dept_name')->get(['id', 'dept_name']),
        ]);
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $subject = Subject::create($request->validated());

        return $this->backToList($request, "“{$subject->name}” added.");
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return $this->backToList($request, "“{$subject->name}” saved.");
    }

    public function destroy(Request $request, Subject $subject): RedirectResponse
    {
        $subject->delete();

        return $this->backToList($request, "“{$subject->name}” deleted.");
    }

    private function backToList(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.subjects')->with('status', $message);
    }
}
