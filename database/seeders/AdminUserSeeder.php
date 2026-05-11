<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'data' => [
                    'name'      => 'Super Admin',
                    'email'     => 'admin@arenabook.com',
                    'password'  => Hash::make('password'),
                    'role'      => 'super_admin',
                    'is_active' => true,
                ],
                'role' => 'super_admin',
            ],
            [
                'data' => [
                    'name'      => 'Venue Manager',
                    'email'     => 'manager@arenabook.com',
                    'password'  => Hash::make('password'),
                    'role'      => 'admin',
                    'is_active' => true,
                ],
                'role' => 'admin',
            ],
            [
                'data' => [
                    'name'      => 'Staff Member',
                    'email'     => 'staff@arenabook.com',
                    'password'  => Hash::make('password'),
                    'role'      => 'staff',
                    'is_active' => true,
                ],
                'role' => 'staff',
            ],
        ];

        foreach ($users as $entry) {
            $user = User::firstOrCreate(
                ['email' => $entry['data']['email']],
                $entry['data']
            );
            $user->assignRole($entry['role']);
        }
    }
}
