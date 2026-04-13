<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use App\Enums\RoleUser;
use App\Traits\Newable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateUserAction
{
    use Newable;
    public function execute(array $data): User
    {
        $role = RoleUser::from($data['role']);


        $user = User::create([
            ...Arr::except($data, ['role']),
            'created_by' => Auth::id(),
        ]);

        $user->assignRole($role->value);

        return $user;
    }
}
