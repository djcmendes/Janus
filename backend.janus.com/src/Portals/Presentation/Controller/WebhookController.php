<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\Handler\TriggerMagnetRunHandler;
use App\Portals\Application\Command\TriggerMagnetRunCommand;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Inbound webhook endpoint.
 *
 * Consumers (GitHub, Stripe, custom integrations, etc.) POST a JSON payload to:
 *   POST /portals/magnets/{id}/webhook
 *
 * Authentication: the caller must include the shared secret either as:
 *   - Authorization: Bearer <secret>
 *   - or query param:  ?secret=<secret>
 *
 * The secret is stored in the magnet's source_config under the key "secret".
 * This endpoint is intentionally PUBLIC (ApiScope::PUBLIC) so external services
 * can call it without a Janus JWT, but the per-magnet secret acts as the gate.
 */
#[Route('/portals/magnets')]
final class WebhookController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard             $guard,
        private readonly MagnetRepositoryInterface $magnetRepository,
        private readonly TriggerMagnetRunHandler  $triggerHandler,
    ) {}

    #[Route('/{id}/webhook', methods: ['POST'])]
    public function receive(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::PUBLIC);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID, Client::CLI);

        $magnet = $this->magnetRepository->findById($id);
        if ($magnet === null) {
            throw new MagnetNotFoundException($id);
        }

        // Validate shared secret
        $config         = $magnet->getSourceConfig();
        $expectedSecret = (string) $config->get('secret', '');

        if ($expectedSecret === '') {
            return new JsonResponse(
                ['errors' => [['message' => 'Webhook is not configured for this magnet.', 'extensions' => ['code' => 'WEBHOOK_NOT_CONFIGURED']]]],
                Response::HTTP_BAD_REQUEST
            );
        }

        $providedSecret = $this->resolveSecret($request);

        if (!hash_equals($expectedSecret, $providedSecret)) {
            return new JsonResponse(
                ['errors' => [['message' => 'Invalid webhook secret.', 'extensions' => ['code' => 'WEBHOOK_UNAUTHORIZED']]]],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Decode payload
        $payload = [];
        $body    = $request->getContent();
        if (!empty($body)) {
            $decoded = json_decode($body, true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $dto = $this->triggerHandler->handle(
            new TriggerMagnetRunCommand(
                magnetId:       $id,
                webhookPayload: $payload,
            )
        );

        return new JsonResponse(
            ['data' => ['run_id' => $dto->id, 'status' => 'queued']],
            Response::HTTP_ACCEPTED
        );
    }

    private function resolveSecret(Request $request): string
    {
        $authHeader = $request->headers->get('Authorization', '');
        if (str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        return (string) $request->query->get('secret', '');
    }
}
