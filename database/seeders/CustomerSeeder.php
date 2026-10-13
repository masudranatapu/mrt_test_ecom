<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Demo customers: one approved (sees prices), one waiting for approval.
     */
    public function run(): void
    {
        $approved = Customer::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'phone' => '01712345678',
                'business_name' => 'Demo Traders',
                'password' => 'password',
                'is_approved' => true,
                'approved_at' => now(),
                'status' => true,
            ]
        );

        $approved->addresses()->firstOrCreate(
            ['label' => 'Home'],
            [
                'name' => 'Demo Customer',
                'phone' => '01712345678',
                'address_line' => 'House 12, Road 5, Block C',
                'area' => 'Mirpur',
                'city' => 'Dhaka',
                'postal_code' => '1216',
                'is_default' => true,
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'pending@example.com'],
            [
                'name' => 'Pending Customer',
                'phone' => '01812345678',
                'password' => 'password',
                'is_approved' => false,
                'status' => true,
            ]
        );
    }
}
