<?php

declare(strict_types=1);

namespace App\Roles\Domain\Exception;

final class RoleNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Role "%s" not found.', $id));
    }
}
