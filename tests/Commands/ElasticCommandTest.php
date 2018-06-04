<?php

use Mockery as m;
use Belt\Core\Testing\BeltTestCase;
use Belt\Elastic\Commands\ElasticCommand;
use Belt\Elastic\Service as ElasticService;

class ElasticCommandTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Elastic\Commands\ElasticCommand::service
     * @covers \Belt\Elastic\Commands\ElasticCommand::handle
     */
    public function testHandle()
    {
        # service
        $service = m::mock(ElasticService::class);
        $service->shouldReceive('getSettings')->once()->andReturnSelf();
        $service->shouldReceive('getMappings')->once()->andReturnSelf();
        $service->shouldReceive('putMappings')->once()->andReturnSelf();
        $service->shouldReceive('deleteIndex')->twice()->andReturnSelf();
        $service->shouldReceive('createIndex')->twice()->andReturnSelf();
        $service->shouldReceive('import')->once()->with('test')->andReturnSelf();

        # get-settings
        $cmd = m::mock(ElasticCommand::class . '[argument]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('get-settings');
        $cmd->handle();

        # get-mappings
        $cmd = m::mock(ElasticCommand::class . '[argument]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('get-mappings');
        $cmd->handle();

        # put-mappings
        $cmd = m::mock(ElasticCommand::class . '[argument]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('put-mappings');
        $cmd->handle();

        # delete-index
        $cmd = m::mock(ElasticCommand::class . '[argument]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('delete-index');
        $cmd->handle();

        # create-index
        $cmd = m::mock(ElasticCommand::class . '[argument]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('create-index');
        $cmd->handle();

        # import
        $cmd = m::mock(ElasticCommand::class . '[argument,option]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('import');
        $cmd->shouldReceive('option')->andReturn('test');
        $cmd->handle();

        # replace-index
        $cmd = m::mock(ElasticCommand::class . '[argument]');
        $cmd->service = $service;
        $cmd->shouldReceive('argument')->andReturn('replace-index');
        $cmd->handle();
    }

}