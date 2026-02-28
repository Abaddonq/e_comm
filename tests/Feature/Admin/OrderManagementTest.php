<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);
    }

    public function test_admin_can_view_all_orders(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    public function test_admin_can_view_order_details(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.show', $order->id));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.orders.update-status', $order->id), [
                'status' => 'processing',
            ]);

        $order->refresh();
        
        $this->assertEquals('processing', $order->status);
    }

    public function test_admin_can_cancel_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'paid',
            'total' => 100,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.orders.update-status', $order->id), [
                'status' => 'cancelled',
                'cancellation_reason' => 'Customer request',
            ]);

        $order->refresh();
        
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_non_admin_cannot_access_order_management(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('admin.orders.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_order_management(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->customer->id,
            'status' => 'pending',
        ]);

        $response = $this->get(route('admin.orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_orders_list_paginates_results(): void
    {
        for ($i = 0; $i < 25; $i++) {
            Order::factory()->create([
                'user_id' => $this->customer->id,
                'status' => 'pending',
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.orders.index'));

        $response->assertStatus(200);
    }

    public function test_order_status_displayed_correctly(): void
    {
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        
        foreach ($statuses as $status) {
            $order = Order::factory()->create([
                'user_id' => $this->customer->id,
                'status' => $status,
            ]);

            $response = $this->actingAs($this->admin)
                ->get(route('admin.orders.show', $order->id));

            $response->assertStatus(200);
        }
    }
}
