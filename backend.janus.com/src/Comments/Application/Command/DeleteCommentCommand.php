<?php

/**
 * @file DeleteCommentCommand.php
 *
 * Payload for the delete-comment write operation.
 *
 * @package App\Comments\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command;

/**
 * Carries the data required to delete an existing comment.
 */
final class DeleteCommentCommand
{
    /**
     * @param string $id               UUID of the comment to delete.
     * @param string $requestingUserId UUID of the user requesting the deletion.
     * @param bool   $isAdmin          Whether the requesting user holds ROLE_ADMIN.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $requestingUserId,
        public readonly bool   $isAdmin = false,
    ) {}
}
