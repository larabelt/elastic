<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Elastic\Engine;
use Belt\Elastic\Modifiers\TimestampsSortModifier;

class ElasticTimestampsSortModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\TimestampsSortModifier::modify
     */
    public function test()
    {
        $engine = m::mock(ElasticEngine::class);
        $modifier = new TimestampsSortModifier($engine);

        # created_at
        $this->assertFalse(isset($engine->sort['created_at']));
        $modifier->modify(new PaginateRequest(['orderBy' => 'created_at']));
        $this->assertTrue(isset($engine->sort['created_at']));

        # updated_at
        $this->assertFalse(isset($engine->sort['updated_at']));
        $modifier->modify(new PaginateRequest(['orderBy' => 'updated_at']));
        $this->assertTrue(isset($engine->sort['updated_at']));
    }

}