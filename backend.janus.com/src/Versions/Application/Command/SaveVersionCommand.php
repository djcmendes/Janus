<?php

/**
 * @file SaveVersionCommand.php
 *
 * Write-side payload for creating a new Version snapshot.
 *
 * @package App\Versions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command;

/**
 * Carries all data required to create a new named Version for a collection item.
 */
final class SaveVersionCommand
{
    /**
     * @param string               $collection Collection the item belongs to.
     * @param string               $item       UUID/ID of the item to snapshot.
     * @param string               $key        Human-readable label for this version (e.g. "main", "draft").
     * @param array<string, mixed> $data       Full data snapshot of the item at this point in time.
     * @param array<string, mixed>|null $delta  Optional diff against the previous version.
     * @param string|null          $userId     UUID of the user creating this version, or null.
     */
    public function __construct(
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $key,
        public readonly array   $data,
        public readonly ?array  $delta  = null,
        public readonly ?string $userId = null,
    ) {}
}
