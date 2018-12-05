<?php

use Belt\Content\Http\Controllers\Web;

Route::group(['middleware' => ['web']], function () {

    if (config('belt.core.translate.prefix-urls')) {
        foreach ((array) config('belt.core.translate.locales') as $locale) {
            $code = array_get($locale, 'code');
            Route::get("$code/search", Web\SearchController::class . '@index');
        }
    }

    Route::get('search', Web\SearchController::class . '@index');

});