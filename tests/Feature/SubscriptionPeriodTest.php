<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriptionPeriodTest extends TestCase
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

    public function test_admin_can_list_subscription_periods(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->getJson('/api/subscription-periods');

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

    public function test_admin_can_create_subscription_period(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $payload = [
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => '2026-11-01 00:00:00',
            'ends_at' => '2026-11-30 23:59:59',
            'base_price' => 499.00,
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'billing_cycle' => 'monthly',
        ];

        $response = $this->postJson(
            '/api/subscription-periods',
            $payload
        );

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('subscription_periods', [
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'base_price' => 499.00,
            'included_units' => 1000,
            'billing_cycle' => 'monthly',
        ]);
    }

    public function test_admin_can_view_subscription_period(): void
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

        $response = $this->getJson(
            "/api/subscription-periods/{$period->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $period->id
            );
    }

    public function test_admin_can_update_subscription_period(): void
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

        $payload = [
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => '2026-10-01 00:00:00',
            'ends_at' => '2026-10-31 23:59:59',
            'base_price' => 599.00,
            'included_units' => 1500,
            'overage_rate' => 0.750000,
            'billing_cycle' => 'monthly',
        ];

        $response = $this->putJson(
            "/api/subscription-periods/{$period->id}",
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('subscription_periods', [
            'id' => $period->id,
            'starts_at' => '2026-10-01 00:00:00',
            'ends_at' => '2026-10-31 23:59:59',
        ]);
    }

    public function test_admin_can_delete_subscription_period(): void
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

        $response = $this->deleteJson(
            "/api/subscription-periods/{$period->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('subscription_periods', [
            'id' => $period->id,
        ]);
    }

    public function test_create_subscription_period_requires_valid_data(): void
    {
        $response = $this->postJson(
            '/api/subscription-periods',
            []
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'subscription_id',
            'starts_at',
        ]);
    }


    public function test_period_dates_must_be_valid(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $payload = [
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => '2026-10-31 00:00:00',
            'ends_at' => '2026-10-01 00:00:00',
            'base_price' => 499.00,
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'billing_cycle' => 'monthly',
        ];

        $response = $this->postJson(
            '/api/subscription-periods',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_normal_user_cannot_create_subscription_period(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $response = $this->postJson(
            '/api/subscription-periods',
            []
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_create_subscription_period(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->postJson(
            '/api/subscription-periods',
            []
        );

        $response->assertStatus(401);
    }
}