<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Fields\Application\Command\CreateFieldCommand;
use App\Fields\Application\Command\DeleteFieldCommand;
use App\Fields\Application\Command\Handler\CreateFieldHandler;
use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use App\Fields\Application\Command\Handler\UpdateFieldHandler;
use App\Fields\Application\Command\UpdateFieldCommand;
use App\Fields\Application\Query\GetFieldByCollectionAndNameQuery;
use App\Fields\Application\Query\GetFieldsByCollectionQuery;
use App\Fields\Application\Query\GetFieldsQuery;
use App\Fields\Application\Query\Handler\GetFieldByCollectionAndNameHandler;
use App\Fields\Application\Query\Handler\GetFieldsByCollectionHandler;
use App\Fields\Application\Query\Handler\GetFieldsHandler;
use App\Fields\Domain\Exception\FieldAlreadyExistsException;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Presentation\DTO\CreateFieldRequest;
use App\Fields\Presentation\DTO\UpdateFieldRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fields', name: 'fields_')]
final class FieldsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard                       $guard,
        private readonly GetFieldsHandler                  $getFieldsHandler,
        private readonly GetFieldsByCollectionHandler      $getFieldsByCollectionHandler,
        private readonly GetFieldByCollectionAndNameHandler $getFieldByCollectionAndNameHandler,
        private readonly CreateFieldHandler                $createFieldHandler,
        private readonly UpdateFieldHandler                $updateFieldHandler,
        private readonly DeleteFieldHandler                $deleteFieldHandler,
    ) {}

    /** GET /fields */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getFieldsHandler->handle(new GetFieldsQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /fields/:collection */
    #[Route('/{collection}', name: 'list_by_collection', methods: ['GET'], priority: -1)]
    public function listByCollection(string $collection): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $dtos = $this->getFieldsByCollectionHandler->handle(new GetFieldsByCollectionQuery($collection));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $dtos),
            'meta' => ['total_count' => count($dtos), 'filter_count' => count($dtos)],
        ]);
    }

    /** GET /fields/:collection/:field */
    #[Route('/{collection}/{field}', name: 'get', methods: ['GET'], priority: -2)]
    public function get(string $collection, string $field): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getFieldByCollectionAndNameHandler->handle(
                new GetFieldByCollectionAndNameQuery($collection, $field)
            );
        } catch (FieldNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /fields/:collection */
    #[Route('/{collection}', name: 'create', methods: ['POST'], priority: -1)]
    public function create(string $collection, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateFieldRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createFieldHandler->handle(new CreateFieldCommand(
                collection: $collection,
                field:      $req->field,
                type:       $req->type,
                label:      $req->label,
                note:       $req->note,
                required:   $req->required,
                readonly:   $req->readonly,
                hidden:     $req->hidden,
                sortOrder:  $req->sortOrder,
                interface:  $req->interface,
                options:    $req->options,
            ));
        } catch (CollectionNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (FieldAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'FIELD_EXISTS'), Response::HTTP_CONFLICT);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /fields/:collection/:field */
    #[Route('/{collection}/{field}', name: 'patch', methods: ['PATCH'], priority: -2)]
    public function patch(string $collection, string $field, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $req = UpdateFieldRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updateFieldHandler->handle(new UpdateFieldCommand(
                collection: $collection,
                field:      $field,
                label:      $req->label,
                note:       $req->note,
                required:   $req->required,
                readonly:   $req->readonly,
                hidden:     $req->hidden,
                sortOrder:  $req->sortOrder,
                interface:  $req->interface,
                options:    $req->options,
            ));
        } catch (FieldNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /fields/:collection/:field */
    #[Route('/{collection}/{field}', name: 'delete', methods: ['DELETE'], priority: -2)]
    public function delete(string $collection, string $field): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteFieldHandler->handle(new DeleteFieldCommand($collection, $field));
        } catch (FieldNotFoundException $e) {
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

    private function error(string $message, string $code): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => $code]]]];
    }
}
