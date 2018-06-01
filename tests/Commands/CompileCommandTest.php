<?php

use Mockery as m;
use Belt\Core\Testing\BeltTestCase;
use Belt\Content\Page;
use Belt\Content\Commands\CompileCommand;
use Belt\Content\Services\CompileService;
use Illuminate\Database\Eloquent\Builder;

class CompileCommandTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Commands\CompileCommand::service
     * @covers \Belt\Content\Commands\CompileCommand::qb
     * @covers \Belt\Content\Commands\CompileCommand::handle
     */
    public function testHandle()
    {

        # service
        $cmd = new CompileCommand();
        $this->assertInstanceOf(CompileService::class, $cmd->service());

        # qb
        $this->assertInstanceOf(Builder::class, $cmd->qb('pages'));

        # handle
        $pages = factory(Page::class, 2)->make();

        $qb = m::mock(Builder::class);
        $qb->shouldReceive('whereIn')->once()->withArgs(['id', [1,2]])->andReturnSelf();
        $qb->shouldReceive('get')->once()->andReturn($pages);

        $service = m::mock(CompileService::class);
        $service->shouldReceive('compile')->twice()->andReturn(true);

        $cmd = m::mock(CompileCommand::class . '[argument, option, qb, service]');
        $cmd->shouldReceive('argument')->once()->with('classes')->andReturn('pages');
        $cmd->shouldReceive('option')->once()->with('ids')->andReturn('1,2');
        $cmd->shouldReceive('qb')->once()->with('pages')->andReturn($qb);
        $cmd->shouldReceive('service')->once()->andReturn($service);

        $cmd->handle();
    }

}