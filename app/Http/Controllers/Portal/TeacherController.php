<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Teachers, managed from both portals (/admin/teachers and /tech/teachers). Each belongs to a
 * department and has a designation, both picked from their own tables.
 */
class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.teachers', [
            'portal' => $request->user()->role->value,
            'teachers' => Teacher::with(['department', 'designation'])->orderBy('name')->get(),
            'departments' => Department::orderBy('dept_name')->get(['id', 'dept_name']),
            'designations' => Designation::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $teacher = Teacher::create($request->validated());

        return $this->backToList($request, "“{$teacher->name}” added.");
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $teacher->update($request->validated());

        return $this->backToList($request, "“{$teacher->name}” saved.");
    }

    public function destroy(Request $request, Teacher $teacher): RedirectResponse
    {
        $teacher->delete();

        return $this->backToList($request, "“{$teacher->name}” removed.");
    }

    private function backToList(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.teachers')->with('status', $message);
    }
}
