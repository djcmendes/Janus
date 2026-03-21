<?php

declare(strict_types=1);

namespace App\Versions\Domain\Exception;

final class VersionNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Version "%s" not found.', $id));
    }
}
