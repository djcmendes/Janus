<?php

/**
 * @file UtilsController.php
 *
 * HTTP controller for utility endpoints (sort, hash, cache, random string).
 *
 * @package App\Utils\Presentation\Controller
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Utils\Presentation\Controller;

use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Application\Service\RequestGuard;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Provides administrative utility operations over HTTP.
 *
 * All actions require ROLE_ADMIN. Available endpoints:
 *   POST /utils/sort/{collection}  — reorder items by sort field
 *   GET  /utils/hash/generate      — bcrypt hash of a value
 *   GET  /utils/hash/verify        — verify a value against a bcrypt hash
 *   POST /utils/cache/clear        — flush the application cache pool
 *   GET  /utils/random/string      — cryptographically secure random string
 */
#[Route('/utils', name: 'utils_')]
final class UtilsController extends AbstractController
{
    /**
     * @param RequestGuard $guard Validates authentication and client type.
     */
    public function __construct(private readonly RequestGuard $guard) {}

    /**
     * POST /utils/sort/:collection
     *
     * Reorders items in a user-defined collection using its configured sort field.
     * Body: { "items": [{ "id": "uuid", "sort": 1 }, ...] }
     */
    #[Route('/sort/{collection}', name: 'sort', methods: ['POST'], priority: 20)]
    public function sort(
        string                             $collection,
        Request                            $request,
        Connection                         $connection,
        CollectionMetaRepositoryInterface  $collectionRepository,
    ): JsonResponse {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $meta = $collectionRepository->findByName($collection);
        if ($meta === null) {
            return $this->json(
                ['errors' => [['message' => sprintf('Collection "%s" not found.', $collection), 'extensions' => ['code' => 'NOT_FOUND']]]],
                Response::HTTP_NOT_FOUND,
            );
        }

        $sortField = $meta->getSortField();
        if ($sortField === null) {
            return $this->json(
                ['errors' => [['message' => sprintf('Collection "%s" has no sort field configured.', $collection), 'extensions' => ['code' => 'NO_SORT_FIELD']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $body  = json_decode($request->getContent(), true);
        $items = $body['items'] ?? [];

        if (!is_array($items) || empty($items)) {
            return $this->json(
                ['errors' => [['message' => 'Request body must contain a non-empty "items" array.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $table           = '`' . str_replace('`', '``', $collection) . '`';
        $quotedSortField = '`' . str_replace('`', '``', $sortField) . '`';

        foreach ($items as $item) {
            if (!isset($item['id'], $item['sort'])) {
                continue;
            }
            $connection->executeStatement(
                "UPDATE {$table} SET {$quotedSortField} = ? WHERE id = ?",
                [(int) $item['sort'], $item['id']],
            );
        }

        return $this->json(['data' => ['updated' => count($items)]]);
    }

    /**
     * GET /utils/hash/generate?value=plaintext
     *
     * Generates a bcrypt hash of the provided value.
     */
    #[Route('/hash/generate', name: 'hash_generate', methods: ['GET'], priority: 20)]
    public function hashGenerate(Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $value = (string) $request->query->get('value', '');

        if ($value === '') {
            return $this->json(
                ['errors' => [['message' => 'Query parameter "value" is required.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->json(['data' => ['hash' => password_hash($value, PASSWORD_BCRYPT)]]);
    }

    /**
     * GET /utils/hash/verify?value=plaintext&hash=$2y$...
     *
     * Verifies a plaintext value against a bcrypt hash.
     */
    #[Route('/hash/verify', name: 'hash_verify', methods: ['GET'], priority: 20)]
    public function hashVerify(Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $value = (string) $request->query->get('value', '');
        $hash  = (string) $request->query->get('hash', '');

        if ($value === '' || $hash === '') {
            return $this->json(
                ['errors' => [['message' => 'Query parameters "value" and "hash" are required.', 'extensions' => ['code' => 'VALIDATION_ERROR']]]],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return $this->json(['data' => ['valid' => password_verify($value, $hash)]]);
    }

    /**
     * POST /utils/cache/clear
     *
     * Clears the application cache pool.
     */
    #[Route('/cache/clear', name: 'cache_clear', methods: ['POST'], priority: 20)]
    public function cacheClear(CacheItemPoolInterface $cache): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $cache->clear();

        return $this->json(['data' => ['cleared' => true]]);
    }

    /**
     * GET /utils/random/string?length=32
     *
     * Returns a cryptographically secure random alphanumeric string.
     * `length` is clamped to [1, 256].
     */
    #[Route('/random/string', name: 'random_string', methods: ['GET'], priority: 20)]
    public function randomString(Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $length  = max(1, min(256, (int) $request->query->get('length', 32)));
        $charset = (string) $request->query->get('charset', '');

        if ($charset === '') {
            $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        }

        $charsetLen = strlen($charset);
        $result     = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $charset[random_int(0, $charsetLen - 1)];
        }

        return $this->json(['data' => ['random' => $result]]);
    }
}
