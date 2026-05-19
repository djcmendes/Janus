<?php

/**
 * @file ServerController.php
 *
 * HTTP controller for server status endpoints (ping, info, health).
 *
 * @package App\Server\Presentation\Controller
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Application\Service\RequestGuard;
use App\Server\Application\Service\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Exposes server status and health information over HTTP.
 *
 * Endpoints:
 *   GET /server/ping   — public liveness probe
 *   GET /server/info   — application/runtime info (authenticated)
 *   GET /server/health — infrastructure health checks (authenticated; 200 or 503)
 */
#[Route('/server', name: 'server_')]
final class ServerController extends AbstractController
{
    /**
     * @param RequestGuard  $guard         Validates authentication and client type.
     * @param ServerService $serverService Assembles info and health data.
     */
    public function __construct(
        private readonly RequestGuard  $guard,
        private readonly ServerService $serverService,
    ) {}

    /**
     * GET /server/ping
     * Public health-check used by load balancers and monitoring.
     */
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::PUBLIC);

        return $this->json(['data' => 'pong']);
    }

    /**
     * GET /server/info
     * Returns basic application/server information. Requires authentication.
     */
    #[Route('/info', name: 'info', methods: ['GET'])]
    public function info(): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        return $this->json(['data' => $this->serverService->getInfo()]);
    }

    /**
     * GET /server/health
     * Returns connectivity status for all infrastructure services. Requires authentication.
     */
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $checks = $this->serverService->getHealth();

        $allOk  = array_values($checks) === array_fill(0, count($checks), 'ok');
        $status = $allOk ? 200 : 503;

        return $this->json(['data' => $checks], $status);
    }
}
