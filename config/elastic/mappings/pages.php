<?php

return [
    'properties' => [
        'id' => \Belt\Elastic\ConfigHelper::property('primary_key'),
        'is_active' => \Belt\Elastic\ConfigHelper::property('boolean'),
        'subtype' => \Belt\Elastic\ConfigHelper::property('template'),
        'name' => \Belt\Elastic\ConfigHelper::property('name'),
        'slug' => \Belt\Elastic\ConfigHelper::property('string'),
        'subtype' => \Belt\Elastic\ConfigHelper::property('string'),
        'body' => \Belt\Elastic\ConfigHelper::property('text'),
        'meta_description' => \Belt\Elastic\ConfigHelper::property('text'),
        'meta_keywords' => \Belt\Elastic\ConfigHelper::property('text'),
        'meta_title' => \Belt\Elastic\ConfigHelper::property('text'),
        'searchable' => \Belt\Elastic\ConfigHelper::property('text'),
        'created_at' => \Belt\Elastic\ConfigHelper::property('integer'),
        'updated_at' => \Belt\Elastic\ConfigHelper::property('integer'),
        'terms' => \Belt\Elastic\ConfigHelper::property('long'),
    ],
];