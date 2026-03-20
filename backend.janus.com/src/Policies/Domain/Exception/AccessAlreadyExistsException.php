<?php

declare(strict_types=1);

namespace App\Policies\Domain\Exception;

final class AccessAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('This role-policy assignment already exists.');
    }
}
