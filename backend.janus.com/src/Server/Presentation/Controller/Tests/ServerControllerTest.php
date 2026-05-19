<?php

/**
 * @file ServerControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all ServerController test cases.
 *
 * Strategy: RequestGuard is a final class — it is instantiated as a real
 * object backed by mocked TokenStorageInterface and RequestStack.
 * ServerService is also final and has no interface; it is instantiated as
 * a real object with a mocked Connection and invalid DSN strings so that
 * redis/rabbitmq checks return 'invalid X_URL' rather than making real
 * network connections.
 *
 * @package App\Server\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Presentation\Controller\Tests;

use App\Heimdall\Domain\Service\RequestGuard;
use App\Server\Application\Service\ServerService;
use App\Server\Presentation\Controller\ServerController;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Common setup, teardown, and helpers for all ServerController test suites.
 */
#[CoversClass(className: ServerController::class)]
abstract class ServerControllerTest extends TestCase
{
    /** Invalid DSN that makes parse_url() return false — avoids real network calls. */
    protected const string INVALID_DSN = ':';

    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    /** @var ServerService */
    protected ServerService $serverService;

    /** @var RequestGuard */
    protected RequestGuard $guard;

    /** @var ServerController */
    protected ServerController $class;

    /** @var ReflectionClass<ServerController> */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getRoles(): array { return ['ROLE_USER']; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'test@example.com'; }
        };

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->method('getToken')->willReturn($token);

        $webRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($webRequest);

        $this->connection = $this->createMock(Connection::class);

        $this->serverService = new ServerService(
            connection:  $this->connection,
            redisUrl:    self::INVALID_DSN,
            rabbitmqDsn: self::INVALID_DSN,
        );

        $this->guard = new RequestGuard(
            tokenStorage: $this->tokenStorage,
            requestStack: $this->requestStack,
        );

        $this->class = new ServerController(
            guard:         $this->guard,
            serverService: $this->serverService,
        );

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('has')->willReturnMap([
            ['serializer', false],
        ]);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(ServerController::class);
    }

    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->container,
            $this->connection,
            $this->serverService,
            $this->guard,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     */
    protected function buildControllerWithUnauthenticatedGuard(): ServerController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $ctrl = new ServerController(guard: $guard, serverService: $this->serverService);
        $ctrl->setContainer(container: $this->container);

        return $ctrl;
    }
}
