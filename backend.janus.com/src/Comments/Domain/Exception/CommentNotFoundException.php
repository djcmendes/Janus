<?php

declare(strict_types=1);

namespace App\Comments\Domain\Exception;

final class CommentNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Comment "%s" not found.', $id));
    }
}
