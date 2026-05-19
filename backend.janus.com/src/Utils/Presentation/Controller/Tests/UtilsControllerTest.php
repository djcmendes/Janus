<?php

/**
 * @file UtilsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all UtilsController test cases.
 *
 * Strategy: RequestGuard is a final class — it is instantiated as a real
 * object backed by mocked TokenStorageInterface and RequestStack.
 * The Symfony container is mocked to return a fake AuthorizationChecker
 * so that denyAccessUnlessGranted() can be controlled per test.
 * Action-injected dependencies (Connection, CollectionMetaRepositoryInterface,
 * CacheInterface) are mocked and passed directly to the action method.
 *
 * @package App\Utils\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Utils\Presentation\Controller\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Heimdall\Application\Service\RequestGuard;
use App\Utils\Presentation\Controller\UtilsController;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Common setup, teardown, and helpers for all UtilsController test suites.
 */
#[CoversClass(className: UtilsController::class)]
abstract class UtilsControllerTest extends TestCase
{
    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&AuthorizationCheckerInterface */
    protected MockObject $authorizationChecker;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    /** @var MockObject&CollectionMetaRepositoryInterface */
    protected MockObject $collectionRepository;

    /** @var MockObject&CacheItemPoolInterface */
    protected MockObject $cache;

    /** @var RequestGuard */
    protected RequestGuard $guard;

    /** @var UtilsController */
    protected UtilsController $class;

    /** @var ReflectionClass<UtilsController> */
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

        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->authorizationChecker->method('isGranted')->willReturn(false);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $this->container->method('get')->willReturnMap([
            ['security.authorization_checker', $this->authorizationChecker],
        ]);

        $this->connection           = $this->createMock(Connection::class);
        $this->collectionRepository = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->cache                = $this->createMock(CacheItemPoolInterface::class);

        $this->guard = new RequestGuard(
            tokenStorage: $this->tokenStorage,
            requestStack: $this->requestStack,
        );

        $this->class = new UtilsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(UtilsController::class);
    }

    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->authorizationChecker,
            $this->container,
            $this->connection,
            $this->collectionRepository,
            $this->cache,
            $this->guard,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller whose authorization checker grants every ROLE_ADMIN check.
     */
    protected function buildControllerWithAdminGuard(): UtilsController
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $container->method('get')->willReturnMap([
            ['security.authorization_checker', $authChecker],
        ]);

        $controller = new UtilsController(guard: $this->guard);
        $controller->setContainer(container: $container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     */
    protected function buildControllerWithUnauthenticatedGuard(): UtilsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $controller = new UtilsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Creates a real CollectionMeta instance with a specified sort field value.
     *
     * CollectionMeta is final and cannot be mocked; it is created as a real object.
     */
    protected function makeCollectionMetaWithSortField(?string $sortField): CollectionMeta
    {
        $meta = new CollectionMeta('articles');
        $meta->setSortField($sortField);

        return $meta;
    }

    /**
     * Builds a JSON Request with the given body and the X-Client-Type header set.
     *
     * @param array<string, mixed> $body
     */
    protected function jsonRequest(array $body, string $clientType = 'web'): Request
    {
        return new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => $clientType],
            content: json_encode($body),
        );
    }
}
