<?php

/**
 * @file TriggerDeploymentCommand.php
 *
 * CQRS command payload for triggering a deployment run against a provider.
 *
 * @package App\Deployments\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command;

/**
 * Carries the provider UUID and optional triggering user to execute a deployment run.
 */
final class TriggerDeploymentCommand
{
    /**
     * @param string      $providerId  UUID of the DeploymentProvider to trigger.
     * @param string|null $triggeredBy UUID of the user initiating the run, or null for system-triggered runs.
     */
    public function __construct(
        public readonly string  $providerId,
        public readonly ?string $triggeredBy = null,
    ) {}
}
