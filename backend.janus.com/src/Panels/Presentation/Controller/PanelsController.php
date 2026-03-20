<?php

declare(strict_types=1);

namespace App\Panels\Presentation\Controller;

use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Panels\Application\Command\CreatePanelCommand;
use App\Panels\Application\Command\DeletePanelCommand;
use App\Panels\Application\Command\Handler\CreatePanelHandler;
use App\Panels\Application\Command\Handler\DeletePanelHandler;
use App\Panels\Application\Command\Handler\UpdatePanelHandler;
use App\Panels\Application\Command\UpdatePanelCommand;
use App\Panels\Application\Query\GetPanelByIdQuery;
use App\Panels\Application\Query\GetPanelsQuery;
use App\Panels\Application\Query\Handler\GetPanelByIdHandler;
use App\Panels\Application\Query\Handler\GetPanelsHandler;
use App\Panels\Domain\Exception\PanelNotFoundException;
use App\Panels\Presentation\DTO\CreatePanelRequest;
use App\Panels\Presentation\DTO\UpdatePanelRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panels', name: 'panels_')]
final class PanelsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /** GET /panels */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetPanelsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $limit       = max(1, (int) ($request->query->get('limit', 25)));
        $offset      = max(0, (int) ($request->query->get('offset', 0)));
        $dashboardId = $request->query->get('dashboard');

        $result = $handler->handle(new GetPanelsQuery($limit, $offset, $dashboardId));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /panels/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetPanelByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetPanelByIdQuery($id));
        } catch (PanelNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /panels */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreatePanelHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CreatePanelRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreatePanelRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new CreatePanelCommand(
                $dto->dashboardId,
                $dto->type,
                $dto->name,
                $dto->note,
                $dto->options,
                $dto->positionX,
                $dto->positionY,
                $dto->width,
                $dto->height,
            ));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /panels/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdatePanelHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdatePanelRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdatePanelRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdatePanelCommand(
                $id,
                $dto->type,
                $dto->name,
                $dto->note,
                $dto->options,
                $dto->positionX,
                $dto->positionY,
                $dto->width,
                $dto->height,
            ));
        } catch (PanelNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /panels/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeletePanelHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeletePanelCommand($id));
        } catch (PanelNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
