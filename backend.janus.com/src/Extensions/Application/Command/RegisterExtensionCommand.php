<?php

/**
 * @file RegisterExtensionCommand.php
 *
 * CQRS command payload for registering a new extension in the registry.
 *
 * @package App\Extensions\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Command;

/**
 * Carries all data required to create and persist a new Extension record.
 */
final class RegisterExtensionCommand
{
    /**
     * @param string                    $name        Package/bundle name.
     * @param string                    $type        ExtensionType enum value string.
     * @param string                    $version     Semantic version string.
     * @param bool                      $enabled     Whether to enable on registration (default false).
     * @param string|null               $description Optional human-readable description.
     * @param array<string, mixed>|null $meta        Entry-point configuration, or null.
     */
    public function __construct(
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $version,
        public readonly bool    $enabled     = false,
        public readonly ?string $description = null,
        public readonly ?array  $meta        = null,
    ) {}
}
