<?php

use App\Enums\RoleUser;

return [
    RoleUser::BRANCH_MANAGER->value => [
        'users' => [
            'view',
            'create',
            'update',
        ],

        'branches' => [
            'view',
        ],

        'sales' => [
            'view',
            'create',
            'update',
            'cancel',
        ],
    ],

    RoleUser::BRANCH_EMPLOYEE->value => [
        'sales' => [
            'view',
            'create',
        ],
    ],
];
