<?php

/**
 * @file DeploymentProviderInactiveException.php
 *
 * Domain exception thrown when a trigger is attempted against an inactive provider.
 *
 * @package App\Deployments\Domain\Exception
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Exception;

/**
 * Thrown when TriggerDeploymentHandler finds a provider whose isActive flag is false.
 *
 * Maps to HTTP 422 Unprocessable Entity in the presentation layer.
 */
final class DeploymentProviderInactiveException extends \RuntimeException
{
    /**
     * @param string $id UUID of the inactive provider.
     */
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Deployment provider "%s" is inactive.', $id));
    }
}
