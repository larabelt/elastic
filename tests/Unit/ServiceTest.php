<?php namespace Tests\Belt\Elastic\Unit;

use Mockery as m;
use Belt\Core\Tests\BeltTestCase;
use Belt\Elastic\Engine as ElasticEngine;
use Belt\Elastic\Service as ElasticService;
use Elasticsearch\Namespaces\IndicesNamespace;
use Elasticsearch\Client as Elastic;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;

class ServiceTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Service::__construct
     * @covers \Belt\Elastic\Service::elastic
     * @covers \Belt\Elastic\Service::engine
     * @covers \Belt\Elastic\Service::indices
     * @covers \Belt\Elastic\Service::disk
     * @covers \Belt\Elastic\Service::writeConfig
     * @covers \Belt\Elastic\Service::deleteIndex
     * @covers \Belt\Elastic\Service::createIndex
     * @covers \Belt\Elastic\Service::getSettings
     * @covers \Belt\Elastic\Service::getMappings
     * @covers \Belt\Elastic\Service::putMappings
     * @covers \Belt\Elastic\Service::upsert
     */
    public function test()
    {
        app()['config']->set('belt.elastic.index.name', 'test');
        app()['config']->set('belt.elastic.mappings', [
            'pages' => [],
            'posts' => [],
        ]);

        # construct
        $service = new ElasticService(['console' => true]);
        $this->assertNotNull($service->console);
        $service = new ElasticService();

        # elastic
        $elastic = m::mock(Elastic::class);
        $service->elastic = $elastic;
        $this->assertEquals($elastic, $service->elastic());

        # engine
        $engine = m::mock(ElasticEngine::class);
        $service->engine = $engine;
        $this->assertEquals($engine, $service->engine());

        # indices
        $indices = m::mock(IndicesNamespace::class);
        $elastic->shouldReceive('indices')->andReturn($indices);
        $this->assertEquals($indices, $service->indices());

        # disk
        $disk = m::mock(Filesystem::class);
        $service->disk = $disk;
        $this->assertEquals($disk, $service->disk());

        # writeConfig
        $path = 'path\to\config';
        $array = ['foo' => 'bar'];
        $disk->shouldReceive('put')->matchArgs([$path])->andReturnSelf();
        $service->writeConfig($path, $array);

        # deleteIndex
        $indices = m::mock(IndicesNamespace::class);
        $indices->shouldReceive('delete')->with(['index' => 'test'])->andReturnSelf();
        $service->indices = $indices;
        $service->index = 'test';
        $service->deleteIndex();

        # deleteIndex (fail)
        $indices = m::mock(IndicesNamespace::class);
        $indices->shouldReceive('delete')->with(['index' => 'invalid'])->andThrow(new \Exception());
        $service->indices = $indices;
        $service->index = 'invalid';
        $service->deleteIndex();

        # createIndex
        $indices = m::mock(IndicesNamespace::class);
        $indices->shouldReceive('create')->andReturnSelf();
        $indices->shouldReceive('putMapping')->andReturn([]);
        $service->indices = $indices;
        $service->createIndex();

        # createIndex (fail)
        $indices = m::mock(IndicesNamespace::class);
        $indices->shouldReceive('create')->andThrow(new \Exception());
        $service->indices = $indices;
        $service->createIndex();

        # getSettings
        $settings = ['test' => ['settings' => ['foo' => 'bar']]];
        $indices = m::mock(IndicesNamespace::class);
        $indices->shouldReceive('getSettings')->andReturn($settings);
        $service->index = 'test';
        $service->indices = $indices;
        $disk->shouldReceive('put')->matchArgs(['config/belt/elastic/settings.php'])->andReturnSelf();
        $service->getSettings();

        # getMappings
        $mappings = ['test' => ['mappings' => [
            'pages' => ['foo' => 'bar'],
            'posts' => ['foo' => 'bar'],
        ]]];
        $indices = m::mock(IndicesNamespace::class);
        $indices->shouldReceive('getMapping')->andReturn($mappings);
        $service->indices = $indices;
        $disk->shouldReceive('put')->with('config/belt/elastic/mappings/pages.php', ['foo'=>'bar'])->andReturnSelf();
        $disk->shouldReceive('put')->with('config/belt/elastic/mappings/posts.php', ['foo'=>'bar'])->andReturnSelf();
        $service->getMappings();

        # upsert
        $items = new Collection(['stuff']);
        $qb = m::mock(\Illuminate\Database\Eloquent\Builder::class);
        $qb->shouldReceive('__clone')->andReturnSelf();
        $qb->shouldReceive('take')->andReturnSelf();
        $qb->shouldReceive('offset')->andReturnSelf();
        $qb->shouldReceive('get')->andReturn($items);
        $morphHelper = m::mock(\Belt\Core\Helpers\MorphHelper::class);
        $morphHelper->shouldReceive('type2QB')->with('foo')->andReturn($qb);
        $engine = m::mock(ElasticEngine::class);
        $engine->shouldReceive('update')->with($items)->andReturnNull();
        $service->engine = $engine;
        $service->morphHelper = $morphHelper;
        $service->upsert(['foo']);

    }

}
