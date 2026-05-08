<?php

/**
 * @file ActivityControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all ActivityController test cases.
 *
 * Strategy: RequestGuard, GetActivityHandler, and GetActivityByIdHandler are
 * all declared `final` and cannot be mocked directly. Instead, each is
 * instantiated as a real object whose injectable dependencies (interfaces and
 * non-final Symfony classes) are mocked. Tests control behaviour at the
 * dependency layer — no bypass extension required.
 *
 * @package App\Activity\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Presentation\Controller\Tests;

use App\Activity\Application\Query\Handler\GetActivityByIdHandler;
use App\Activity\Application\Query\Handler\GetActivityHandler;
use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Activity\Presentation\Controller\ActivityController;
use App\Heimdall\Domain\Service\RequestGuard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
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
 * for all ActivityController test suites.
 */
#[CoversClass(ActivityController::class)]
abstract class ActivityControllerTest extends TestCase
{
    /**
     * Mock of the token storage — controls authentication state for RequestGuard.
     * @var MockObject&TokenStorageInterface
     */
    protected MockObject $tokenStorage;

    /**
     * Mock of the Symfony request stack — controls the X-Client-Type header seen by RequestGuard.
     * @var MockObject&RequestStack
     */
    protected MockObject $requestStack;

    /**
     * Mock repository used by GetActivityHandler (list queries).
     * @var MockObject&ActivityRepositoryInterface
     */
    protected MockObject $listRepository;

    /**
     * Mock repository used by GetActivityByIdHandler (single-record queries).
     * @var MockObject&ActivityRepositoryInterface
     */
    protected MockObject $getByIdRepository;

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
     * Real GetActivityHandler instance backed by $listRepository.
     * @var GetActivityHandler
     */
    protected GetActivityHandler $getActivityHandler;

    /**
     * Real GetActivityByIdHandler instance backed by $getByIdRepository.
     * @var GetActivityByIdHandler
     */
    protected GetActivityByIdHandler $getActivityByIdHandler;

    /**
     * The system under test.
     * @var ActivityController
     */
    protected ActivityController $class;

    /**
     * Reflection of ActivityController for reading private properties.
     * @var ReflectionClass
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     *
     * @throws Exception
     */
    public function setUp(): void
    {
        $user  = $this->createMock(type: UserInterface::class);
        $token = $this->createMock(type: TokenInterface::class);

        $token->method(constraint: 'getUser')
              ->willReturn(value: $user);

        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);

        $this->tokenStorage->method(constraint:  'getToken')
                           ->willReturn(value: $token);

        // ── Client-type happy-path (WEB is in the allowed list) ───────────────
        $webRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);

        $this->requestStack = $this->createMock(type: RequestStack::class);

        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: $webRequest);

        // ── Repositories (tests configure per-scenario return values) ─────────
        $this->listRepository    = $this->createMock(type: ActivityRepositoryInterface::class);
        $this->getByIdRepository = $this->createMock(type: ActivityRepositoryInterface::class);

        // ── Real final instances ──────────────────────────────────────────────
        $this->guard                  = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);
        $this->getActivityHandler     = new GetActivityHandler(repository: $this->listRepository);
        $this->getActivityByIdHandler = new GetActivityByIdHandler(repository: $this->getByIdRepository);

        // ── Symfony container (satisfies denyAccessUnlessGranted + json()) ────
        $this->authorizationChecker = $this->createMock(type: AuthorizationCheckerInterface::class);

        $this->authorizationChecker->method(constraint: 'isGranted')
                                   ->willReturn(value: true);

        $this->container = $this->createMock(type: ContainerInterface::class);

        $this->container->method(constraint:  'has')
                        ->willReturnMap(valueMap: [
                            [ 'security.authorization_checker', true ],
                            [ 'serializer', false ],
                        ]);

        $this->container->method(constraint: 'get')
                        ->willReturnMap(valueMap: [
                            [ 'security.authorization_checker', $this->authorizationChecker ]
                        ]);

        $this->class = new ActivityController(
            guard:                  $this->guard,
            getActivityHandler:     $this->getActivityHandler,
            getActivityByIdHandler: $this->getActivityByIdHandler,
        );

        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(objectOrClass: ActivityController::class);
    }

    /**
     * TestCase Destructor.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->tokenStorage);
        unset($this->requestStack);
        unset($this->listRepository);
        unset($this->getByIdRepository);
        unset($this->container);
        unset($this->authorizationChecker);
        unset($this->guard);
        unset($this->getActivityHandler);
        unset($this->getActivityByIdHandler);
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validate_webservice_request() to throw UnauthorizedException.
     *
     * @return ActivityController A controller instance pre-wired to fail on authentication.
     * @throws Exception
     */
    protected function buildControllerWithUnauthenticatedGuard(): ActivityController
    {
        $tokenStorage = $this->createMock(type: TokenStorageInterface::class);

        $tokenStorage->method(constraint: 'getToken')
                     ->willReturn(value: null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack
        );

        $controller = new ActivityController(
            guard:                  $guard,
            getActivityHandler:     $this->getActivityHandler,
            getActivityByIdHandler: $this->getActivityByIdHandler
        );

        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard whose request stack serves a request
     * with the CLI client type, which is not in the allowed list, causing
     * authorize() to throw UnauthorizedException.
     *
     * @return ActivityController A controller instance pre-wired to fail on client authorisation.
     * @throws Exception
     */
    protected function buildControllerWithUnauthorizedClient(): ActivityController
    {
        $cliRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'cli']);

        $requestStack = $this->createMock(type: RequestStack::class);

        $requestStack->method(constraint: 'getCurrentRequest')
                     ->willReturn(value: $cliRequest);

        $guard = new RequestGuard(
            tokenStorage: $this->tokenStorage,
            requestStack: $requestStack
        );

        $controller = new ActivityController(
            guard:                  $guard,
            getActivityHandler:     $this->getActivityHandler,
            getActivityByIdHandler: $this->getActivityByIdHandler
        );

        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Returns a controller whose container serves an authorization checker that
     * denies access, causing denyAccessUnlessGranted() to throw AccessDeniedException.
     *
     * @return ActivityController
     * @throws Exception
     */
    protected function buildControllerWithAccessDenied(): ActivityController
    {
        $authChecker = $this->createMock(type: AuthorizationCheckerInterface::class);
        $authChecker->method(constraint: 'isGranted')
                    ->willReturn(value: false);

        $container = $this->createMock(type: ContainerInterface::class);

        $container->method(constraint: 'has')
                  ->willReturnMap(valueMap: [
                      ['security.authorization_checker', true],
                      ['serializer', false],
                  ]);

        $container->method(constraint: 'get')
                  ->willReturnMap(valueMap: [[
                      'security.authorization_checker',
                      $authChecker
                  ]]);

        $controller = new ActivityController(
            guard:                  $this->guard,
            getActivityHandler:     $this->getActivityHandler,
            getActivityByIdHandler: $this->getActivityByIdHandler
        );
        $controller->setContainer(container: $container);

        return $controller;
    }

    /**
     * Creates a fully-populated Activity entity for use in test assertions.
     *
     * The entity is pre-filled with deterministic test values for userId, ip,
     * and userAgent so individual tests do not need to repeat that boilerplate.
     * The UUID is generated by Activity's constructor (Uuid::v7) and is
     * accessible via $activity->getId() for assertion purposes.
     *
     * @param string      $action     The action type (e.g. 'create', 'update', 'delete').
     * @param string|null $collection The collection the action was performed on, or null.
     * @param string|null $item       The item identifier affected by the action, or null.
     *
     * @return Activity A hydrated Activity entity with fixed test metadata.
     */
    protected function makeActivity(
        string  $action     = 'create',
        ?string $collection = 'posts',
        ?string $item       = '1',
    ): Activity {
        $activity = new Activity(
            action:     $action,
            collection: $collection,
            item:       $item
        );
        $activity->setUserId(v: 'bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp(v: '127.0.0.1');
        $activity->setUserAgent(v: 'PHPUnit');

        return $activity;
    }
}
