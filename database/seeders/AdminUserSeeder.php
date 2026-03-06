<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_SEED_EMAIL', 'admin@decoremotto.com');
        $password = env('ADMIN_SEED_PASSWORD');

        if (app()->environment('production') && empty($password)) {
            throw new \RuntimeException('ADMIN_SEED_PASSWORD must be set in production.');
        }

        if (empty($password)) {
            $password = Str::random(20);
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin User',
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        if (!app()->environment('production')) {
            $this->command->warn('Admin user created for local/testing use.');
            $this->command->warn('Email: ' . $email);
            $this->command->warn('Password: ' . $password);
        }
    }
}
