<?php

declare(strict_types=1);

namespace App\Files\Presentation\Controller;

use App\Files\Application\Command\CreateFolderCommand;
use App\Files\Application\Command\DeleteFolderCommand;
use App\Files\Application\Command\Handler\CreateFolderHandler;
use App\Files\Application\Command\Handler\DeleteFolderHandler;
use App\Files\Application\Command\Handler\UpdateFolderHandler;
use App\Files\Application\Command\UpdateFolderCommand;
use App\Files\Application\Query\GetFolderByIdQuery;
use App\Files\Application\Query\GetFoldersQuery;
use App\Files\Application\Query\Handler\GetFolderByIdHandler;
use App\Files\Application\Query\Handler\GetFoldersHandler;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Presentation\DTO\CreateFolderRequest;
use App\Files\Presentation\DTO\UpdateFolderRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/folders', name: 'folders_')]
final class FoldersController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard         $guard,
        private readonly GetFoldersHandler    $getFoldersHandler,
        private readonly GetFolderByIdHandler $getFolderByIdHandler,
        private readonly CreateFolderHandler  $createFolderHandler,
        private readonly UpdateFolderHandler  $updateFolderHandler,
        private readonly DeleteFolderHandler  $deleteFolderHandler,
    ) {}

    /** GET /folders */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getFoldersHandler->handle(new GetFoldersQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /folders/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getFolderByIdHandler->handle(new GetFolderByIdQuery($id));
        } catch (FolderNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /folders */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $req = CreateFolderRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createFolderHandler->handle(new CreateFolderCommand(
                name:     $req->name,
                parentId: $req->parentId,
            ));
        } catch (FolderNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /folders/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $req = UpdateFolderRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updateFolderHandler->handle(new UpdateFolderCommand(
                id:       $id,
                name:     $req->name,
                parentId: $req->parentId,
            ));
        } catch (FolderNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /folders/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $this->deleteFolderHandler->handle(new DeleteFolderCommand($id));
        } catch (FolderNotFoundException $e) {
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
