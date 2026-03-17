<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

class LogoutUserAction
{
    public function execute(User $user): array
    {
        $user->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => 'Logout successful!',
        ];
    }
}
