<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanvasController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/canvas', [CanvasController::class, 'index']);
Route::post('/canvas/save', [CanvasController::class, 'store']);
Route::post('/canvas/update/{id}', [CanvasController::class, 'update']);
Route::delete('/canvas/delete/{id}', [CanvasController::class, 'destroy']);
