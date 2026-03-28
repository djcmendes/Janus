<?php

declare(strict_types=1);

namespace App\Server\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Server\Domain\Service\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/server', name: 'server_')]
final class ServerController extends AbstractController
{
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
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::PUBLIC);

        return $this->json(['data' => 'pong']);
    }

    /**
     * GET /server/info
     * Returns basic application/server information. Requires authentication.
     */
    #[Route('/info', name: 'info', methods: ['GET'])]
    public function info(): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $checks = $this->serverService->getHealth();

        $allOk  = array_values($checks) === array_fill(0, count($checks), 'ok');
        $status = $allOk ? 200 : 503;

        return $this->json(['data' => $checks], $status);
    }
}
