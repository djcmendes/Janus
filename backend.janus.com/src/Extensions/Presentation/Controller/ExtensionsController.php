<?php

/**
 * @file ExtensionsController.php
 *
 * HTTP controller for the Extensions resource. Thin presentation layer — delegates
 * all business logic to Application-layer handlers.
 *
 * @package App\Extensions\Presentation\Controller
 * @author  David Mendes
 */

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
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
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
 * REST controller for the /extensions resource.
 *
 * GET /extensions and GET /extensions/{id} are accessible to all authenticated users.
 * POST, PATCH, and DELETE require ROLE_ADMIN.
 */
#[Route('/extensions', name: 'extensions_')]
final class ExtensionsController extends AbstractController
{
    /**
     * @param RequestGuard $guard Authentication and client-type enforcement.
     */
    public function __construct(
        private readonly RequestGuard $guard,
    ) {}

    /**
     * Returns a paginated list of extensions. Accessible to all authenticated users.
     *
     * @param Request             $request HTTP request carrying pagination and filter params.
     * @param GetExtensionsHandler $handler Retrieves the paginated result set.
     *
     * @return JsonResponse Paginated array under "data" with "meta" counts.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, GetExtensionsHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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

    /**
     * Returns a single extension by UUID. Accessible to all authenticated users.
     *
     * @param string                  $id      UUID path parameter.
     * @param GetExtensionByIdHandler $handler Retrieves the extension DTO.
     *
     * @return JsonResponse Extension DTO under "data", or 404 error envelope.
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, GetExtensionByIdHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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

    /**
     * Registers a new extension. Requires ROLE_ADMIN.
     *
     * @param Request                  $request HTTP request carrying the JSON body.
     * @param RegisterExtensionHandler $handler Creates and persists the extension.
     *
     * @return JsonResponse Created extension DTO under "data" (HTTP 201), or error envelope.
     */
    #[Route('', name: 'register', methods: ['POST'])]
    public function register(Request $request, RegisterExtensionHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $body = json_decode($request->getContent(), true) ?? [];

        $name    = trim((string) ($body['name'] ?? ''));
        $type    = trim((string) ($body['type'] ?? ''));
        $version = trim((string) ($body['version'] ?? ''));

        if ($name === '' || $version === '') {
            return $this->json(
                ['errors' => [['message' => 'name and version are required.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        if (ExtensionType::tryFrom($type) === null) {
            return $this->json(
                ['errors' => [['message' => "Invalid extension type '{$type}'.", 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $result = $handler->handle(new RegisterExtensionCommand(
            name:        $name,
            type:        $type,
            version:     $version,
            enabled:     (bool) ($body['enabled'] ?? false),
            description: isset($body['description']) ? (string) $body['description'] : null,
            meta:        isset($body['meta']) && is_array($body['meta']) ? $body['meta'] : null,
        ));

        return $this->json(['data' => $result], Response::HTTP_CREATED);
    }

    /**
     * Partially updates an existing extension. Requires ROLE_ADMIN.
     *
     * @param string                  $id      UUID path parameter.
     * @param Request                 $request HTTP request carrying the JSON body.
     * @param UpdateExtensionHandler  $handler Applies the partial update.
     *
     * @return JsonResponse Updated extension DTO under "data", or error envelope.
     */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request, UpdateExtensionHandler $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $body    = json_decode($request->getContent(), true) ?? [];
        $enabled = array_key_exists('enabled', $body) ? (bool) $body['enabled'] : UpdateExtensionCommand::UNCHANGED;
        $version = array_key_exists('version', $body) ? (string) $body['version'] : UpdateExtensionCommand::UNCHANGED;
        $meta    = array_key_exists('meta', $body) ? $body['meta'] : UpdateExtensionCommand::UNCHANGED;

        try {
            $result = $handler->handle(new UpdateExtensionCommand($id, $enabled, $version, $meta));
        } catch (ExtensionNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json(['data' => $result]);
    }

    /**
     * Deletes an extension by UUID. Requires ROLE_ADMIN.
     *
     * @param string                  $id      UUID path parameter.
     * @param DeleteExtensionHandler  $handler Removes the extension from persistence.
     *
     * @return Response HTTP 204 No Content on success, or 404 error envelope.
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, DeleteExtensionHandler $handler): Response
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
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
