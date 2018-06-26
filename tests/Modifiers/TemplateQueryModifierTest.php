<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Modifiers\TemplateQueryModifier;

class TemplateQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\TemplateQueryModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new TemplateQueryModifier($engine);

        $this->assertFalse(isset($engine->query['bool']['must'][0]['terms']['template']));
        $modifier->modify(new PaginateRequest(['template' => 'foo']));
        $this->assertTrue(isset($engine->query['bool']['must'][0]['terms']['template']));
    }

}