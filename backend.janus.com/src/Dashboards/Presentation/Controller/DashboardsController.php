<?php

/**
 * @file DashboardsController.php
 *
 * HTTP controller for the Dashboards resource. Thin presentation layer — delegates
 * all business logic to Application-layer handlers.
 *
 * @package App\Dashboards\Presentation\Controller
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\Controller;

use App\Dashboards\Application\Command\CreateDashboardCommand;
use App\Dashboards\Application\Command\DeleteDashboardCommand;
use App\Dashboards\Application\Command\Handler\CreateDashboardHandler;
use App\Dashboards\Application\Command\Handler\DeleteDashboardHandler;
use App\Dashboards\Application\Command\Handler\UpdateDashboardHandler;
use App\Dashboards\Application\Command\UpdateDashboardCommand;
use App\Dashboards\Application\Query\GetDashboardByIdQuery;
use App\Dashboards\Application\Query\GetDashboardsQuery;
use App\Dashboards\Application\Query\Handler\GetDashboardByIdHandler;
use App\Dashboards\Application\Query\Handler\GetDashboardsHandler;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Presentation\DTO\CreateDashboardRequest;
use App\Dashboards\Presentation\DTO\UpdateDashboardRequest;
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
 * REST controller for the /dashboards resource.
 *
 * All write actions are restricted to ROLE_ADMIN. Read actions are available to
 * all authenticated users, with non-admins automatically scoped to their own dashboards.
 */
#[Route('/dashboards', name: 'dashboards_')]
final class DashboardsController extends AbstractController
{
    /**
     * @param RequestGuard $guard Authentication and client-type enforcement.
     */
    public function __construct(
        private readonly RequestGuard $guard,
    ) {}

    /**
     * Returns a paginated list of dashboards.
     *
     * Admins may pass ?user= to filter by owner; non-admins are always scoped
     * to their own dashboards regardless of query parameters.
     *
     * @param Request              $request HTTP request carrying pagination/filter params.
     * @param GetDashboardsHandler $handler Retrieves the paginated result set.
     *
     * @return JsonResponse Paginated array under "data" with "meta" counts.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetDashboardsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $currentUserId = $this->guard->validate_authenticated_user_id();
        $isAdmin       = $this->isGranted('ROLE_ADMIN');

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));

        $userId = $isAdmin ? $request->query->get('user') : $currentUserId;

        $result = $handler->handle(new GetDashboardsQuery($limit, $offset, $userId));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /**
     * Returns a single dashboard by UUID.
     *
     * @param string                  $id      UUID path parameter.
     * @param GetDashboardByIdHandler $handler Retrieves the dashboard DTO.
     *
     * @return JsonResponse Dashboard DTO under "data", or 404 error envelope.
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetDashboardByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetDashboardByIdQuery($id));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /**
     * Creates a new dashboard.
     *
     * Restricted to ROLE_ADMIN. The authenticated user's UUID becomes the owner.
     *
     * @param Request                $request HTTP request carrying the JSON body.
     * @param CreateDashboardHandler $handler Creates and persists the dashboard.
     *
     * @return JsonResponse Created dashboard DTO under "data" (HTTP 201), or error envelope.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateDashboardHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $body = json_decode($request->getContent(), true) ?? [];
            $dto  = CreateDashboardRequest::fromArray($body);
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $userId = $this->guard->validate_authenticated_user_id();
        $result = $handler->handle(new CreateDashboardCommand($dto->name, $dto->icon, $dto->note, $userId));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /**
     * Updates a dashboard by UUID.
     *
     * Restricted to ROLE_ADMIN. Only fields present in the request body are modified.
     *
     * @param string                 $id      UUID path parameter.
     * @param Request                $request HTTP request carrying the JSON patch body.
     * @param UpdateDashboardHandler $handler Applies the partial update and persists it.
     *
     * @return JsonResponse Updated dashboard DTO under "data", or error envelope.
     */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateDashboardHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $body = json_decode($request->getContent(), true) ?? [];
            $dto  = UpdateDashboardRequest::fromArray($body);
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new UpdateDashboardCommand($id, $dto->name, $dto->icon, $dto->note));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /**
     * Deletes a dashboard by UUID.
     *
     * Restricted to ROLE_ADMIN. Cascade-deletes all panels belonging to the dashboard.
     *
     * @param string                 $id      UUID path parameter.
     * @param DeleteDashboardHandler $handler Removes the dashboard and its panels.
     *
     * @return Response HTTP 204 No Content on success, or 404 error envelope.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteDashboardHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteDashboardCommand($id));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
