<?php

declare(strict_types=1);

namespace App\Schema\Presentation\Controller;

use App\Schema\Application\Command\ApplySchemaCommand;
use App\Schema\Application\Command\Handler\ApplySchemaHandler;
use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use App\Schema\Application\Query\Handler\GetSchemaSnapshotHandler;
use App\Schema\Domain\Service\SchemaDiffService;
use App\Schema\Domain\Service\SchemaSnapshotService;
use App\Schema\Presentation\DTO\ApplySchemaRequest;
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

#[Route('/schema', name: 'schema_')]
final class SchemaController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard        $guard,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator,
    ) {}

    /** GET /schema/snapshot */
    #[Route('/snapshot', name: 'snapshot', methods: ['GET'])]
    public function snapshot(GetSchemaSnapshotHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json(['data' => $handler->handle(new GetSchemaSnapshotQuery())]);
    }

    /**
     * POST /schema/diff
     *
     * Body: { "snapshot": { ... } }
     * Returns the diff between the posted snapshot and the current live schema.
     */
    #[Route('/diff', name: 'diff', methods: ['POST'])]
    public function diff(
        Request              $request,
        SchemaSnapshotService $snapshotService,
        SchemaDiffService    $diffService,
    ): JsonResponse {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
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
     * Body: { "snapshot": { ... }, "force": false }
     * Applies the snapshot to the current schema (create/update; delete only if force=true).
     */
    #[Route('/apply', name: 'apply', methods: ['POST'])]
    public function apply(Request $request, ApplySchemaHandler $handler): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var ApplySchemaRequest $dto */
        $dto = $this->serializer->deserialize($request->getContent(), ApplySchemaRequest::class, 'json');

        $errors = $this->validator->validate($dto);
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
