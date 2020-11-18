<?php

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

use Illuminate\Support\Facades\Route;

foreach (config('custom-table.apis') as $key => $value) {
	foreach ($value['routes'] as $routeKey => $routeParams) {
		Route::get($routeParams['url'], $routeParams['action']);
	}
}