<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_usage_endpoint_is_rate_limited_to_60_requests_per_minute(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        for ($attempt = 1; $attempt <= 60; $attempt++) {
            $this->postJson('/api/usage', [])->assertStatus(422);
        }

        $this->postJson('/api/usage', [])
            ->assertStatus(429);
    }
}
