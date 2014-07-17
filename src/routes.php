<?php

/*
|--------------------------------------------------------------------------
| Input Routes
|--------------------------------------------------------------------------
*/

Route::any('api/input/{all?}', 'Tdt\Input\Controllers\InputController@handle')

->where('all', '.*');

Route::get('api/stats/{data_provider}', 'Tdt\Input\Controllers\StatController@handle')

->where('museum', '[a-zA-Z]+');
