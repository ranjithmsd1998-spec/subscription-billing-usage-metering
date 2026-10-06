<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlanTest extends TestCase
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

    private function createMerchant(array $overrides = []): Merchant
    {
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

    private function createPlan(
        Merchant $merchant,
        array $overrides = []
    ): Plan {
        return Plan::create(array_merge([
            'merchant_id' => $merchant->id,
            'name' => 'Basic Plan',
            'code' => 'BASIC-' . uniqid(),
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

    public function test_admin_can_list_plans(): void
    {
        $merchant = $this->createMerchant();

        $this->createPlan($merchant);
        $this->createPlan($merchant);

        $response = $this->getJson('/api/plans');

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

    public function test_admin_can_create_plan(): void
    {
        $merchant = $this->createMerchant();

        $payload = [
            'merchant_id' => $merchant->id,
            'name' => 'Premium Plan',
            'code' => 'PREMIUM',
            'description' => 'Premium subscription plan',
            'currency' => 'USD',
            'base_price' => 999.00,
            'billing_cycle' => 'monthly',
            'included_units' => 5000,
            'overage_rate' => 0.250000,
            'unit_name' => 'units',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/plans', $payload);

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

        $this->assertDatabaseHas('plans', [
            'merchant_id' => $merchant->id,
            'name' => 'Premium Plan',
            'code' => 'PREMIUM',
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'included_units' => 5000,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_plan(): void
    {
        $merchant = $this->createMerchant();
        $plan = $this->createPlan($merchant);

        $response = $this->getJson(
            "/api/plans/{$plan->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $plan->id
            );
    }

    public function test_admin_can_update_plan(): void
    {
        $merchant = $this->createMerchant();

        $plan = $this->createPlan($merchant, [
            'name' => 'Old Plan',
            'code' => 'OLD-PLAN',
            'base_price' => 499.00,
        ]);

        $payload = [
            'merchant_id' => $merchant->id,
            'name' => 'Updated Plan',
            'code' => 'UPDATED-PLAN',
            'description' => 'Updated subscription plan',
            'currency' => 'USD',
            'base_price' => 999.00,
            'billing_cycle' => 'yearly',
            'included_units' => 10000,
            'overage_rate' => 0.250000,
            'unit_name' => 'units',
            'status' => 'inactive',
        ];

        $response = $this->putJson(
            "/api/plans/{$plan->id}",
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'Updated Plan',
            'code' => 'UPDATED-PLAN',
            'base_price' => 999.00,
            'billing_cycle' => 'yearly',
            'included_units' => 10000,
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_delete_plan(): void
    {
        $merchant = $this->createMerchant();
        $plan = $this->createPlan($merchant);

        $response = $this->deleteJson(
            "/api/plans/{$plan->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('plans', [
            'id' => $plan->id,
        ]);
    }

    public function test_create_plan_requires_valid_data(): void
    {
        $response = $this->postJson('/api/plans', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'merchant_id',
            'name',
            'code',
            'base_price',
            'billing_cycle',
        ]);
    }

    public function test_plan_code_must_be_unique_for_same_merchant(): void
    {
        $merchant = $this->createMerchant();

        $this->createPlan($merchant, [
            'code' => 'DUPLICATE-CODE',
        ]);

        $payload = [
            'merchant_id' => $merchant->id,
            'name' => 'Another Plan',
            'code' => 'DUPLICATE-CODE',
            'description' => 'Another plan',
            'currency' => 'USD',
            'base_price' => 799.00,
            'billing_cycle' => 'monthly',
            'included_units' => 2000,
            'overage_rate' => 0.500000,
            'unit_name' => 'units',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/plans',
            $payload
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'code',
            ]);
    }

    public function test_same_plan_code_can_be_used_by_different_merchants(): void
    {
        $merchantOne = $this->createMerchant();
        $merchantTwo = $this->createMerchant();

        $this->createPlan($merchantOne, [
            'code' => 'SHARED-CODE',
        ]);

        $payload = [
            'merchant_id' => $merchantTwo->id,
            'name' => 'Merchant Two Plan',
            'code' => 'SHARED-CODE',
            'description' => 'Plan for merchant two',
            'currency' => 'USD',
            'base_price' => 599.00,
            'billing_cycle' => 'monthly',
            'included_units' => 1500,
            'overage_rate' => 0.500000,
            'unit_name' => 'units',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/plans',
            $payload
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('plans', [
            'merchant_id' => $merchantTwo->id,
            'code' => 'SHARED-CODE',
        ]);
    }

    public function test_normal_user_cannot_create_plan(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $payload = [
            'merchant_id' => 1,
            'name' => 'Unauthorized Plan',
            'code' => 'UNAUTHORIZED',
            'description' => 'Unauthorized plan',
            'currency' => 'USD',
            'base_price' => 499.00,
            'billing_cycle' => 'monthly',
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'unit_name' => 'units',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/plans',
            $payload
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_create_plan(): void
    {
        $this->app['auth']->forgetGuards();

        $payload = [
            'merchant_id' => 1,
            'name' => 'Guest Plan',
            'code' => 'GUEST-PLAN',
            'description' => 'Guest plan',
            'currency' => 'USD',
            'base_price' => 499.00,
            'billing_cycle' => 'monthly',
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'unit_name' => 'units',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/plans',
            $payload
        );

        $response->assertStatus(401);
    }
}