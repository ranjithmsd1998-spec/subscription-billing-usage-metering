<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Models\UsageEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsageEventTest extends TestCase
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

    public function test_admin_can_list_usage_events(): void
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
            $period
        );

        $response = $this->getJson('/api/usage-events');

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

    public function test_admin_can_create_usage_event(): void
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
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => 'USAGE-' . uniqid(),
            'usage_units' => 250,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 12:00:00',
            'metadata' => [
                'source' => 'api',
            ],
        ];

        $response = $this->postJson(
            '/api/usage-events',
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

        $this->assertDatabaseHas('usage_events', [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => $payload['idempotency_key'],
            'usage_units' => 250,
            'unit_name' => 'units',
        ]);
    }

    public function test_admin_can_view_usage_event(): void
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

        $event = $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period
        );

        $response = $this->getJson(
            "/api/usage-events/{$event->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $event->id
            );
    }

    public function test_admin_can_update_usage_event(): void
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

        $event = $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period
        );

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => $event->idempotency_key,
            'usage_units' => 500,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 14:00:00',
            'metadata' => [
                'source' => 'updated-api',
            ],
        ];

        $response = $this->putJson(
            "/api/usage-events/{$event->id}",
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('usage_events', [
            'id' => $event->id,
            'usage_units' => 500,
        ]);
    }

    public function test_admin_cannot_delete_usage_event(): void
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

        $event = $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period
        );

        $response = $this->deleteJson(
            "/api/usage-events/{$event->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'usage_event',
            ]);
        $this->assertDatabaseHas('usage_events', [
            'id' => $event->id,
        ]);
    }

    public function test_create_usage_event_requires_valid_data(): void
    {
        $response = $this->postJson(
            '/api/usage-events',
            []
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'merchant_id',
            'customer_id',
            'subscription_id',
            'subscription_period_id',
            'idempotency_key',
            'usage_units',
            'occurred_at',
        ]);
    }

    public function test_duplicate_idempotency_key_is_rejected(): void
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

        $idempotencyKey = 'DUPLICATE-' . uniqid();

        $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period,
            [
                'idempotency_key' => $idempotencyKey,
            ]
        );

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => $idempotencyKey,
            'usage_units' => 200,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 12:00:00',
        ];

        $response = $this->postJson(
            '/api/usage-events',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_negative_usage_units_are_rejected(): void
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
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => 'NEGATIVE-' . uniqid(),
            'usage_units' => -10,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 12:00:00',
        ];

        $response = $this->postJson(
            '/api/usage-events',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_usage_event_relationships_must_belong_to_same_merchant(): void
    {
        $merchantOne = $this->createMerchant();
        $merchantTwo = $this->createMerchant();

        $customer = $this->createCustomer($merchantOne);
        $plan = $this->createPlan($merchantOne);

        $subscription = $this->createSubscription(
            $merchantOne,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $payload = [
            'merchant_id' => $merchantTwo->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => 'MERCHANT-MISMATCH-' . uniqid(),
            'usage_units' => 100,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 12:00:00',
        ];

        $response = $this->postJson(
            '/api/usage-events',
            $payload
        );

        $response->assertStatus(422);
    }

    public function test_normal_user_cannot_create_usage_event(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $response = $this->postJson(
            '/api/usage-events',
            []
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_create_usage_event(): void
    {
        $this->app['auth']->forgetGuards();

        $response = $this->postJson(
            '/api/usage-events',
            []
        );

        $response->assertStatus(401);
    }
}