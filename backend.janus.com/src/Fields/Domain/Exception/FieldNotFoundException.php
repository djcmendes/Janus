<?php

/**
 * @file FieldNotFoundException.php
 *
 * Domain exception raised when a field cannot be located in the given collection.
 *
 * @package App\Fields\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Domain\Exception;

/**
 * Thrown when a requested field does not exist in the specified collection.
 *
 * HTTP presentation layer maps this to a 404 NOT_FOUND response.
 */
final class FieldNotFoundException extends \RuntimeException
{
    /**
     * @param string $collection Collection name the field was searched in.
     * @param string $field      Field name that was not found.
     */
    public function __construct(string $collection, string $field)
    {
        parent::__construct(sprintf('Field "%s" not found in collection "%s".', $field, $collection));
    }
}
