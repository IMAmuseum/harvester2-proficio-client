<?php

/*
|--------------------------------------------------------------------------
| Proficio-Client Package Routes
|--------------------------------------------------------------------------
*/

use Imamuseum\ProficioClient\ProficioController as Proficio;

Route::group(['prefix' => 'proficio'], function() {

    Route::get('getAllObjectIDs', function () {
        $proficio = new Proficio();
        $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : config('proficio.catalog');
        return $proficio->getAllObjectIDs($catalog);
    });

    Route::get('getSpecificObject/{id}', function($id) {
        $proficio = new Proficio();
        $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : config('proficio.catalog');
        return $proficio->getSpecificObject($id, $catalog);
    });

    Route::get('getUpdatedObjectIDs', function () {
        $proficio = new Proficio();
        $start = isset($_GET['start']) ? $_GET['start'] : config('proficio.start');
        $take = isset($_GET['take']) ? $_GET['take'] : config('proficio.take');
        $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : config('proficio.catalog');
        return $proficio->getUpdatedObjectIDs($catalog, $start, $take);
    });

    Route::get('getTableForObject/{table}/{id}', function ($table, $id) {
        $proficio = new Proficio();
        $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : config('proficio.catalog');
        return $proficio->queryTable($catalog, $table, $id);
    });

    Route::get('getTableForAllObjects/{table}', function ($table) {
        $proficio = new Proficio();
        $start = isset($_GET['start']) ? $_GET['start'] : config('proficio.start');
        $take = isset($_GET['take']) ? $_GET['take'] : config('proficio.take');
        $catalog = isset($_GET['catalog']) ? $_GET['catalog'] : config('proficio.catalog');
        return $proficio->getTableForAllObjects($catalog, $table, $start, $take);
    });
});
