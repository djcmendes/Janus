<?php

declare(strict_types=1);

namespace App\Flows\Presentation\Controller;

use App\Flows\Application\Command\CreateOperationCommand;
use App\Flows\Application\Command\DeleteOperationCommand;
use App\Flows\Application\Command\Handler\CreateOperationHandler;
use App\Flows\Application\Command\Handler\DeleteOperationHandler;
use App\Flows\Application\Command\Handler\UpdateOperationHandler;
use App\Flows\Application\Command\UpdateOperationCommand;
use App\Flows\Application\Query\GetOperationByIdQuery;
use App\Flows\Application\Query\GetOperationsQuery;
use App\Flows\Application\Query\Handler\GetOperationByIdHandler;
use App\Flows\Application\Query\Handler\GetOperationsHandler;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Domain\Exception\OperationNotFoundException;
use App\Flows\Presentation\DTO\CreateOperationRequest;
use App\Flows\Presentation\DTO\UpdateOperationRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/operations', name: 'operations_')]
final class OperationsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /** GET /operations */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetOperationsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));
        $flowId = $request->query->get('flow');

        $result = $handler->handle(new GetOperationsQuery($limit, $offset, $flowId));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /operations/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetOperationByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $handler->handle(new GetOperationByIdQuery($id));
        } catch (OperationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /operations */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateOperationHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CreateOperationRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateOperationRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new CreateOperationCommand(
                $dto->flowId,
                $dto->name,
                $dto->type,
                $dto->options,
                $dto->resolve,
                $dto->nextSuccess,
                $dto->nextFailure,
                $dto->sortOrder,
            ));
        } catch (FlowNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /operations/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateOperationHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdateOperationRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdateOperationRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdateOperationCommand(
                $id,
                $dto->name,
                $dto->type,
                $dto->options,
                $dto->resolve,
                $dto->nextSuccess,
                $dto->nextFailure,
                $dto->sortOrder,
            ));
        } catch (OperationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /operations/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteOperationHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteOperationCommand($id));
        } catch (OperationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
