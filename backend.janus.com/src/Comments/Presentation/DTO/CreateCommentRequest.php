<?php

declare(strict_types=1);

namespace App\Comments\Presentation\DTO;

final class CreateCommentRequest
{
    public function __construct(
        public readonly string $comment,
        public readonly string $collection,
        public readonly string $item,
    ) {}

    public static function fromArray(array $data): self
    {
        $comment    = trim($data['comment'] ?? '');
        $collection = $data['collection'] ?? '';
        $item       = $data['item'] ?? '';

        if ($comment === '' || $collection === '' || $item === '') {
            throw new \InvalidArgumentException('comment, collection, and item are required.');
        }

        return new self($comment, $collection, $item);
    }
}
