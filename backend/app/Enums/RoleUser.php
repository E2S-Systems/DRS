<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleUser: string
{
    case ADMIN = 'admin';                 // Acesso Global
    case BRANCH_MANAGER = 'manager';      // Gerente da Filial
    case BRANCH_EMPLOYEE = 'employee';    // Funcionário da Filial

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador Global',
            self::BRANCH_MANAGER => 'Gerente de Filial',
            self::BRANCH_EMPLOYEE => 'Funcionário de Filial',
        };
    }
}
