<?php

declare(strict_types=1);

use App\Actions\User\CreateUserAction;
use App\Enums\RoleUser;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed([
        PermissionsSeeder::class,
        RolesSeeder::class,
    ]);
});

test('creates user with all required attributes', function () {
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'role' => RoleUser::ADMIN->value,
    ];

    $action = CreateUserAction::new();
    $user = $action->execute($data);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->first_name)->toBe('John')
        ->and($user->last_name)->toBe('Doe')
        ->and($user->email)->toBe('john.doe@example.com')
        ->and($user->exists)->toBeTrue();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);
});

test('assigns role to created user', function () {
    $data = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@example.com',
        'password' => 'securepassword',
        'role' => RoleUser::BRANCH_MANAGER->value,
    ];

    $action = CreateUserAction::new();
    $user = $action->execute($data);

    expect($user->hasRole(RoleUser::BRANCH_MANAGER->value))->toBeTrue()
        ->and($user->roles->first()->name)->toBe('manager');
});

test('hashes password during user creation', function () {
    $plainPassword = 'mySecretPassword123';
    $data = [
        'first_name' => 'Bob',
        'last_name' => 'Johnson',
        'email' => 'bob.johnson@example.com',
        'password' => $plainPassword,
        'role' => RoleUser::BRANCH_EMPLOYEE->value,
    ];

    $action = CreateUserAction::new();
    $user = $action->execute($data);

    expect($user->password)->not->toBe($plainPassword)
        ->and(Hash::check($plainPassword, $user->password))->toBeTrue();
});

test('creates user with employee role', function () {
    $data = [
        'first_name' => 'Alice',
        'last_name' => 'Brown',
        'email' => 'alice.brown@example.com',
        'password' => 'employeepass',
        'role' => RoleUser::BRANCH_EMPLOYEE->value,
    ];

    $action = CreateUserAction::new();
    $user = $action->execute($data);

    expect($user->hasRole(RoleUser::BRANCH_EMPLOYEE->value))->toBeTrue()
        ->and($user->roles->count())->toBe(1);
});

test('does not include role in user attributes', function () {
    $data = [
        'first_name' => 'Charlie',
        'last_name' => 'Wilson',
        'email' => 'charlie.wilson@example.com',
        'password' => 'testpassword',
        'role' => RoleUser::ADMIN->value,
    ];

    $action = CreateUserAction::new();
    $user = $action->execute($data);

    // Verify 'role' is not stored as a direct attribute on users table
    // Spatie Permission stores roles in model_has_roles table, not in a role column
    $this->assertDatabaseHas('users', [
        'email' => 'charlie.wilson@example.com',
    ]);
    
    // Verify no 'role' column exists by checking table structure
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
    expect($columns)->not->toContain('role');

    // But role relationship exists via Spatie Permission
    expect($user->hasRole(RoleUser::ADMIN->value))->toBeTrue();
});
