<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Enums\RoleUser;
use App\Models\User;
use App\Traits\Newable;
use Illuminate\Support\Arr;

class CreateUserAction
{
    use Newable;

    public function execute(array $data): User
    {
        $role = RoleUser::from($data['role']);

        $user = User::create(Arr::except($data, ['role']));

        $user->assignRole($role->value);

        return $user;
    }
}
