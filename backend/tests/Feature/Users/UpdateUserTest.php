<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed([
        PermissionsSeeder::class,
        RolesSeeder::class,
    ]);
});

test('authenticated user with update permission can update a user', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create([
        'first_name' => 'Old',
        'last_name' => 'Name',
        'email' => 'old@example.com',
    ]);

    $payload = [
        'first_name' => 'Updated',
        'last_name' => 'User',
        'email' => 'updated@example.com',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertOk()
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
        ->assertJsonPath('data.first_name', 'Updated')
        ->assertJsonPath('data.last_name', 'User')
        ->assertJsonPath('data.email', 'updated@example.com')
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'first_name' => 'Updated',
        'last_name' => 'User',
        'email' => 'updated@example.com',
    ]);
});

test('partial update only changes provided fields', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $payload = [
        'first_name' => 'Jane',
    ];

    $response = $this->actingAsUser($manager)
        ->patchJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.first_name', 'Jane');

    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);
});

test('can update password with confirmation', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create();

    $payload = [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertOk();

    $targetUser->refresh();
    expect(\Illuminate\Support\Facades\Hash::check('newpassword123', $targetUser->password))->toBeTrue();
});

test('invalid email format returns 422', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create();

    $payload = [
        'email' => 'not-an-email',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('duplicate email returns 422', function () {
    $manager = User::factory()->manager()->create();
    $existingUser = User::factory()->employee()->create([
        'email' => 'taken@example.com',
    ]);
    $targetUser = User::factory()->employee()->create([
        'email' => 'original@example.com',
    ]);

    $payload = [
        'email' => 'taken@example.com',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('can update to same email without unique validation error', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create([
        'email' => 'same@example.com',
    ]);

    $payload = [
        'first_name' => 'Updated',
        'email' => 'same@example.com',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertOk();
});

test('password less than 8 characters returns 422', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create();

    $payload = [
        'password' => 'short',
        'password_confirmation' => 'short',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('password confirmation mismatch returns 422', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create();

    $payload = [
        'password' => 'password123',
        'password_confirmation' => 'different',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('attempt to update non-existent user returns 404', function () {
    $manager = User::factory()->manager()->create();
    $nonExistentId = 99999;

    $payload = [
        'first_name' => 'Test',
    ];

    $response = $this->actingAsUser($manager)
        ->putJson("/api/v1/users/{$nonExistentId}", $payload);

    $response->assertNotFound();
});

test('unauthenticated request returns 401', function () {
    $targetUser = User::factory()->employee()->create();

    $payload = [
        'first_name' => 'Test',
    ];

    $response = $this->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertUnauthorized();
});

test('authenticated user without update permission returns 403', function () {
    $employee = User::factory()->employee()->create();
    $targetUser = User::factory()->employee()->create();

    $payload = [
        'first_name' => 'Test',
    ];

    $response = $this->actingAsUser($employee)
        ->putJson("/api/v1/users/{$targetUser->id}", $payload);

    $response->assertForbidden();
});
