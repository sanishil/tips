<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Departments, managed from both portals (/admin/departments and /tech/departments).
 * The role middleware on those routes guarantees the user's role matches the portal.
 */
class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.departments', [
            'portal' => $request->user()->role->value,
            'departments' => Department::orderBy('dept_name')->get(),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return $this->backToList($request, "“{$department->dept_name}” added.");
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return $this->backToList($request, "“{$department->dept_name}” saved.");
    }

    public function destroy(Request $request, Department $department): RedirectResponse
    {
        $department->delete();

        return $this->backToList($request, "“{$department->dept_name}” deleted.");
    }

    private function backToList(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.departments')->with('status', $message);
    }
}
