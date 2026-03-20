<?php

declare(strict_types=1);

namespace App\Roles\Domain\Exception;

final class RoleAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('A role named "%s" already exists.', $name));
    }
}
