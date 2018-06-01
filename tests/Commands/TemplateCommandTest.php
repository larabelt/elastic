<?php

use Mockery as m;
use Belt\Core\Testing\BeltTestCase;
use Belt\Core\Helpers\MorphHelper;
use Belt\Content\Behaviors\IncludesTemplateInterface;
use Belt\Content\Behaviors\IncludesTemplate;
use Belt\Content\Commands\TemplateCommand;
use Illuminate\Database\Eloquent\Model;

class TemplateCommandTest extends BeltTestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Content\Commands\TemplateCommand::helper
     * @covers \Belt\Content\Commands\TemplateCommand::handle
     */
    public function testHandle()
    {

        $cmd = new TemplateCommand();

        # helper
        $this->assertInstanceOf(MorphHelper::class, $cmd->helper());

        # handle
        $helper = m::mock(MorphHelper::class);
        $helper->shouldReceive('type2Class')->andReturn(TemplateCommandTestStub::class);

        $cmd = m::mock(TemplateCommand::class . '[helper,argument,option]');
        $cmd->shouldReceive('helper')->andReturn($helper);
        $cmd->shouldReceive('argument')->andReturn('reconcile-params');
        $cmd->shouldReceive('option')->andReturn('test');

        $cmd->handle();
    }

}

class TemplateCommandTestStub extends Model implements IncludesTemplateInterface
{
    use IncludesTemplate;

    public static function all($columns = ['*'])
    {
        $item = m::mock(Model::class);
        $item->shouldReceive('touch')->andReturnSelf();

        return [$item];
    }

    public function getMorphClass()
    {
        return 'pages';
    }
}