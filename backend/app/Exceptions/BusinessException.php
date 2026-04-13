<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public function __construct(
        string $message = 'Erro de regra de negócio',
        protected int $status = 400
    ) {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
