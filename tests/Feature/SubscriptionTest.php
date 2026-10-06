<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubscriptionTest extends TestCase
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

    public function test_admin_can_list_subscriptions(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $response = $this->getJson('/api/subscriptions');

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

    public function test_admin_can_create_subscription(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
            'cancelled_at' => null,
        ];

        $response = $this->postJson(
            '/api/subscriptions',
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

        $this->assertDatabaseHas('subscriptions', [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_subscription(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $subscription->id
            );
    }

    public function test_admin_can_update_subscription(): void
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
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'cancelled',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
            'cancelled_at' => '2026-10-15 10:00:00',
        ];

        $response = $this->putJson(
            "/api/subscriptions/{$subscription->id}",
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_admin_can_delete_non_active_subscription(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan,
            [
                'status' => 'cancelled',
                'cancelled_at' => '2026-10-15 10:00:00',
            ]
        );

        $response = $this->deleteJson(
            "/api/subscriptions/{$subscription->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('subscriptions', [
            'id' => $subscription->id,
        ]);
    }

    public function test_create_subscription_requires_valid_data(): void
    {
        $response = $this->postJson('/api/subscriptions', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'merchant_id',
            'customer_id',
            'plan_id',
            'started_at',
            'current_period_start',
            'current_period_end',
        ]);
    }

    public function test_customer_and_plan_must_belong_to_same_merchant(): void
    {
        $merchantOne = $this->createMerchant();
        $merchantTwo = $this->createMerchant();

        $customer = $this->createCustomer($merchantOne);
        $plan = $this->createPlan($merchantTwo);

        $payload = [
            'merchant_id' => $merchantOne->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
        ];

        $response = $this->postJson(
            '/api/subscriptions',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_inactive_plan_cannot_be_used_for_active_subscription(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);

        $plan = $this->createPlan($merchant, [
            'status' => 'inactive',
        ]);

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
        ];

        $response = $this->postJson(
            '/api/subscriptions',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_duplicate_active_subscription_is_rejected(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-11-01 00:00:00',
            'current_period_start' => '2026-11-01 00:00:00',
            'current_period_end' => '2026-11-30 23:59:59',
        ];

        $response = $this->postJson(
            '/api/subscriptions',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_subscription_period_dates_must_be_valid(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);
        $plan = $this->createPlan($merchant);

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-10-10 00:00:00',
            'current_period_start' => '2026-10-15 00:00:00',
            'current_period_end' => '2026-10-01 23:59:59',
        ];

        $response = $this->postJson(
            '/api/subscriptions',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_normal_user_cannot_create_subscription(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $payload = [
            'merchant_id' => 1,
            'customer_id' => 1,
            'plan_id' => 1,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
        ];

        $response = $this->postJson(
            '/api/subscriptions',
            $payload
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_create_subscription(): void
    {
        $this->app['auth']->forgetGuards();

        $payload = [
            'merchant_id' => 1,
            'customer_id' => 1,
            'plan_id' => 1,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
        ];

        $response = $this->postJson(
            '/api/subscriptions',
            $payload
        );

        $response->assertStatus(401);
    }
}