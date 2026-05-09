<?php

/**
 * @file GetExtensionsQuery.php
 *
 * CQRS query payload for retrieving a paginated list of extensions.
 *
 * @package App\Extensions\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Query;

/**
 * Carries pagination and filter parameters for the extensions list.
 */
final class GetExtensionsQuery
{
    /**
     * @param int         $limit   Maximum number of records per page.
     * @param int         $offset  Zero-based record offset.
     * @param string|null $type    Filter by ExtensionType value string; null returns all types.
     * @param bool|null   $enabled Filter by enabled state; null returns all.
     */
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $type    = null,
        public readonly ?bool   $enabled = null,
    ) {}
}
