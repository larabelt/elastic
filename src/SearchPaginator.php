<?php

namespace Belt\Elastic;

use Belt\Core\Pagination\BaseLengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Scout\EngineManager;

/**
 * Class DefaultLengthAwarePaginator
 * @package Belt\Core\Pagination
 */
class SearchPaginator extends BaseLengthAwarePaginator
{

    /**
     * Build pagination query.
     *
     * @return SearchPaginator
     */
    public function build()
    {

        /**
         * @var $engine \Belt\Elastic\Engine
         */
        $engine = app(EngineManager::class)->driver('elastic');

        # request
        $request = $this->request;
        $engine->setRequest($request);

        # include / types
        if ($include = $request->get('include', [])) {
            $include = explode(',', $include);
        }

        # class / config
        foreach (config('belt.search.classes') as $modelClass => $paginateClass) {
            $morphKey = (new $modelClass)->getMorphClass();
            if ($include && !in_array($morphKey, $include)) {
                continue;
            }
        }

        # execute search
        $results = $engine->performSearch();

        # morph results
        $items = $engine->morphResults($results);

        # create paginator
        $paginator = new LengthAwarePaginator(
            //$items->toArray(),
            $items,
            array_get($results, 'hits.total', $items->count()),
            $request->perPage(),
            $request->page()
        );

        $this->setPaginator($paginator);

        return $this;
    }

}