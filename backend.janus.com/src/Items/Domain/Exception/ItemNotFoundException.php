<?php

declare(strict_types=1);

namespace App\Items\Domain\Exception;

final class ItemNotFoundException extends \RuntimeException
{
    public function __construct(string $collection, string $id)
    {
        parent::__construct(sprintf('Item "%s" not found in collection "%s".', $id, $collection));
    }
}
