<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Super admin + one demo staff member with limited permissions.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mrtdev@gmail.com'],
            [
                'name' => 'MRT Dev',
                'password' => 'password',
                'status' => true,
                'is_super_admin' => true,
                'permissions' => [],
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Demo Staff',
                'password' => 'password',
                'status' => true,
                'is_super_admin' => false,
                'permissions' => ['orders', 'products', 'inventory'],
            ]
        );
    }
}
