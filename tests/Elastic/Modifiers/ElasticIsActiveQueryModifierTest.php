<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\IsActiveQueryModifier;

class ElasticIsActiveQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\Modifiers\IsActiveQueryModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new IsActiveQueryModifier($engine);

        $this->assertFalse(isset($engine->query['bool']['must'][0]['terms']['is_active']));
        $modifier->modify(new PaginateRequest(['is_active' => 'true']));
        $this->assertTrue(isset($engine->query['bool']['must'][0]['terms']['is_active']));
    }

}