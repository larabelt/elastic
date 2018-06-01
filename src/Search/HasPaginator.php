<?php

namespace Belt\Elastic\Search;

/**
 * Class HasPaginator
 * @package Belt\Core\Pagination
 */
trait HasPaginator
{

    /**
     * @return mixed
     */
    public function getPaginatorClass()
    {
        return static::$paginatorClass;
    }

}