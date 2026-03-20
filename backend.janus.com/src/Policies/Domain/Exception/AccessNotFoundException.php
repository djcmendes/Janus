<?php

declare(strict_types=1);

namespace App\Policies\Domain\Exception;

final class AccessNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Access entry "%s" not found.', $id));
    }
}
