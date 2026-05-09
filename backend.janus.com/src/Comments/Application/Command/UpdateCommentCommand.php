<?php

/**
 * @file UpdateCommentCommand.php
 *
 * Payload for the update-comment write operation.
 *
 * @package App\Comments\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command;

/**
 * Carries the data required to update the text of an existing comment.
 */
final class UpdateCommentCommand
{
    /**
     * @param string $id               UUID of the comment to update.
     * @param string $comment          New text body for the comment.
     * @param string $requestingUserId UUID of the user requesting the update.
     * @param bool   $isAdmin          Whether the requesting user holds ROLE_ADMIN.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $requestingUserId,
        public readonly bool   $isAdmin = false,
    ) {}
}
