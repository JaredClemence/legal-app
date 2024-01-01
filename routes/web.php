<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KCBA\MemberController;
use App\Http\Middleware\KCBA\IsBarMember;
use App\Models\KCBA\Member as BarMember;

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

Route::prefix('kcba')->group(function(){
    Route::get('login', function(){} )->name('bar.login');
    Route::get('register', function(){} )->name('bar.register');
    
    Route::middleware([IsBarMember::class])->group(function(){
        Route::get('users',[MemberController::class,'index']);//->can('viewAll', BarMember::class);
        Route::post('users', [MemberController::class,'create']);//->can('create', BarMember::class);
        Route::get('users/{member}',[MemberController::class,'edit']);//->can('update', 'id');
        Route::post('users/{member}',[MemberController::class,'update']);//->can('update', 'id');
        Route::delete('users/{member}', [MemberController::class,'destroy']);//->can('delete', 'id');
    });
});

require __DIR__.'/auth.php';
