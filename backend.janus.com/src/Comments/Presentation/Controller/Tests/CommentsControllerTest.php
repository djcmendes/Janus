<?php

/**
 * @file CommentsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all CommentsController test cases.
 *
 * Strategy: All injected dependencies (RequestGuard, handlers) are final
 * and cannot be mocked directly. Each is instantiated as a real object
 * backed by mocked interfaces. Tests control behaviour at the dependency
 * layer — no bypass extension required.
 *
 * @package App\Comments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Presentation\Controller\Tests;

use App\Comments\Application\Command\Handler\CreateCommentHandler;
use App\Comments\Application\Command\Handler\DeleteCommentHandler;
use App\Comments\Application\Command\Handler\UpdateCommentHandler;
use App\Comments\Application\Query\Handler\GetCommentByIdHandler;
use App\Comments\Application\Query\Handler\GetCommentsHandler;
use App\Comments\Domain\Entity\Comment;
use App\Comments\Domain\Repository\CommentRepositoryInterface;
use App\Comments\Presentation\Controller\CommentsController;
use App\Heimdall\Application\Service\RequestGuard;
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
 * for all CommentsController test suites.
 */
#[CoversClass(className: CommentsController::class)]
abstract class CommentsControllerTest extends TestCase
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
     * Mock repository used by list/getById handlers.
     * @var MockObject&CommentRepositoryInterface
     */
    protected MockObject $readRepository;

    /**
     * Mock repository used by create/update/delete handlers.
     * @var MockObject&CommentRepositoryInterface
     */
    protected MockObject $writeRepository;

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
     * @var GetCommentsHandler
     */
    protected GetCommentsHandler $getCommentsHandler;

    /** @var GetCommentByIdHandler */
    protected GetCommentByIdHandler $getCommentByIdHandler;

    /** @var CreateCommentHandler */
    protected CreateCommentHandler $createCommentHandler;

    /** @var UpdateCommentHandler */
    protected UpdateCommentHandler $updateCommentHandler;

    /** @var DeleteCommentHandler */
    protected DeleteCommentHandler $deleteCommentHandler;

    /**
     * The system under test.
     * @var CommentsController
     */
    protected CommentsController $class;

    /**
     * Reflection of CommentsController for reading private properties.
     * @var ReflectionClass<CommentsController>
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
            public function getId(): string { return CommentsControllerTest::AUTH_USER_UUID; }
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

        $this->readRepository  = $this->createMock(CommentRepositoryInterface::class);
        $this->writeRepository = $this->createMock(CommentRepositoryInterface::class);

        $this->guard                 = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);
        $this->getCommentsHandler    = new GetCommentsHandler(repository: $this->readRepository);
        $this->getCommentByIdHandler = new GetCommentByIdHandler(repository: $this->readRepository);
        $this->createCommentHandler  = new CreateCommentHandler(repository: $this->writeRepository);
        $this->updateCommentHandler  = new UpdateCommentHandler(repository: $this->writeRepository);
        $this->deleteCommentHandler  = new DeleteCommentHandler(repository: $this->writeRepository);

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

        $this->class = new CommentsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(CommentsController::class);
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
            $this->container,
            $this->authorizationChecker,
            $this->guard,
            $this->getCommentsHandler,
            $this->getCommentByIdHandler,
            $this->createCommentHandler,
            $this->updateCommentHandler,
            $this->deleteCommentHandler,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     *
     * @return CommentsController A controller instance pre-wired to fail on authentication.
     */
    protected function buildControllerWithUnauthenticatedGuard(): CommentsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $controller = new CommentsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Creates a fully-populated Comment entity for use in test assertions.
     *
     * @param string $collection The collection name.
     * @param string $item       The item identifier.
     * @param string $userId     UUID of the comment author.
     *
     * @return Comment A hydrated domain entity with deterministic test values.
     */
    protected function makeComment(
        string $collection = 'posts',
        string $item       = '42',
        string $userId     = 'aaaaaaaa-0000-7000-8000-000000000001',
    ): Comment {
        return new Comment($collection, $item, 'Hello world', $userId);
    }
}
