<?php

declare(strict_types=1);

namespace App\Users\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Users\Application\Command\CreateUserCommand;
use App\Users\Application\Command\DeleteUserCommand;
use App\Users\Application\Command\Handler\CreateUserHandler;
use App\Users\Application\Command\Handler\DeleteUserHandler;
use App\Users\Application\Command\Handler\InviteUserHandler;
use App\Users\Application\Command\Handler\UpdateUserHandler;
use App\Users\Application\Command\InviteUserCommand;
use App\Users\Application\Command\UpdateUserCommand;
use App\Users\Application\Query\GetUserByIdQuery;
use App\Users\Application\Query\GetUsersQuery;
use App\Users\Application\Query\Handler\GetUserByIdHandler;
use App\Users\Application\Query\Handler\GetUsersHandler;
use App\Users\Domain\Exception\UserAlreadyExistsException;
use App\Users\Domain\Exception\UserNotFoundException;
use App\Users\Presentation\DTO\CreateUserRequest;
use App\Users\Presentation\DTO\InviteUserRequest;
use App\Users\Presentation\DTO\UpdateUserRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users_')]
final class UsersController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard       $guard,
        private readonly GetUsersHandler    $getUsersHandler,
        private readonly GetUserByIdHandler $getUserByIdHandler,
        private readonly CreateUserHandler  $createUserHandler,
        private readonly UpdateUserHandler  $updateUserHandler,
        private readonly DeleteUserHandler  $deleteUserHandler,
        private readonly InviteUserHandler  $inviteUserHandler,
    ) {}

    /** GET /users */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getUsersHandler->handle(new GetUsersQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /users/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getUserByIdHandler->handle(new GetUserByIdQuery($id));
        } catch (UserNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /users */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateUserRequest::fromArray(
                json_decode($request->getContent(), true) ?? []
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createUserHandler->handle(
                new CreateUserCommand($req->email, $req->password, $req->firstName, $req->lastName, $req->roles)
            );
        } catch (UserAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'USER_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /users/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = UpdateUserRequest::fromArray(
                json_decode($request->getContent(), true) ?? []
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->updateUserHandler->handle(
                new UpdateUserCommand($id, $req->firstName, $req->lastName, $req->roles, $req->password, $req->status)
            );
        } catch (UserNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /users/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteUserHandler->handle(new DeleteUserCommand($id));
        } catch (UserNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /** POST /users/invite */
    #[Route('/invite', name: 'invite', methods: ['POST'])]
    public function invite(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = InviteUserRequest::fromArray(
                json_decode($request->getContent(), true) ?? []
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->inviteUserHandler->handle(
                new InviteUserCommand($req->email, $req->firstName, $req->lastName, $req->roles)
            );
        } catch (UserAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'USER_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
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
