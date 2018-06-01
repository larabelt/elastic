<?php

namespace Belt\Elastic\Search\Mock;

use Belt\Core\Pagination\BaseLengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class MockSearchPaginator
 * @package Belt\Core
 * @codeCoverageIgnore
 */
class MockSearchPaginator extends BaseLengthAwarePaginator
{

    /**
     * Build pagination query.
     *
     * @return MockSearchPaginator
     */
    public function build()
    {
        $paginator = new LengthAwarePaginator([],0,10,1);

        $this->setPaginator($paginator);

        return $this;
    }

}