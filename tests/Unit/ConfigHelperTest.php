<?php namespace Tests\Belt\Elastic\Unit;

use Belt\Elastic\ConfigHelper;

class ConfigHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Belt\Elastic\ConfigHelper::analyzer
     * @covers \Belt\Elastic\ConfigHelper::normalizer
     * @covers \Belt\Elastic\ConfigHelper::property
     */
    public function test()
    {
        $helper = new ConfigHelper();

        $this->assertEmpty($helper->analyzer('test'));
        $this->assertEmpty($helper->normalizer('test'));
        $this->assertEmpty($helper->property('test'));
    }

}