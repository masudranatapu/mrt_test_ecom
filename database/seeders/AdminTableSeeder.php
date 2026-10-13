<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
            ]
        );
    }
}
