<?php

namespace Belt\Elastic\Http\Controllers\Web;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Http\Controllers\BaseController;
use Belt\Core\Pagination\DefaultLengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class SearchController
 * @package Belt\Core\Http\Controllers\Auth
 */
class SearchController extends BaseController
{

    /**
     * Show search results
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $request = PaginateRequest::extend($request);

        $classes = config('belt.search.classes');

        /**
         * @var $pager LengthAwarePaginator
         */
        $pager = null;
        $paginators = new Collection();
        foreach ($classes as $modelClass => $paginateClass) {
            $builder = new DefaultLengthAwarePaginator($modelClass::query(), new $paginateClass($request->all()));
            $builder->build();
            if ($builder && $builder->paginator) {
                $paginators->push($builder->paginator);
                if (!$pager || $builder->paginator->lastPage() > $pager->lastPage()) {
                    $pager = $builder->paginator;
                    $pager->withPath('search');
                }
            }
        }

        return view('belt-content::search.web.index', compact('paginators', 'pager'));
    }

}
