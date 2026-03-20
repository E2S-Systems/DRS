<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Traits\Newable;

class LogoutUserAction
{
    use Newable;
    public function execute(User $user): array
    {
        $user->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => 'Logout successful!',
        ];
    }
}
