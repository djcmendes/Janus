<?php

declare(strict_types=1);

namespace App\Shares\Presentation\Controller;

use App\Shares\Application\Command\AuthenticateShareCommand;
use App\Shares\Application\Command\CreateShareCommand;
use App\Shares\Application\Command\DeleteShareCommand;
use App\Shares\Application\Command\Handler\AuthenticateShareHandler;
use App\Shares\Application\Command\Handler\CreateShareHandler;
use App\Shares\Application\Command\Handler\DeleteShareHandler;
use App\Shares\Application\Query\GetShareByIdQuery;
use App\Shares\Application\Query\GetSharesQuery;
use App\Shares\Application\Query\Handler\GetShareByIdHandler;
use App\Shares\Application\Query\Handler\GetSharesHandler;
use App\Shares\Domain\Exception\ShareForbiddenException;
use App\Shares\Domain\Exception\ShareInvalidException;
use App\Shares\Domain\Exception\ShareNotFoundException;
use App\Shares\Presentation\DTO\AuthenticateShareRequest;
use App\Shares\Presentation\DTO\CreateShareRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shares', name: 'shares_')]
final class SharesController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /** GET /shares */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetSharesHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $currentUserId = $this->guard->validate_authenticated_user_id();
        $isAdmin       = $this->isGranted('ROLE_ADMIN');

        $limit      = max(1, (int) ($request->query->get('limit', 25)));
        $offset     = max(0, (int) ($request->query->get('offset', 0)));
        $collection = $request->query->get('collection');

        // Admins see all shares; regular users see only their own
        $userId = $isAdmin ? $request->query->get('user') : $currentUserId;

        $result = $handler->handle(new GetSharesQuery($limit, $offset, $collection, $userId));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /shares/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: 10)]
    public function get(string $id, GetShareByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetShareByIdQuery($id));
        } catch (ShareNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /shares */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateShareHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId = $this->guard->validate_authenticated_user_id();

        /** @var CreateShareRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateShareRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new CreateShareCommand(
            $dto->collection,
            $dto->item,
            $userId,
            $dto->name,
            $dto->password,
            $dto->expiresAt,
            $dto->maxUses,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** POST /shares/auth — public endpoint to authenticate with a share token */
    #[Route('/auth', name: 'auth', methods: ['POST'], priority: 20)]
    public function auth(Request $request, AuthenticateShareHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::PUBLIC);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        /** @var AuthenticateShareRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), AuthenticateShareRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new AuthenticateShareCommand($dto->token, $dto->password));
        } catch (ShareNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (ShareInvalidException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'SHARE_INVALID']]]],
                Response::HTTP_UNAUTHORIZED,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /shares/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteShareHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteShareCommand($id, $userId, $isAdmin));
        } catch (ShareNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (ShareForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
