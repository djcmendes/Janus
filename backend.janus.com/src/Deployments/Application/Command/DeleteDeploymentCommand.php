<?php

/**
 * @file DeleteDeploymentCommand.php
 *
 * CQRS command payload for deleting a deployment provider by UUID.
 *
 * @package App\Deployments\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command;

/**
 * Carries the UUID of the DeploymentProvider to remove.
 */
final class DeleteDeploymentCommand
{
    /**
     * @param string $id UUID of the DeploymentProvider to delete.
     */
    public function __construct(public readonly string $id) {}
}
