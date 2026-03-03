<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function ($table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function ($table) {
            $table->foreignId('user_id')->change();
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->change();
        });
    }
};
