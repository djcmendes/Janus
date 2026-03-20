<?php

declare(strict_types=1);

namespace App\Policies\Domain\Exception;

final class PolicyNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Policy "%s" not found.', $id));
    }
}
