<?php

declare(strict_types=1);

namespace App\Relations\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Relations\Application\Command\CreateRelationCommand;
use App\Relations\Application\Command\DeleteRelationCommand;
use App\Relations\Application\Command\Handler\CreateRelationHandler;
use App\Relations\Application\Command\Handler\DeleteRelationHandler;
use App\Relations\Application\Command\Handler\UpdateRelationHandler;
use App\Relations\Application\Command\UpdateRelationCommand;
use App\Relations\Application\Query\GetRelationByCollectionAndFieldQuery;
use App\Relations\Application\Query\GetRelationsQuery;
use App\Relations\Application\Query\Handler\GetRelationByCollectionAndFieldHandler;
use App\Relations\Application\Query\Handler\GetRelationsHandler;
use App\Relations\Domain\Exception\RelationAlreadyExistsException;
use App\Relations\Domain\Exception\RelationNotFoundException;
use App\Relations\Presentation\DTO\CreateRelationRequest;
use App\Relations\Presentation\DTO\UpdateRelationRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relations', name: 'relations_')]
final class RelationsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard                              $guard,
        private readonly GetRelationsHandler                      $getRelationsHandler,
        private readonly GetRelationByCollectionAndFieldHandler   $getRelationHandler,
        private readonly CreateRelationHandler                    $createRelationHandler,
        private readonly UpdateRelationHandler                    $updateRelationHandler,
        private readonly DeleteRelationHandler                    $deleteRelationHandler,
    ) {}

    /** GET /relations */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getRelationsHandler->handle(new GetRelationsQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /relations/:collection/:field */
    #[Route('/{collection}/{field}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $collection, string $field): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getRelationHandler->handle(
                new GetRelationByCollectionAndFieldQuery($collection, $field)
            );
        } catch (RelationNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /relations */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateRelationRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createRelationHandler->handle(new CreateRelationCommand(
                manyCollection:     $req->manyCollection,
                manyField:          $req->manyField,
                oneCollection:      $req->oneCollection,
                oneField:           $req->oneField,
                junctionCollection: $req->junctionCollection,
            ));
        } catch (RelationAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'RELATION_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /relations/:collection/:field */
    #[Route('/{collection}/{field}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $collection, string $field, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $req = UpdateRelationRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updateRelationHandler->handle(new UpdateRelationCommand(
                manyCollection:     $collection,
                manyField:          $field,
                oneCollection:      $req->oneCollection,
                oneField:           $req->oneField,
                junctionCollection: $req->junctionCollection,
            ));
        } catch (RelationNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /relations/:collection/:field */
    #[Route('/{collection}/{field}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $collection, string $field): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteRelationHandler->handle(new DeleteRelationCommand($collection, $field));
        } catch (RelationNotFoundException $e) {
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

    private function error(string $message, string $code): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => $code]]]];
    }
}
