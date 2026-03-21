<?php

declare(strict_types=1);

namespace App\Collections\Domain\Exception;

final class CollectionAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Collection "%s" already exists.', $name));
    }
}
