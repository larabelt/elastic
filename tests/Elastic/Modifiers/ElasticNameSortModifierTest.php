<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\NameSortModifier;

class ElasticNameSortModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\Modifiers\NameSortModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new NameSortModifier($engine);

        $this->assertFalse(isset($engine->sort['name.keyword']));
        $modifier->modify(new PaginateRequest(['orderBy' => 'name']));
        $this->assertTrue(isset($engine->sort['name.keyword']));
    }

}