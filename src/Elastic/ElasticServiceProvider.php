<?php

namespace Belt\Elastic\Elastic;

use Belt, Elasticsearch;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Scout\EngineManager;

/**
 * Class ElasticServiceProvider
 * @package Belt\Content
 */
class ElasticServiceProvider extends BaseServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('belt.elastic.index.name') && config('belt.elastic.index.hosts')) {

            $engine = new ElasticEngine(Elasticsearch\ClientBuilder::create()
                ->setHosts(config('belt.elastic.index.hosts'))
                ->build(),
                config('belt.elastic.index.name'),
                config('belt.elastic.index')
            );

            app(EngineManager::class)->extend('elastic', function () use ($engine) {
                return $engine;
            });
        }
    }

}