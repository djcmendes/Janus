<?php

declare(strict_types=1);

namespace App\Server\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/server', name: 'server_')]
final class ServerController extends AbstractController
{
    /**
     * GET /server/ping
     * Public health-check endpoint used by load balancers and monitoring.
     */
    #[Route('/ping', name: 'ping', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json(['data' => 'pong']);
    }

    /**
     * GET /server/info
     * Returns basic server/app information (authenticated).
     */
    #[Route('/info', name: 'info', methods: ['GET'])]
    public function info(): JsonResponse
    {
        return $this->json([
            'data' => [
                'project_name'    => 'Janus',
                'version'         => '1.0.0',
                'node_version'    => null,
                'php_version'     => PHP_VERSION,
                'max_upload_size' => ini_get('upload_max_filesize'),
                'rate_limiter_enabled' => false,
            ],
        ]);
    }
}
