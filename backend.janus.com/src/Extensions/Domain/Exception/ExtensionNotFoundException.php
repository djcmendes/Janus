<?php

/**
 * @file ExtensionNotFoundException.php
 *
 * Domain exception thrown when an Extension cannot be located by UUID.
 *
 * @package App\Extensions\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Domain\Exception;

/**
 * Thrown by repositories and handlers when no Extension exists for a given UUID.
 *
 * Maps to HTTP 404 Not Found in the presentation layer.
 */
final class ExtensionNotFoundException extends \RuntimeException
{
    /**
     * @param string $id UUID of the extension that could not be found.
     */
    public function __construct(string $id)
    {
        parent::__construct("Extension '{$id}' not found.");
    }
}
