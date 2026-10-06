<?php

namespace App\Providers;

use App\Repositories\MerchantRepository;
use App\Repositories\MerchantRepositoryInterface;
use App\Repositories\PlanRepository;
use App\Repositories\PlanRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomerRepositoryInterface;
use App\Repositories\SubscriptionRepository;
use App\Repositories\SubscriptionRepositoryInterface;
use App\Repositories\SubscriptionPeriodRepository;
use App\Repositories\SubscriptionPeriodRepositoryInterface;
use App\Services\MerchantService;
use App\Services\MerchantServiceInterface;
use App\Services\PlanService;
use App\Services\PlanServiceInterface;
use App\Services\CustomerService;
use App\Services\CustomerServiceInterface;
use App\Services\SubscriptionService;
use App\Services\SubscriptionServiceInterface;
use App\Services\SubscriptionPeriodService;
use App\Services\SubscriptionPeriodServiceInterface;
use App\Repositories\UsageEventRepository;
use App\Repositories\UsageEventRepositoryInterface;
use App\Services\UsageEventService;
use App\Services\UsageEventServiceInterface;
use App\Repositories\DailyUsageAggregateRepository;
use App\Repositories\DailyUsageAggregateRepositoryInterface;
use App\Services\DailyUsageAggregateService;
use App\Services\DailyUsageAggregateServiceInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoiceRepositoryInterface;
use App\Services\InvoiceService;
use App\Services\InvoiceServiceInterface;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentRepositoryInterface;
use App\Services\PaymentService;
use App\Services\PaymentServiceInterface;
use App\Repositories\PlanChangeRepository;
use App\Repositories\PlanChangeRepositoryInterface;
use App\Services\PlanChangeService;
use App\Services\PlanChangeServiceInterface;
use App\Services\AuthService;
use App\Services\AuthServiceInterface;

use App\Services\DashboardService;
use App\Services\DashboardServiceInterface;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            MerchantRepositoryInterface::class,
            MerchantRepository::class
        );

        $this->app->bind(
            MerchantServiceInterface::class,
            MerchantService::class
        );

        $this->app->bind(
            PlanRepositoryInterface::class,
            PlanRepository::class
        );

        $this->app->bind(
            PlanServiceInterface::class,
            PlanService::class
        );

        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );

        $this->app->bind(
            CustomerServiceInterface::class,
            CustomerService::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            SubscriptionRepository::class
        );

        $this->app->bind(
            SubscriptionServiceInterface::class,
            SubscriptionService::class
        );

        $this->app->bind(
            SubscriptionPeriodRepositoryInterface::class,
            SubscriptionPeriodRepository::class
        );

        $this->app->bind(
            SubscriptionPeriodServiceInterface::class,
            SubscriptionPeriodService::class
        );

        $this->app->bind(
            UsageEventRepositoryInterface::class,
            UsageEventRepository::class
        );

        $this->app->bind(
            UsageEventServiceInterface::class,
            UsageEventService::class
        );
        $this->app->bind(
            DailyUsageAggregateServiceInterface::class,
            DailyUsageAggregateService::class
        );

        $this->app->bind(
            DailyUsageAggregateRepositoryInterface::class,
            DailyUsageAggregateRepository::class
        );
        $this->app->bind(
            InvoiceServiceInterface::class,
            InvoiceService::class
        );

        $this->app->bind(
            InvoiceRepositoryInterface::class,
            InvoiceRepository::class
        );
        
        $this->app->bind(
            PaymentServiceInterface::class,
            PaymentService::class
        );

        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );

        $this->app->bind(
            PlanChangeRepositoryInterface::class,
            PlanChangeRepository::class
        );

        $this->app->bind(
            PlanChangeServiceInterface::class,
            PlanChangeService::class
        );

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );


        $this->app->bind(
            DashboardServiceInterface::class,
            DashboardService::class
        );
        $this->app->bind(
            \App\Repositories\MerchantDashboardRepositoryInterface::class,
            \App\Repositories\MerchantDashboardRepository::class
        );

        $this->app->bind(
            \App\Services\MerchantDashboardServiceInterface::class,
            \App\Services\MerchantDashboardService::class
        );

    }

    public function boot(): void
    {
        //
    }
}