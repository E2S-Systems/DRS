<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;

class UpdateUserAction
{
    public function handle(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }
}