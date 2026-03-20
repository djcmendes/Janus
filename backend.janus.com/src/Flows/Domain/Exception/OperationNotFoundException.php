<?php

declare(strict_types=1);

namespace App\Flows\Domain\Exception;

final class OperationNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Operation '{$id}' not found.");
    }
}
