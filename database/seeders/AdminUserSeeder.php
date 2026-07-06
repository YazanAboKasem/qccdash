<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@adqcc.gov.ae'],
            [
                'name' => 'ADQCC Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@adqcc.gov.ae'],
            [
                'name' => 'Campaign Manager',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@adqcc.gov.ae'],
            [
                'name' => 'Report Viewer',
                'password' => bcrypt('password'),
                'role' => 'viewer',
                'is_active' => true,
            ]
        );
    }
}
