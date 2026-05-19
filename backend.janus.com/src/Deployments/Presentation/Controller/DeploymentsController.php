<?php

/**
 * @file DeploymentsController.php
 *
 * HTTP controller for the Deployments resource. Thin presentation layer — delegates
 * all business logic to Application-layer handlers.
 *
 * @package App\Deployments\Presentation\Controller
 * @author  David Mendes
 */

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
use App\Heimdall\Application\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * REST controller for the /deployments resource.
 *
 * All actions are restricted to ROLE_ADMIN. The `list`, `get`, `create`, and `delete`
 * actions manage DeploymentProvider records; the `run` action triggers a Deployment run
 * against a specified provider.
 */
#[Route('/deployments', name: 'deployments_')]
final class DeploymentsController extends AbstractController
{
    /**
     * @param RequestGuard $guard Authentication and client-type enforcement.
     */
    public function __construct(
        private readonly RequestGuard $guard,
    ) {}

    /**
     * Returns a paginated list of deployment providers.
     *
     * @param Request              $request HTTP request carrying pagination params.
     * @param GetDeploymentsHandler $handler Retrieves the paginated result set.
     *
     * @return JsonResponse Paginated array under "data" with "meta" counts.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetDeploymentsHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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

    /**
     * Returns a single deployment provider by UUID.
     *
     * @param string                   $id      UUID path parameter.
     * @param GetDeploymentByIdHandler $handler Retrieves the provider DTO.
     *
     * @return JsonResponse Provider DTO under "data", or 404 error envelope.
     */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: 10)]
    public function get(string $id, GetDeploymentByIdHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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

    /**
     * Creates a new deployment provider.
     *
     * @param Request                $request HTTP request carrying the JSON body.
     * @param CreateDeploymentHandler $handler Creates and persists the provider.
     *
     * @return JsonResponse Created provider DTO under "data" (HTTP 201), or error envelope.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateDeploymentHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $body = json_decode($request->getContent(), true) ?? [];
            $dto  = CreateDeploymentRequest::fromArray($body);
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
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

    /**
     * Deletes a deployment provider by UUID.
     *
     * @param string                  $id      UUID path parameter.
     * @param DeleteDeploymentHandler $handler Removes the provider from persistence.
     *
     * @return Response HTTP 204 No Content on success, or 404 error envelope.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: 10)]
    public function delete(string $id, DeleteDeploymentHandler $handler): Response
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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

    /**
     * Triggers a deployment run against the specified provider.
     *
     * @param string                   $id      UUID of the DeploymentProvider to trigger.
     * @param TriggerDeploymentHandler $handler Executes the build hook and records the run.
     *
     * @return JsonResponse Run record DTO under "data" (HTTP 201), or error envelope.
     */
    #[Route('/{id}/run', name: 'run', methods: ['POST'], priority: 20)]
    public function run(string $id, TriggerDeploymentHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userId = $this->guard->validateAuthenticatedUserId();

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
