<?php

/**
 * @file VersionAlreadyExistsException.php
 *
 * Domain exception thrown when a Version with the same collection+item+key triplet already exists.
 *
 * @package App\Versions\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Exception;

/**
 * Thrown by SaveVersionHandler when the unique collection+item+key constraint would be violated.
 */
final class VersionAlreadyExistsException extends \RuntimeException
{
    /**
     * @param string $collection Collection that already contains the version.
     * @param string $item       Item identifier that already has this version label.
     * @param string $key        Version label that conflicts with the existing record.
     */
    public function __construct(string $collection, string $item, string $key)
    {
        parent::__construct(sprintf(
            'A version with key "%s" already exists for item "%s" in collection "%s".',
            $key, $item, $collection,
        ));
    }
}
