<?php

namespace Database\Seeders;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@drs.systems'],
            [
                'first_name' => 'DRS',
                'last_name' => 'Admin',
                'password' => 'drs@123456',
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => now(),
            ]
        );
        $admin->assignRole(RoleUser::ADMIN->value);

        User::factory(100)->create();
    }
}
