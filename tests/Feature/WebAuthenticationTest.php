<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response
            ->assertRedirect(route('login'));
    }

    public function test_user_can_login_to_web_application(): void
    {
        User::factory()->create([
            'email' => 'web@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'web@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_invalid_web_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'web@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'web@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertGuest();
    }
}
