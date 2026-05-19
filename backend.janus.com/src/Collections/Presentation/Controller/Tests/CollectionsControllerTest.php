<?php

/**
 * @file CollectionsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all CollectionsController test cases.
 *
 * Strategy: CollectionsController and all five handlers are declared `final` and
 * cannot be mocked directly. Each is instantiated as a real object whose injectable
 * dependencies (interfaces and non-final Symfony/Doctrine classes) are mocked.
 * Tests control behaviour at the dependency layer — no bypass extension required.
 *
 * @package App\Collections\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\Controller\Tests;

use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Application\Command\Handler\DeleteCollectionHandler;
use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Application\Query\Handler\GetCollectionByNameHandler;
use App\Collections\Application\Query\Handler\GetCollectionsHandler;
use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Collections\Presentation\Controller\CollectionsController;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Heimdall\Application\Service\RequestGuard;
use Doctrine\DBAL\Connection;
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
 * for all CollectionsController test suites.
 */
#[CoversClass(className: CollectionsController::class)]
abstract class CollectionsControllerTest extends TestCase
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
     * Shared mock of CollectionMetaRepositoryInterface used by all five handlers.
     * @var MockObject&CollectionMetaRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * Mock of FieldMetaRepositoryInterface used by CreateCollectionHandler and DeleteCollectionHandler.
     * @var MockObject&FieldMetaRepositoryInterface
     */
    protected MockObject $fieldRepository;

    /**
     * Mock of the Doctrine DBAL Connection — suppresses real DDL in SchemaManagerService.
     * @var MockObject&Connection
     */
    protected MockObject $connection;

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
     * Real SchemaManagerService instance backed by a mocked Connection to suppress DDL.
     * @var SchemaManagerService
     */
    protected SchemaManagerService $schemaManager;

    /**
     * Real RequestGuard backed by mocked TokenStorage and RequestStack.
     * @var RequestGuard
     */
    protected RequestGuard $guard;

    /**
     * Real GetCollectionsHandler backed by $repository.
     * @var GetCollectionsHandler
     */
    protected GetCollectionsHandler $getCollectionsHandler;

    /**
     * Real GetCollectionByNameHandler backed by $repository.
     * @var GetCollectionByNameHandler
     */
    protected GetCollectionByNameHandler $getCollectionByNameHandler;

    /**
     * Real CreateCollectionHandler backed by $repository, $fieldRepository, and $schemaManager.
     * @var CreateCollectionHandler
     */
    protected CreateCollectionHandler $createCollectionHandler;

    /**
     * Real UpdateCollectionHandler backed by $repository.
     * @var UpdateCollectionHandler
     */
    protected UpdateCollectionHandler $updateCollectionHandler;

    /**
     * Real DeleteCollectionHandler backed by $repository, $schemaManager, and $fieldRepository.
     * @var DeleteCollectionHandler
     */
    protected DeleteCollectionHandler $deleteCollectionHandler;

    /**
     * The system under test.
     * @var CollectionsController
     */
    protected CollectionsController $class;

    /**
     * Reflection of CollectionsController for reading private properties.
     * @var ReflectionClass<CollectionsController>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     *
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $user  = $this->createMock(UserInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->method('getToken')->willReturn($token);

        // ── WEB client type — allowed by all controller actions ───────────────
        $webRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($webRequest);

        // ── Repository and infrastructure mocks ──────────────────────────────
        $this->repository      = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->fieldRepository = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->connection      = $this->createMock(Connection::class);
        $this->schemaManager   = new SchemaManagerService($this->connection);

        // ── Real final handler instances ──────────────────────────────────────
        $this->guard                      = new RequestGuard($this->tokenStorage, $this->requestStack);
        $this->getCollectionsHandler      = new GetCollectionsHandler($this->repository);
        $this->getCollectionByNameHandler = new GetCollectionByNameHandler($this->repository);
        $this->createCollectionHandler    = new CreateCollectionHandler(
            $this->repository,
            $this->fieldRepository,
            $this->schemaManager,
        );
        $this->updateCollectionHandler    = new UpdateCollectionHandler($this->repository);
        $this->deleteCollectionHandler    = new DeleteCollectionHandler(
            $this->repository,
            $this->schemaManager,
            $this->fieldRepository,
        );

        // ── Symfony container (satisfies denyAccessUnlessGranted + json()) ────
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->authorizationChecker->method('isGranted')->willReturn(true);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $this->container->method('get')->willReturnMap([
            ['security.authorization_checker', $this->authorizationChecker],
        ]);

        $this->class = new CollectionsController(
            guard:                      $this->guard,
            getCollectionsHandler:      $this->getCollectionsHandler,
            getCollectionByNameHandler: $this->getCollectionByNameHandler,
            createCollectionHandler:    $this->createCollectionHandler,
            updateCollectionHandler:    $this->updateCollectionHandler,
            deleteCollectionHandler:    $this->deleteCollectionHandler,
        );

        $this->class->setContainer($this->container);

        $this->reflection = new ReflectionClass(CollectionsController::class);
    }

    /**
     * TestCase Destructor.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->repository,
            $this->fieldRepository,
            $this->connection,
            $this->schemaManager,
            $this->container,
            $this->authorizationChecker,
            $this->guard,
            $this->getCollectionsHandler,
            $this->getCollectionByNameHandler,
            $this->createCollectionHandler,
            $this->updateCollectionHandler,
            $this->deleteCollectionHandler,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Creates a fully-populated domain CollectionMeta for use in test scenarios.
     *
     * @return CollectionMeta A hydrated domain entity with deterministic test metadata.
     */
    protected function makeCollectionMeta(): CollectionMeta
    {
        $collection = new CollectionMeta('articles');
        $collection->setLabel('Articles');

        return $collection;
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     *
     * @return CollectionsController A controller pre-wired to fail on authentication.
     * @throws Exception
     */
    protected function buildControllerWithUnauthenticatedGuard(): CollectionsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard($tokenStorage, $this->requestStack);

        $controller = new CollectionsController(
            guard:                      $guard,
            getCollectionsHandler:      $this->getCollectionsHandler,
            getCollectionByNameHandler: $this->getCollectionByNameHandler,
            createCollectionHandler:    $this->createCollectionHandler,
            updateCollectionHandler:    $this->updateCollectionHandler,
            deleteCollectionHandler:    $this->deleteCollectionHandler,
        );

        $controller->setContainer($this->container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard whose request stack serves a CLI client,
     * which is not in the allowed list, causing authorize() to throw UnauthorizedException.
     *
     * @return CollectionsController A controller pre-wired to fail on client authorisation.
     * @throws Exception
     */
    protected function buildControllerWithUnauthorizedClient(): CollectionsController
    {
        $cliRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'cli']);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($cliRequest);

        $guard = new RequestGuard($this->tokenStorage, $requestStack);

        $controller = new CollectionsController(
            guard:                      $guard,
            getCollectionsHandler:      $this->getCollectionsHandler,
            getCollectionByNameHandler: $this->getCollectionByNameHandler,
            createCollectionHandler:    $this->createCollectionHandler,
            updateCollectionHandler:    $this->updateCollectionHandler,
            deleteCollectionHandler:    $this->deleteCollectionHandler,
        );

        $controller->setContainer($this->container);

        return $controller;
    }

    /**
     * Returns a controller whose container serves an authorization checker that denies
     * access, causing denyAccessUnlessGranted() to throw AccessDeniedException.
     *
     * @return CollectionsController
     * @throws Exception
     */
    protected function buildControllerWithAccessDenied(): CollectionsController
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn(false);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $container->method('get')->willReturnMap([
            ['security.authorization_checker', $authChecker],
        ]);

        $controller = new CollectionsController(
            guard:                      $this->guard,
            getCollectionsHandler:      $this->getCollectionsHandler,
            getCollectionByNameHandler: $this->getCollectionByNameHandler,
            createCollectionHandler:    $this->createCollectionHandler,
            updateCollectionHandler:    $this->updateCollectionHandler,
            deleteCollectionHandler:    $this->deleteCollectionHandler,
        );

        $controller->setContainer($container);

        return $controller;
    }
}
