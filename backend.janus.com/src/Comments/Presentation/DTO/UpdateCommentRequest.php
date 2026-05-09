<?php

/**
 * @file UpdateCommentRequest.php
 *
 * Presentation-layer DTO for parsing and validating comment update request bodies.
 *
 * @package App\Comments\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Presentation\DTO;

/**
 * Parses and validates the JSON body of a PATCH /comments/{id} request.
 */
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
