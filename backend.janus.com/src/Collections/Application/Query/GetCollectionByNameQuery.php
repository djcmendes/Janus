<?php

/**
 * @file GetCollectionByNameQuery.php
 *
 * CQRS query payload for fetching a single collection by its name.
 *
 * @package App\Collections\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query;

/**
 * Carries the collection name for the get-collection-by-name use case.
 */
final class GetCollectionByNameQuery
{
    /**
     * Constructor
     *
     * @param string $name The collection name to retrieve.
     */
    public function __construct(public readonly string $name) {}
}
