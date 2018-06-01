<?php

use Mockery as m;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Content\Term;
use Belt\Elastic\Modifiers\TermableQueryModifier;
use Illuminate\Database\Eloquent\Collection;

class ElasticTermableQueryModifierTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Modifiers\TermableQueryModifier::terms
     * @covers \Belt\Elastic\Modifiers\TermableQueryModifier::modify
     * @covers \Belt\Elastic\Modifiers\TermableQueryModifier::find
     * @covers \Belt\Elastic\Modifiers\TermableQueryModifier::params
     * @covers \Belt\Elastic\Modifiers\TermableQueryModifier::filter
     * @covers \Belt\Elastic\Modifiers\TermableQueryModifier::query
     */
    public function test()
    {
        Term::unguard();

        $engine = m::mock(ElasticEngine::class);

        # terms
        $modifier = new TermableQueryModifier($engine);
        $this->assertInstanceOf(Term::class, $modifier->terms());

        # find
        $modifier = new TermableQueryModifier($engine);
        $term = factory(Term::class)->make(['id' => 1]);
        $collection = new Collection([$term]);
        $termsRepo = m::mock(Term::class);
        $termsRepo->shouldReceive('newQuery')->andReturnSelf();
        $termsRepo->shouldReceive('whereIn')->with('id', [1])->andReturnSelf();
        $termsRepo->shouldReceive('orWhereIn')->with('slug', [1])->andReturnSelf();
        $termsRepo->shouldReceive('get')->andReturn($collection);
        $modifier->terms = $termsRepo;
        $modifier->find([1]);

        # modify
        $modifier = m::mock(TermableQueryModifier::class . '[params,filter,query]', [$engine]);
        $modifier->shouldReceive('params')->once()->andReturn([]);
        $modifier->shouldReceive('filter')->once();
        $modifier->shouldReceive('query')->once();
        $modifier->modify(new PaginateRequest(['term' => '1']));

        # filter
        $params['filter'] = [
            1 => [
                [1, 2, 3],
                [4, 5, 6],
            ],
            2 => [7, 8, 9]
        ];
        $modifier = new TermableQueryModifier($engine);
        $this->assertFalse(isset($engine->filter[0]['bool']['should']));
        $modifier->filter($params);
        $this->assertTrue(isset($engine->filter[0]['bool']['should']));

        # query
        $params['query'] = [
            1 => [
                [1, 2, 3],
                [4, 5, 6],
            ],
            2 => [7, 8, 9]
        ];
        $modifier = new TermableQueryModifier($engine);
        $this->assertFalse(isset($engine->query['bool']['should'][0]));
        $modifier->query($params);
        $this->assertTrue(isset($engine->query['bool']['should'][0]));

        # params
        $modifier = m::mock(TermableQueryModifier::class . '[find]', [$engine]);
        $modifier->shouldReceive('find')->with([1])->andReturn([$this->termFactory(1, [2, 3])]);
        $modifier->shouldReceive('find')->with([4])->andReturn([$this->termFactory(4, [5, 6])]);
        $modifier->shouldReceive('find')->with([7])->andReturn([$this->termFactory(7, [8, 9])]);
        $params = $modifier->params(new PaginateRequest(['term' => '1+4,~7']));
        //dump($params);
        $this->assertNotEmpty($params['filter']);
        $this->assertEquals([1, 2, 3], $params['filter'][0][0]);
        $this->assertEquals([4, 5, 6], $params['filter'][0][1]);
        $this->assertNotEmpty($params['query']);
        $this->assertEquals([7, 8, 9], $params['query'][1][0]);

    }

    public function termFactory($id, $child_ids = [], $parent = null)
    {
        $term = factory(Term::class)->make(['id' => $id]);
        $term->setAppends([]);
        $term->descendants = new Collection();

        foreach ($child_ids as $child_id) {
            $child = $this->termFactory($child_id, [], $term);
            $term->descendants->add($child);
        }

        return $term;
    }

}