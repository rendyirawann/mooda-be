<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Event\EventController;
use App\Http\Controllers\Api\Fnb\InventoryController;
use App\Http\Controllers\Api\Fnb\KitchenController;
use App\Http\Controllers\Api\Fnb\MenuController;
use App\Http\Controllers\Api\Fnb\OrderController;
use App\Http\Controllers\Api\Fnb\RecipeController;
use App\Http\Controllers\Api\Fnb\ReportController;
use App\Http\Controllers\Api\Fnb\TableController;
use App\Http\Controllers\Api\Laundry\LaundryOrderController;
use App\Http\Controllers\Api\Laundry\LaundryServiceController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\ShiftController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 — dikonsumsi Mooda FE (mobile). Semua di bawah prefix /api/v1.
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {

    // ---- Publik ----
    Route::get('/health', [MetaController::class, 'health']);
    Route::get('/config', [MetaController::class, 'config']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // ---- Terproteksi (Bearer token / Sanctum) ----
    Route::middleware('auth:sanctum')->group(function () {

        // Auth & akun
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/account/tenant', [AccountController::class, 'tenant']);
        Route::get('/account/plan', [AccountController::class, 'plan']);

        // ---- F&B (Dine) ----
        Route::prefix('fnb')->group(function () {
            Route::get('/menus', [MenuController::class, 'index']);
            Route::get('/menus/{id}', [MenuController::class, 'show']);

            Route::get('/orders', [OrderController::class, 'index']);
            Route::post('/orders', [OrderController::class, 'store']);
            Route::get('/orders/{id}', [OrderController::class, 'show']);
            Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);

            Route::get('/kitchen/orders', [KitchenController::class, 'queue']);
            Route::post('/kitchen/items/{id}/bump', [KitchenController::class, 'bump']);

            Route::get('/tables', [TableController::class, 'index']);

            Route::get('/inventory/ingredients', [InventoryController::class, 'ingredients']);
            Route::post('/inventory/movements', [InventoryController::class, 'storeMovement']);
            Route::post('/inventory/opname', [InventoryController::class, 'opname']);

            Route::get('/recipes/{menuId}', [RecipeController::class, 'show']);

            Route::get('/reports/sales', [ReportController::class, 'sales']);
            Route::get('/reports/hpp', [ReportController::class, 'hpp']);
        });

        // Shift (lintas vertical)
        Route::get('/shifts/current', [ShiftController::class, 'current']);
        Route::post('/shifts/open', [ShiftController::class, 'open']);
        Route::post('/shifts/close', [ShiftController::class, 'close']);

        // ---- Laundry ----
        Route::prefix('laundry')->group(function () {
            Route::get('/services', [LaundryServiceController::class, 'services']);
            Route::get('/customers', [LaundryServiceController::class, 'customers']);
            Route::get('/orders', [LaundryOrderController::class, 'index']);
            Route::post('/orders', [LaundryOrderController::class, 'store']);
            Route::post('/orders/{id}/advance', [LaundryOrderController::class, 'advance']);
        });

        // ---- Event (roadmap) ----
        Route::get('/event/events', [EventController::class, 'index']);
    });
});
