<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoryController;
use App\Models\History;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/login', [UserController::class, 'login']);
Route::post('/auth/register', [UserController::class, 'register']);

Route::group(['middleware' => ["auth:sanctum"]], function () {
    Route::delete('/auth/logout', [UserController::class, 'logout']);
    Route::post('/history/prediction', [HistoryController::class, 'predict']);
    Route::get('/history', [HistoryController::class, 'history']);
});