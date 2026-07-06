<?php

declare(strict_types=1);

use PedroPCardoso\StartupKit\Orders\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('startup-kit-orders.routes.prefix', 'orders'))
    ->middleware(config('startup-kit-orders.routes.middleware', ['api']))
    ->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
    });
