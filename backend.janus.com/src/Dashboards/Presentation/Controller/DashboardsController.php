<?php

declare(strict_types=1);

namespace App\Dashboards\Presentation\Controller;

use App\Dashboards\Application\Command\CreateDashboardCommand;
use App\Dashboards\Application\Command\DeleteDashboardCommand;
use App\Dashboards\Application\Command\Handler\CreateDashboardHandler;
use App\Dashboards\Application\Command\Handler\DeleteDashboardHandler;
use App\Dashboards\Application\Command\Handler\UpdateDashboardHandler;
use App\Dashboards\Application\Command\UpdateDashboardCommand;
use App\Dashboards\Application\Query\GetDashboardByIdQuery;
use App\Dashboards\Application\Query\GetDashboardsQuery;
use App\Dashboards\Application\Query\Handler\GetDashboardByIdHandler;
use App\Dashboards\Application\Query\Handler\GetDashboardsHandler;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Presentation\DTO\CreateDashboardRequest;
use App\Dashboards\Presentation\DTO\UpdateDashboardRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/dashboards', name: 'dashboards_')]
final class DashboardsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {}

    /** GET /dashboards */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetDashboardsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $currentUserId = $this->guard->validate_authenticated_user_id();
        $isAdmin       = $this->isGranted('ROLE_ADMIN');

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));

        // Admins may pass ?user= to filter; non-admins see only their own
        $userId = $isAdmin ? $request->query->get('user') : $currentUserId;

        $result = $handler->handle(new GetDashboardsQuery($limit, $offset, $userId));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /dashboards/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetDashboardByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetDashboardByIdQuery($id));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /dashboards */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateDashboardHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CreateDashboardRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateDashboardRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $userId = $this->guard->validate_authenticated_user_id();

        $result = $handler->handle(new CreateDashboardCommand($dto->name, $dto->icon, $dto->note, $userId));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /dashboards/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateDashboardHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdateDashboardRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdateDashboardRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdateDashboardCommand($id, $dto->name, $dto->icon, $dto->note));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /dashboards/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteDashboardHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteDashboardCommand($id));
        } catch (DashboardNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
