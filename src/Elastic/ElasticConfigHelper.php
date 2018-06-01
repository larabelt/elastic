<?php

namespace Belt\Elastic\Elastic;

/**
 * Class ElasticHelper
 * @package Belt\Content\Services
 */
class ElasticConfigHelper
{

    public static $analyzers = [];

    public static $normalizers = [
        'lower_asciifolding' => [
            'type' => 'custom',
            'filter' => ['lowercase', 'asciifolding']
        ],
    ];

    public static $properties = [
        'boolean' => [
            'type' => 'boolean',
        ],
        'datetime' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword',
                    'ignore_above' => 256,
                ],
            ],
        ],
        'integer' => [
            'type' => 'integer',
        ],
        'long' => [
            'type' => 'long',
        ],
        'float' => [
            'type' => 'float',
        ],
        'geo_point' => [
            'type' => 'geo_point',
        ],
        'name' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword',
                    'ignore_above' => 256,
                    'normalizer' => 'lower_asciifolding',
                ],
            ],
            'analyzer' => 'snowball',
        ],
        'primary_key' => [
            'type' => 'long',
        ],
        'string' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword',
                    'ignore_above' => 256,
                ],
            ],
        ],
        'text' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword',
                    'ignore_above' => 256,
                ],
            ],
            'analyzer' => 'snowball',
        ],

    ];

    public static function analyzer($key)
    {
        return static::$analyzers[$key] ?? [];
    }

    public static function normalizer($key)
    {
        return static::$normalizers[$key] ?? [];
    }

    public static function property($key)
    {
        return static::$properties[$key] ?? [];
    }

}