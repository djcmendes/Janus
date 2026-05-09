<?php

/**
 * @file CreateCommentCommand.php
 *
 * Payload for the create-comment write operation.
 *
 * @package App\Comments\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command;

/**
 * Carries the data required to create a new comment on a collection item.
 */
final class CreateCommentCommand
{
    /**
     * @param string $collection Name of the collection the comment belongs to.
     * @param string $item       Identifier of the item the comment is attached to.
     * @param string $comment    Text body of the comment.
     * @param string $userId     UUID of the authenticated user creating the comment.
     */
    public function __construct(
        public readonly string $collection,
        public readonly string $item,
        public readonly string $comment,
        public readonly string $userId,
    ) {}
}
