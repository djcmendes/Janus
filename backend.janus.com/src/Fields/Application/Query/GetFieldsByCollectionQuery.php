<?php

/**
 * @file GetFieldsByCollectionQuery.php
 *
 * CQRS query payload for retrieving all fields belonging to a specific collection.
 *
 * @package App\Fields\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Query;

/**
 * Query for retrieving all FieldMeta records belonging to a given collection.
 */
final class GetFieldsByCollectionQuery
{
    /**
     * @param string $collection Collection name to filter by.
     */
    public function __construct(public readonly string $collection) {}
}
