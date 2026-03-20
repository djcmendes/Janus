<?php

declare(strict_types=1);

namespace App\Policies\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Policies\Application\Command\CreatePolicyCommand;
use App\Policies\Application\Command\DeletePolicyCommand;
use App\Policies\Application\Command\Handler\CreatePolicyHandler;
use App\Policies\Application\Command\Handler\DeletePolicyHandler;
use App\Policies\Application\Command\Handler\UpdatePolicyHandler;
use App\Policies\Application\Command\UpdatePolicyCommand;
use App\Policies\Application\Query\GetPoliciesQuery;
use App\Policies\Application\Query\GetPolicyByIdQuery;
use App\Policies\Application\Query\Handler\GetPoliciesHandler;
use App\Policies\Application\Query\Handler\GetPolicyByIdHandler;
use App\Policies\Domain\Exception\PolicyAlreadyExistsException;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use App\Policies\Presentation\DTO\CreatePolicyRequest;
use App\Policies\Presentation\DTO\UpdatePolicyRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/policies', name: 'policies_')]
final class PoliciesController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard         $guard,
        private readonly GetPoliciesHandler   $getPoliciesHandler,
        private readonly GetPolicyByIdHandler $getPolicyByIdHandler,
        private readonly CreatePolicyHandler  $createPolicyHandler,
        private readonly UpdatePolicyHandler  $updatePolicyHandler,
        private readonly DeletePolicyHandler  $deletePolicyHandler,
    ) {}

    /** GET /policies */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getPoliciesHandler->handle(new GetPoliciesQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /policies/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getPolicyByIdHandler->handle(new GetPolicyByIdQuery($id));
        } catch (PolicyNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /policies */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreatePolicyRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createPolicyHandler->handle(new CreatePolicyCommand(
                name: $req->name, description: $req->description, icon: $req->icon,
                enforceTfa: $req->enforceTfa, adminAccess: $req->adminAccess,
                appAccess: $req->appAccess, ipAccess: $req->ipAccess,
            ));
        } catch (PolicyAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'POLICY_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /policies/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $req = UpdatePolicyRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updatePolicyHandler->handle(new UpdatePolicyCommand(
                id: $id, name: $req->name, description: $req->description, icon: $req->icon,
                enforceTfa: $req->enforceTfa, adminAccess: $req->adminAccess,
                appAccess: $req->appAccess, ipAccess: $req->ipAccess,
            ));
        } catch (PolicyNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (PolicyAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'POLICY_EXISTS'), Response::HTTP_CONFLICT);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /policies/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deletePolicyHandler->handle(new DeletePolicyCommand($id));
        } catch (PolicyNotFoundException $e) {
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
