<?php

/**
 * @file CommentNotFoundException.php
 *
 * Domain exception thrown when a requested comment record cannot be found.
 *
 * @package App\Comments\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Exception;

/**
 * Thrown when a Comment lookup by UUID returns no result.
 */
final class CommentNotFoundException extends \RuntimeException
{
    /**
     * @param string $id UUID of the comment that was not found.
     */
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Comment "%s" not found.', $id));
    }
}
