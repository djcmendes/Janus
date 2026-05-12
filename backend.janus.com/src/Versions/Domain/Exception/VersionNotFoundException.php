<?php

/**
 * @file VersionNotFoundException.php
 *
 * Domain exception thrown when a requested Version record cannot be found.
 *
 * @package App\Versions\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Exception;

/**
 * Thrown by handlers and the repository when a Version lookup by UUID yields no result.
 */
final class VersionNotFoundException extends \RuntimeException
{
    /**
     * @param string $id UUID of the Version that could not be found.
     */
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Version "%s" not found.', $id));
    }
}
