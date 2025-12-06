<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.auth')->prefix('api/organizations')->group(function () {
    Route::get('/nearby', [OrganizationController::class, 'nearby']);
    Route::get('/search', [OrganizationController::class, 'search']);
    Route::get('/activity/search', [OrganizationController::class, 'byActivityTitle']);

    Route::get('/building/{buildingId}', [OrganizationController::class, 'byBuilding']);
    Route::get('/activity/{activityId}', [OrganizationController::class, 'byActivity']);
    Route::get('/{id}', [OrganizationController::class, 'show']);
});
