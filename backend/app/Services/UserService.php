<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Enums\RoleUser;
use Illuminate\Support\Arr;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function create(array $data): User
    {
        $role = RoleUser::from($data['role']);

        $user = User::create(Arr::except($data, ['role']));

        $user->assignRole($role->value);

        return $user;
    }
}
