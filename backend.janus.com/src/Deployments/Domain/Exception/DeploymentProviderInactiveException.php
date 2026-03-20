<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Exception;

final class DeploymentProviderInactiveException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Deployment provider "%s" is inactive.', $id));
    }
}
