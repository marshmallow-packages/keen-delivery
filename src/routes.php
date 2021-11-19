<?php

use Illuminate\Support\Facades\Route;

foreach (config('keen-delivery.routes') as $route) {
    Route::get($route['path'], $route['controller'])
        ->name($route['name']);
}
