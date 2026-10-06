<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerTest extends TestCase
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

    public function test_admin_can_list_customers(): void
    {
        $merchant = $this->createMerchant();

        $this->createCustomer($merchant);
        $this->createCustomer($merchant);
        $this->createCustomer($merchant);

        $response = $this->getJson('/api/customers');

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

    public function test_admin_can_create_customer(): void
    {
        $merchant = $this->createMerchant();

        $payload = [
            'merchant_id' => $merchant->id,
            'name' => 'John Customer',
            'email' => 'john@example.com',
            'code' => 'CUST-001',
            'phone' => '+919876543210',
            'status' => 'active',
            'metadata' => [
                'source' => 'website',
            ],
        ];

        $response = $this->postJson('/api/customers', $payload);

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

        $this->assertDatabaseHas('customers', [
            'merchant_id' => $merchant->id,
            'name' => 'John Customer',
            'email' => 'john@example.com',
            'code' => 'CUST-001',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_customer(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);

        $response = $this->getJson(
            "/api/customers/{$customer->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $customer->id
            );
    }

    public function test_admin_can_update_customer(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer($merchant, [
            'name' => 'Old Customer',
            'email' => 'old@example.com',
            'code' => 'OLD-CODE',
        ]);

        $payload = [
            'merchant_id' => $merchant->id,
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'code' => 'UPDATED-CODE',
            'phone' => '+919999999999',
            'status' => 'inactive',
            'metadata' => [
                'source' => 'updated',
            ],
        ];

        $response = $this->putJson(
            "/api/customers/{$customer->id}",
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'code' => 'UPDATED-CODE',
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_delete_customer(): void
    {
        $merchant = $this->createMerchant();
        $customer = $this->createCustomer($merchant);

        $response = $this->deleteJson(
            "/api/customers/{$customer->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('customers', [
            'id' => $customer->id,
        ]);
    }

    public function test_create_customer_requires_valid_data(): void
    {
        $response = $this->postJson('/api/customers', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'merchant_id',
            'code',
            'name',
            'status',
        ]);
    }

    public function test_customer_code_must_be_unique_for_same_merchant(): void
    {
        $merchant = $this->createMerchant();

        $this->createCustomer($merchant, [
            'code' => 'DUPLICATE-CODE',
        ]);

        $payload = [
            'merchant_id' => $merchant->id,
            'name' => 'Another Customer',
            'email' => 'another@example.com',
            'code' => 'DUPLICATE-CODE',
            'phone' => '+1234567890',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/customers',
            $payload
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'code',
            ]);
    }

    public function test_same_customer_email_can_be_used_by_different_merchants(): void
    {
        $merchantOne = $this->createMerchant();
        $merchantTwo = $this->createMerchant();

        $this->createCustomer($merchantOne, [
            'email' => 'shared@example.com',
        ]);

        $payload = [
            'merchant_id' => $merchantTwo->id,
            'name' => 'Merchant Two Customer',
            'email' => 'shared@example.com',
            'code' => 'CUSTOMER-TWO',
            'phone' => '+1234567890',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/customers',
            $payload
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'merchant_id' => $merchantTwo->id,
            'email' => 'shared@example.com',
        ]);
    }

    public function test_normal_user_cannot_create_customer(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $payload = [
            'merchant_id' => 1,
            'name' => 'Unauthorized Customer',
            'email' => 'unauthorized@example.com',
            'code' => 'UNAUTHORIZED',
            'phone' => '+1234567890',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/customers',
            $payload
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_create_customer(): void
    {
        $this->app['auth']->forgetGuards();

        $payload = [
            'merchant_id' => 1,
            'name' => 'Guest Customer',
            'email' => 'guest@example.com',
            'code' => 'GUEST-CUSTOMER',
            'phone' => '+1234567890',
            'status' => 'active',
        ];

        $response = $this->postJson(
            '/api/customers',
            $payload
        );

        $response->assertStatus(401);
    }
}