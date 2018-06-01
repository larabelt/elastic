<?php

namespace Belt\Elastic\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Elastic\ElasticEngine;

/**
 * Class PaginationQueryModifier
 * @package Belt\Content\Elastic\Modifiers
 */
abstract class PaginationQueryModifier
{

    /**
     * @var ElasticEngine
     */
    public $engine;

    public function __construct(ElasticEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Modify query
     *
     * @param PaginateRequest $request
     * @return mixed
     */
    abstract public function modify(PaginateRequest $request);

}