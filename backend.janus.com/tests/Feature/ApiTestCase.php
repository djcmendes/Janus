<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Heimdall\Infrastructure\JWT\JwtService;
use App\Users\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Base class for all feature (HTTP-level) tests.
 *
 * Responsibilities:
 *   - Spin up a SQLite in-memory database before each test.
 *   - Provide helpers to create users and issue JWT tokens.
 *   - Provide a typed HTTP-client factory with pre-set headers.
 */
abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();

        $this->client = static::createClient();

        /** @var EntityManagerInterface $em */
        $em       = static::getContainer()->get(EntityManagerInterface::class);
        $this->em = $em;

        $schemaTool = new SchemaTool($em);
        $metadata   = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($this->em->getMetadataFactory()->getAllMetadata());
        parent::tearDown();
    }

    /**
     * Persists a User entity with a hashed password and the given roles.
     */
    protected function createUser(
        string $email,
        string $plainPassword,
        array  $roles = ['ROLE_USER'],
    ): User {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User($email);
        $user->setRoles($roles);
        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Issues a JWT access token for the given user via the real JwtService.
     */
    protected function getToken(User $user): string
    {
        /** @var JwtService $jwt */
        $jwt = static::getContainer()->get(JwtService::class);

        return $jwt->issueAccessToken($user);
    }

    /**
     * Makes an authenticated API request.
     *
     * @param array<string, mixed> $data Request body (will be JSON-encoded)
     */
    protected function apiRequest(
        string  $method,
        string  $uri,
        array   $data  = [],
        ?string $token = null,
        string  $clientType = 'web',
    ): void {
        $server = [
            'CONTENT_TYPE'      => 'application/json',
            'HTTP_X_CLIENT_TYPE' => $clientType,
        ];

        if ($token !== null) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        $this->client->request(
            $method,
            $uri,
            [],
            [],
            $server,
            $method !== 'GET' ? json_encode($data, JSON_THROW_ON_ERROR) : null,
        );
    }

    /**
     * Decodes the last response body as an associative array.
     *
     * @return array<string, mixed>
     */
    protected function responseJson(): array
    {
        $content = $this->client->getResponse()->getContent();
        return json_decode((string) $content, true, 512, JSON_THROW_ON_ERROR);
    }
}
