<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/
Route::middleware('api.key')->post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (API Token Auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['api.key', 'auth:api'])->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Sidebar Menu
    Route::middleware('role:admin,manager,cashier')->get(
        '/sidebar-menu',
        [MenuController::class, 'sidebar']
    );

    // Products (Admin & Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/products/{id}', [ProductController::class, 'update']);
        });

        // Products (Admin only)
        Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    // Products (All roles)
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
    });

        // Orders (Cashier, Manager, Admin)
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });

        Route::middleware('role:admin,manager')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });
});



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
