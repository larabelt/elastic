<?php

namespace Belt\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Elastic\Modifiers\PaginationQueryModifier;
use Belt\Core\Helpers;

class IdSortModifier extends PaginationQueryModifier
{
    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {

        if ($orderBy = $request->get('orderBy')) {
            $sortHelper = new Helpers\SortHelper($orderBy);
            if ($sort = $sortHelper->getByColumn('id')) {
                $this->engine->sort['id'] = [
                    'order' => $sort->dir,
                ];
            }
        }

    }
}