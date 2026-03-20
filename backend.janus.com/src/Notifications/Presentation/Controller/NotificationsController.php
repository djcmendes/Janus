<?php

declare(strict_types=1);

namespace App\Notifications\Presentation\Controller;

use App\Notifications\Application\Command\CreateNotificationCommand;
use App\Notifications\Application\Command\DeleteNotificationCommand;
use App\Notifications\Application\Command\Handler\CreateNotificationHandler;
use App\Notifications\Application\Command\Handler\DeleteNotificationHandler;
use App\Notifications\Application\Command\Handler\MarkNotificationReadHandler;
use App\Notifications\Application\Command\MarkNotificationReadCommand;
use App\Notifications\Application\Query\GetNotificationByIdQuery;
use App\Notifications\Application\Query\GetNotificationsQuery;
use App\Notifications\Application\Query\Handler\GetNotificationByIdHandler;
use App\Notifications\Application\Query\Handler\GetNotificationsHandler;
use App\Notifications\Domain\Exception\NotificationForbiddenException;
use App\Notifications\Domain\Exception\NotificationNotFoundException;
use App\Notifications\Presentation\DTO\CreateNotificationRequest;
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

#[Route('/notifications', name: 'notifications_')]
final class NotificationsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {}

    /** GET /notifications */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetNotificationsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $currentUserId = $this->guard->validate_authenticated_user_id();
        $isAdmin       = $this->isGranted('ROLE_ADMIN');

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));

        // Admins may query any recipient; regular users see only their own
        $recipientId = $isAdmin
            ? $request->query->get('recipient') ?? $currentUserId
            : $currentUserId;

        $readParam = $request->query->get('read');
        $read      = $readParam !== null ? filter_var($readParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;

        $result = $handler->handle(new GetNotificationsQuery($limit, $offset, $recipientId, $read));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /notifications/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetNotificationByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetNotificationByIdQuery($id));
        } catch (NotificationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /notifications — admin only (system sends notifications to users) */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateNotificationHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CreateNotificationRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateNotificationRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new CreateNotificationCommand(
            $dto->recipientId,
            $dto->subject,
            $dto->message,
            $dto->senderId,
            $dto->collection,
            $dto->item,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /notifications/:id — marks notification as read */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, MarkNotificationReadHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        try {
            $result = $handler->handle(new MarkNotificationReadCommand($id, $userId, $isAdmin));
        } catch (NotificationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (NotificationForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /notifications/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteNotificationHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);
        $userId  = $this->guard->validate_authenticated_user_id();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteNotificationCommand($id, $userId, $isAdmin));
        } catch (NotificationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (NotificationForbiddenException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'FORBIDDEN']]]],
                Response::HTTP_FORBIDDEN,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
