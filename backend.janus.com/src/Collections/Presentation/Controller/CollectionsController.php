<?php

/**
 * @file CollectionsController.php
 *
 * HTTP controller for the /collections resource.
 *
 * @package App\Collections\Presentation\Controller
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\Controller;

use App\Collections\Application\Command\CreateCollectionCommand;
use App\Collections\Application\Command\DeleteCollectionCommand;
use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Application\Command\Handler\DeleteCollectionHandler;
use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Application\Command\UpdateCollectionCommand;
use App\Collections\Application\Query\GetCollectionByNameQuery;
use App\Collections\Application\Query\GetCollectionsQuery;
use App\Collections\Application\Query\Handler\GetCollectionByNameHandler;
use App\Collections\Application\Query\Handler\GetCollectionsHandler;
use App\Collections\Domain\Exception\CollectionAlreadyExistsException;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Presentation\DTO\CreateCollectionRequest;
use App\Collections\Presentation\DTO\UpdateCollectionRequest;
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
 * Thin HTTP controller exposing CRUD operations for CMS collections.
 *
 * Delegates all business logic to Application-layer handlers and formats
 * responses using the standard JSON envelope defined by RequestGuard.
 */
#[Route('/collections', name: 'collections_')]
final class CollectionsController extends AbstractController
{
    /**
     * Constructor
     *
     * @param RequestGuard               $guard                      Validates API version, scope, and client type.
     * @param GetCollectionsHandler      $getCollectionsHandler      Handles paginated list queries.
     * @param GetCollectionByNameHandler $getCollectionByNameHandler Handles single-collection lookup.
     * @param CreateCollectionHandler    $createCollectionHandler    Handles collection creation.
     * @param UpdateCollectionHandler    $updateCollectionHandler    Handles partial updates.
     * @param DeleteCollectionHandler    $deleteCollectionHandler    Handles deletion.
     */
    public function __construct(
        private readonly RequestGuard               $guard,
        private readonly GetCollectionsHandler      $getCollectionsHandler,
        private readonly GetCollectionByNameHandler $getCollectionByNameHandler,
        private readonly CreateCollectionHandler    $createCollectionHandler,
        private readonly UpdateCollectionHandler    $updateCollectionHandler,
        private readonly DeleteCollectionHandler    $deleteCollectionHandler,
    ) {}

    /**
     * Lists all collections with pagination.
     *
     * @param  Request      $request HTTP request carrying optional limit and offset query params.
     * @return JsonResponse          200 with data array and meta totals.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getCollectionsHandler->handle(new GetCollectionsQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /**
     * Returns a single collection by name.
     *
     * @param  string       $name Collection name path parameter.
     * @return JsonResponse       200 with the collection data, or 404 if not found.
     */
    #[Route('/{name}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $name): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getCollectionByNameHandler->handle(new GetCollectionByNameQuery($name));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /**
     * Creates a new collection with its backing database table and primary-key field.
     *
     * @param  Request      $request HTTP request with the JSON creation payload.
     * @return JsonResponse          201 with the created collection, 409 on name conflict, 422 on validation error.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateCollectionRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createCollectionHandler->handle(new CreateCollectionCommand(
                name:            $req->name,
                label:           $req->label,
                icon:            $req->icon,
                note:            $req->note,
                hidden:          $req->hidden,
                singleton:       $req->singleton,
                sortField:       $req->sortField,
                primaryKeyField: $req->primaryKeyField,
                primaryKeyType:  $req->primaryKeyType,
            ));
        } catch (CollectionAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'COLLECTION_EXISTS'), Response::HTTP_CONFLICT);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /**
     * Partially updates a collection's metadata fields.
     *
     * @param  string       $name    Collection name path parameter.
     * @param  Request      $request HTTP request with the JSON partial-update payload.
     * @return JsonResponse          200 with the updated collection, or 404 if not found.
     */
    #[Route('/{name}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $name, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $req = UpdateCollectionRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updateCollectionHandler->handle(new UpdateCollectionCommand(
                name:      $name,
                label:     $req->label,
                icon:      $req->icon,
                note:      $req->note,
                hidden:    $req->hidden,
                singleton: $req->singleton,
                sortField: $req->sortField,
            ));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /**
     * Deletes a collection, all its field metadata, and its backing database table.
     *
     * @param  string       $name Collection name path parameter.
     * @return JsonResponse       204 on success, 404 if not found, 422 if the name is a system table.
     */
    #[Route('/{name}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $name): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteCollectionHandler->handle(new DeleteCollectionCommand($name));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Builds a 404 NOT_FOUND error envelope.
     *
     * @param  string        $message Human-readable error description.
     * @return array<string, mixed>    Standard error envelope.
     */
    private function notFound(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'NOT_FOUND']]]];
    }

    /**
     * Builds a 422 VALIDATION_ERROR error envelope.
     *
     * @param  string        $message Human-readable validation failure description.
     * @return array<string, mixed>    Standard error envelope.
     */
    private function validationError(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'VALIDATION_ERROR']]]];
    }

    /**
     * Builds a generic error envelope with a custom error code.
     *
     * @param  string        $message Human-readable error description.
     * @param  string        $code    Machine-readable error code (e.g. 'COLLECTION_EXISTS').
     * @return array<string, mixed>    Standard error envelope.
     */
    private function error(string $message, string $code): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => $code]]]];
    }
}
