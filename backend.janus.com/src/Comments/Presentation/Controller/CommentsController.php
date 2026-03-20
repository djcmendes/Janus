<?php

declare(strict_types=1);

namespace App\Comments\Presentation\Controller;

use App\Comments\Application\Command\CreateCommentCommand;
use App\Comments\Application\Command\DeleteCommentCommand;
use App\Comments\Application\Command\Handler\CreateCommentHandler;
use App\Comments\Application\Command\Handler\DeleteCommentHandler;
use App\Comments\Application\Command\Handler\UpdateCommentHandler;
use App\Comments\Application\Command\UpdateCommentCommand;
use App\Comments\Application\Query\GetCommentByIdQuery;
use App\Comments\Application\Query\GetCommentsQuery;
use App\Comments\Application\Query\Handler\GetCommentByIdHandler;
use App\Comments\Application\Query\Handler\GetCommentsHandler;
use App\Comments\Domain\Exception\CommentForbiddenException;
use App\Comments\Domain\Exception\CommentNotFoundException;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comments', name: 'comments_')]
final class CommentsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard $guard,
    ) {}

    /** GET /comments */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetCommentsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $limit      = max(1, (int) ($request->query->get('limit', 25)));
        $offset     = max(0, (int) ($request->query->get('offset', 0)));
        $collection = $request->query->get('collection');
        $item       = $request->query->get('item');

        $result = $handler->handle(new GetCommentsQuery($limit, $offset, $collection, $item));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /comments/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetCommentByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetCommentByIdQuery($id));
        } catch (CommentNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /comments */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateCommentHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId = $this->guard->validate_authenticated_user_id();

        $data       = json_decode($request->getContent(), true) ?? [];
        $comment    = trim($data['comment'] ?? '');
        $collection = $data['collection'] ?? null;
        $item       = $data['item'] ?? null;

        if ($comment === '' || $collection === null || $item === null) {
            return $this->json(
                ['errors' => [['message' => 'comment, collection, and item are required.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new CreateCommentCommand($collection, $item, $comment, $userId));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /comments/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateCommentHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        $data    = json_decode($request->getContent(), true) ?? [];
        $comment = trim($data['comment'] ?? '');

        if ($comment === '') {
            return $this->json(
                ['errors' => [['message' => 'comment is required.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new UpdateCommentCommand($id, $comment, $userId, $isAdmin));
        } catch (CommentNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (CommentForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /comments/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, DeleteCommentHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteCommentCommand($id, $userId, $isAdmin));
        } catch (CommentNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (CommentForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
