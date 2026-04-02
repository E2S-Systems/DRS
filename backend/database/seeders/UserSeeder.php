<?php

declare(strict_types=1);

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

        User::factory()->count(10)->admin()->create();
        User::factory()->count(30)->manager()->create();
        User::factory()->count(60)->employee()->create();
    }
}
