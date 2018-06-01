<?php

namespace Belt\Elastic\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Elastic\Modifiers\PaginationQueryModifier;

class NeedleQueryModifier extends PaginationQueryModifier
{
    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {
        if ($needle = $this->needle($request)) {
            $this->engine->query['bool']['should'][]['multi_match'] = [
                'query' => $needle,
                'fields' => ['name^10', 'meta_title^5', 'meta_keywords^5', 'meta_description^5', 'searchable'],
                'type' => 'best_fields',
                'tie_breaker' => 0.3,
            ];
            //$this->engine->query['bool']['should'][]['wildcard'] = [
            //    'name' => "*$needle*",
            //];
            $this->engine->query['bool']['should'][]['wildcard'] = [
                'name' => [
                    'wildcard' => "*$needle*",
                    'boost' => 2 * strlen($needle),
                ]
            ];
        }
    }

    /**
     * @param PaginateRequest $request
     * @return mixed|null|string|string[]
     */
    public function needle(PaginateRequest $request)
    {
        $needle = strtolower($request->needle());
        $needle = str_replace(["'", '"'], ' ', urldecode($needle));
        $needle = preg_replace('/\s+/', ' ', $needle);
        $needle = preg_replace("/[^0-9a-zA-Z ]/", '', $needle);

        return trim($needle);
    }
}