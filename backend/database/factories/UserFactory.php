<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RoleUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'last_login_at' => now(),
        ];
    }

    /**
     * Indicate that the user should have a specific role assigned.
     *
     * @param  string|RoleUser  $role  The role to assign
     */
    public function withRole(string|RoleUser $role): static
    {
        return $this->afterCreating(function ($user) use ($role) {
            $roleName = $role instanceof RoleUser ? $role->value : $role;
            $user->assignRole($roleName);
        });
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->withRole(RoleUser::ADMIN);
    }

    /**
     * Indicate that the user should be a manager.
     */
    public function manager(): static
    {
        return $this->withRole(RoleUser::BRANCH_MANAGER);
    }

    /**
     * Indicate that the user should be an employee.
     */
    public function employee(): static
    {
        return $this->withRole(RoleUser::BRANCH_EMPLOYEE);
    }
}
