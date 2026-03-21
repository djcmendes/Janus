<?php

declare(strict_types=1);

namespace App\Comments\Application\Command;

final class UpdateCommentCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $requestingUserId,
        public readonly bool   $isAdmin = false,
    ) {}
}
