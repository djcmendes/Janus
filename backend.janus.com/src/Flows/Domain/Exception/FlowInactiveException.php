<?php

declare(strict_types=1);

namespace App\Flows\Domain\Exception;

final class FlowInactiveException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Flow '{$id}' is inactive and cannot be triggered.");
    }
}
