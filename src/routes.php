<?php

/*
|--------------------------------------------------------------------------
| Input Routes
|--------------------------------------------------------------------------
*/

Route::any('api/input/{all?}', 'Tdt\Input\Controllers\InputController@handle')

->where('all', '.*');

Route::get('api/stats/artists/{data_provider?}', 'Tdt\Input\Controllers\ArtistStatController@handle')

->where('museum', '[a-zA-Z]+');

Route::get('api/stats/objects/{data_provider?}', 'Tdt\Input\Controllers\ObjectStatController@handle')

->where('museum', '[a-zA-Z]+');

Route::get('api/stats/institutions/{data_provider?}', 'Tdt\Input\Controllers\InstitutionStatController@handle')

->where('museum', '[a-zA-Z]+');
