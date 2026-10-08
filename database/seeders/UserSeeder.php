<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@crm.com',
                'password' => 'password',
                'role' => 'admin',
            ],
            [
                'name' => 'Sales Manager',
                'email' => 'sales.manager@crm.com',
                'password' => 'password',
                'role' => 'sales_manager',
            ],
            [
                'name' => 'Sales Representative',
                'email' => 'sales.rep@crm.com',
                'password' => 'password',
                'role' => 'sales_representative',
            ],
            [
                'name' => 'Support Agent',
                'email' => 'support@crm.com',
                'password' => 'password',
                'role' => 'support_agent',
            ],
            [
                'name' => 'Accountant',
                'email' => 'accountant@crm.com',
                'password' => 'password',
                'role' => 'accountant',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                [
                    'email' => $userData['email'],
                ],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}
