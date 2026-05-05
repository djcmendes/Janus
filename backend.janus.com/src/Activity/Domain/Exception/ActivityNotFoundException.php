<?php

/**
 * @file ActivityNotFoundException.php
 *
 * Domain exception thrown when an Activity record cannot be found by its UUID.
 *
 * @package App\Activity\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Exception;

/**
 * Thrown by query handlers when an Activity lookup by UUID returns no result.
 */
final class ActivityNotFoundException extends \RuntimeException
{
    /**
     * @param string $id The UUID that was looked up and not found, included in the exception message.
     */
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Activity "%s" not found.', $id));
    }
}
