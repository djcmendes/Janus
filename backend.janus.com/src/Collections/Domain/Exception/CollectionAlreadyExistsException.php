<?php

/**
 * @file CollectionAlreadyExistsException.php
 *
 * Domain exception thrown when attempting to create a collection whose name is already taken.
 *
 * @package App\Collections\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Exception;

/**
 * Thrown by CreateCollectionHandler when a collection with the given name already exists
 * in the janus_collections table.
 */
final class CollectionAlreadyExistsException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $name The collection name that caused the conflict.
     */
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Collection "%s" already exists.', $name));
    }
}
