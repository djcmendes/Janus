<?php

/**
 * @file GetFieldsQuery.php
 *
 * CQRS query payload for retrieving a paginated list of all field metadata records.
 *
 * @package App\Fields\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Query;

/**
 * Query for retrieving a paginated slice of all FieldMeta records across all collections.
 */
final class GetFieldsQuery
{
    /**
     * @param int $limit  Maximum number of records to return.
     * @param int $offset Zero-based pagination offset.
     */
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
