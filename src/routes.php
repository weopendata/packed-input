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

Route::get('api/stats/works/{n}', 'Tdt\Input\Controllers\InstitutionStatController@handle')

->where('n', '[2-9]+');
Route::get('api/stats/works', 'Tdt\Input\Controllers\InstitutionStatController@handle');

Route::get('api/stats/normalisedworks', 'Tdt\Input\Controllers\InstitutionStatController@normalized');

Route::get('api/query', 'Tdt\Input\Controllers\QueryController@handle');

Route::get('api/suggest', 'Tdt\Input\Controllers\QueryController@suggest');

Route::any('/', 'Tdt\Input\Controllers\UIController@index');
