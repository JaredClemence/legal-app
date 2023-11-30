<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\Paypal\BillingPlansController;
use App\Http\Controllers\Paypal\ProductController;
use App\Http\Middleware\EnsureAuthorizedToken;
use App\Http\Controllers\ProbateChampionMembershipController;

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
Route::get('/test/fixed', function () {
    return view('testPaypal');
});
Route::get('/test/subscription', [PaypalController::class, 'establishPaymentPlan']);

Route::get('/probatechampions/success', function(Request $request){
    dd(["type"=>"success", "request"=>$request]);
})->name('champions.links.success');
Route::get('/probatechampions/cancel', function(Request $request){
    dd(["type"=>"cancel", "request"=>$request]);
})->name('champions.links.cancel');
Route::get('/probatechampions/fixed/link', [ProbateChampionMembershipController::class,'getRedirectLink'])->name('champions.order.link');
Route::get('/probatechampions/new', [ProbateChampionMembershipController::class,'create'])->name('champions.new.links');
Route::get('/probatechampions/fixed', [ProbateChampionMembershipController::class,'sendFixedPriceClientToPaypal'])->name('champions.new.single');
Route::get('/probatechampions/subscribe', [ProbateChampionMembershipController::class,'createSubscription'])->name('champions.new.subscription');

Route::prefix('paypal')->group(function(){
    Route::post('/paypal/payment', [PaypalController::class, 'payment'])->name('paypal_fixed_payment');
    Route::post('/paypal/payment_plan', [PaypalController::class, 'establishPaymentPlan'])->name('paypal_payment_plan');
    Route::get('/paypal/success', [PaypalController::class, 'success'])->name('paypal_success');
    Route::get('/paypal/cancel', [PaypalController::class, 'cancel'])->name('paypal_cancel');

    Route::middleware(EnsureAuthorizedToken::class)->group( function(){
        Route::prefix('{apiNickname}')->group( function(){

                Route::get('/plans', [BillingPlansController::class, 'show'])->name('paypal.plans.list');
                Route::get('/plans/new', [BillingPlansController::class, 'showNewPlanForm'])->name('paypal.plans.new');
                Route::get('/plans/{id}/deactivate', [BillingPlansController::class, 'deactivate'])->name('paypal.plans.deactivate');
                Route::post('/plans/new', [BillingPlansController::class, 'create']);
                /**
                 * @todo Must create a product controller. Once created, the new plan pathway must start by selecting an existing product.
                 */

                Route::get('/product', [ProductController::class, 'index'])->name('paypal.product.list');
                Route::get('/product/new', [ProductController::class, 'create'])->name('paypal.product.new');
                Route::post('/product/new', [ProductController::class, 'store']);
                Route::get('/product/{id}', [ProductController::class, 'show'])->name('paypal.product.detail');
                Route::get('/product/{id}/deactivate', [ProductController::class, 'deactivate'])->name('paypal.product.deactivate');

        });
    });

});