<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyUsageAggregate;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Models\User;
use App\Models\UsageEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DailyUsageAggregateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        );
    }

    private function createMerchant(
        array $overrides = []
    ): Merchant {
        return Merchant::create(array_merge([
            'name' => 'Test Merchant',
            'code' => 'MERCHANT-' . uniqid(),
            'email' => 'merchant-' . uniqid() . '@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'phone' => '+1234567890',
        ], $overrides));
    }

    private function createCustomer(
        Merchant $merchant,
        array $overrides = []
    ): Customer {
        return Customer::create(array_merge([
            'merchant_id' => $merchant->id,
            'name' => 'Test Customer',
            'email' => 'customer-' . uniqid() . '@example.com',
            'code' => 'CUSTOMER-' . uniqid(),
            'phone' => '+1234567890',
            'status' => 'active',
            'metadata' => null,
        ], $overrides));
    }

    private function createPlan(
        Merchant $merchant,
        array $overrides = []
    ): Plan {
        return Plan::create(array_merge([
            'merchant_id' => $merchant->id,
            'name' => 'Basic Plan',
            'code' => 'PLAN-' . uniqid(),
            'description' => 'Basic subscription plan',
            'currency' => 'USD',
            'base_price' => 499.00,
            'billing_cycle' => 'monthly',
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'unit_name' => 'units',
            'status' => 'active',
        ], $overrides));
    }

    private function createSubscription(
        Merchant $merchant,
        Customer $customer,
        Plan $plan,
        array $overrides = []
    ): Subscription {
        return Subscription::create(array_merge([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
            'cancelled_at' => null,
        ], $overrides));
    }

    private function createSubscriptionPeriod(
        Subscription $subscription,
        Plan $plan,
        array $overrides = []
    ): SubscriptionPeriod {
        return SubscriptionPeriod::create(array_merge([
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => '2026-10-01 00:00:00',
            'ends_at' => '2026-10-31 23:59:59',
            'base_price' => 499.00,
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'billing_cycle' => 'monthly',
        ], $overrides));
    }

    private function createUsageEvent(
        Merchant $merchant,
        Customer $customer,
        Subscription $subscription,
        SubscriptionPeriod $period,
        array $overrides = []
    ): UsageEvent {
        return UsageEvent::create(array_merge([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => 'EVENT-' . uniqid(),
            'usage_units' => 100,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 10:00:00',
            'metadata' => null,
        ], $overrides));
    }

    private function createAggregate(
        Merchant $merchant,
        Customer $customer,
        Subscription $subscription,
        SubscriptionPeriod $period,
        array $overrides = []
    ): DailyUsageAggregate {
        return DailyUsageAggregate::create(array_merge([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'usage_date' => '2026-10-10 00:00:00',
            'total_usage_units' => 100,
            'unit_name' => 'units',
            'event_count' => 1,
        ], $overrides));
    }

    public function test_admin_can_list_daily_usage_aggregates(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $this->createAggregate(
            $merchant,
            $customer,
            $subscription,
            $period
        );

        $response = $this->getJson(
            "/api/daily-usage-aggregates?subscription_period_id={$period->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    public function test_admin_can_view_daily_usage_aggregate(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $aggregate = $this->createAggregate(
            $merchant,
            $customer,
            $subscription,
            $period
        );

        $response = $this->getJson(
            "/api/daily-usage-aggregates/{$aggregate->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $aggregate->id
            );
    }

    public function test_admin_can_generate_daily_usage_aggregate(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period,
            [
                'idempotency_key' => 'EVENT-1-' . uniqid(),
                'usage_units' => 100,
                'occurred_at' => '2026-10-10 10:00:00',
            ]
        );

        $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period,
            [
                'idempotency_key' => 'EVENT-2-' . uniqid(),
                'usage_units' => 250,
                'occurred_at' => '2026-10-10 15:00:00',
            ]
        );

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'usage_date' => '2026-10-10 00:00:00',
        ];

        $response = $this->postJson(
            '/api/daily-usage-aggregates/generate',
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'daily_usage_aggregates',
            [
                'merchant_id' => $merchant->id,
                'customer_id' => $customer->id,
                'subscription_id' => $subscription->id,
                'subscription_period_id' => $period->id,
                'usage_date' => '2026-10-10 00:00:00',
                'total_usage_units' => 350,
                'event_count' => 2,
            ]
        );
    }

    public function test_generate_daily_usage_aggregate_is_idempotent(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period,
            [
                'usage_units' => 300,
                'occurred_at' => '2026-10-10 10:00:00',
            ]
        );

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'usage_date' => '2026-10-10 00:00:00',
        ];

        $firstResponse = $this->postJson(
            '/api/daily-usage-aggregates/generate',
            $payload
        );

        $firstResponse->assertStatus(200);

        $secondResponse = $this->postJson(
            '/api/daily-usage-aggregates/generate',
            $payload
        );

        $secondResponse->assertStatus(200);

        $this->assertDatabaseCount(
            'daily_usage_aggregates',
            1
        );

        $this->assertDatabaseHas(
            'daily_usage_aggregates',
            [
                'subscription_id' => $subscription->id,
                'usage_date' => '2026-10-10 00:00:00',
                'total_usage_units' => 300,
                'event_count' => 1,
            ]
        );
    }

    public function test_generate_requires_valid_data(): void
    {
        $response = $this->postJson(
            '/api/daily-usage-aggregates/generate',
            []
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'subscription_period_id',
            'usage_date',
        ]);
    }


    public function test_normal_user_cannot_generate_daily_usage_aggregate(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $response = $this->postJson(
            '/api/daily-usage-aggregates/generate',
            []
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_generate_daily_usage_aggregate(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->postJson(
            '/api/daily-usage-aggregates/generate',
            []
        );

        $response->assertStatus(401);
    }
}