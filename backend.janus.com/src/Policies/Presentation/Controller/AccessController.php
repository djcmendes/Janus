<?php

declare(strict_types=1);

namespace App\Policies\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Policies\Application\Command\CreateAccessCommand;
use App\Policies\Application\Command\DeleteAccessCommand;
use App\Policies\Application\Command\Handler\CreateAccessHandler;
use App\Policies\Application\Command\Handler\DeleteAccessHandler;
use App\Policies\Application\Query\GetAccessQuery;
use App\Policies\Application\Query\Handler\GetAccessHandler;
use App\Policies\Domain\Exception\AccessAlreadyExistsException;
use App\Policies\Domain\Exception\AccessNotFoundException;
use App\Policies\Presentation\DTO\CreateAccessRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/access', name: 'access_')]
final class AccessController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard       $guard,
        private readonly GetAccessHandler   $getAccessHandler,
        private readonly CreateAccessHandler $createAccessHandler,
        private readonly DeleteAccessHandler $deleteAccessHandler,
    ) {}

    /** GET /access */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->getAccessHandler->handle(new GetAccessQuery($limit, $offset));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** POST /access */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateAccessRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $dto = $this->createAccessHandler->handle(new CreateAccessCommand($req->policyId, $req->roleId));
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND
            );
        } catch (AccessAlreadyExistsException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'ACCESS_EXISTS']]]],
                Response::HTTP_CONFLICT
            );
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** DELETE /access/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteAccessHandler->handle(new DeleteAccessCommand($id));
        } catch (AccessNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
