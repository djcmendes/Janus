<?php

declare(strict_types=1);

namespace App\Permissions\Domain\Exception;

final class PermissionNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Permission "%s" not found.', $id));
    }
}
