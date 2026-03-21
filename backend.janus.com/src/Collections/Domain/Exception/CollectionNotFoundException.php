<?php

declare(strict_types=1);

namespace App\Collections\Domain\Exception;

final class CollectionNotFoundException extends \RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Collection "%s" not found.', $name));
    }
}
