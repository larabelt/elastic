<?php

namespace Belt\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Elastic\Modifiers\PaginationQueryModifier;

class TemplateQueryModifier extends PaginationQueryModifier
{
    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {
        if ($request->query->has('template')) {
            $template = $request->query->get('template', 'default');
            $this->engine->query['bool']['must'][]['terms'] = [
                'template' => [$template],
            ];
        }
    }
}