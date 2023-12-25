<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KCBA\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware([])->group(function(){
    Route::prefix('kcba')->group(function(){
        Route::get('users',[UserController::class,'index']);
        Route::post('users', [UserController::class,'create']);
        Route::get('users/{id}',[UserController::class,'edit']);
        Route::post('users/{id}',[UserController::class,'update']);
        Route::delete('users/{id}', [UserController::class,'destroy']);
    });
});

require __DIR__.'/auth.php';
