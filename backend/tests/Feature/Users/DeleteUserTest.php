<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed([
        PermissionsSeeder::class,
        RolesSeeder::class,
    ]);
});

test('authenticated user with delete permission can soft delete a user', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->employee()->create([
        'first_name' => 'ToDelete',
        'last_name' => 'User',
    ]);

    $userId = $targetUser->id;
    $userCountBefore = User::withTrashed()->count();

    $response = $this->actingAsUser($admin)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

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
        ->assertJsonPath('success', true);

    // Assert soft delete: deleted_at is set
    $deletedUser = User::withTrashed()->find($userId);
    expect($deletedUser)->not->toBeNull()
        ->and($deletedUser->deleted_at)->not->toBeNull()
        ->and($deletedUser->trashed())->toBeTrue();

    // Assert record still exists in database (not hard deleted)
    $userCountAfter = User::withTrashed()->count();
    expect($userCountAfter)->toBe($userCountBefore);
    
    $this->assertDatabaseHas('users', [
        'id' => $userId,
    ]);
});

test('soft deleted user has deleted_at timestamp set', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->employee()->create();

    $response = $this->actingAsUser($admin)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

    $response->assertOk();

    $deletedUser = User::withTrashed()->find($targetUser->id);
    
    expect($deletedUser->deleted_at)
        ->not->toBeNull()
        ->and($deletedUser->deleted_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('soft deleted user is excluded from normal queries', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->employee()->create();
    $userId = $targetUser->id;

    $this->actingAsUser($admin)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

    // User should not be found in normal query
    $foundUser = User::find($userId);
    expect($foundUser)->toBeNull();

    // But should be found with withTrashed
    $trashedUser = User::withTrashed()->find($userId);
    expect($trashedUser)->not->toBeNull();
});

test('soft deleted user is not hard deleted from database', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->employee()->create();
    $userId = $targetUser->id;

    $this->actingAsUser($admin)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

    // Verify record still exists in raw database
    $rawRecord = DB::table('users')->where('id', $userId)->first();
    expect($rawRecord)->not->toBeNull()
        ->and($rawRecord->deleted_at)->not->toBeNull();
});

test('attempt to delete non-existent user returns 404', function () {
    $admin = User::factory()->admin()->create();
    $nonExistentId = 99999;

    $response = $this->actingAsUser($admin)
        ->deleteJson("/api/v1/users/{$nonExistentId}");

    $response->assertNotFound();
});

test('unauthenticated request returns 401', function () {
    $targetUser = User::factory()->employee()->create();

    $response = $this->deleteJson("/api/v1/users/{$targetUser->id}");

    $response->assertUnauthorized();
});

test('authenticated user without delete permission returns 403', function () {
    $manager = User::factory()->manager()->create();
    $targetUser = User::factory()->employee()->create();

    $response = $this->actingAsUser($manager)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

    $response->assertForbidden();
});

test('employee without delete permission cannot delete users', function () {
    $employee = User::factory()->employee()->create();
    $targetUser = User::factory()->employee()->create();

    $response = $this->actingAsUser($employee)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

    $response->assertForbidden();
    
    // Verify user was not deleted
    expect(User::find($targetUser->id))->not->toBeNull();
});

test('deleted user response includes user resource data', function () {
    $admin = User::factory()->admin()->create();
    $targetUser = User::factory()->employee()->create([
        'first_name' => 'Delete',
        'last_name' => 'Me',
        'email' => 'deleteme@example.com',
    ]);

    $response = $this->actingAsUser($admin)
        ->deleteJson("/api/v1/users/{$targetUser->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $targetUser->id)
        ->assertJsonPath('data.first_name', 'Delete')
        ->assertJsonPath('data.last_name', 'Me')
        ->assertJsonPath('data.email', 'deleteme@example.com');
});
