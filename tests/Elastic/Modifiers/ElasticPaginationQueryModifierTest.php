<?php

use Mockery as m;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\NeedleQueryModifier;

class ElasticPaginationQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\Modifiers\PaginationQueryModifier::__construct
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new NeedleQueryModifier($engine);
        $this->assertEquals($engine, $modifier->engine);
    }

}