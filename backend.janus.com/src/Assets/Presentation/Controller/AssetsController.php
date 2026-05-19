<?php

/**
 * @file AssetsController.php
 *
 * HTTP controller for serving stored files as transformed image assets.
 *
 * @package App\Assets\Presentation\Controller
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Presentation\Controller;

use App\Assets\Application\Query\GetAssetQuery;
use App\Assets\Application\Query\Handler\GetAssetHandler;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Application\Service\RequestGuard;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Heimdall\Domain\Exception\UnauthorizedException;

/**
 * Exposes stored files as on-the-fly transformed image assets over HTTP.
 */
#[Route('/assets', name: 'assets_')]
final class AssetsController extends AbstractController
{
    /**
     * Constructor
     *
     * @param RequestGuard $guard  Validates the API version, scope, and allowed client types.
     */
    public function __construct(private readonly RequestGuard $guard)
    {}

    /**
     * Returns the requested file as a transformed image response.
     *
     * Width and height are clamped to a minimum of 1. An invalid fit falls back to 'contain'
     * inside the handler. FileNotFoundException yields 404; RuntimeException yields 422.
     *
     * @param  string          $id      UUID of the file record to serve.
     * @param  Request         $request HTTP request carrying optional width, height, fit, and format params.
     * @param  GetAssetHandler $handler Handler resolved by Symfony's controller argument resolver.
     *
     * @return Response  Binary image response on success, or a JSON error envelope on failure.
     *
     * @throws UnauthorizedException  When the request is unauthenticated or the client type is not allowed.
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id, Request $request, GetAssetHandler $handler): Response
    {
        $this->guard->validateWebserviceRequest(version: ApiVersion::JANUS_100, scope: ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);

        $width  = $request->query->has(key: 'width')  ? max(1, (int) $request->query->get(key: 'width'))  : null;
        $height = $request->query->has(key: 'height') ? max(1, (int) $request->query->get(key: 'height')) : null;
        $fit    = (string) $request->query->get(key: 'fit', default: 'contain');
        $format = (string) $request->query->get(key: 'format', default: 'jpg');

        try {
            $asset = $handler->handle(query: new GetAssetQuery(id: $id, width: $width, height: $height, fit: $fit, format: $format));
        } catch (FileNotFoundException $e) {
            return $this->json(
                data:   [ 'errors' => [[ 'message' => $e->getMessage(), 'extensions' => [ 'code' => 'NOT_FOUND' ]]]],
                status: Response::HTTP_NOT_FOUND,
            );
        } catch (RuntimeException $e) {
            return $this->json(
                data:   [ 'errors' => [[ 'message' => 'Asset could not be processed.', 'extensions' => [ 'code' => 'TRANSFORM_ERROR' ]]]],
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return new Response(
            content: $asset->content,
            status:  Response::HTTP_OK,
            headers: [
                'Content-Type'        => $asset->mimeType,
                'Content-Disposition' => 'inline; filename="' . $asset->filename . '"',
                'Cache-Control'       => 'public, max-age=31536000, immutable',
            ],
        );
    }
}
