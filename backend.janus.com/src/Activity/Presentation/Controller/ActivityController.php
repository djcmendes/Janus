<?php

declare(strict_types=1);

namespace App\Activity\Presentation\Controller;

use App\Activity\Application\Query\GetActivityByIdQuery;
use App\Activity\Application\Query\GetActivityQuery;
use App\Activity\Application\Query\Handler\GetActivityByIdHandler;
use App\Activity\Application\Query\Handler\GetActivityHandler;
use App\Activity\Domain\Exception\ActivityNotFoundException;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity', name: 'activity_')]
final class ActivityController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard           $guard,
        private readonly GetActivityHandler     $getActivityHandler,
        private readonly GetActivityByIdHandler $getActivityByIdHandler,
    ) {}

    /**
     * GET /activity
     * Supports ?collection=, ?action=, ?user= filters
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getActivityHandler->handle(new GetActivityQuery(
            limit:      $limit,
            offset:     $offset,
            collection: $request->query->get('collection') ?: null,
            action:     $request->query->get('action')     ?: null,
            userId:     $request->query->get('user')       ?: null,
        ));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /activity/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $this->getActivityByIdHandler->handle(new GetActivityByIdQuery($id));
        } catch (ActivityNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto->toArray()]);
    }
}
