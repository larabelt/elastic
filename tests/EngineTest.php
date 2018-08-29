<?php

use Mockery as m;
use Belt\Core\Helpers\MorphHelper;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing;
use Belt\Content\Page;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Modifiers\IsActiveQueryModifier;
use Elasticsearch\Client as Elastic;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;

class EngineTest extends Testing\BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Engine::__construct
     * @covers \Belt\Elastic\Engine::setRequest
     * @covers \Belt\Elastic\Engine::setOptions
     * @covers \Belt\Elastic\Engine::update
     * @covers \Belt\Elastic\Engine::delete
     * @covers \Belt\Elastic\Engine::search
     * @covers \Belt\Elastic\Engine::performSearch
     * @covers \Belt\Elastic\Engine::addModifiers
     * @covers \Belt\Elastic\Engine::applyModifiers
     * @covers \Belt\Elastic\Engine::morphResults
     * @covers \Belt\Elastic\Engine::paginate
     * @covers \Belt\Elastic\Engine::mapIds
     * @covers \Belt\Elastic\Engine::map
     * @covers \Belt\Elastic\Engine::getTotalCount
     */
    public function test()
    {
        //app()['config']->set('belt.elastic.index.min_score', 1);

        $results = [
            'hits' => [
                'total' => 2,
                'hits' => [
                    ['_type' => 'pages', '_id' => 1],
                    ['_type' => 'pages', '_id' => 2],
                ]
            ]
        ];

        $elastic = m::mock(Elastic::class);
        $elastic->shouldReceive('bulk')->andReturnSelf();
        $elastic->shouldReceive('search')->andReturn($results);

        $engine = new ElasticEngine($elastic, 'test', ['min_score' => 1]);

        # setRequest
        $request = new PaginateRequest([
            'q' => 'test',
            'perPage' => 25,
            'page' => 3,
            'include' => 'pages,posts',
            'min_score' => .5,
            'debug' => true,
            'orderBy' => 'score',
            'embed' => 'params',
        ]);
        $engine->setRequest($request);
        $this->assertEquals(25, $engine->size);
        $this->assertEquals(50, $engine->from);
        $this->assertEquals('pages,posts', $engine->types);

        # setOptions
        $engine->setOptions([
            'debug' => false,
            'needle' => 'test2',
            'from' => 100,
            'size' => 20,
            'types' => 'pages',
            'min_score' => .25
        ]);
        $this->assertEquals(20, $engine->size);
        $this->assertEquals(100, $engine->from);
        $this->assertEquals('pages', $engine->types);

        $models = new Collection([new Page()]);

        # update
        $engine->update($models);

        # delete
        $engine->delete($models);

        # performSearch
        $engine->performSearch([
            'sort' => 'default',
            'numericFilters' => ['foo', 'bar'],
        ]);

        # addModifiers
        $this->assertFalse(isset($engine::$modifiers['test'][0]));
        $engine::addModifiers('test', IsActiveQueryModifier::class);
        $this->assertTrue(isset($engine::$modifiers['test'][0]));

        # applyModifiers
        $engine->applyModifiers();

        # morphResults
        $morphHelper = m::mock(MorphHelper::class);
        $morphHelper->shouldReceive('morph')->andReturn(new Page());
        $engine->morphHelper = $morphHelper;
        $items = $engine->morphResults($results);
        $this->assertInstanceOf(Collection::class, $items);

        # empty placeholder functions
        $engine->getTotalCount([]);
        $engine->map([], '');
        $engine->mapIds([]);
        $engine->paginate(m::mock(Builder::class), 1, 1);
        $engine->search(m::mock(Builder::class));

    }


}