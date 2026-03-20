<?php

declare(strict_types=1);

namespace App\Comments\Domain\Exception;

final class CommentForbiddenException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('You do not have permission to modify this comment.');
    }
}
