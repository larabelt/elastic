<?php

use Belt\ElasticConfigHelper;

class ElasticConfigHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Belt\ElasticConfigHelper::analyzer
     * @covers \Belt\ElasticConfigHelper::normalizer
     * @covers \Belt\ElasticConfigHelper::property
     */
    public function test()
    {
        $helper = new ElasticConfigHelper();

        $this->assertEmpty($helper->analyzer('test'));
        $this->assertEmpty($helper->normalizer('test'));
        $this->assertEmpty($helper->property('test'));
    }


}