<?php

declare(strict_types=1);

namespace App\Revisions\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Revisions\Application\Query\GetRevisionByIdQuery;
use App\Revisions\Application\Query\GetRevisionsQuery;
use App\Revisions\Application\Query\Handler\GetRevisionByIdHandler;
use App\Revisions\Application\Query\Handler\GetRevisionsHandler;
use App\Revisions\Domain\Exception\RevisionNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/revisions', name: 'revisions_')]
final class RevisionsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard           $guard,
        private readonly GetRevisionsHandler    $getRevisionsHandler,
        private readonly GetRevisionByIdHandler $getRevisionByIdHandler,
    ) {}

    /**
     * GET /revisions
     * Supports ?collection= and ?item= filters
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getRevisionsHandler->handle(new GetRevisionsQuery(
            limit:      $limit,
            offset:     $offset,
            collection: $request->query->get('collection') ?: null,
            item:       $request->query->get('item')       ?: null,
        ));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /revisions/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $this->getRevisionByIdHandler->handle(new GetRevisionByIdQuery($id));
        } catch (RevisionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto->toArray()]);
    }
}
