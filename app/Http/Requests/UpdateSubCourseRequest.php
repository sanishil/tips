<?php

namespace App\Http\Requests;

/**
 * Edit a sub-course: the same rules as adding one (its own name doesn't count as taken).
 */
class UpdateSubCourseRequest extends StoreSubCourseRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateSubCourse';
}
