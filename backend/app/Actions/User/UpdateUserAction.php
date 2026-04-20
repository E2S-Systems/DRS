<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Enums\RoleUser;
use App\Models\User;
use App\Traits\Newable;
use Illuminate\Support\Arr;

class UpdateUserAction
{
    use Newable;

    public function execute(User $user, array $data): User
    {
        $role = isset($data['role']) ? RoleUser::from($data['role']) : null;

        $user->update(Arr::except($data, ['role']));

        if ($role) {
            $user->syncRoles($role->value);
        }

        return $user->load('roles');
    }
}
