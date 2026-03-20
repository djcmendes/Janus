<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

final class UserNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('User "%s" not found.', $id));
    }
}
