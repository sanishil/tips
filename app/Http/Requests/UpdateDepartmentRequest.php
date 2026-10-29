<?php

namespace App\Http\Requests;

/**
 * Edit a department: the same rules as adding one (its own name doesn't count as taken).
 */
class UpdateDepartmentRequest extends StoreDepartmentRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateDepartment';
}
