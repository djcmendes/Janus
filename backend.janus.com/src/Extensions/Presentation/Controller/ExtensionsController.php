<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller;

use App\Extensions\Application\Command\DeleteExtensionCommand;
use App\Extensions\Application\Command\Handler\DeleteExtensionHandler;
use App\Extensions\Application\Command\Handler\RegisterExtensionHandler;
use App\Extensions\Application\Command\Handler\UpdateExtensionHandler;
use App\Extensions\Application\Command\RegisterExtensionCommand;
use App\Extensions\Application\Command\UpdateExtensionCommand;
use App\Extensions\Application\Query\GetExtensionByIdQuery;
use App\Extensions\Application\Query\GetExtensionsQuery;
use App\Extensions\Application\Query\Handler\GetExtensionByIdHandler;
use App\Extensions\Application\Query\Handler\GetExtensionsHandler;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Presentation\DTO\RegisterExtensionRequest;
use App\Extensions\Presentation\DTO\UpdateExtensionRequest;
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

#[Route('/extensions', name: 'extensions_')]
final class ExtensionsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {}

    /** GET /extensions — authenticated users can browse the registry */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetExtensionsHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $limit  = max(1, (int) ($request->query->get('limit', 25)));
        $offset = max(0, (int) ($request->query->get('offset', 0)));
        $type   = $request->query->get('type');

        $enabledParam = $request->query->get('enabled');
        $enabled      = $enabledParam !== null
            ? filter_var($enabledParam, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;

        $result = $handler->handle(new GetExtensionsQuery($limit, $offset, $type, $enabled));

        return $this->json([
            'data' => $result['data'],
            'meta' => [
                'total_count'  => $result['total'],
                'filter_count' => $result['total'],
            ],
        ]);
    }

    /** GET /extensions/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetExtensionByIdHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        try {
            $dto = $handler->handle(new GetExtensionByIdQuery($id));
        } catch (ExtensionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $dto]);
    }

    /** POST /extensions — admin: register a new extension in the registry */
    #[Route('', name: 'register', methods: ['POST'])]
    public function register(Request $request, RegisterExtensionHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var RegisterExtensionRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), RegisterExtensionRequest::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new RegisterExtensionCommand(
            $dto->name,
            $dto->type,
            $dto->version,
            $dto->enabled,
            $dto->description,
            $dto->meta,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /** PATCH /extensions/:id — admin: update enabled flag, version, meta */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateExtensionHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UpdateExtensionRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), UpdateExtensionRequest::class, 'json');

        try {
            $result = $handler->handle(new UpdateExtensionCommand(
                $id,
                $dto->enabled,
                $dto->version,
                $dto->meta,
            ));
        } catch (ExtensionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /** DELETE /extensions/:id — admin: remove extension from registry */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteExtensionHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $handler->handle(new DeleteExtensionCommand($id));
        } catch (ExtensionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
