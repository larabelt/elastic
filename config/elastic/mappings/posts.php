<?php

return [
    'properties' => [
        'id' => \Belt\Elastic\ConfigHelper::property('primary_key'),
        'is_active' => \Belt\Elastic\ConfigHelper::property('boolean'),
        'template' => \Belt\Elastic\ConfigHelper::property('template'),
        'name' => \Belt\Elastic\ConfigHelper::property('name'),
        'slug' => \Belt\Elastic\ConfigHelper::property('string'),
        'template' => \Belt\Elastic\ConfigHelper::property('string'),
        'body' => \Belt\Elastic\ConfigHelper::property('text'),
        'meta_description' => \Belt\Elastic\ConfigHelper::property('text'),
        'meta_keywords' => \Belt\Elastic\ConfigHelper::property('text'),
        'meta_title' => \Belt\Elastic\ConfigHelper::property('text'),
        'searchable' => \Belt\Elastic\ConfigHelper::property('text'),
        'post_at' => \Belt\Elastic\ConfigHelper::property('integer'),
        'created_at' => \Belt\Elastic\ConfigHelper::property('integer'),
        'updated_at' => \Belt\Elastic\ConfigHelper::property('integer'),
        'terms' => \Belt\Elastic\ConfigHelper::property('long'),
    ],
];