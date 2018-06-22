<?php

$host = env('ELASTIC_HOST');

return [
    'name' => env('ELASTIC_INDEX', false),
    'hosts' => $host ? [$host] : [],
    'types' => 'pages,posts,places,events,terms,lists',
    'min_score' => env('ELASTIC_MIN_SCORE', 0),
];