<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.auth')->prefix('organizations')->group(function () {
    Route::get('/nearby', [OrganizationController::class, 'nearby']);
    Route::get('/search', [OrganizationController::class, 'search']);
    Route::get('/activity/search', [OrganizationController::class, 'byActivityTitle']);

    Route::get('/building/{id}', [OrganizationController::class, 'byBuilding'])->where('id', '[0-9]+');;
    Route::get('/activity/{id}', [OrganizationController::class, 'byActivity'])->where('id', '[0-9]+');;
    Route::get('/{id}', [OrganizationController::class, 'show'])->where('id', '[0-9]+');;
});
