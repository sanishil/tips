<?php

namespace App\Http\Requests;

/**
 * Edit a subject: the same rules as adding one (its own name and code don't count as taken).
 */
class UpdateSubjectRequest extends StoreSubjectRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateSubject';
}
