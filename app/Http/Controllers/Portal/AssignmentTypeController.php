<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentTypeRequest;
use App\Http\Requests\UpdateAssignmentTypeRequest;
use App\Models\AssignmentType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Assignment types, managed from both portals (/admin/assignment-types and /tech/assignment-types).
 * Students pick one under "This is a…" on the cover page.
 */
class AssignmentTypeController extends Controller
{
    /**
     * Sorted naturally, so "Assignment 2" comes before "Assignment 10".
     */
    public function index(Request $request): View
    {
        return view('portal.assignment-types', [
            'portal' => $request->user()->role->value,
            'assignmentTypes' => AssignmentType::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
        ]);
    }

    public function store(StoreAssignmentTypeRequest $request): RedirectResponse
    {
        $assignmentType = AssignmentType::create($request->validated());

        return $this->backToList($request, "“{$assignmentType->name}” added.");
    }

    public function update(UpdateAssignmentTypeRequest $request, AssignmentType $assignmentType): RedirectResponse
    {
        $assignmentType->update($request->validated());

        return $this->backToList($request, "“{$assignmentType->name}” saved.");
    }

    public function destroy(Request $request, AssignmentType $assignmentType): RedirectResponse
    {
        $assignmentType->delete();

        return $this->backToList($request, "“{$assignmentType->name}” deleted.");
    }

    private function backToList(Request $request, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.assignment-types')->with('status', $message);
    }
}
