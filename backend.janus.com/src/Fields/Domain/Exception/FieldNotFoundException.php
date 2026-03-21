<?php

declare(strict_types=1);

namespace App\Fields\Domain\Exception;

final class FieldNotFoundException extends \RuntimeException
{
    public function __construct(string $collection, string $field)
    {
        parent::__construct(sprintf('Field "%s" not found in collection "%s".', $field, $collection));
    }
}
