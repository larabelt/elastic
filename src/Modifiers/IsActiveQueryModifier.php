<?php

namespace Belt\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Elastic\Modifiers\PaginationQueryModifier;

class IsActiveQueryModifier extends PaginationQueryModifier
{
    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {
        if ($request->query->has('is_active')) {
            $is_active = $request->query->get('is_active') ? true : false;
            $this->engine->query['bool']['must'][]['terms'] = [
                'is_active' => [$is_active],
            ];
        }
    }
}