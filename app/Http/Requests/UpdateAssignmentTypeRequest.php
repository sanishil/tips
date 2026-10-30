<?php

namespace App\Http\Requests;

/**
 * Rename an assignment type: the same rules as adding one (its own name doesn't count as taken).
 */
class UpdateAssignmentTypeRequest extends StoreAssignmentTypeRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateAssignmentType';
}
