<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\IdSortModifier;

class ElasticIdSortModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\Modifiers\IdSortModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new IdSortModifier($engine);

        $this->assertFalse(isset($engine->sort['id']));
        $modifier->modify(new PaginateRequest(['orderBy' => 'id']));
        $this->assertTrue(isset($engine->sort['id']));
    }

}