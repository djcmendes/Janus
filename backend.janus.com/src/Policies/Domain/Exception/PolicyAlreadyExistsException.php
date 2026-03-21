<?php

declare(strict_types=1);

namespace App\Policies\Domain\Exception;

final class PolicyAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('A policy named "%s" already exists.', $name));
    }
}
