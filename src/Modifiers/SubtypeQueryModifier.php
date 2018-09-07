<?php

namespace Belt\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Elastic\Modifiers\PaginationQueryModifier;

class SubtypeQueryModifier extends PaginationQueryModifier
{
    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {
        if ($request->query->has('subtype')) {
            $subtype = $request->query->get('subtype', 'default');
            $this->engine->query['bool']['must'][]['terms'] = [
                //'subtype' => [$subtype],
                'subtype' => explode(',', $subtype),
            ];
        }
    }
}