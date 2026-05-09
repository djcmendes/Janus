<?php

/**
 * @file GetCommentsQuery.php
 *
 * Payload for the list-comments read operation.
 *
 * @package App\Comments\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query;

/**
 * Carries the pagination and filter parameters for listing comments.
 */
final class GetCommentsQuery
{
    /**
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to comments on a specific collection, or null for all.
     * @param string|null $item       Filter to comments on a specific item, or null for all.
     */
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection = null,
        public readonly ?string $item       = null,
    ) {}
}
