<?php

namespace Belt\Elastic\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Elastic\Modifiers\PaginationQueryModifier;
use Belt\Core\Helpers;

class TimestampsSortModifier extends PaginationQueryModifier
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

            # created_at
            if ($sort = $sortHelper->getByColumn('created_at')) {
                $this->engine->sort['created_at'] = [
                    'unmapped_type' => 'float',
                    'order' => $sort->dir,
                ];
            }

            # updated_at
            if ($sort = $sortHelper->getByColumn('updated_at')) {
                $this->engine->sort['updated_at'] = [
                    'unmapped_type' => 'float',
                    'order' => $sort->dir,
                ];
            }
        }

    }
}