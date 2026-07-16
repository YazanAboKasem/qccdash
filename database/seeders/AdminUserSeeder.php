<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sephora.local'],
            [
                'name' => 'MY SEPHORA Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@sephora.local'],
            [
                'name' => 'Campaign Manager',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@sephora.local'],
            [
                'name' => 'Report Viewer',
                'password' => bcrypt('password'),
                'role' => 'viewer',
                'is_active' => true,
            ]
        );
    }
}
