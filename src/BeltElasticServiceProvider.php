<?php

namespace Belt\Elastic;

use Belt, Elasticsearch, Laravel, Validator;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Class BeltElasticServiceProvider
 * @package Belt\Elastic
 */
class BeltElasticServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/../routes/admin.php';
        include __DIR__ . '/../routes/api.php';
        include __DIR__ . '/../routes/web.php';

        # beltable values for global belt command
        $this->app['belt']->addPackage('elastic', ['dir' => __DIR__ . '/..']);
        $this->app['belt']->publish('belt-elastic:publish');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(GateContract $gate, Router $router)
    {
        // set backup view paths
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'belt-elastic');
        //$this->loadViewsFrom(__DIR__ . '/../resources/views/docs', 'belt-docs');

        // set backup translation paths
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'belt-elastic');

        // policies
        $this->registerPolicies($gate);

        // morphMap
        Relation::morphMap([]);

        // commands
        $this->commands(Belt\Elastic\Commands\ElasticCommand::class);
        $this->commands(Belt\Elastic\Commands\PublishCommand::class);

        # engines
        $this->app->register(Laravel\Scout\ScoutServiceProvider::class);

        $engine = new Belt\Elastic\Engine(Elasticsearch\ClientBuilder::create()
            ->setHosts(config('belt.elastic.index.hosts'))
            ->build(),
            config('belt.elastic.index.name'),
            config('belt.elastic.index')
        );

        app(Laravel\Scout\EngineManager::class)->extend('elastic', function () use ($engine) {
            return $engine;
        });
    }

    /**
     * Register the application's policies.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function registerPolicies(GateContract $gate)
    {
        foreach ($this->policies as $key => $value) {
            $gate->policy($key, $value);
        }
    }

}