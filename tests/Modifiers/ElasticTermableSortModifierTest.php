<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Term;
use Belt\Elastic\Engine;
use Belt\Elastic\Modifiers\TermableSortModifier;
use Illuminate\Database\Eloquent\Collection;

class ElasticTermableSortModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\TermableSortModifier::modify
     * @covers \Belt\Elastic\Modifiers\TermableSortModifier::terms
     * @covers \Belt\Elastic\Modifiers\TermableSortModifier::find
     */
    public function test()
    {
        $engine = m::mock(Engine::class);
        $modifier = m::mock(TermableSortModifier::class . '[find]', [$engine]);

        # terms
        $this->assertInstanceOf(Term::class, $modifier->terms());

        # modify
        $modifier->shouldReceive('find')->andReturn($this->termFactory(44));
        $this->assertFalse(isset($engine->sort['_script']));
        $modifier->modify(new PaginateRequest(['orderBy' => 'term:44']));
        $this->assertTrue(isset($engine->sort['_script']));

        # find @todo add mock
        $builder = m::mock(\Illuminate\Database\Eloquent\Builder::class);
        $builder->shouldReceive('newQuery')->andReturnSelf();
        $builder->shouldReceive('whereIn')->andReturnSelf();
        $builder->shouldReceive('orWhereIn')->andReturnSelf();
        $builder->shouldReceive('get')->andReturn([]);

        $modifier = new TermableSortModifier($engine);
        $modifier->terms = $builder;
        $modifier->find(1);
    }

    public function termFactory($id)
    {
        $term = factory(Term::class)->make(['id' => $id]);
        $term->setAppends([]);

        return new Collection([$term]);
    }

}