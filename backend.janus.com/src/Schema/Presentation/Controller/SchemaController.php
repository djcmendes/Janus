<?php

/**
 * @file SchemaController.php
 *
 * HTTP controller for schema snapshot, diff, and apply endpoints.
 *
 * @package App\Schema\Presentation\Controller
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\Controller;

use App\Schema\Application\Command\ApplySchemaCommand;
use App\Schema\Application\Command\Handler\ApplySchemaHandlerInterface;
use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use App\Schema\Application\Query\Handler\GetSchemaSnapshotHandlerInterface;
use App\Schema\Domain\Service\SchemaDiffServiceInterface;
use App\Schema\Domain\Service\SchemaSnapshotServiceInterface;
use App\Schema\Presentation\DTO\ApplySchemaRequest;
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
 * Exposes schema snapshot, diff, and apply operations over HTTP.
 *
 * All three actions require ROLE_ADMIN. Endpoints:
 *   GET  /schema/snapshot — export the full current schema
 *   POST /schema/diff     — diff a posted snapshot against the live schema
 *   POST /schema/apply    — apply a snapshot (create/update; delete only when force=true)
 */
#[Route('/schema', name: 'schema_')]
final class SchemaController extends AbstractController
{
    /**
     * @param RequestGuard $guard Validates authentication and client type.
     */
    public function __construct(
        private readonly RequestGuard        $guard,
    ) {}

    /**
     * GET /schema/snapshot
     *
     * Returns a complete schema snapshot (collections, fields, relations).
     *
     * @param GetSchemaSnapshotHandlerInterface $handler Query handler that delegates to SchemaSnapshotService.
     * @return JsonResponse 200 with { "data": { snapshot } }.
     */
    #[Route('/snapshot', name: 'snapshot', methods: ['GET'])]
    public function snapshot(GetSchemaSnapshotHandlerInterface $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json(['data' => $handler->handle(new GetSchemaSnapshotQuery())]);
    }

    /**
     * POST /schema/diff
     *
     * Computes the diff between a client-supplied snapshot and the live schema.
     * Body: { "snapshot": { ... } }
     *
     * @param Request                        $request         Carries the JSON body with the reference snapshot.
     * @param SchemaSnapshotServiceInterface $snapshotService Reads the current live schema state.
     * @param SchemaDiffServiceInterface     $diffService     Computes the structured difference.
     * @return JsonResponse 200 with { "data": { diff } } or 422 on invalid body.
     */
    #[Route('/diff', name: 'diff', methods: ['POST'])]
    public function diff(
        Request                        $request,
        SchemaSnapshotServiceInterface $snapshotService,
        SchemaDiffServiceInterface     $diffService,
    ): JsonResponse {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $body = json_decode($request->getContent(), true);

        if (!is_array($body) || !isset($body['snapshot']) || !is_array($body['snapshot'])) {
            return $this->json(
                ['errors' => [['message' => 'Request body must contain a "snapshot" object.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $current = $snapshotService->snapshot();
        $diff    = $diffService->diff($current, $body['snapshot']);

        return $this->json(['data' => $diff]);
    }

    /**
     * POST /schema/apply
     *
     * Applies a snapshot to the live schema (create/update always; delete only when force=true).
     * Body: { "snapshot": { ... }, "force": false }
     *
     * @param Request                      $request Carries the JSON body with snapshot and optional force flag.
     * @param ApplySchemaHandlerInterface  $handler Command handler that orchestrates all DDL/metadata changes.
     * @return JsonResponse 200 with { "data": { applied, skipped } }, 422 on validation error,
     *                      or 422 with SCHEMA_ERROR code on invalid snapshot data.
     */
    #[Route('/apply', name: 'apply', methods: ['POST'])]
    public function apply(Request $request, ApplySchemaHandlerInterface $handler): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var ApplySchemaRequest $dto */
        $dto = $this->container->get('serializer')->deserialize($request->getContent(), ApplySchemaRequest::class, 'json');

        $errors = $this->container->get('validator')->validate($dto);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => [['message' => (string) $errors->get(0)->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        try {
            $result = $handler->handle(new ApplySchemaCommand($dto->snapshot, $dto->force));
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'SCHEMA_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->json(['data' => $result]);
    }
}
