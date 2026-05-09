<?php

/**
 * @file DeploymentNotFoundException.php
 *
 * Domain exception thrown when a DeploymentProvider cannot be located by UUID.
 *
 * @package App\Deployments\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Exception;

/**
 * Thrown by repositories and handlers when no DeploymentProvider exists for a given UUID.
 *
 * Maps to HTTP 404 Not Found in the presentation layer.
 */
final class DeploymentNotFoundException extends \RuntimeException
{
    /**
     * @param string $id UUID of the provider that could not be found.
     */
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Deployment provider "%s" not found.', $id));
    }
}
