<?php

declare(strict_types=1);

namespace App\Relations\Domain\Exception;

final class RelationAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $collection, string $field)
    {
        parent::__construct(sprintf('A relation for "%s.%s" already exists.', $collection, $field));
    }
}
