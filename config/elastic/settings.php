<?php

return [
    'index' => [
        'creation_date' => '1494446553881',
        'number_of_shards' => 1,
        'number_of_replicas' => '1',
        'uuid' => '7G6hNEI1TUCWv5yHONivlg',
        'version' => [
            'created' => '5030099',
        ],
        'provided_name' => 'bradenton',
    ],
    'analysis' => [
        'analyzer' => [],
        'normalizer' => [
            'lower_asciifolding' => \Belt\Elastic\ConfigHelper::normalizer('lower_asciifolding'),
        ],
    ],
];