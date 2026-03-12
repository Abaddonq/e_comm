<?php

use App\Support\OrderStatusMapper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('fulfillment_status', 40)->nullable()->after('status');
            $table->string('payment_status', 40)->nullable()->after('fulfillment_status');
            $table->string('return_status', 40)->nullable()->after('payment_status');
            $table->timestamp('status_updated_at')->nullable()->after('return_status');

            $table->index('fulfillment_status');
            $table->index('payment_status');
            $table->index('return_status');
            $table->index('status_updated_at');
        });

        DB::table('orders')
            ->select(['id', 'status', 'paid_at', 'cancelled_at', 'updated_at'])
            ->orderBy('id')
            ->chunkById(250, function ($orders) {
                foreach ($orders as $order) {
                    $mapped = OrderStatusMapper::mapLegacyStatus((string) $order->status);

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'fulfillment_status' => $mapped['fulfillment_status'],
                            'payment_status' => $mapped['payment_status'],
                            'return_status' => $mapped['return_status'],
                            'status_updated_at' => $order->cancelled_at ?? $order->paid_at ?? $order->updated_at ?? now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['fulfillment_status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['return_status']);
            $table->dropIndex(['status_updated_at']);

            $table->dropColumn([
                'fulfillment_status',
                'payment_status',
                'return_status',
                'status_updated_at',
            ]);
        });
    }
};
