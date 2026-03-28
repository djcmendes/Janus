<?php

declare(strict_types=1);

namespace App\Deployments\Presentation\Controller;

use App\Deployments\Application\Command\CreateDeploymentCommand;
use App\Deployments\Application\Command\DeleteDeploymentCommand;
use App\Deployments\Application\Command\Handler\CreateDeploymentHandler;
use App\Deployments\Application\Command\Handler\DeleteDeploymentHandler;
use App\Deployments\Application\Command\Handler\TriggerDeploymentHandler;
use App\Deployments\Application\Command\TriggerDeploymentCommand;
use App\Deployments\Application\Query\GetDeploymentByIdQuery;
use App\Deployments\Application\Query\GetDeploymentsQuery;
use App\Deployments\Application\Query\Handler\GetDeploymentByIdHandler;
use App\Deployments\Application\Query\Handler\GetDeploymentsHandler;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Exception\DeploymentProviderInactiveException;
use App\Deployments\Presentation\DTO\CreateDeploymentRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/deployments', name: 'deployments_')]
final class DeploymentsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /** GET /deployments */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetDeploymentsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));

        $result = $handler->handle(new GetDeploymentsQuery($limit, $offset));

        return $this->json([
            'data' => $result['data'],
            'meta' => ['total_count' => $result['total'], 'filter_count' => $result['total']],
        ]);
    }

    /** GET /deployments/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: 10)]
    public function get(string $id, GetDeploymentByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $handler->handle(new GetDeploymentByIdQuery($id));
        } catch (DeploymentNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /deployments */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateDeploymentHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CreateDeploymentRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateDeploymentRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new CreateDeploymentCommand(
            $dto->name,
            $dto->type,
            $dto->url,
            $dto->options,
            $dto->isActive,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** DELETE /deployments/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: 10)]
    public function delete(string $id, DeleteDeploymentHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteDeploymentCommand($id));
        } catch (DeploymentNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /** POST /deployments/:id/run */
    #[Route('/{id}/run', name: 'run', methods: ['POST'], priority: 20)]
    public function run(string $id, TriggerDeploymentHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userId = $this->guard->validate_authenticated_user_id();

        try {
            $result = $handler->handle(new TriggerDeploymentCommand($id, $userId));
        } catch (DeploymentNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (DeploymentProviderInactiveException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'PROVIDER_INACTIVE']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }
}
