<?php

declare(strict_types=1);

namespace App\Translations\Presentation\Controller;

use App\Translations\Application\Command\CreateTranslationCommand;
use App\Translations\Application\Command\DeleteTranslationCommand;
use App\Translations\Application\Command\Handler\CreateTranslationHandler;
use App\Translations\Application\Command\Handler\DeleteTranslationHandler;
use App\Translations\Application\Command\Handler\UpdateTranslationHandler;
use App\Translations\Application\Command\UpdateTranslationCommand;
use App\Translations\Application\Query\GetTranslationByIdQuery;
use App\Translations\Application\Query\GetTranslationsQuery;
use App\Translations\Application\Query\Handler\GetTranslationByIdHandler;
use App\Translations\Application\Query\Handler\GetTranslationsHandler;
use App\Translations\Domain\Exception\TranslationAlreadyExistsException;
use App\Translations\Domain\Exception\TranslationNotFoundException;
use App\Translations\Presentation\DTO\CreateTranslationRequest;
use App\Translations\Presentation\DTO\UpdateTranslationRequest;
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

#[Route('/translations', name: 'translations_')]
final class TranslationsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {}

    /** GET /translations — public so the frontend can load strings without auth */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetTranslationsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::PUBLIC);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $limit    = max(1, (int) ($request->query->get('limit', 100)));
        $offset   = max(0, (int) ($request->query->get('offset', 0)));
        $language = $request->query->get('language');
        $key      = $request->query->get('key');

        $result = $handler->handle(new GetTranslationsQuery($limit, $offset, $language, $key));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /translations/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetTranslationByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::PUBLIC);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetTranslationByIdQuery($id));
        } catch (TranslationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /translations — admin only */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, CreateTranslationHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var CreateTranslationRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), CreateTranslationRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new CreateTranslationCommand($dto->language, $dto->key, $dto->value));
        } catch (TranslationAlreadyExistsException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'ALREADY_EXISTS']]]],
                Response::HTTP_CONFLICT,
            );
        }

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /translations/:id — admin only; updates the translated value */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateTranslationHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdateTranslationRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdateTranslationRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new UpdateTranslationCommand($id, $dto->value));
        } catch (TranslationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /translations/:id — admin only */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteTranslationHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteTranslationCommand($id));
        } catch (TranslationNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
