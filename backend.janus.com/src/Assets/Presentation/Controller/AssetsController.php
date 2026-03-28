<?php

declare(strict_types=1);

namespace App\Assets\Presentation\Controller;

use App\Assets\Application\Query\GetAssetQuery;
use App\Assets\Application\Query\Handler\GetAssetHandler;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assets', name: 'assets_')]
final class AssetsController extends AbstractController
{
    public function __construct(private readonly RequestGuard $guard) {}

    /** GET /assets/:id?width=&height=&fit=&format= */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, Request $request, GetAssetHandler $handler): Response
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);

        $width  = $request->query->has('width')  ? max(1, (int) $request->query->get('width'))  : null;
        $height = $request->query->has('height') ? max(1, (int) $request->query->get('height')) : null;
        $fit    = (string) $request->query->get('fit', 'contain');
        $format = (string) $request->query->get('format', 'jpg');

        try {
            $asset = $handler->handle(new GetAssetQuery($id, $width, $height, $fit, $format));
        } catch (FileNotFoundException $e) {
            return $this->json(
                ['errors' => [['message' => $e->getMessage(), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        } catch (\RuntimeException $e) {
            return $this->json(
                ['errors' => [['message' => 'Asset could not be processed.', 'extensions' => ['code' => 'TRANSFORM_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return new Response(
            $asset->content,
            Response::HTTP_OK,
            [
                'Content-Type'        => $asset->mimeType,
                'Content-Disposition' => 'inline; filename="' . $asset->filename . '"',
                'Cache-Control'       => 'public, max-age=31536000, immutable',
            ],
        );
    }
}
