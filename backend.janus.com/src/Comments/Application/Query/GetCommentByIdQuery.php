<?php

/**
 * @file GetCommentByIdQuery.php
 *
 * Payload for the get-comment-by-id read operation.
 *
 * @package App\Comments\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query;

/**
 * Carries the UUID needed to retrieve a single comment by its primary key.
 */
final class GetCommentByIdQuery
{
    /**
     * @param string $id UUID of the comment to retrieve.
     */
    public function __construct(public readonly string $id) {}
}
