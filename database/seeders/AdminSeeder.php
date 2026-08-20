<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['super_admin', 'agent', 'owner', 'barber'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $adminRole = Role::where('name', 'super_admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@barbershop.test'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
