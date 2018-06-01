<?php

namespace Belt\Elastic\Elastic;

use Belt;
use Belt\Core\Behaviors\HasConfig;
use Belt\Core\Helpers;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Search;
use Elasticsearch\Client as Elastic;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

/**
 * Class ElasticEngine
 * @package Belt\Content\Search
 */
class ElasticEngine extends Engine implements Search\HasPaginatorInterface
{

    use HasConfig, Search\HasPaginator;

    public static $paginatorClass = ElasticSearchPaginator::class;

    /**
     * @var Elastic
     */
    public $elastic;

    /**
     * Index where the models will be saved.
     *
     * @var string
     */
    protected $index;

    /**
     * @var PaginateRequest
     */
    public $request;

    /**
     * @var integer
     */
    public $from = 0;

    /**
     * @var integer
     */
    public $size = 10;

    /**
     * @var integer
     */
    public $min_score = 0;

    /**
     * @var array
     */
    public static $modifiers = [];

    /**
     * @var Helpers\MorphHelper
     */
    public $morphHelper;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var array
     */
    public $query = [];

    /**
     * @var array
     */
    public $filter = [];

    /**
     * @var array
     */
    public $sort = [];

    /**
     * @var array
     */
    public $types = '';

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * Create a new engine instance.
     *
     * @param Elastic $elastic
     * @param string $index
     * @param array $config
     */
    public function __construct(Elastic $elastic, $index, $config = [])
    {
        $this->config = $config;
        $this->elastic = $elastic;
        $this->index = $index;
        $this->morphHelper = new Helpers\MorphHelper();
        $this->min_score = (float) $this->config('min_score', 0);
        $this->types = $this->config('types');
    }

    /**
     * Use request to set various value
     *
     * @param PaginateRequest $request
     */
    public function setRequest(PaginateRequest $request)
    {
        $this->request = $request;

        $options = [];

        if ($from = $request->offset()) {
            $options['from'] = $from;
        }

        if ($size = $request->perPage()) {
            $options['size'] = $size;
        }

        if ($debug = (bool) $request->get('debug', false)) {
            $options['debug'] = $debug;
        }

        if ($include = $request->get('include')) {
            $options['types'] = $include;
        }

        if ($min_score = $request->has('min_score')) {
            $options['min_score'] = (float) $request->get('min_score');
        }

        $this->setOptions($options);
    }

    /**
     * Use options to set/override various values.
     *
     * Options take precedence over setRequest.
     *
     * @param $options
     */
    public function setOptions($options)
    {
        if (array_has($options, 'debug')) {
            $this->debug = array_get($options, 'debug');
        }

        if (array_has($options, 'from')) {
            $this->from = array_get($options, 'from');
        }

        if (array_has($options, 'min_score')) {
            $this->min_score = (float) array_get($options, 'min_score');
        }

        if (array_has($options, 'size')) {
            $this->size = array_get($options, 'size');
        }

        if (array_has($options, 'types')) {
            $this->types = array_get($options, 'types');
        }
    }

    /**
     * Update the given model in the index.
     *
     * @param  Collection $models
     * @return void
     */
    public function update($models)
    {
        $params['body'] = [];

        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'update' => [
                    '_id' => $model->getKey(),
                    '_index' => $this->index,
                    '_type' => $model->searchableAs(),
                ]
            ];
            $params['body'][] = [
                'doc' => $model->toSearchableArray(),
                'doc_as_upsert' => true
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * Remove the given model from the index.
     *
     * @param  Collection $models
     * @return void
     */
    public function delete($models)
    {
        $params['body'] = [];

        $models->each(function ($model) use (&$params) {
            $params['body'][] = [
                'delete' => [
                    '_id' => $model->getKey(),
                    '_index' => $this->index,
                    '_type' => $model->searchableAs(),
                ]
            ];
        });

        $this->elastic->bulk($params);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {

    }

    /**
     * Perform the given search on the engine.
     *
     * @param array $options
     * @return mixed
     */
    public function performSearch(array $options = [])
    {

        $this->setOptions($options);

        $this->params = [
            'index' => $this->index,
            'type' => $this->types,
            'body' => [
                'from' => $this->from,
                'min_score' => $this->min_score ?: 0,
                'query' => [],
                'size' => $this->size,
                'sort' => [],
            ],
        ];

        $this->query = [
            'bool' => [
                'must' => [],
                'should' => [],
                'must_not' => [],
                'filter' => [],
            ],
        ];

        $this->applyModifiers();

        $this->params['body']['sort'] = $this->sort;
        $this->params['body']['query'] = $this->query;
        $this->params['body']['query']['bool']['filter'] = $this->filter;

        return $this->elastic->search($this->params);
    }

    /**
     * @param string $type
     * @param mixed $classes
     */
    public static function addModifiers($type, $classes)
    {
        static::$modifiers[$type] = static::$modifiers[$type] ?? [];

        $classes = is_array($classes) ? $classes : [$classes];

        foreach ($classes as $class) {
            if (!in_array($class, static::$modifiers[$type])) {
                static::$modifiers[$type][] = $class;
            }
        }
    }

    /**
     * @return mixed
     */
    public function applyModifiers()
    {
        $modifiers = [];
        foreach (explode(',', $this->types) as $type) {
            foreach (array_get(static::$modifiers, $type, []) as $class) {
                if (!in_array($class, $modifiers)) {
                    $modifiers[] = $class;
                }
            }
        }

        foreach ($modifiers as $class) {
            $modifier = new $class($this);
            $modifier->modify($this->request);
        }

        return $this->query;
    }

    /**
     * @param $results
     * @return Collection
     */
    public function morphResults($results)
    {

        $items = new Collection();
        foreach (array_get($results, 'hits.hits', []) as $result) {
            $this->debug($result);
            $id = array_get($result, '_id');
            $type = array_get($result, '_type');
            $item = $this->morphHelper->morph($type, $id);
            if ($item) {
                $items->push($item);
            }
        }

        return $items;
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  Builder $builder
     * @param  int $perPage
     * @param  int $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {

    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  mixed $results
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return Collection
     */
    public function map($results, $model)
    {

    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {

    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed $results
     * @return int
     */
    public function getTotalCount($results)
    {

    }

    /**
     * @codeCoverageIgnore
     * @param $result
     * @return void
     */
    public function debug($result)
    {
        if ($this->debug) {

            $msg = [];

            $source = array_get($result, '_source', []);

            $msg[] = sprintf('%s: #%s %s (%s)',
                array_get($result, '_type'),
                array_get($result, '_id'),
                array_get($source, 'name'),
                array_get($result, '_score')
            );

            if ($terms = array_get($source, 'terms')) {
                $msg[] = sprintf("terms: %s", implode(',', $terms));
            }

            $starts_at = array_get($source, 'starts_at');
            $ends_at = array_get($source, 'ends_at');
            if ($starts_at || $ends_at) {
                $msg[] = sprintf("starts: %s, ends: %s", date('Y-m-d H:i', $starts_at), date('Y-m-d H:i', $ends_at));
            }

            $msg = implode("\n", $msg);

            dump($msg);
        }
    }

}