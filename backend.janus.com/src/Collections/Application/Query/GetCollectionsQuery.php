<?php

/**
 * @file GetCollectionsQuery.php
 *
 * CQRS query payload for listing collections with pagination.
 *
 * @package App\Collections\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query;

/**
 * Carries pagination parameters for the list-collections use case.
 */
final class GetCollectionsQuery
{
    /**
     * Constructor
     *
     * @param int $limit  Maximum number of records to return (capped at 100 by the controller).
     * @param int $offset Number of records to skip (zero-based pagination offset).
     */
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
