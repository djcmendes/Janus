<?php

declare(strict_types=1);

namespace App\Roles\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Roles\Application\Command\CreateRoleCommand;
use App\Roles\Application\Command\DeleteRoleCommand;
use App\Roles\Application\Command\Handler\CreateRoleHandler;
use App\Roles\Application\Command\Handler\DeleteRoleHandler;
use App\Roles\Application\Command\Handler\UpdateRoleHandler;
use App\Roles\Application\Command\UpdateRoleCommand;
use App\Roles\Application\Query\GetRoleByIdQuery;
use App\Roles\Application\Query\GetRolesQuery;
use App\Roles\Application\Query\Handler\GetRoleByIdHandler;
use App\Roles\Application\Query\Handler\GetRolesHandler;
use App\Roles\Domain\Exception\RoleAlreadyExistsException;
use App\Roles\Domain\Exception\RoleNotFoundException;
use App\Roles\Presentation\DTO\CreateRoleRequest;
use App\Roles\Presentation\DTO\UpdateRoleRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/roles', name: 'roles_')]
final class RolesController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard       $guard,
        private readonly GetRolesHandler    $getRolesHandler,
        private readonly GetRoleByIdHandler $getRoleByIdHandler,
        private readonly CreateRoleHandler  $createRoleHandler,
        private readonly UpdateRoleHandler  $updateRoleHandler,
        private readonly DeleteRoleHandler  $deleteRoleHandler,
    ) {}

    /** GET /roles */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getRolesHandler->handle(new GetRolesQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /roles/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getRoleByIdHandler->handle(new GetRoleByIdQuery($id));
        } catch (RoleNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /roles */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateRoleRequest::fromArray(
                json_decode($request->getContent(), true) ?? []
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createRoleHandler->handle(new CreateRoleCommand(
                name:        $req->name,
                description: $req->description,
                icon:        $req->icon,
                enforceTfa:  $req->enforceTfa,
                adminAccess: $req->adminAccess,
                appAccess:   $req->appAccess,
            ));
        } catch (RoleAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'ROLE_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /roles/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $req = UpdateRoleRequest::fromArray(
            json_decode($request->getContent(), true) ?? []
        );

        try {
            $dto = $this->updateRoleHandler->handle(new UpdateRoleCommand(
                id:          $id,
                name:        $req->name,
                description: $req->description,
                icon:        $req->icon,
                enforceTfa:  $req->enforceTfa,
                adminAccess: $req->adminAccess,
                appAccess:   $req->appAccess,
            ));
        } catch (RoleNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (RoleAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'ROLE_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /roles/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteRoleHandler->handle(new DeleteRoleCommand($id));
        } catch (RoleNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            data: null,
            status: Response::HTTP_NO_CONTENT
        );
    }

    // ── Error helpers ──────────────────────────────────────────────────────

    private function notFound(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'NOT_FOUND']]]];
    }

    private function validationError(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'VALIDATION_ERROR']]]];
    }

    private function error(string $message, string $code): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => $code]]]];
    }
}
