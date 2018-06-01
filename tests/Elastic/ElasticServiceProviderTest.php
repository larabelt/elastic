<?php

use Belt\Core\Testing;
use Belt\ElasticServiceProvider;
use Laravel\Scout\EngineManager;

class ElasticServiceProviderTest extends Testing\BeltTestCase
{
    /**
     * @covers \Belt\ElasticServiceProvider::register
     * @covers \Belt\ElasticServiceProvider::boot
     */
    public function test()
    {
        try {
            app(EngineManager::class)->driver('elastic');
            $this->exceptionNotThrown();
        } catch (\Exception $e) {

        }

        app()['config']->set('belt.elastic.index.name', 'test');
        app()['config']->set('belt.elastic.index.hosts', ['http://0.0.0.0']);

        $provider = new ElasticServiceProvider(app());
        $provider->register();
        $provider->boot();

        $this->assertNotEmpty(app(EngineManager::class)->driver('elastic'));

    }

}