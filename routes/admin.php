<?php

Route::group([
    'prefix' => 'admin/belt/elastic',
    'middleware' => ['web', 'admin']
],
    function () {

        # admin/belt/elastic home
        Route::get('{any?}', function () {
            return view('belt-elastic::base.admin.dashboard');
        })->where('any', '(.*)');

    }
);