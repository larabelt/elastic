<?php

use Mockery as m;
use Belt\Core\Testing\BeltTestCase;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Http\Requests\PaginatePages;
use Belt\Content\Http\Requests\PaginatePosts;
use Belt\Content\Page;
use Belt\Content\Post;
use Belt\ElasticSearchPaginator;
use Belt\Content\Search\Mock\MockEngine;
use Illuminate\Http\Request;
use Laravel\Scout\EngineManager;

class ElasticSearchPaginatorTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\ElasticSearchPaginator::build
     */
    public function test()
    {

        app()['config']->set('belt.search.classes', [
            Page::class => PaginatePages::class,
            Post::class => PaginatePosts::class,
        ]);

        app(EngineManager::class)->extend('elastic', function ($app) {
            return new MockEngine();
        });

        $request = new Request(['include' => 'pages']);
        $request = PaginateRequest::extend($request);

        $paginator = new ElasticSearchPaginator(null, $request);

        $paginator->build();
    }

}

class ElasticSearchPaginatorStub
{
    public function getMorphClass()
    {
        return 'pages';
    }
}