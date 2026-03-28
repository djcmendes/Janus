<?php

declare(strict_types=1);

namespace App\Versions\Presentation\Controller;

use App\Versions\Application\Command\DeleteVersionCommand;
use App\Versions\Application\Command\Handler\DeleteVersionHandler;
use App\Versions\Application\Command\Handler\PromoteVersionHandler;
use App\Versions\Application\Command\Handler\SaveVersionHandler;
use App\Versions\Application\Command\Handler\UpdateVersionHandler;
use App\Versions\Application\Command\PromoteVersionCommand;
use App\Versions\Application\Command\SaveVersionCommand;
use App\Versions\Application\Command\UpdateVersionCommand;
use App\Versions\Application\Query\GetVersionByIdQuery;
use App\Versions\Application\Query\GetVersionsQuery;
use App\Versions\Application\Query\Handler\GetVersionByIdHandler;
use App\Versions\Application\Query\Handler\GetVersionsHandler;
use App\Versions\Domain\Exception\VersionAlreadyExistsException;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Presentation\DTO\SaveVersionRequest;
use App\Versions\Presentation\DTO\UpdateVersionRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/versions', name: 'versions_')]
final class VersionsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /** GET /versions */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetVersionsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit      = max(1, (int) ($request->query->get('limit', 25)));
        $offset     = max(0, (int) ($request->query->get('offset', 0)));
        $collection = $request->query->get('collection');
        $item       = $request->query->get('item');

        $result = $handler->handle(new GetVersionsQuery($limit, $offset, $collection, $item));

        return $this->json([
            'data' => $result['data'],
            'meta' => ['total_count' => $result['total'], 'filter_count' => $result['total']],
        ]);
    }

    /** GET /versions/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: 10)]
    public function get(string $id, GetVersionByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $handler->handle(new GetVersionByIdQuery($id));
        } catch (VersionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /versions */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, SaveVersionHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var SaveVersionRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), SaveVersionRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $userId = $this->guard->validate_authenticated_user_id();

        try {
            $result = $handler->handle(new SaveVersionCommand(
                $dto->collection,
                $dto->item,
                $dto->key,
                $dto->data,
                is_array($dto->delta) ? $dto->delta : null,
                $userId,
            ));
        } catch (VersionAlreadyExistsException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'CONFLICT']]]],
                Response::HTTP_CONFLICT,
            );
        }

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /versions/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: 10)]
    public function patch(string $id, Request $request, UpdateVersionHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdateVersionRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdateVersionRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdateVersionCommand($id, $dto->key, $dto->delta));
        } catch (VersionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /versions/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: 10)]
    public function delete(string $id, DeleteVersionHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteVersionCommand($id));
        } catch (VersionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /** POST /versions/:id/promote */
    #[Route('/{id}/promote', name: 'promote', methods: ['POST'], priority: 20)]
    public function promote(string $id, PromoteVersionHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $result = $handler->handle(new PromoteVersionCommand($id));
        } catch (VersionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (\RuntimeException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'PROMOTE_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->json(['data' => $result]);
    }
}
