<?php

namespace App\Http\Requests;

/**
 * Edit a course: the same rules as adding one (its own name doesn't count as taken).
 */
class UpdateCourseRequest extends StoreCourseRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateCourse';
}
