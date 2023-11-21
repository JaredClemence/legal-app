<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaypalController;

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
    return redirect('/test/fixed');
});
Route::get('/test/fixed', function () {
    return view('testPaypal');
});
Route::get('/test/subscription', [PaypalController::class, 'establishPaymentPlan']);

Route::post('/paypal/payment', [PaypalController::class, 'payment'])->name('paypal_fixed_payment');
Route::post('/paypal/payment_plan', [PaypalController::class, 'establishPaymentPlan'])->name('paypal_payment_plan');
Route::get('/paypal/success', [PaypalController::class, 'success'])->name('paypal_success');
Route::get('/paypal/cancel', [PaypalController::class, 'cancel'])->name('paypal_cancel');
