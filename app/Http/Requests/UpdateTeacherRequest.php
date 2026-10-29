<?php

namespace App\Http\Requests;

/**
 * Edit a teacher: the same rules as adding one (their own name doesn't count as taken).
 */
class UpdateTeacherRequest extends StoreTeacherRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateTeacher';
}
