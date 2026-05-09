<?php

/**
 * @file CommentForbiddenException.php
 *
 * Domain exception thrown when a user attempts to modify a comment they do not own.
 *
 * @package App\Comments\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Exception;

/**
 * Thrown when a non-owner non-admin user tries to update or delete a comment.
 */
final class CommentForbiddenException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('You do not have permission to modify this comment.');
    }
}
