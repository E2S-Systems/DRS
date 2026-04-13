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

test('authenticated user with view permission can show a user', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $response = $this->actingAsUser($manager)
        ->getJson("/api/v1/users/{$targetUser->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'role',
                'is_active',
                'last_login_at',
                'created_at',
                'updated_at',
            ],
            'success',
            'message',
        ])
        ->assertJsonPath('data.id', $targetUser->id)
        ->assertJsonPath('data.first_name', 'John')
        ->assertJsonPath('data.last_name', 'Doe')
        ->assertJsonPath('data.email', 'john.doe@example.com')
        ->assertJsonPath('success', true);
});

test('request for non-existent user returns 404', function () {
    $manager = User::factory()->manager()->create();
    $nonExistentId = 99999;

    $response = $this->actingAsUser($manager)
        ->getJson("/api/v1/users/{$nonExistentId}");

    $response->assertNotFound();
});

test('unauthenticated request returns 401', function () {
    $user = User::factory()->employee()->create();

    $response = $this->getJson("/api/v1/users/{$user->id}");

    $response->assertUnauthorized();
});

test('authenticated user without view permission returns 403', function () {
    $employee = User::factory()->employee()->create();
    $targetUser = User::factory()->employee()->create();

    $response = $this->actingAsUser($employee)
        ->getJson("/api/v1/users/{$targetUser->id}");

    $response->assertForbidden();
});

test('response includes all user resource fields', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->admin()->create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'is_active' => true,
    ]);

    $response = $this->actingAsUser($manager)
        ->getJson("/api/v1/users/{$targetUser->id}");

    $response->assertOk();

    $data = $response->json('data');
    expect($data)->toHaveKeys([
        'id',
        'first_name',
        'last_name',
        'email',
        'role',
        'is_active',
        'last_login_at',
        'created_at',
        'updated_at',
    ])
        ->and($data['id'])->toBe($targetUser->id)
        ->and($data['is_active'])->toBe(true)
        ->and($data['role'])->toBe('admin');
});
