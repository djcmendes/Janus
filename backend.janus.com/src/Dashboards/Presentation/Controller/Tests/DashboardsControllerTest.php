<?php

/**
 * @file DashboardsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all DashboardsController test cases.
 *
 * Strategy: All injected dependencies (RequestGuard, handlers) are final
 * and cannot be mocked directly. Each is instantiated as a real object
 * backed by mocked interfaces. Tests control behaviour at the dependency
 * layer — no bypass extension required.
 *
 * @package App\Dashboards\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\Controller\Tests;

use App\Dashboards\Application\Command\Handler\CreateDashboardHandler;
use App\Dashboards\Application\Command\Handler\DeleteDashboardHandler;
use App\Dashboards\Application\Command\Handler\UpdateDashboardHandler;
use App\Dashboards\Application\Query\Handler\GetDashboardByIdHandler;
use App\Dashboards\Application\Query\Handler\GetDashboardsHandler;
use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Dashboards\Presentation\Controller\DashboardsController;
use App\Heimdall\Application\Service\RequestGuard;
use App\Panels\Domain\Repository\PanelRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Common setup, teardown, shared instances, and scenario-builder helpers
 * for all DashboardsController test suites.
 */
#[CoversClass(className: DashboardsController::class)]
abstract class DashboardsControllerTest extends TestCase
{
    /** @var string UUID returned by the fake authenticated user's getId() method. */
    public const string AUTH_USER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Mock of the token storage — controls authentication state for RequestGuard.
     * @var MockObject&TokenStorageInterface
     */
    protected MockObject $tokenStorage;

    /**
     * Mock of the Symfony request stack — controls the X-Client-Type header.
     * @var MockObject&RequestStack
     */
    protected MockObject $requestStack;

    /**
     * Mock repository used by list/getById query handlers.
     * @var MockObject&DashboardRepositoryInterface
     */
    protected MockObject $readRepository;

    /**
     * Mock repository used by create/update/delete command handlers.
     * @var MockObject&DashboardRepositoryInterface
     */
    protected MockObject $writeRepository;

    /**
     * Mock of the panel repository used by DeleteDashboardHandler cascade.
     * @var MockObject&PanelRepositoryInterface
     */
    protected MockObject $panelRepository;

    /**
     * Mock of the Symfony service container — satisfies AbstractController internals.
     * @var MockObject&ContainerInterface
     */
    protected MockObject $container;

    /**
     * Mock of the Symfony authorization checker — controls ROLE_ADMIN access.
     * @var MockObject&AuthorizationCheckerInterface
     */
    protected MockObject $authorizationChecker;

    /**
     * Real RequestGuard instance backed by mocked dependencies.
     * @var RequestGuard
     */
    protected RequestGuard $guard;

    /**
     * Real handler instances backed by mock repositories.
     * @var GetDashboardsHandler
     */
    protected GetDashboardsHandler $getDashboardsHandler;

    /** @var GetDashboardByIdHandler */
    protected GetDashboardByIdHandler $getDashboardByIdHandler;

    /** @var CreateDashboardHandler */
    protected CreateDashboardHandler $createDashboardHandler;

    /** @var UpdateDashboardHandler */
    protected UpdateDashboardHandler $updateDashboardHandler;

    /** @var DeleteDashboardHandler */
    protected DeleteDashboardHandler $deleteDashboardHandler;

    /**
     * The system under test.
     * @var DashboardsController
     */
    protected DashboardsController $class;

    /**
     * Reflection of DashboardsController for reading private properties.
     * @var ReflectionClass<DashboardsController>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getId(): string { return DashboardsControllerTest::AUTH_USER_UUID; }
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

        $this->readRepository  = $this->createMock(DashboardRepositoryInterface::class);
        $this->writeRepository = $this->createMock(DashboardRepositoryInterface::class);
        $this->panelRepository = $this->createMock(PanelRepositoryInterface::class);

        $this->guard                   = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);
        $this->getDashboardsHandler    = new GetDashboardsHandler(repository: $this->readRepository);
        $this->getDashboardByIdHandler = new GetDashboardByIdHandler(repository: $this->readRepository);
        $this->createDashboardHandler  = new CreateDashboardHandler(repository: $this->writeRepository);
        $this->updateDashboardHandler  = new UpdateDashboardHandler(repository: $this->writeRepository);
        $this->deleteDashboardHandler  = new DeleteDashboardHandler(
            repository:      $this->writeRepository,
            panelRepository: $this->panelRepository,
        );

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

        $this->class = new DashboardsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(DashboardsController::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->readRepository,
            $this->writeRepository,
            $this->panelRepository,
            $this->container,
            $this->authorizationChecker,
            $this->guard,
            $this->getDashboardsHandler,
            $this->getDashboardByIdHandler,
            $this->createDashboardHandler,
            $this->updateDashboardHandler,
            $this->deleteDashboardHandler,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     *
     * @return DashboardsController A controller instance pre-wired to fail on authentication.
     */
    protected function buildControllerWithUnauthenticatedGuard(): DashboardsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $controller = new DashboardsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard and a container where ROLE_ADMIN is granted.
     *
     * @return DashboardsController A controller instance with admin access.
     */
    protected function buildControllerWithAdminGuard(): DashboardsController
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

        $controller = new DashboardsController(guard: $this->guard);
        $controller->setContainer(container: $container);

        return $controller;
    }

    /**
     * Creates a fully-populated Dashboard entity for use in test assertions.
     *
     * @param string      $name   Dashboard name.
     * @param string|null $userId Owner UUID.
     *
     * @return Dashboard A hydrated domain entity with deterministic test values.
     */
    protected function makeDashboard(
        string  $name   = 'Test Dashboard',
        ?string $userId = 'aaaaaaaa-0000-7000-8000-000000000001',
    ): Dashboard {
        return Dashboard::reconstitute(
            id:        'bbbbbbbb-0000-7000-8000-000000000001',
            name:      $name,
            icon:      null,
            note:      null,
            userId:    $userId,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new \DateTimeImmutable('2024-06-01T00:00:00Z'),
        );
    }
}
