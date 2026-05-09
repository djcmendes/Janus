<?php

/**
 * @file DeploymentProviderType.php
 *
 * Backed enum representing the integration type of a deployment provider.
 *
 * @package App\Deployments\Domain\Enum
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Enum;

/**
 * Identifies how a DeploymentProvider is connected to the external build system.
 */
enum DeploymentProviderType: string
{
    /** Generic HTTP webhook — POST to a custom URL. */
    case WEBHOOK = 'webhook';

    /** Netlify build hook — POST to a Netlify build trigger URL. */
    case NETLIFY = 'netlify';

    /** Vercel deploy hook — POST to a Vercel deploy trigger URL. */
    case VERCEL  = 'vercel';

    /** Custom integration — caller-defined behaviour via options payload. */
    case CUSTOM  = 'custom';
}
