<?php

/**
 * @file CreateDeploymentCommand.php
 *
 * CQRS command payload for creating a new deployment provider.
 *
 * @package App\Deployments\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command;

/**
 * Carries all data required to create a new DeploymentProvider.
 */
final class CreateDeploymentCommand
{
    /**
     * @param string                    $name     Human-readable provider name.
     * @param string                    $type     Integration type (webhook, netlify, vercel, custom).
     * @param string                    $url      Webhook or build-hook URL.
     * @param array<string, mixed>|null $options  Extra options (HTTP headers, site ID, etc.), or null.
     * @param bool                      $isActive Whether the provider is initially active.
     */
    public function __construct(
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $url,
        public readonly ?array  $options  = null,
        public readonly bool    $isActive = true,
    ) {}
}
