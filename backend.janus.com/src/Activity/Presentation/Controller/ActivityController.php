<?php

/**
 * @file ActivityController.php
 *
 * HTTP presentation layer for the Activity module.
 * Exposes read-only endpoints for querying audit-log entries.
 * All endpoints require an authenticated ROLE_ADMIN session.
 *
 * @package App\Activity\Presentation\Controller
 * @author  David Mendes
 */

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

/**
 * Handles HTTP requests for activity log resources.
 *
 * Mounted under the `/activity` prefix. Every action validates the
 * incoming request through {@see RequestGuard} before touching any
 * application handler.
 */
#[Route(path: '/activity', name: 'activity_')]
final class ActivityController extends AbstractController
{
    /**
     * Constructor
     *
     * @param RequestGuard           $guard                  Validates API version, scope, and client type.
     * @param GetActivityHandler     $getActivityHandler     Handles paginated activity list queries.
     * @param GetActivityByIdHandler $getActivityByIdHandler Handles single-record activity queries.
     */
    public function __construct(
        private readonly RequestGuard           $guard,
        private readonly GetActivityHandler     $getActivityHandler,
        private readonly GetActivityByIdHandler $getActivityByIdHandler,
    ) {}

    /**
     * Returns a paginated list of activity log entries.
     *
     * Supports optional query-string filters:
     * - `collection` — filter by the affected collection name
     * - `action`     — filter by action type (e.g. `create`, `update`, `delete`)
     * - `user`       — filter by the acting user UUID
     * - `limit`      — max records per page (default 25, capped at 100)
     * - `offset`     — pagination offset (default 0)
     *
     * @param Request $request The incoming HTTP request carrying optional filter parameters.
     *
     * @return JsonResponse JSON envelope `{ data: [...], meta: { total_count, filter_count } }`.
     */
    #[Route(path: '', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(
            version: ApiVersion::JANUS_100,
            scope:   ApiScope::AUTHENTICATED
        );

        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $this->denyAccessUnlessGranted(attribute: 'ROLE_ADMIN');

        $limit  = min((int) $request->query->get(key: 'limit', default: 25), 100);
        $offset = (int) $request->query->get(key: 'offset', default: 0);

        $result = $this->getActivityHandler->handle(
            query: new GetActivityQuery(
                limit:      $limit,
                offset:     $offset,
                collection: $request->query->get(key: 'collection') ?: null,
                action:     $request->query->get(key: 'action')     ?: null,
                userId:     $request->query->get(key: 'user')       ?: null,
            )
        );

        return $this->json(data: [
            'data' => array_map(
                callback: fn ($dto) => $dto->toArray(),
                array: $result['data']
            ),
            'meta' => [
                'total_count'  => $result['unfiltered_total'],
                'filter_count' => $result['filter_total'],
            ],
        ]);
    }

    /**
     * Returns a single activity log entry by its UUID.
     *
     * @param string $id UUID of the activity record to retrieve.
     *
     * @return JsonResponse JSON envelope `{ data: { ... } }` on success,
     *                      or `{ errors: [{ message, extensions: { code: "NOT_FOUND" } }] }` (HTTP 404) when not found.
     *
     * @throws ActivityNotFoundException Caught internally; results in a 404 JSON error response.
     */
    #[Route(path: '/{id}', name: 'get', methods: [ 'GET' ], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validateWebserviceRequest(
            version: ApiVersion::JANUS_100,
            scope:   ApiScope::AUTHENTICATED
        );

        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted(attribute: 'ROLE_ADMIN');

        try {
            $dto = $this->getActivityByIdHandler->handle(query: new GetActivityByIdQuery($id));
        } catch (ActivityNotFoundException $e) {
            return $this->json(
                data: [ 'errors' => [[
                    'message'    => $e->getMessage(),
                    'extensions' => [ 'code' => 'NOT_FOUND' ]
                ]]],
                status: Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(data: [ 'data' => $dto->toArray() ]);
    }
}
