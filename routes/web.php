<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KCBA\MemberController;
use App\Http\Middleware\KCBA\IsBarMember;
use App\Models\KCBA\Member as BarMember;
use App\Http\Middleware\KCBA\TimedTokenValid;

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

Route::get('mail', function(){
    $name = "Section Member";
    $unsubscribeNow = "http://example.com";
    $confirmLink = "http://example.com";
    return view('mail.optin', compact("name", "confirmLink", "unsubscribeNow"));
} );

Route::get('webmail', function(){
    $name = "Section Member";
    $unsubscribeNow = "http://example.com";
    $confirmLink = "http://example.com";
    return view('mail.optin', compact(["name","unsubscribeNow","confirmLink"]));
} );

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
    
    Route::post('users', [MemberController::class,'create'])->middleware([TimedTokenValid::class]);//->can('create', BarMember::class);
    Route::post('users/bulk', [MemberController::class,'createBulk'])->name('kcba.bulk.create');
    //Route::post('users/bulk', [MemberController::class,'createBulk']);//->can('create', BarMember::class);
    Route::middleware([IsBarMember::class])->group(function(){
        Route::get('users',[MemberController::class,'index']);//->can('viewAll', BarMember::class);
        Route::get('users/bulk', [MemberController::class,'showBulkForm']);//->can('create', BarMember::class);
        Route::get('users/{member}',[MemberController::class,'edit'])->name('kcba.member.edit');//->can('update', 'id');
        Route::post('users/{member}',[MemberController::class,'update']);//->can('update', 'id');
        Route::delete('users/{member}', [MemberController::class,'destroy']);//->can('delete', 'id');
    });
});



require __DIR__.'/auth.php';
