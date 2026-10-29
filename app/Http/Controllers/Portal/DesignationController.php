<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Designation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Designations, managed from both portals (/admin/designations and /tech/designations).
 * Teachers pick theirs from this list.
 */
class DesignationController extends Controller
{
    public function index(Request $request): View
    {
        return view('portal.designations', [
            'portal' => $request->user()->role->value,
            'designations' => Designation::withCount('teachers')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDesignationRequest $request): RedirectResponse
    {
        $designation = Designation::create($request->validated());

        return $this->backToList($request, 'status', "“{$designation->name}” added.");
    }

    public function update(UpdateDesignationRequest $request, Designation $designation): RedirectResponse
    {
        $designation->update($request->validated());

        return $this->backToList($request, 'status', "“{$designation->name}” saved.");
    }

    /**
     * A designation still held by teachers can't go (the database refuses too); say why instead.
     */
    public function destroy(Request $request, Designation $designation): RedirectResponse
    {
        $teacherCount = $designation->teachers()->count();

        if ($teacherCount > 0) {
            return $this->backToList(
                $request,
                'error',
                "“{$designation->name}” is used by {$teacherCount} ".Str::plural('teacher', $teacherCount).'. Change their designation first.',
            );
        }

        $designation->delete();

        return $this->backToList($request, 'status', "“{$designation->name}” deleted.");
    }

    private function backToList(Request $request, string $type, string $message): RedirectResponse
    {
        return redirect()->route($request->user()->role->value.'.designations')->with($type, $message);
    }
}
