<?php

namespace Belt\Elastic\Search\Mock;

use Belt;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Scout\EngineManager;

/**
 * Class MockEngineServiceProvider
 * @package Belt\Content
 * @codeCoverageIgnore
 */
class MockEngineServiceProvider extends BaseServiceProvider
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
        app(EngineManager::class)->extend('mock', function ($app) {
            return new MockEngine();
        });
    }

}