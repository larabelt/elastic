<?php

namespace Belt\Elastic\Http\Controllers\Api;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Http\Controllers\BaseController;
use Belt\Content\Search\Local\LocalSearchPaginator;
use Illuminate\Http\Request;
use Laravel\Scout\EngineManager;

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
        $default_engine = config('belt.search.default_engine', 'local');

        $engine = $request->get('engine', $default_engine);

        $paginatorClass = LocalSearchPaginator::class;

        if ($engine != 'local') {
            try {
                $driver = app(EngineManager::class)->driver($engine);
                $paginatorClass = $driver->getPaginatorClass();
            } catch (\Exception $e) {
                abort(404);
            }
        }

        $request = PaginateRequest::extend($request);

        $request->merge(['is_active' => true, 'is_searchable' => true]);

        $paginator = new $paginatorClass(null, $request);

        return response()->json($paginator->build()->toArray());
    }

}
