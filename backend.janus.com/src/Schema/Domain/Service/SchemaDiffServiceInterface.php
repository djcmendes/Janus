<?php

/**
 * @file SchemaDiffServiceInterface.php
 *
 * Contract for computing the diff between two schema snapshots.
 *
 * @package App\Schema\Domain\Service
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service;

/**
 * Computes the structured diff between a current and target schema snapshot.
 */
interface SchemaDiffServiceInterface
{
    /**
     * @param array<string, mixed> $current Current live schema snapshot.
     * @param array<string, mixed> $target  Desired target schema snapshot.
     *
     * @return array<string, array<string, list<array>>>
     */
    public function diff(array $current, array $target): array;
}
