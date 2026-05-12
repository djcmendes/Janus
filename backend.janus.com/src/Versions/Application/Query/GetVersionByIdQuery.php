<?php

/**
 * @file GetVersionByIdQuery.php
 *
 * Read-side payload for fetching a single Version record by its UUID.
 *
 * @package App\Versions\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query;

/**
 * Carries the UUID used to look up a single Version record.
 */
final class GetVersionByIdQuery
{
    /**
     * @param string $id UUID of the version record to retrieve.
     */
    public function __construct(public readonly string $id) {}
}
