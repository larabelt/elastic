<?php

namespace Belt\Elastic\Search\Mock;

use Belt\Content\Search;
use Belt\Core\Http\Requests\PaginateRequest;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MockEngine
 * @package Belt\Content
 * @codeCoverageIgnore
 */
class MockEngine implements Search\HasPaginatorInterface
{

    use Search\HasPaginator;

    public static $paginatorClass = MockSearchPaginator::class;

    /**
     * Use request to set various value
     *
     * @param PaginateRequest $request
     */
    public function setRequest(PaginateRequest $request)
    {

    }

    /**
     * Perform the given search on the engine.
     *
     * @param array $options
     * @return mixed
     */
    public function performSearch(array $options = [])
    {
        return [];
    }

    /**
     * @param $results
     * @return Collection
     */
    public function morphResults($results)
    {
        return new Collection();
    }


}