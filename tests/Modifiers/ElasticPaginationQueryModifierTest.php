<?php

use Mockery as m;
use Belt\Core\Testing\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Modifiers\NeedleQueryModifier;

class ElasticPaginationQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\PaginationQueryModifier::__construct
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new NeedleQueryModifier($engine);
        $this->assertEquals($engine, $modifier->engine);
    }

}