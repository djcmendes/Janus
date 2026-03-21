<?php

declare(strict_types=1);

namespace App\Comments\Application\Command;

final class CreateCommentCommand
{
    public function __construct(
        public readonly string $collection,
        public readonly string $item,
        public readonly string $comment,
        public readonly string $userId,
    ) {}
}
