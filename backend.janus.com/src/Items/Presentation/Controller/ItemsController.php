<?php

declare(strict_types=1);

namespace App\Items\Presentation\Controller;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Items\Application\Command\CreateItemCommand;
use App\Items\Application\Command\DeleteItemCommand;
use App\Items\Application\Command\Handler\CreateItemHandler;
use App\Items\Application\Command\Handler\DeleteItemHandler;
use App\Items\Application\Command\Handler\UpdateItemHandler;
use App\Items\Application\Command\UpdateItemCommand;
use App\Items\Application\Query\GetItemByIdQuery;
use App\Items\Application\Query\GetItemsQuery;
use App\Items\Application\Query\Handler\GetItemByIdHandler;
use App\Items\Application\Query\Handler\GetItemsHandler;
use App\Items\Domain\Exception\ItemNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/items', name: 'items_')]
final class ItemsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard       $guard,
        private readonly GetItemsHandler    $getItemsHandler,
        private readonly GetItemByIdHandler $getItemByIdHandler,
        private readonly CreateItemHandler  $createItemHandler,
        private readonly UpdateItemHandler  $updateItemHandler,
        private readonly DeleteItemHandler  $deleteItemHandler,
    ) {}

    /** GET /items/:collection */
    #[Route('/{collection}', name: 'list', methods: ['GET'])]
    public function list(string $collection, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        try {
            $result = $this->getItemsHandler->handle(new GetItemsQuery($collection, $limit, $offset));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'data' => $result['data'],
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /items/:collection/:id */
    #[Route('/{collection}/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $collection, string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $item = $this->getItemByIdHandler->handle(new GetItemByIdQuery($collection, $id));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (ItemNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $item]);
    }

    /** POST /items/:collection */
    #[Route('/{collection}', name: 'create', methods: ['POST'])]
    public function create(string $collection, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $item = $this->createItemHandler->handle(new CreateItemCommand($collection, $data));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $item], Response::HTTP_CREATED);
    }

    /** PATCH /items/:collection/:id */
    #[Route('/{collection}/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $collection, string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $item = $this->updateItemHandler->handle(new UpdateItemCommand($collection, $id, $data));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (ItemNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $item]);
    }

    /** DELETE /items/:collection/:id */
    #[Route('/{collection}/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $collection, string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $this->deleteItemHandler->handle(new DeleteItemCommand($collection, $id));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (ItemNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
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
