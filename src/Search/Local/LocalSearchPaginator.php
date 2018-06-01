<?php

namespace Belt\Elastic\Search\Local;

use Belt\Core\Pagination\BaseLengthAwarePaginator;
use Belt\Core\Pagination\DefaultLengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class DefaultLengthAwarePaginator
 * @package Belt\Core\Pagination
 */
class LocalSearchPaginator extends BaseLengthAwarePaginator
{

    /**
     * Build pagination query.
     *
     * @return LocalSearchPaginator
     */
    public function build()
    {

        $request = $this->request;

        $classes = config('belt.search.classes');

        $include = $request->get('include') ? explode(',', $request->get('include')) : [];

        /**
         * @var $pager LengthAwarePaginator
         */
        $pager = null;
        $items = new Collection();
        foreach ($classes as $modelClass => $paginateClass) {

            $morphKey = (new $modelClass)->getMorphClass();

            if ($include && !in_array($morphKey, $include)) {
                continue;
            }

            $builder = new DefaultLengthAwarePaginator($modelClass::query(), new $paginateClass($request->all()));
            $builder->build();
            if ($builder && $builder->paginator) {
                foreach ($builder->paginator->items() as $item) {
                    $items->push($item);
                }
                if (!$pager || $builder->paginator->lastPage() > $pager->lastPage()) {
                    $pager = $builder->paginator;
                }
            }
        }

        $paginator = new LengthAwarePaginator(
            //$items->toArray(),
            $items,
            $pager ? $pager->total() : 0,
            $pager ? $pager->perPage() : $request->perPage(),
            $pager ? $pager->currentPage() : 1
        );

        $this->setPaginator($paginator);

        return $this;
    }

}