<?php

/**
 * @file VersionsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all VersionsController test cases.
 *
 * Strategy: All injected dependencies (RequestGuard, handlers) are final
 * and cannot be mocked directly. Each is instantiated as a real object
 * backed by mocked interfaces. The serializer and validator are wired
 * via the Symfony container mock to support deserialization in write actions.
 *
 * Note: symfony/serializer and symfony/validator are not installed locally.
 * Local stub interfaces (VersionsSerializerStub, VersionsValidatorStub) are
 * defined below to enable PHPUnit mocking without requiring those packages.
 *
 * @package App\Versions\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\Controller\Tests;

use App\Versions\Application\Command\Handler\DeleteVersionHandler;
use App\Versions\Application\Command\Handler\PromoteVersionHandler;
use App\Versions\Application\Command\Handler\SaveVersionHandler;
use App\Versions\Application\Command\Handler\UpdateVersionHandler;
use App\Versions\Application\Query\Handler\GetVersionByIdHandler;
use App\Versions\Application\Query\Handler\GetVersionsHandler;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use App\Versions\Infrastructure\Service\VersionService;
use App\Versions\Presentation\Controller\VersionsController;
use App\Versions\Presentation\DTO\SaveVersionRequest;
use App\Versions\Presentation\DTO\UpdateVersionRequest;
use App\Heimdall\Domain\Service\RequestGuard;
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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Minimal serializer stub for mocking — symfony/serializer not installed locally.
 */
interface VersionsSerializerStub
{
    public function serialize(mixed $data, string $format, array $context = []): string;
    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed;
}

/**
 * Minimal validator stub for mocking — symfony/validator not installed locally.
 */
interface VersionsValidatorStub
{
    public function validate(mixed $value, mixed $constraints = null, mixed $groups = null): \Countable;
}

/**
 * Common setup, teardown, shared instances, and scenario-builder helpers
 * for all VersionsController test suites.
 */
#[CoversClass(className: VersionsController::class)]
abstract class VersionsControllerTest extends TestCase
{
    /** @var string UUID returned by the fake authenticated user's getId() method. */
    public const string AUTH_USER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&VersionRepositoryInterface */
    protected MockObject $readRepository;

    /** @var MockObject&VersionRepositoryInterface */
    protected MockObject $writeRepository;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&AuthorizationCheckerInterface */
    protected MockObject $authorizationChecker;

    /** @var MockObject&VersionsSerializerStub */
    protected MockObject $serializer;

    /** @var MockObject&VersionsValidatorStub */
    protected MockObject $validator;

    /** @var RequestGuard */
    protected RequestGuard $guard;

    /** @var GetVersionsHandler */
    protected GetVersionsHandler $getVersionsHandler;

    /** @var GetVersionByIdHandler */
    protected GetVersionByIdHandler $getVersionByIdHandler;

    /** @var SaveVersionHandler */
    protected SaveVersionHandler $saveVersionHandler;

    /** @var UpdateVersionHandler */
    protected UpdateVersionHandler $updateVersionHandler;

    /** @var DeleteVersionHandler */
    protected DeleteVersionHandler $deleteVersionHandler;

    /** @var PromoteVersionHandler */
    protected PromoteVersionHandler $promoteVersionHandler;

    /** @var VersionsController */
    protected VersionsController $class;

    /** @var ReflectionClass<VersionsController> */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getId(): string { return VersionsControllerTest::AUTH_USER_UUID; }
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

        $this->readRepository  = $this->createMock(VersionRepositoryInterface::class);
        $this->writeRepository = $this->createMock(VersionRepositoryInterface::class);
        $this->connection      = $this->createMock(Connection::class);

        $versionService = new VersionService(connection: $this->connection);

        $this->guard                 = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);
        $this->getVersionsHandler    = new GetVersionsHandler(repository: $this->readRepository);
        $this->getVersionByIdHandler = new GetVersionByIdHandler(repository: $this->readRepository);
        $this->saveVersionHandler    = new SaveVersionHandler(repository: $this->writeRepository);
        $this->updateVersionHandler  = new UpdateVersionHandler(repository: $this->writeRepository);
        $this->deleteVersionHandler  = new DeleteVersionHandler(repository: $this->writeRepository);
        $this->promoteVersionHandler = new PromoteVersionHandler(
            repository: $this->writeRepository,
            versionService: $versionService,
        );

        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->authorizationChecker->method('isGranted')->willReturn(false);

        $this->serializer = $this->createMock(VersionsSerializerStub::class);
        $this->serializer->method('serialize')->willReturnCallback(
            static fn(mixed $data) => (string) json_encode($data),
        );

        $this->validator = $this->createMock(VersionsValidatorStub::class);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', true],
            ['validator', true],
        ]);
        $this->container->method('get')->willReturnMap([
            ['security.authorization_checker', $this->authorizationChecker],
            ['serializer', $this->serializer],
            ['validator', $this->validator],
        ]);

        $this->class = new VersionsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(VersionsController::class);
    }

    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->readRepository,
            $this->writeRepository,
            $this->connection,
            $this->container,
            $this->authorizationChecker,
            $this->serializer,
            $this->validator,
            $this->guard,
            $this->getVersionsHandler,
            $this->getVersionByIdHandler,
            $this->saveVersionHandler,
            $this->updateVersionHandler,
            $this->deleteVersionHandler,
            $this->promoteVersionHandler,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller whose authorization checker grants every ROLE_ADMIN check,
     * with serializer and validator still wired — used for write-action tests.
     *
     * @return VersionsController A controller instance pre-wired to pass all access checks.
     */
    protected function buildControllerWithAdminGuard(): VersionsController
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', true],
            ['validator', true],
        ]);
        $container->method('get')->willReturnMap([
            ['security.authorization_checker', $authChecker],
            ['serializer', $this->serializer],
            ['validator', $this->validator],
        ]);

        $controller = new VersionsController(guard: $this->guard);
        $controller->setContainer(container: $container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     *
     * @return VersionsController A controller instance pre-wired to fail on authentication.
     */
    protected function buildControllerWithUnauthenticatedGuard(): VersionsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $controller = new VersionsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Creates a domain Version with deterministic test values.
     *
     * @return Version A hydrated entity for test assertion comparisons.
     */
    protected function makeVersion(): Version
    {
        return new Version('articles', 'item-uuid-1', 'main', ['title' => 'Hello']);
    }

    /**
     * Creates a pre-populated SaveVersionRequest for use in create tests.
     *
     * @return SaveVersionRequest A valid request DTO.
     */
    protected function makeSaveRequest(): SaveVersionRequest
    {
        $req             = new SaveVersionRequest();
        $req->collection = 'articles';
        $req->item       = 'item-uuid-1';
        $req->key        = 'main';
        $req->data       = ['title' => 'Hello'];

        return $req;
    }

    /**
     * Creates a pre-populated UpdateVersionRequest for use in patch tests.
     *
     * @return UpdateVersionRequest An update request with the key set.
     */
    protected function makeUpdateRequest(): UpdateVersionRequest
    {
        $req      = new UpdateVersionRequest();
        $req->key = 'draft';

        return $req;
    }

    /**
     * Returns an empty Countable suitable as the validator response when validation passes.
     *
     * @return \Countable A countable with count() === 0.
     */
    protected function makeEmptyViolations(): \Countable
    {
        return new class implements \Countable {
            public function count(): int { return 0; }
        };
    }

    /**
     * Builds a Symfony Request with JSON body and the X-Client-Type header set.
     *
     * @param  array<string, mixed> $body       Payload to JSON-encode as the request body.
     * @param  string               $clientType Value for the HTTP_X_CLIENT_TYPE server var.
     * @return Request A request instance ready for controller action calls.
     */
    protected function jsonRequest(array $body, string $clientType = 'web'): Request
    {
        return new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => $clientType],
            content: json_encode($body),
        );
    }
}
