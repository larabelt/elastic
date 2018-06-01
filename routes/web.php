<?php

use Belt\Content\Http\Controllers\Web;

Route::group(['middleware' => ['web']], function () {

    # search
    Route::get('search', Web\SearchController::class . '@index');

});