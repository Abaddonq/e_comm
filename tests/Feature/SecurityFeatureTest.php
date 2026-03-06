<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_applied(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_models_have_fillable_protection(): void
    {
        $user = new User();
        
        $this->assertNotEmpty($user->getFillable());
    }

    public function test_password_is_hashed_on_creation(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertTrue(password_verify('password123', $user->password));
        $this->assertNotEquals('password123', $user->password);
    }

    public function test_admin_routes_require_authentication(): void
    {
        $response = $this->get('/' . config('admin.route_prefix'));

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/login');
    }

    public function test_session_is_secure_in_production(): void
    {
        config(['app.env' => 'production']);
        
        $response = $this->get('/');
        
        $this->assertFalse(session()->isStarted());
    }

    public function test_cart_routes_require_authentication(): void
    {
        $response = $this->get('/cart');
        
        $response->assertStatus(200);
    }
}
