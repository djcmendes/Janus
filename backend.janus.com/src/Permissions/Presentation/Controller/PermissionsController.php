<?php

declare(strict_types=1);

namespace App\Permissions\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Permissions\Application\Command\CreatePermissionCommand;
use App\Permissions\Application\Command\DeletePermissionCommand;
use App\Permissions\Application\Command\Handler\CreatePermissionHandler;
use App\Permissions\Application\Command\Handler\DeletePermissionHandler;
use App\Permissions\Application\Command\Handler\UpdatePermissionHandler;
use App\Permissions\Application\Command\UpdatePermissionCommand;
use App\Permissions\Application\Query\GetPermissionByIdQuery;
use App\Permissions\Application\Query\GetPermissionsQuery;
use App\Permissions\Application\Query\Handler\GetPermissionByIdHandler;
use App\Permissions\Application\Query\Handler\GetPermissionsHandler;
use App\Permissions\Domain\Exception\PermissionNotFoundException;
use App\Permissions\Presentation\DTO\CreatePermissionRequest;
use App\Permissions\Presentation\DTO\UpdatePermissionRequest;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/permissions', name: 'permissions_')]
final class PermissionsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard              $guard,
        private readonly GetPermissionsHandler     $getPermissionsHandler,
        private readonly GetPermissionByIdHandler  $getPermissionByIdHandler,
        private readonly CreatePermissionHandler   $createPermissionHandler,
        private readonly UpdatePermissionHandler   $updatePermissionHandler,
        private readonly DeletePermissionHandler   $deletePermissionHandler,
    ) {}

    /** GET /permissions?policy=<id> */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit    = min((int) $request->query->get('limit', 25), 100);
        $offset   = (int) $request->query->get('offset', 0);
        $policyId = $request->query->get('policy') ?: null;

        $result = $this->getPermissionsHandler->handle(new GetPermissionsQuery($limit, $offset, $policyId));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /permissions/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getPermissionByIdHandler->handle(new GetPermissionByIdQuery($id));
        } catch (PermissionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /permissions */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreatePermissionRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createPermissionHandler->handle(new CreatePermissionCommand(
                policyId: $req->policyId, action: $req->action, collection: $req->collection,
                fields: $req->fields, permissionsFilter: $req->permissionsFilter,
                validation: $req->validation, presets: $req->presets,
            ));
        } catch (PolicyNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /permissions/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = UpdatePermissionRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->updatePermissionHandler->handle(new UpdatePermissionCommand(
                id: $id, action: $req->action, collection: $req->collection,
                fields: $req->fields, permissionsFilter: $req->permissionsFilter,
                validation: $req->validation, presets: $req->presets,
            ));
        } catch (PermissionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /permissions/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deletePermissionHandler->handle(new DeletePermissionCommand($id));
        } catch (PermissionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function notFound(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'NOT_FOUND']]]];
    }

    private function validationError(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'VALIDATION_ERROR']]]];
    }
}
