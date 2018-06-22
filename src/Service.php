<?php

namespace Belt\Elastic;

use Belt, Riimu;
use Belt\Core\Helpers\BeltHelper;
use Belt\Core\Helpers\MorphHelper;
use Belt\Core\Behaviors\HasConsole;
use Elasticsearch\Client as Elastic;
use Laravel\Scout\EngineManager;

/**
 * Class Service
 * @package Belt\Elastic
 */
class Service
{
    use HasConsole;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    public $disk;

    /**
     * @var Elastic
     */
    public $elastic;

    /**
     * @var ElasticEngine
     */
    public $engine;

    /**
     * @var string
     */
    public $index;

    /**
     * @var \Elasticsearch\Namespaces\IndicesNamespace
     */
    public $indices;

    /**
     * @var MorphHelper
     */
    public $morphHelper;

    public function __construct($params = [])
    {
        $this->index = config('belt.elastic.index.name');
        $this->morphHelper = new MorphHelper();

        if ($console = array_get($params, 'console')) {
            $this->console = $console;
        }
    }

    /**
     * @return Elastic
     */
    public function elastic()
    {
        return $this->elastic = $this->elastic ?: $this->engine()->elastic;
    }

    /**
     * @return ElasticEngine
     */
    public function engine()
    {
        return $this->engine = $this->engine ?: app(EngineManager::class)->driver('elastic');
    }

    /**
     * @return \Elasticsearch\Namespaces\IndicesNamespace
     */
    public function indices()
    {
        return $this->indices ?: $this->elastic()->indices();
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk()
    {
        return $this->disk = $this->disk ?: BeltHelper::baseDisk();
    }

    /**
     * Write $config array to configuration file
     *
     * @param $path
     * @param $array
     */
    public function writeConfig($path, $array)
    {
        $contents = (new Riimu\Kit\PHPEncoder\PHPEncoder())->encode($array);

        $this->disk()->put($path, "<?php \r\n\r\nreturn " . $contents . ';');
    }

    /**
     * Delete elastic index
     */
    public function deleteIndex()
    {
        try {
            $this->indices()->delete(['index' => $this->index]);
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * Delete elastic index
     */
    public function createIndex()
    {

        try {
            $this->indices()->create([
                'index' => $this->index,
                'body' => [
                    'number_of_shards' => config("belt.elastic.settings.index.number_of_shards", 5),
                    'number_of_replicas' => config("belt.elastic.settings.index.number_of_replicas", 1),
                    'refresh_interval' => config("belt.elastic.settings.index.refresh_interval", 0),
                    'analysis' => config("belt.elastic.settings.analysis", []),
                ]
            ]);

            $this->putMappings();

        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }
    }

    /**
     * Fetch (and write) index settings
     */
    public function getSettings()
    {
        $settings = $this->indices()->getSettings(['index' => $this->index]);

        $this->writeConfig('config/belt/elastic/settings.php', array_get($settings, $this->index . '.settings', []));
    }

    /**
     * Fetch (and write) index mappings
     */
    public function getMappings()
    {
        $mappings = $this->indices()->getMapping(['index' => $this->index]);

        foreach (array_get($mappings, "$this->index.mappings", []) as $key => $mapping) {
            $this->writeConfig("config/belt/elastic/mappings/$key.php", $mapping);
        }
    }

    /**
     * Push index mappings
     *
     * @return mixed
     */
    public function putMappings()
    {
        $this->info('put-mappings:');

        $mappings = config('belt.elastic.mappings');

        foreach ($mappings as $type => $mapping) {
            $result = $this->indices()->putMapping([
                'index' => $this->index,
                'type' => $type,
                'body' => $mapping
            ]);
            $this->info(sprintf('%s: %s', $type, array_get($result, 'acknowledged', 0)));
        }
    }

    /**
     * Upsert items to elastic index
     *
     * @param $types
     */
    public function upsert($types, $limit = 10)
    {
        $types = $types ?: config('belt.elastic.index.types');
        $types = is_array($types) ? $types : explode(',', $types);

        foreach ($types as $type) {
            $page = 1;
            do {
                $qb = $this->morphHelper->type2QB($type);
                $items = $qb->take($limit)->offset($limit * $page - $limit)->get();
                if ($count = $items->count()) {
                    $this->engine()->update($items);
                }
                $this->info(sprintf('%s page: %s', $type, $page));
                $page++;
            } while ($count >= $limit);
        }

    }

}