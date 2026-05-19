<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\Controller\Tests;

use App\Extensions\Application\Command\Handler\DeleteExtensionHandler;
use App\Extensions\Application\Command\Handler\RegisterExtensionHandler;
use App\Extensions\Application\Command\Handler\UpdateExtensionHandler;
use App\Extensions\Application\Query\Handler\GetExtensionByIdHandler;
use App\Extensions\Application\Query\Handler\GetExtensionsHandler;
use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;
use App\Extensions\Presentation\Controller\ExtensionsController;
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

#[CoversClass(className: ExtensionsController::class)]
abstract class ExtensionsControllerTest extends TestCase
{
    public const string AUTH_USER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&ExtensionRepositoryInterface */
    protected MockObject $repository;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&AuthorizationCheckerInterface */
    protected MockObject $authorizationChecker;

    protected RequestGuard $guard;
    protected GetExtensionsHandler $getExtensionsHandler;
    protected GetExtensionByIdHandler $getExtensionByIdHandler;
    protected RegisterExtensionHandler $registerExtensionHandler;
    protected UpdateExtensionHandler $updateExtensionHandler;
    protected DeleteExtensionHandler $deleteExtensionHandler;
    protected ExtensionsController $class;

    /** @var ReflectionClass<ExtensionsController> */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getId(): string { return ExtensionsControllerTest::AUTH_USER_UUID; }
            public function getRoles(): array { return ['ROLE_USER']; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'user@example.com'; }
        };

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->method('getToken')->willReturn($token);

        $webRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($webRequest);

        $this->repository = $this->createMock(ExtensionRepositoryInterface::class);

        // Default: not admin (list/get are accessible to all authenticated users).
        // Admin-only tests build a controller with admin granted.
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

        $this->guard                    = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);
        $this->getExtensionsHandler     = new GetExtensionsHandler(repository: $this->repository);
        $this->getExtensionByIdHandler  = new GetExtensionByIdHandler(repository: $this->repository);
        $this->registerExtensionHandler = new RegisterExtensionHandler(repository: $this->repository);
        $this->updateExtensionHandler   = new UpdateExtensionHandler(repository: $this->repository);
        $this->deleteExtensionHandler   = new DeleteExtensionHandler(repository: $this->repository);

        $this->class = new ExtensionsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(ExtensionsController::class);
    }

    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->repository,
            $this->container,
            $this->authorizationChecker,
            $this->guard,
            $this->getExtensionsHandler,
            $this->getExtensionByIdHandler,
            $this->registerExtensionHandler,
            $this->updateExtensionHandler,
            $this->deleteExtensionHandler,
            $this->class,
            $this->reflection,
        );
    }

    protected function buildControllerWithUnauthenticatedGuard(): ExtensionsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(tokenStorage: $tokenStorage, requestStack: $this->requestStack);

        $controller = new ExtensionsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    protected function buildControllerWithAdminGuard(): ExtensionsController
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

        $controller = new ExtensionsController(guard: $this->guard);
        $controller->setContainer(container: $container);

        return $controller;
    }

    protected function makeExtension(): Extension
    {
        return Extension::reconstitute(
            id:          'bbbbbbbb-0000-7000-8000-000000000001',
            name:        'my-hook',
            type:        ExtensionType::HOOK,
            version:     '1.0.0',
            enabled:     true,
            description: null,
            meta:        null,
            createdAt:   new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:   new \DateTimeImmutable('2024-01-01T00:00:00Z'),
        );
    }

    protected function jsonRequest(array $body, string $clientType = 'web'): Request
    {
        return new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => $clientType],
            content: json_encode($body),
        );
    }
}
