<?php

declare(strict_types=1);

namespace App\Comments\Presentation\DTO;

final class UpdateCommentRequest
{
    public function __construct(
        public readonly string $comment,
    ) {}

    public static function fromArray(array $data): self
    {
        $comment = trim($data['comment'] ?? '');

        if ($comment === '') {
            throw new \InvalidArgumentException('comment is required.');
        }

        return new self($comment);
    }
}
