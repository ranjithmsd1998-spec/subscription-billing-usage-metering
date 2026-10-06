<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Models\UsageEvent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoBillingSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Merchant
        |--------------------------------------------------------------------------
        */

        $merchant = Merchant::updateOrCreate(
            ['code' => 'DEMO'],
            [
                'name' => 'Demo SaaS',
                'email' => 'demo@example.com',
                'status' => 'active',
                'timezone' => 'Asia/Kolkata',
                'currency' => 'INR',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Plans
        |--------------------------------------------------------------------------
        */

        $basicPlan = Plan::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'code' => 'BASIC',
            ],
            [
                'name' => 'Basic',
                'description' => 'Basic monthly subscription plan.',
                'currency' => 'INR',
                'base_price' => 499.00,
                'billing_cycle' => 'monthly',
                'included_units' => 1000,
                'overage_rate' => 0.50,
                'unit_name' => 'API Calls',
                'status' => 'active',
            ]
        );

        $premiumPlan = Plan::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'code' => 'PREMIUM',
            ],
            [
                'name' => 'Premium',
                'description' => 'Premium monthly subscription plan.',
                'currency' => 'INR',
                'base_price' => 999.00,
                'billing_cycle' => 'monthly',
                'included_units' => 5000,
                'overage_rate' => 0.25,
                'unit_name' => 'API Calls',
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        $customer1 = Customer::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'code' => 'CUST001',
            ],
            [
                'name' => 'Ranjith Kumar',
                'email' => 'ranjith@example.com',
                'phone' => '9876543210',
                'status' => 'active',
                'metadata' => [
                    'source' => 'demo-seeder',
                ],
            ]
        );

        $customer2 = Customer::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'code' => 'CUST002',
            ],
            [
                'name' => 'Demo Customer',
                'email' => 'customer@example.com',
                'phone' => '9876543211',
                'status' => 'active',
                'metadata' => [
                    'source' => 'demo-seeder',
                ],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Billing Period
        |--------------------------------------------------------------------------
        |
        | Current demo billing cycle:
        | Start = 5 days before today
        | End   = 1 month after start
        |
        */

        $periodStart = now()
            ->subDays(5)
            ->startOfDay();

        $periodEnd = $periodStart
            ->copy()
            ->addMonth();

        /*
        |--------------------------------------------------------------------------
        | Subscription 1 - Basic
        |--------------------------------------------------------------------------
        */

        $subscription1 = Subscription::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'customer_id' => $customer1->id,
                'plan_id' => $basicPlan->id,
            ],
            [
                'status' => 'active',
                'started_at' => $periodStart,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'cancelled_at' => null,
            ]
        );

        $subscriptionPeriod1 = SubscriptionPeriod::updateOrCreate(
            [
                'subscription_id' => $subscription1->id,
                'starts_at' => $periodStart,
            ],
            [
                'plan_id' => $basicPlan->id,
                'ends_at' => $periodEnd,
                'base_price' => $basicPlan->base_price,
                'included_units' => $basicPlan->included_units,
                'overage_rate' => $basicPlan->overage_rate,
                'billing_cycle' => $basicPlan->billing_cycle,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Usage Events - Ranjith / Basic
        |--------------------------------------------------------------------------
        |
        | Total:
        | 200 + 300 + 250 + 180 + 220 + 150 = 1300
        |
        | Included:
        | 1000
        |
        | Overage:
        | 300
        |
        | Overage Amount:
        | 300 x ₹0.50 = ₹150
        |
        */

        $usageEvents = [
            [
                'key' => 'demo-usage-001',
                'customer_id' => $customer1->id,
                'subscription_id' => $subscription1->id,
                'subscription_period_id' => $subscriptionPeriod1->id,
                'usage_units' => 200,
                'occurred_at' => $periodStart->copy()
                    ->addDay()
                    ->setTime(10, 0),
            ],

            [
                'key' => 'demo-usage-002',
                'customer_id' => $customer1->id,
                'subscription_id' => $subscription1->id,
                'subscription_period_id' => $subscriptionPeriod1->id,
                'usage_units' => 300,
                'occurred_at' => $periodStart->copy()
                    ->addDays(2)
                    ->setTime(11, 0),
            ],

            [
                'key' => 'demo-usage-003',
                'customer_id' => $customer1->id,
                'subscription_id' => $subscription1->id,
                'subscription_period_id' => $subscriptionPeriod1->id,
                'usage_units' => 250,
                'occurred_at' => $periodStart->copy()
                    ->addDays(3)
                    ->setTime(14, 0),
            ],

            [
                'key' => 'demo-usage-004',
                'customer_id' => $customer1->id,
                'subscription_id' => $subscription1->id,
                'subscription_period_id' => $subscriptionPeriod1->id,
                'usage_units' => 180,
                'occurred_at' => $periodStart->copy()
                    ->addDays(4)
                    ->setTime(9, 30),
            ],

            [
                'key' => 'demo-usage-005',
                'customer_id' => $customer1->id,
                'subscription_id' => $subscription1->id,
                'subscription_period_id' => $subscriptionPeriod1->id,
                'usage_units' => 220,
                'occurred_at' => $periodStart->copy()
                    ->addDays(4)
                    ->setTime(15, 30),
            ],

            [
                'key' => 'demo-usage-006',
                'customer_id' => $customer1->id,
                'subscription_id' => $subscription1->id,
                'subscription_period_id' => $subscriptionPeriod1->id,
                'usage_units' => 150,
                'occurred_at' => $periodStart->copy()
                    ->addDays(5)
                    ->setTime(10, 30),
            ],
        ];

        foreach ($usageEvents as $event) {
            UsageEvent::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                    'idempotency_key' => $event['key'],
                ],
                [
                    'customer_id' => $event['customer_id'],
                    'subscription_id' => $event['subscription_id'],
                    'subscription_period_id' => $event['subscription_period_id'],
                    'usage_units' => $event['usage_units'],
                    'unit_name' => 'API Calls',
                    'occurred_at' => $event['occurred_at'],
                    'metadata' => [
                        'source' => 'demo-seeder',
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Subscription 2 - Premium
        |--------------------------------------------------------------------------
        */

        $subscription2 = Subscription::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'customer_id' => $customer2->id,
                'plan_id' => $premiumPlan->id,
            ],
            [
                'status' => 'active',
                'started_at' => $periodStart,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'cancelled_at' => null,
            ]
        );

        $subscriptionPeriod2 = SubscriptionPeriod::updateOrCreate(
            [
                'subscription_id' => $subscription2->id,
                'starts_at' => $periodStart,
            ],
            [
                'plan_id' => $premiumPlan->id,
                'ends_at' => $periodEnd,
                'base_price' => $premiumPlan->base_price,
                'included_units' => $premiumPlan->included_units,
                'overage_rate' => $premiumPlan->overage_rate,
                'billing_cycle' => $premiumPlan->billing_cycle,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Usage Events - Demo Customer / Premium
        |--------------------------------------------------------------------------
        |
        | Total:
        | 800 + 900 + 1000 + 700 = 3400
        |
        | Included:
        | 5000
        |
        | Overage:
        | 0
        |
        */

        $premiumUsageEvents = [
            [
                'key' => 'demo-usage-007',
                'usage_units' => 800,
                'occurred_at' => $periodStart->copy()
                    ->addDay()
                    ->setTime(9, 0),
            ],

            [
                'key' => 'demo-usage-008',
                'usage_units' => 900,
                'occurred_at' => $periodStart->copy()
                    ->addDays(2)
                    ->setTime(13, 0),
            ],

            [
                'key' => 'demo-usage-009',
                'usage_units' => 1000,
                'occurred_at' => $periodStart->copy()
                    ->addDays(3)
                    ->setTime(16, 0),
            ],

            [
                'key' => 'demo-usage-010',
                'usage_units' => 700,
                'occurred_at' => $periodStart->copy()
                    ->addDays(4)
                    ->setTime(12, 0),
            ],
        ];

        foreach ($premiumUsageEvents as $event) {
            UsageEvent::updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                    'idempotency_key' => $event['key'],
                ],
                [
                    'customer_id' => $customer2->id,
                    'subscription_id' => $subscription2->id,
                    'subscription_period_id' => $subscriptionPeriod2->id,
                    'usage_units' => $event['usage_units'],
                    'unit_name' => 'API Calls',
                    'occurred_at' => $event['occurred_at'],
                    'metadata' => [
                        'source' => 'demo-seeder',
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Done
        |--------------------------------------------------------------------------
        */

        $this->command?->info(
            'Demo billing data seeded successfully.'
        );

        $this->command?->info(
            '10 usage events created.'
        );
    }
}