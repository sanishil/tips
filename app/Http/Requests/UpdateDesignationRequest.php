<?php

namespace App\Http\Requests;

/**
 * Rename a designation: the same rules as adding one (its own name doesn't count as taken).
 */
class UpdateDesignationRequest extends StoreDesignationRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'updateDesignation';
}
