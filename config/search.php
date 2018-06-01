<?php

return [
    'classes' => [
        \Belt\Content\Page::class => \Belt\Content\Http\Requests\PaginatePages::class,
        \Belt\Content\Post::class => \Belt\Content\Http\Requests\PaginatePosts::class,
    ]
];