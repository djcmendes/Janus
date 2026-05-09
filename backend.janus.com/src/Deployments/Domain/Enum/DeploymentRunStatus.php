<?php

/**
 * @file DeploymentRunStatus.php
 *
 * Backed enum representing the lifecycle state of a deployment run.
 *
 * @package App\Deployments\Domain\Enum
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Enum;

/**
 * Lifecycle states for a Deployment run record.
 *
 * Transitions: PENDING → RUNNING → SUCCESS | FAILURE
 */
enum DeploymentRunStatus: string
{
    /** Run has been created but the HTTP request has not yet been sent. */
    case PENDING = 'pending';

    /** HTTP request to the build hook has been sent and is in flight. */
    case RUNNING = 'running';

    /** Build hook responded with a 2xx status code. */
    case SUCCESS = 'success';

    /** Build hook responded with a non-2xx status, or the request threw an exception. */
    case FAILURE = 'failure';
}
