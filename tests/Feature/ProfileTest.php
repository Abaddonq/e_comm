<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile/update-profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertOk();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile/update-profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response->assertOk();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete('/profile/account', [
                'password' => 'password',
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete('/profile/account', [
                'password' => 'wrong-password',
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_cannot_delete_account_with_active_orders(): void
    {
        $user = User::factory()->create();
        
        \App\Models\Order::create([
            'user_id' => $user->id,
            'order_number' => 'TEST-001',
            'status' => 'paid',
            'shipping_name' => 'Test User',
            'shipping_phone' => '05555555555',
            'shipping_address_line1' => 'Test Street 123',
            'shipping_city' => 'Istanbul',
            'shipping_postal_code' => '34000',
            'shipping_country' => 'TR',
            'subtotal' => 100,
            'shipping_cost' => 10,
            'tax' => 10,
            'total' => 120,
            'payment_method' => 'credit_card',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete('/profile/account', [
                'password' => 'password',
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_can_delete_account_with_completed_orders(): void
    {
        $user = User::factory()->create();
        
        \App\Models\Order::create([
            'user_id' => $user->id,
            'order_number' => 'TEST-001',
            'status' => 'delivered',
            'shipping_name' => 'Test User',
            'shipping_phone' => '05555555555',
            'shipping_address_line1' => 'Test Street 123',
            'shipping_city' => 'Istanbul',
            'shipping_postal_code' => '34000',
            'shipping_country' => 'TR',
            'subtotal' => 100,
            'shipping_cost' => 10,
            'tax' => 10,
            'total' => 120,
            'payment_method' => 'credit_card',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete('/profile/account', [
                'password' => 'password',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
