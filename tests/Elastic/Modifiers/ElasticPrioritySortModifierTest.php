<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\PrioritySortModifier;

class ElasticPrioritySortModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\Modifiers\PrioritySortModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new PrioritySortModifier($engine);

        $this->assertFalse(isset($engine->sort['priority']));
        $modifier->modify(new PaginateRequest(['orderBy' => 'priority']));
        $this->assertTrue(isset($engine->sort['priority']));
    }

}