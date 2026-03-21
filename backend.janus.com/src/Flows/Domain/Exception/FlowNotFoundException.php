<?php

declare(strict_types=1);

namespace App\Flows\Domain\Exception;

final class FlowNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Flow '{$id}' not found.");
    }
}
