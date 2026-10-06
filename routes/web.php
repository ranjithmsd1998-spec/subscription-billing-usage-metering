<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MerchantWebController;
use App\Http\Controllers\PlanWebController;
use App\Http\Controllers\SubscriptionWebController;
use App\Http\Controllers\CustomerWebController;
use App\Http\Controllers\SubscriptionPeriodWebController;
use App\Http\Controllers\UsageEventWebController;
use App\Http\Controllers\DailyUsageAggregateWebController;
use App\Http\Controllers\InvoiceWebController;
use App\Http\Controllers\PaymentWebController;
use App\Http\Controllers\PlanChangeWebController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [WebAuthController::class, 'login'])
        ->name('login.store');
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)->name('dashboard');

Route::prefix('merchants')
    ->name('merchants.')
    ->group(function () {

        /*
         * Merchant Blade page.
         */
        Route::get(
            '/',
            [MerchantWebController::class, 'index']
        )->name('index');

        /*
         * AJAX merchant listing.
         */
        Route::get(
            '/data',
            [MerchantWebController::class, 'list']
        )->name('data');

        /*
         * AJAX merchant create.
         */
        Route::post(
            '/',
            [MerchantWebController::class, 'store']
        )->name('store');

        /*
         * AJAX merchant show/edit data.
         */
        Route::get(
            '/{id}',
            [MerchantWebController::class, 'show']
        )->name('show');

        /*
         * AJAX merchant update.
         */
        Route::put(
            '/{id}',
            [MerchantWebController::class, 'update']
        )->name('update');

        /*
         * AJAX merchant delete.
         */
        Route::delete(
            '/{id}',
            [MerchantWebController::class, 'destroy']
        )->name('destroy');
    });

Route::prefix('plans')
->name('plans.')
->group(function () {

    Route::get('/', [PlanWebController::class, 'index'])
        ->name('index');

    Route::get('/data', [PlanWebController::class, 'list'])
        ->name('data');

    Route::post('/', [PlanWebController::class, 'store'])
        ->name('store');

    Route::get('/{id}', [PlanWebController::class, 'show'])
        ->name('show');

    Route::put('/{id}', [PlanWebController::class, 'update'])
        ->name('update');

    Route::delete('/{id}', [PlanWebController::class, 'destroy'])
        ->name('destroy');
});

Route::prefix('customers')
->name('customers.')
->group(function () {
    Route::get('/', [CustomerWebController::class, 'index'])
        ->name('index');

    Route::get('/data', [CustomerWebController::class, 'list'])
        ->name('data');

    Route::post('/', [CustomerWebController::class, 'store'])
        ->name('store');

    Route::get('/{id}', [CustomerWebController::class, 'show'])
        ->name('show');

    Route::put('/{id}', [CustomerWebController::class, 'update'])
        ->name('update');

    Route::delete('/{id}', [CustomerWebController::class, 'destroy'])
        ->name('destroy');
});
Route::prefix('subscriptions')
->name('subscriptions.')
->group(function () {

    Route::get(
        '/',
        [SubscriptionWebController::class, 'index']
    )->name('index');

    Route::get(
        '/data',
        [SubscriptionWebController::class, 'list']
    )->name('data');

    Route::post(
        '/',
        [SubscriptionWebController::class, 'store']
    )->name('store');

    Route::get(
        '/{id}',
        [SubscriptionWebController::class, 'show']
    )->name('show');

    Route::put(
        '/{id}',
        [SubscriptionWebController::class, 'update']
    )->name('update');

    Route::delete(
        '/{id}',
        [SubscriptionWebController::class, 'destroy']
    )->name('destroy');
});

Route::prefix('subscription-periods')
->name('subscription-periods.')
->group(function () {

    /*
        * Subscription Period Blade page.
        */
    Route::get(
        '/',
        [SubscriptionPeriodWebController::class, 'index']
    )->name('index');

    /*
        * AJAX subscription period listing.
        */
    Route::get(
        '/data',
        [SubscriptionPeriodWebController::class, 'list']
    )->name('data');

    /*
        * AJAX subscription period create.
        */
    Route::post(
        '/',
        [SubscriptionPeriodWebController::class, 'store']
    )->name('store');

    /*
        * AJAX subscription period show/edit data.
        */
    Route::get(
        '/{id}',
        [SubscriptionPeriodWebController::class, 'show']
    )->name('show');

    /*
        * AJAX subscription period update.
        */
    Route::put(
        '/{id}',
        [SubscriptionPeriodWebController::class, 'update']
    )->name('update');

    /*
        * AJAX subscription period delete.
        */
    Route::delete(
        '/{id}',
        [SubscriptionPeriodWebController::class, 'destroy']
    )->name('destroy');
});

Route::prefix('usage-events')
->name('usage-events.')
->group(function () {
    Route::get('/', [
        UsageEventWebController::class,
        'index',
    ])->name('index');

    Route::get('/data', [
        UsageEventWebController::class,
        'list',
    ])->name('data');

    Route::post('/', [
        UsageEventWebController::class,
        'store',
    ])->name('store');

    Route::get('/{id}', [
        UsageEventWebController::class,
        'show',
    ])->name('show');

    Route::put('/{id}', [
        UsageEventWebController::class,
        'update',
    ])->name('update');
});

Route::prefix('daily-usage-aggregates')
->name('daily-usage-aggregates.')
->group(function () {
    Route::get('/', [DailyUsageAggregateWebController::class, 'index'])
        ->name('index');

    Route::get('/data', [DailyUsageAggregateWebController::class, 'list'])
        ->name('data');

    Route::get('/{id}', [DailyUsageAggregateWebController::class, 'show'])
        ->name('show');
});

Route::prefix('invoices')
->name('invoices.')
->group(function () {
    Route::get('/', [InvoiceWebController::class, 'index'])
        ->name('index');

    Route::get('/data', [InvoiceWebController::class, 'list'])
        ->name('data');

    Route::post('/generate', [InvoiceWebController::class, 'generate'])
        ->name('generate');

    Route::get('/{id}', [InvoiceWebController::class, 'show'])
        ->name('show');

    Route::post('/{id}/issue', [InvoiceWebController::class, 'issue'])
        ->name('issue');

    Route::post('/{id}/pay', [InvoiceWebController::class, 'pay'])
        ->name('pay');

    Route::post('/{id}/void', [InvoiceWebController::class, 'void'])
        ->name('void');
});

Route::prefix('payments')
->name('payments.')
->group(function () {
    Route::get('/data', [
        PaymentWebController::class,
        'list'
    ])->name('data');

    Route::get('/{id}', [
        PaymentWebController::class,
        'show'
    ])->name('show');

    Route::post('/', [
        PaymentWebController::class,
        'store'
    ])->name('store');

    Route::put('/{id}/status', [
        PaymentWebController::class,
        'updateStatus'
    ])->name('update-status');

    Route::post('/{id}/refund', [
        PaymentWebController::class,
        'refund'
    ])->name('refund');
});
    Route::prefix('plan-changes')
    ->name('plan-changes.')
    ->group(function () {

        /*
         * Plan Change Blade page.
         */
        Route::get(
            '/',
            [PlanChangeWebController::class, 'index']
        )->name('index');

        /*
         * Create a new plan change.
         */
        Route::post(
            '/',
            [PlanChangeWebController::class, 'store']
        )->name('store');

        /*
         * Get plan change history for a subscription.
         */
        Route::get(
            '/subscription/{subscription}/history',
            [PlanChangeWebController::class, 'subscriptionHistory']
        )->name('subscription-history');

        /*
         * Show a single plan change.
         */
        Route::get(
            '/{id}',
            [PlanChangeWebController::class, 'show']
        )->name('show');
    });

});
