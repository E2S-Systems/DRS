<?php

declare(strict_types=1);

use App\Enums\RoleUser;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed([
        PermissionsSeeder::class,
        RolesSeeder::class,
    ]);
});

test('authenticated user with create permission can create a user', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'first_name' => 'New',
        'last_name' => 'User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => RoleUser::BRANCH_EMPLOYEE->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
            ],
            'success',
            'message',
        ])
        ->assertJsonPath('data.first_name', 'New')
        ->assertJsonPath('data.last_name', 'User')
        ->assertJsonPath('data.email', 'newuser@example.com')
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('users', [
        'first_name' => 'New',
        'last_name' => 'User',
        'email' => 'newuser@example.com',
    ]);
});

test('created user has correct role assigned', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'first_name' => 'Manager',
        'last_name' => 'Test',
        'email' => 'manager.test@example.com',
        'password' => 'securepass',
        'password_confirmation' => 'securepass',
        'role' => RoleUser::BRANCH_MANAGER->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertCreated();

    $userId = $response->json('data.id');
    $user = User::find($userId);

    expect($user->hasRole(RoleUser::BRANCH_MANAGER->value))->toBeTrue();
});

test('missing first_name returns 422', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name']);
});

test('invalid email format returns 422', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'not-an-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('duplicate email returns 422', function () {
    $manager = User::factory()->manager()->create();
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $payload = [
        'first_name' => 'Duplicate',
        'last_name' => 'User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('invalid role enum returns 422', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'invalid-role',
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['role']);
});

test('password less than 8 characters returns 422', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('password confirmation mismatch returns 422', function () {
    $manager = User::factory()->manager()->create();

    $payload = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->actingAsUser($manager)
        ->postJson('/api/v1/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('unauthenticated request returns 401', function () {
    $payload = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->postJson('/api/v1/users', $payload);

    $response->assertUnauthorized();
});

test('authenticated user without create permission returns 403', function () {
    $employee = User::factory()->employee()->create();

    $payload = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => RoleUser::ADMIN->value,
    ];

    $response = $this->actingAsUser($employee)
        ->postJson('/api/v1/users', $payload);

    $response->assertForbidden();
});
