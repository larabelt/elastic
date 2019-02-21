<?php namespace Tests\Belt\Elastic\Unit\Modifiers;

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Tests\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Modifiers\NameSortModifier;

class ElasticNameSortModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\NameSortModifier::modify
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