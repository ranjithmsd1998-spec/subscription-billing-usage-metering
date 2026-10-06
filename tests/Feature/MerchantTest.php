<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MerchantTest extends TestCase
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
            'code' => 'TEST-' . uniqid(),
            'email' => 'merchant-' . uniqid() . '@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'phone' => '+1234567890',
        ], $overrides));
    }

    public function test_admin_can_list_merchants(): void
    {
        $this->createMerchant();
        $this->createMerchant();
        $this->createMerchant();

        $response = $this->getJson('/api/merchants');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    public function test_admin_can_create_merchant(): void
    {
        $payload = [
            'name' => 'Test Merchant',
            'code' => 'TEST-MERCHANT',
            'email' => 'merchant@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'phone' => '+1234567890',
        ];

        $response = $this->postJson('/api/merchants', $payload);

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

        $this->assertDatabaseHas('merchants', [
            'name' => 'Test Merchant',
            'code' => 'TEST-MERCHANT',
            'email' => 'merchant@example.com',
            'status' => 'active',
            'currency' => 'USD',
        ]);
    }

    public function test_admin_can_view_merchant(): void
    {
        $merchant = $this->createMerchant();

        $response = $this->getJson(
            "/api/merchants/{$merchant->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonPath(
                'data.id',
                $merchant->id
            );
    }

    public function test_admin_can_update_merchant(): void
    {
        $merchant = $this->createMerchant([
            'name' => 'Old Merchant Name',
            'code' => 'OLD-CODE',
        ]);

        $payload = [
            'name' => 'Updated Merchant Name',
            'code' => 'UPDATED-CODE',
            'email' => 'updated@example.com',
            'status' => 'inactive',
            'timezone' => 'Asia/Kolkata',
            'currency' => 'INR',
            'phone' => '+919876543210',
        ];

        $response = $this->putJson(
            "/api/merchants/{$merchant->id}",
            $payload
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('merchants', [
            'id' => $merchant->id,
            'name' => 'Updated Merchant Name',
            'code' => 'UPDATED-CODE',
            'status' => 'inactive',
            'currency' => 'USD',
        ]);
    }

    public function test_admin_can_delete_merchant(): void
    {
        $merchant = $this->createMerchant();

        $response = $this->deleteJson(
            "/api/merchants/{$merchant->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('merchants', [
            'id' => $merchant->id,
        ]);
    }

    public function test_create_merchant_requires_valid_data(): void
    {
        $response = $this->postJson('/api/merchants', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'name',
            'email',
            'code',
            'status',
        ]);
    }

    public function test_merchant_code_must_be_unique(): void
    {
        $this->createMerchant([
            'code' => 'DUPLICATE-CODE',
        ]);

        $payload = [
            'name' => 'Another Merchant',
            'code' => 'DUPLICATE-CODE',
            'email' => 'another@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
        ];

        $response = $this->postJson(
            '/api/merchants',
            $payload
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'code',
            ]);
    }

    public function test_normal_user_cannot_create_merchant(): void
    {
        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'user',
            ])
        );

        $payload = [
            'name' => 'Unauthorized Merchant',
            'code' => 'UNAUTHORIZED',
            'email' => 'unauthorized@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
        ];

        $response = $this->postJson(
            '/api/merchants',
            $payload
        );

        $response
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_guest_cannot_create_merchant(): void
    {
        $this->app['auth']->forgetGuards();

        $payload = [
            'name' => 'Guest Merchant',
            'code' => 'GUEST-MERCHANT',
            'email' => 'guest@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
        ];

        $response = $this->postJson(
            '/api/merchants',
            $payload
        );

        $response->assertStatus(401);
    }
}