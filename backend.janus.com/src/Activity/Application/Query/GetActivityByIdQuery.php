<?php

/**
 * @file GetActivityByIdQuery.php
 *
 * CQRS query payload for retrieving a single Activity record by UUID.
 *
 * @package App\Activity\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query;

/**
 * Query that carries the UUID of the Activity record to retrieve.
 */
final class GetActivityByIdQuery
{
    /**
     * @param string $id UUID of the Activity record to look up.
     */
    public function __construct(public readonly string $id) {}
}
