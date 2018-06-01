<?php

use Belt\Content\Elastic\ElasticConfigHelper;

class ElasticConfigHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Belt\Content\Elastic\ElasticConfigHelper::analyzer
     * @covers \Belt\Content\Elastic\ElasticConfigHelper::normalizer
     * @covers \Belt\Content\Elastic\ElasticConfigHelper::property
     */
    public function test()
    {
        $helper = new ElasticConfigHelper();

        $this->assertEmpty($helper->analyzer('test'));
        $this->assertEmpty($helper->normalizer('test'));
        $this->assertEmpty($helper->property('test'));
    }


}