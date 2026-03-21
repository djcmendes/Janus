<?php

declare(strict_types=1);

namespace App\Versions\Domain\Exception;

final class VersionAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $collection, string $item, string $key)
    {
        parent::__construct(sprintf(
            'A version with key "%s" already exists for item "%s" in collection "%s".',
            $key, $item, $collection,
        ));
    }
}
