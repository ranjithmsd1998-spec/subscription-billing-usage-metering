<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DailyUsageAggregateController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PlanChangeController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SubscriptionPeriodController;
use App\Http\Controllers\Api\UsageEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    Route::post(
        'register',
        [AuthController::class, 'register']
    );

    Route::post(
        'login',
        [AuthController::class, 'login']
    );

    Route::middleware('auth:sanctum')->group(function () {

        Route::get(
            'me',
            [AuthController::class, 'me']
        );

        Route::post(
            'logout',
            [AuthController::class, 'logout']
        );
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Read Routes
|--------------------------------------------------------------------------
|
| All authenticated users can access read-only APIs.
|
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Merchant - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'merchants',
        [MerchantController::class, 'index']
    );

    Route::get(
        'merchants/{merchant}',
        [MerchantController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Plan - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'plans',
        [PlanController::class, 'index']
    );

    Route::get(
        'plans/{plan}',
        [PlanController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Customer - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'customers',
        [CustomerController::class, 'index']
    );

    Route::get(
        'customers/{customer}',
        [CustomerController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Subscription - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'subscriptions',
        [SubscriptionController::class, 'index']
    );

    Route::get(
        'subscriptions/{subscription}',
        [SubscriptionController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Subscription Period - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'subscription-periods',
        [SubscriptionPeriodController::class, 'index']
    );

    Route::get(
        'subscription-periods/{subscription_period}',
        [SubscriptionPeriodController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Usage Events - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'usage-events',
        [UsageEventController::class, 'index']
    );

    Route::get(
        'usage-events/{usage_event}',
        [UsageEventController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Daily Usage Aggregates - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'daily-usage-aggregates',
        [DailyUsageAggregateController::class, 'index']
    );

    Route::get(
        'daily-usage-aggregates/{id}',
        [DailyUsageAggregateController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Invoice - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'invoices/customer',
        [InvoiceController::class, 'customerInvoices']
    );

    Route::get(
        'invoices/merchant',
        [InvoiceController::class, 'merchantInvoices']
    );

    Route::get(
        'invoices/{id}',
        [InvoiceController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Payment - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'payments/invoice',
        [PaymentController::class, 'byInvoice']
    );

    Route::get(
        'payments/customer',
        [PaymentController::class, 'byCustomer']
    );

    Route::get(
        'payments/merchant',
        [PaymentController::class, 'byMerchant']
    );

    Route::get(
        'payments/{id}',
        [PaymentController::class, 'show']
    );

    /*
    |--------------------------------------------------------------------------
    | Plan Changes - Read
    |--------------------------------------------------------------------------
    */

    Route::get(
        'plan-changes',
        [PlanChangeController::class, 'index']
    );

    Route::get(
        'plan-changes/{plan_change}',
        [PlanChangeController::class, 'show']
    );

    Route::get(
        'subscriptions/{subscription}/plan-changes',
        [PlanChangeController::class, 'subscriptionHistory']
    );
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Only authenticated administrators can create, update, delete,
| generate, issue, pay, void, refund and perform other mutations.
|
*/

Route::middleware([
    'auth:sanctum',
    'admin',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Merchant - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'merchants',
        [MerchantController::class, 'store']
    );

    Route::put(
        'merchants/{merchant}',
        [MerchantController::class, 'update']
    );

    Route::patch(
        'merchants/{merchant}',
        [MerchantController::class, 'update']
    );

    Route::delete(
        'merchants/{merchant}',
        [MerchantController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | Plan - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'plans',
        [PlanController::class, 'store']
    );

    Route::put(
        'plans/{plan}',
        [PlanController::class, 'update']
    );

    Route::patch(
        'plans/{plan}',
        [PlanController::class, 'update']
    );

    Route::delete(
        'plans/{plan}',
        [PlanController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | Customer - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'customers',
        [CustomerController::class, 'store']
    );

    Route::put(
        'customers/{customer}',
        [CustomerController::class, 'update']
    );

    Route::patch(
        'customers/{customer}',
        [CustomerController::class, 'update']
    );

    Route::delete(
        'customers/{customer}',
        [CustomerController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | Subscription - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'subscriptions',
        [SubscriptionController::class, 'store']
    );

    Route::put(
        'subscriptions/{subscription}',
        [SubscriptionController::class, 'update']
    );

    Route::patch(
        'subscriptions/{subscription}',
        [SubscriptionController::class, 'update']
    );

    Route::delete(
        'subscriptions/{subscription}',
        [SubscriptionController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | Subscription Period - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'subscription-periods',
        [SubscriptionPeriodController::class, 'store']
    );

    Route::put(
        'subscription-periods/{subscription_period}',
        [SubscriptionPeriodController::class, 'update']
    );

    Route::patch(
        'subscription-periods/{subscription_period}',
        [SubscriptionPeriodController::class, 'update']
    );

    Route::delete(
        'subscription-periods/{subscription_period}',
        [SubscriptionPeriodController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | Usage Events - Admin
    |--------------------------------------------------------------------------
    |
    | Assignment requirement:
    |
    | POST /api/usage
    |
    | This is the high-throughput usage ingestion endpoint.
    |
    | Rate limit:
    | 60 requests per minute per authenticated user/IP.
    |
    */

    Route::post(
        'usage',
        [UsageEventController::class, 'store']
    )->middleware('throttle:60,1');

    /*
    |--------------------------------------------------------------------------
    | Usage Events - CRUD
    |--------------------------------------------------------------------------
    |
    | Existing CRUD endpoint retained for the application UI.
    |
    */

    Route::post(
        'usage-events',
        [UsageEventController::class, 'store']
    );

    Route::put(
        'usage-events/{usage_event}',
        [UsageEventController::class, 'update']
    );

    Route::patch(
        'usage-events/{usage_event}',
        [UsageEventController::class, 'update']
    );

    Route::delete(
        'usage-events/{usage_event}',
        [UsageEventController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | Daily Usage Aggregates - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'daily-usage-aggregates/generate',
        [DailyUsageAggregateController::class, 'generate']
    );

    /*
    |--------------------------------------------------------------------------
    | Invoice - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'invoices/generate',
        [InvoiceController::class, 'generate']
    );

    Route::patch(
        'invoices/{id}/issue',
        [InvoiceController::class, 'issue']
    );

    Route::patch(
        'invoices/{id}/pay',
        [InvoiceController::class, 'pay']
    );

    Route::patch(
        'invoices/{id}/void',
        [InvoiceController::class, 'void']
    );

    /*
    |--------------------------------------------------------------------------
    | Payment - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'payments',
        [PaymentController::class, 'store']
    );

    Route::patch(
        'payments/{id}/status',
        [PaymentController::class, 'updateStatus']
    );

    Route::patch(
        'payments/{id}/refund',
        [PaymentController::class, 'refund']
    );

    /*
    |--------------------------------------------------------------------------
    | Plan Changes - Admin
    |--------------------------------------------------------------------------
    */

    Route::post(
        'plan-changes',
        [PlanChangeController::class, 'store']
    );

    Route::put(
        'plan-changes/{plan_change}',
        [PlanChangeController::class, 'update']
    );

    Route::patch(
        'plan-changes/{plan_change}',
        [PlanChangeController::class, 'update']
    );

    Route::delete(
        'plan-changes/{plan_change}',
        [PlanChangeController::class, 'destroy']
    );
});

Route::middleware('auth:sanctum')->group(function () {

    // Existing routes...

    Route::get(
        'merchants/{merchant}/dashboard',
        [\App\Http\Controllers\Api\MerchantDashboardController::class, 'show']
    );

});