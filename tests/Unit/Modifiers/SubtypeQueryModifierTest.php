<?php namespace Tests\Belt\Elastic\Unit\Modifiers;

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Tests\Belt\Core\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Modifiers\SubtypeQueryModifier;

class SubtypeQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\SubtypeQueryModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new SubtypeQueryModifier($engine);

        $this->assertFalse(isset($engine->query['bool']['must'][0]['terms']['subtype']));
        $modifier->modify(new PaginateRequest(['subtype' => 'foo']));
        $this->assertTrue(isset($engine->query['bool']['must'][0]['terms']['subtype']));
    }

}