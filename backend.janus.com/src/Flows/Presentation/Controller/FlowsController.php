<?php

declare(strict_types=1);

namespace App\Flows\Presentation\Controller;

use App\Flows\Application\Command\CreateFlowCommand;
use App\Flows\Application\Command\DeleteFlowCommand;
use App\Flows\Application\Command\Handler\CreateFlowHandler;
use App\Flows\Application\Command\Handler\DeleteFlowHandler;
use App\Flows\Application\Command\Handler\TriggerFlowHandler;
use App\Flows\Application\Command\Handler\UpdateFlowHandler;
use App\Flows\Application\Command\TriggerFlowCommand;
use App\Flows\Application\Command\UpdateFlowCommand;
use App\Flows\Application\Query\GetFlowByIdQuery;
use App\Flows\Application\Query\GetFlowsQuery;
use App\Flows\Application\Query\Handler\GetFlowByIdHandler;
use App\Flows\Application\Query\Handler\GetFlowsHandler;
use App\Flows\Domain\Exception\FlowInactiveException;
use App\Flows\Domain\Exception\FlowNotFoundException;
use App\Flows\Presentation\DTO\CreateFlowRequest;
use App\Flows\Presentation\DTO\UpdateFlowRequest;
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

#[Route('/flows', name: 'flows_')]
final class FlowsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {}

    /** GET /flows */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetFlowsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));
        $status = $request->query->get('status');

        $result = $handler->handle(new GetFlowsQuery($limit, $offset, $status));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /flows/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: 10)]
    public function get(string $id, GetFlowByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $handler->handle(new GetFlowByIdQuery($id));
        } catch (FlowNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /flows */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateFlowHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $userId = $this->guard->validate_authenticated_user_id();

        /** @var CreateFlowRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateFlowRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new CreateFlowCommand(
            $dto->name,
            $dto->status,
            $dto->trigger,
            $dto->triggerOptions,
            $userId,
            $dto->description,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /flows/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: 10)]
    public function patch(string $id, Request $request, UpdateFlowHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdateFlowRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdateFlowRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdateFlowCommand(
                $id,
                $dto->name,
                $dto->status,
                $dto->trigger,
                $dto->triggerOptions,
                $dto->description,
            ));
        } catch (FlowNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /flows/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: 10)]
    public function delete(string $id, DeleteFlowHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteFlowCommand($id));
        } catch (FlowNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /** POST /flows/:id/trigger */
    #[Route('/{id}/trigger', name: 'trigger', methods: ['POST'], priority: 20)]
    public function trigger(string $id, Request $request, TriggerFlowHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $userId = $this->guard->validate_authenticated_user_id();

        $body    = json_decode($request->getContent(), true) ?? [];
        $payload = $body['payload'] ?? [];

        try {
            $handler->handle(new TriggerFlowCommand($id, $payload, $userId));
        } catch (FlowNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (FlowInactiveException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FLOW_INACTIVE']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->json(['data' => ['triggered' => true, 'flow_id' => $id]]);
    }
}
