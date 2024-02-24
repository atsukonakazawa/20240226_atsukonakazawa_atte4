<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\BreaktimeController;


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

Route::middleware('auth')->group(function () {
    Route::get('/', [AuthController::class,'index']);
    Route::get('/attendance',[WorkController::class,'sendDay'])->name("form.send-day");
});

Route::get('/workIn',[WorkController::class,'workIn'])->name("form.work-in");
Route::get('/workOut',[WorkController::class,'workOut'])->name("form.work-out");
Route::get('/breakIn',[BreaktimeController::class,'breakIn'])->name("form.break-in");
Route::get('/breakOut',[BreaktimeController::class,'breakOut'])->name("form.break-out");
Route::get('/attendance/nextday',[WorkController::class,'nextDay'])->name("form.nextDay");
Route::get('/attendance/daybefore',[WorkController::class,'dayBefore'])->name("form.dayBefore");


