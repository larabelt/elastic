<?php namespace Tests\Belt\Elastic\Unit\Modifiers;

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Tests\Belt\Core\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Modifiers\NeedleQueryModifier;

class ElasticNeedleQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\NeedleQueryModifier::modify
     * @covers \Belt\Elastic\Modifiers\NeedleQueryModifier::needle
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new NeedleQueryModifier($engine);

        # modify
        $this->assertFalse(isset($engine->query['bool']['should'][0]['multi_match']));
        $this->assertFalse(isset($engine->query['bool']['should'][1]['wildcard']));
        $modifier->modify(new PaginateRequest(['q' => 'test']));
        $this->assertTrue(isset($engine->query['bool']['should'][0]['multi_match']));
        $this->assertTrue(isset($engine->query['bool']['should'][1]['wildcard']));

        # needle
        $this->assertEquals('foo bar', $modifier->needle(new PaginateRequest(['q' => 'FOO    "BAR@#$%"'])));
    }

}