<?php

/**
 * @file FieldAlreadyExistsException.php
 *
 * Domain exception raised when a field creation is attempted but a field
 * with the same name already exists in the target collection.
 *
 * @package App\Fields\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Domain\Exception;

/**
 * Thrown when attempting to create a field that already exists in the given collection.
 *
 * HTTP presentation layer maps this to a 409 FIELD_EXISTS response.
 */
final class FieldAlreadyExistsException extends \RuntimeException
{
    /**
     * @param string $collection Collection name where the conflict was detected.
     * @param string $field      Field name that already exists.
     */
    public function __construct(string $collection, string $field)
    {
        parent::__construct(sprintf('Field "%s" already exists in collection "%s".', $field, $collection));
    }
}
