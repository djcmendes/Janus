<?php

/**
 * @file GetFieldByCollectionAndNameQuery.php
 *
 * CQRS query payload for retrieving a single field by its collection and column name.
 *
 * @package App\Fields\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Query;

/**
 * Query for retrieving a single FieldMeta record identified by collection + field name.
 */
final class GetFieldByCollectionAndNameQuery
{
    /**
     * @param string $collection Collection name.
     * @param string $field      Column name within the collection.
     */
    public function __construct(
        public readonly string $collection,
        public readonly string $field,
    ) {}
}
