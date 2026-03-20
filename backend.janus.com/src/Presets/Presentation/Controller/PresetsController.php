<?php

declare(strict_types=1);

namespace App\Presets\Presentation\Controller;

use App\Presets\Application\Command\CreatePresetCommand;
use App\Presets\Application\Command\DeletePresetCommand;
use App\Presets\Application\Command\Handler\CreatePresetHandler;
use App\Presets\Application\Command\Handler\DeletePresetHandler;
use App\Presets\Application\Command\Handler\UpdatePresetHandler;
use App\Presets\Application\Command\UpdatePresetCommand;
use App\Presets\Application\Query\GetPresetByIdQuery;
use App\Presets\Application\Query\GetPresetsQuery;
use App\Presets\Application\Query\Handler\GetPresetByIdHandler;
use App\Presets\Application\Query\Handler\GetPresetsHandler;
use App\Presets\Domain\Exception\PresetForbiddenException;
use App\Presets\Domain\Exception\PresetNotFoundException;
use App\Presets\Presentation\DTO\CreatePresetRequest;
use App\Presets\Presentation\DTO\UpdatePresetRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/presets', name: 'presets_')]
final class PresetsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /** GET /presets */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetPresetsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $currentUserId = $this->guard->validate_authenticated_user_id();
        $isAdmin       = $this->isGranted('ROLE_ADMIN');

        $limit      = max(1, (int) ($request->query->get('limit', 25)));
        $offset     = max(0, (int) ($request->query->get('offset', 0)));
        $collection = $request->query->get('collection');

        // Admins may filter by any user; regular users see only their own presets
        $userId = $isAdmin
            ? $request->query->get('user')
            : $currentUserId;

        $result = $handler->handle(new GetPresetsQuery($limit, $offset, $collection, $userId));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /presets/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetPresetByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetPresetByIdQuery($id));
        } catch (PresetNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /presets */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreatePresetHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId = $this->guard->validate_authenticated_user_id();

        /** @var CreatePresetRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreatePresetRequest::class, 'json');

        $result = $handler->handle(new CreatePresetCommand(
            $dto->collection,
            $dto->layout,
            $dto->layoutOptions,
            $dto->layoutQuery,
            $dto->filter,
            $dto->search,
            $dto->bookmark,
            $userId,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /presets/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdatePresetHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        /** @var UpdatePresetRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdatePresetRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdatePresetCommand(
                $id,
                $dto->collection,
                $dto->layout,
                $dto->layoutOptions,
                $dto->layoutQuery,
                $dto->filter,
                $dto->search,
                $dto->bookmark,
                $userId,
                $isAdmin,
            ));
        } catch (PresetNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (PresetForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /presets/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeletePresetHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeletePresetCommand($id, $userId, $isAdmin));
        } catch (PresetNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (PresetForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
