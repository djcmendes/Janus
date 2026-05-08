<?php

/**
 * @file CollectionNotFoundException.php
 *
 * Domain exception thrown when a requested collection does not exist.
 *
 * @package App\Collections\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Exception;

/**
 * Thrown by query and command handlers when a lookup by collection name yields no result.
 */
final class CollectionNotFoundException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $name The collection name that was not found.
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Collection "%s" not found.', $name));
    }
}
