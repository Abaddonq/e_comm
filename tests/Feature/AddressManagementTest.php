<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_addresses(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/profile?tab=addresses');

        $response->assertStatus(200);
        $response->assertSee($address->full_name);
    }

    public function test_user_cannot_view_other_users_addresses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/profile?tab=addresses');

        $response->assertStatus(200);
        $response->assertDontSee($address->full_name);
    }

    public function test_user_can_create_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/addresses', [
            'full_name' => 'John Doe',
            'phone' => '+905555555555',
            'address_line1' => 'Test Street 123',
            'city' => 'Istanbul',
            'postal_code' => '34000',
            'country' => 'TR',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'full_name' => 'John Doe',
            'city' => 'Istanbul',
        ]);
    }

    public function test_user_can_update_their_address(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put('/addresses/' . $address->id, [
            'full_name' => 'Updated Name',
            'phone' => '+905555555555',
            'address_line1' => 'Updated Street 456',
            'city' => 'Ankara',
            'postal_code' => '06000',
            'country' => 'TR',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'full_name' => 'Updated Name',
            'city' => 'Ankara',
        ]);
    }

    public function test_user_cannot_update_other_users_address(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put('/addresses/' . $address->id, [
            'full_name' => 'Hacked Name',
            'phone' => '+905555555555',
            'address_line1' => 'Hacked Street',
            'city' => 'Hack',
            'postal_code' => '00000',
            'country' => 'Hack',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_address(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete('/addresses/' . $address->id);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_cannot_delete_address_with_active_orders(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_number' => 'TEST-001',
            'status' => 'paid',
            'shipping_name' => $address->full_name,
            'shipping_phone' => $address->phone,
            'shipping_address_line1' => $address->address_line1,
            'shipping_city' => $address->city,
            'shipping_postal_code' => $address->postal_code,
            'shipping_country' => $address->country,
            'subtotal' => 100,
            'shipping_cost' => 10,
            'tax' => 10,
            'total' => 120,
            'payment_method' => 'credit_card',
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete('/addresses/' . $address->id);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }

    public function test_can_delete_address_with_completed_orders(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'order_number' => 'TEST-001',
            'status' => 'delivered',
            'shipping_name' => $address->full_name,
            'shipping_phone' => $address->phone,
            'shipping_address_line1' => $address->address_line1,
            'shipping_city' => $address->city,
            'shipping_postal_code' => $address->postal_code,
            'shipping_country' => $address->country,
            'subtotal' => 100,
            'shipping_cost' => 10,
            'tax' => 10,
            'total' => 120,
            'payment_method' => 'credit_card',
        ]);

        $response = $this->actingAs($user)->delete('/addresses/' . $address->id);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_guest_cannot_access_address_create_page(): void
    {
        $response = $this->get('/addresses/create');

        $response->assertRedirect('/login');
    }

    public function test_address_validation_requires_all_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/addresses', []);

        $response->assertSessionHasErrors([
            'full_name',
            'phone',
            'address_line1',
            'city',
            'postal_code',
            'country',
        ]);
    }
}
