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

test('authenticated user with view permission can list users', function () {
    $manager = User::factory()->manager()->create();
    User::factory()->count(5)->create();

    $response = $this->actingAsUser($manager)
        ->getJson('/api/v1/users');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'counts' => [
                'total',
                'active',
                'inactive',
            ],
            'success',
            'message',
        ])
        ->assertJsonPath('success', true);
});

test('unauthenticated request returns 401', function () {
    $response = $this->getJson('/api/v1/users');

    $response->assertUnauthorized();
});

test('authenticated user without view permission returns 403', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->actingAsUser($employee)
        ->getJson('/api/v1/users');

    $response->assertForbidden();
});

test('returns paginated results', function () {
    $manager = User::factory()->manager()->create();
    User::factory()->count(15)->create();

    $response = $this->actingAsUser($manager)
        ->getJson('/api/v1/users?per_page=10');

    $response->assertOk()
        ->assertJsonStructure([
            'data',
            'links',
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ],
        ]);

    expect($response->json('meta.per_page'))->toBe(10)
        ->and(count($response->json('data')))->toBeLessThanOrEqual(10);
});

test('filters users by search term on first name', function () {
    $manager = User::factory()->manager()->create();

    $searchableUser = User::factory()->employee()->create([
        'first_name' => 'Unique',
        'last_name' => 'Person',
        'email' => 'unique@example.com',
    ]);

    // Create additional users with roles
    User::factory()->employee()->count(5)->create();

    $response = $this->actingAsUser($manager)
        ->getJson('/api/v1/users?search=unique');

    $response->assertOk();

    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['first_name'])->toBe('Unique');
});

test('filters users by search term on email', function () {
    $manager = User::factory()->manager()->create();

    $searchableUser = User::factory()->employee()->create([
        'email' => 'findme@special.com',
    ]);

    // Create additional users with roles
    User::factory()->employee()->count(5)->create();

    $response = $this->actingAsUser($manager)
        ->getJson('/api/v1/users?search=findme');

    $response->assertOk();

    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['email'])->toBe('findme@special.com');
});

test('filters users by active status', function () {
    $manager = User::factory()->manager()->create();

    // Create users with roles and specific active status
    User::factory()->employee()->count(3)->create(['is_active' => true]);
    User::factory()->employee()->count(2)->create(['is_active' => false]);

    $response = $this->actingAsUser($manager)
        ->getJson('/api/v1/users?status=1');

    $response->assertOk();

    $activeCount = $response->json('counts.active');
    expect($activeCount)->toBeGreaterThanOrEqual(3);
});

test('returns correct counts object', function () {
    $manager = User::factory()->manager()->create();

    // Create users with roles and specific active status
    User::factory()->employee()->count(5)->create(['is_active' => true]);
    User::factory()->employee()->count(3)->create(['is_active' => false]);

    $response = $this->actingAsUser($manager)
        ->getJson('/api/v1/users');

    $response->assertOk()
        ->assertJsonStructure([
            'counts' => [
                'total',
                'active',
                'inactive',
            ],
        ]);

    $counts = $response->json('counts');
    expect($counts['total'])->toBeGreaterThanOrEqual(8)
        ->and($counts['active'])->toBeGreaterThanOrEqual(5)
        ->and($counts['inactive'])->toBeGreaterThanOrEqual(3);
});
