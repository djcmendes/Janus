<?php

/**
 * @file GetExtensionByIdQuery.php
 *
 * CQRS query payload for retrieving a single Extension by UUID.
 *
 * @package App\Extensions\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Query;

/**
 * Carries the UUID identifying the extension to retrieve.
 */
final class GetExtensionByIdQuery
{
    /**
     * @param string $id UUID of the extension to retrieve.
     */
    public function __construct(public readonly string $id) {}
}
