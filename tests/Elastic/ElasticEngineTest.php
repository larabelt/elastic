<?php

use Mockery as m;
use Belt\Core\Helpers\MorphHelper;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Core\Testing;
use Belt\Content\Page;
use Belt\Content\Elastic\ElasticEngine;
use Belt\Content\Elastic\Modifiers\IsActiveQueryModifier;
use Elasticsearch\Client as Elastic;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;

class ElasticEngineTest extends Testing\BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Elastic\ElasticEngine::__construct
     * @covers \Belt\Content\Elastic\ElasticEngine::setRequest
     * @covers \Belt\Content\Elastic\ElasticEngine::setOptions
     * @covers \Belt\Content\Elastic\ElasticEngine::update
     * @covers \Belt\Content\Elastic\ElasticEngine::delete
     * @covers \Belt\Content\Elastic\ElasticEngine::search
     * @covers \Belt\Content\Elastic\ElasticEngine::performSearch
     * @covers \Belt\Content\Elastic\ElasticEngine::addModifiers
     * @covers \Belt\Content\Elastic\ElasticEngine::applyModifiers
     * @covers \Belt\Content\Elastic\ElasticEngine::morphResults
     * @covers \Belt\Content\Elastic\ElasticEngine::paginate
     * @covers \Belt\Content\Elastic\ElasticEngine::mapIds
     * @covers \Belt\Content\Elastic\ElasticEngine::map
     * @covers \Belt\Content\Elastic\ElasticEngine::getTotalCount
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