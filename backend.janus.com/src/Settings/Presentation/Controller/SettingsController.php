<?php

declare(strict_types=1);

namespace App\Settings\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Settings\Application\Command\Handler\UpdateSettingsHandler;
use App\Settings\Application\Command\UpdateSettingsCommand;
use App\Settings\Application\Query\GetSettingsQuery;
use App\Settings\Application\Query\Handler\GetSettingsHandler;
use App\Settings\Presentation\DTO\UpdateSettingsRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings', name: 'settings_')]
final class SettingsController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard          $guard,
        private readonly GetSettingsHandler    $getSettingsHandler,
        private readonly UpdateSettingsHandler $updateSettingsHandler,
    ) {}

    /** GET /settings */
    #[Route('', name: 'get', methods: ['GET'])]
    public function get(): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $dto = $this->getSettingsHandler->handle(new GetSettingsQuery());

        return $this->json(['data' => $dto->toArray()]);
    }

    /** PATCH /settings */
    #[Route('', name: 'patch', methods: ['PATCH'])]
    public function patch(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = UpdateSettingsRequest::fromArray(
                json_decode($request->getContent(), true) ?? []
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $dto = $this->updateSettingsHandler->handle(new UpdateSettingsCommand(
            projectName:       $req->projectName,
            defaultLanguage:   $req->defaultLanguage,
            defaultAppearance: $req->defaultAppearance,
            projectUrl:        $req->projectUrl,
            projectLogo:       $req->projectLogo,
            projectColor:      $req->projectColor,
        ));

        return $this->json(['data' => $dto->toArray()]);
    }
}
