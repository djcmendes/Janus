<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Exception;

final class ExtensionNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Extension '{$id}' not found.");
    }
}
