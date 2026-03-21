<?php

declare(strict_types=1);

namespace App\Relations\Domain\Exception;

final class RelationNotFoundException extends \RuntimeException
{
    public function __construct(string $collection, string $field)
    {
        parent::__construct(sprintf('Relation for "%s.%s" not found.', $collection, $field));
    }
}
