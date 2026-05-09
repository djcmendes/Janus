<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller\Tests;

use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\Handler\CreateFieldHandler;
use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use App\Fields\Application\Command\Handler\UpdateFieldHandler;
use App\Fields\Application\Query\Handler\GetFieldByCollectionAndNameHandler;
use App\Fields\Application\Query\Handler\GetFieldsByCollectionHandler;
use App\Fields\Application\Query\Handler\GetFieldsHandler;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Fields\Presentation\Controller\FieldsController;
use App\Heimdall\Domain\Service\RequestGuard;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[CoversClass(FieldsController::class)]
abstract class FieldsControllerTest extends TestCase
{
    public const string AUTH_USER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $repository;

    /** @var MockObject&CollectionMetaRepositoryInterface */
    protected MockObject $collectionRepository;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&AuthorizationCheckerInterface */
    protected MockObject $authorizationChecker;

    protected SchemaManagerService $schemaManager;
    protected RequestGuard $guard;
    protected GetFieldsHandler $getFieldsHandler;
    protected GetFieldsByCollectionHandler $getFieldsByCollectionHandler;
    protected GetFieldByCollectionAndNameHandler $getFieldByCollectionAndNameHandler;
    protected CreateFieldHandler $createFieldHandler;
    protected UpdateFieldHandler $updateFieldHandler;
    protected DeleteFieldHandler $deleteFieldHandler;
    protected FieldsController $class;

    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getId(): string { return FieldsControllerTest::AUTH_USER_UUID; }
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

        $this->repository           = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->collectionRepository = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->connection           = $this->createMock(Connection::class);
        $this->schemaManager        = new SchemaManagerService($this->connection);

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

        $this->guard = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);

        $this->getFieldsHandler                   = new GetFieldsHandler(repository: $this->repository);
        $this->getFieldsByCollectionHandler       = new GetFieldsByCollectionHandler(repository: $this->repository);
        $this->getFieldByCollectionAndNameHandler = new GetFieldByCollectionAndNameHandler(repository: $this->repository);
        $this->createFieldHandler                 = new CreateFieldHandler(
            fieldRepository:      $this->repository,
            collectionRepository: $this->collectionRepository,
            schemaManager:        $this->schemaManager,
        );
        $this->updateFieldHandler = new UpdateFieldHandler(repository: $this->repository);
        $this->deleteFieldHandler = new DeleteFieldHandler(
            repository:    $this->repository,
            schemaManager: $this->schemaManager,
        );

        $this->class = $this->buildController();
        $this->class->setContainer(container: $this->container);
    }

    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->repository,
            $this->collectionRepository,
            $this->connection,
            $this->schemaManager,
            $this->container,
            $this->authorizationChecker,
            $this->guard,
            $this->getFieldsHandler,
            $this->getFieldsByCollectionHandler,
            $this->getFieldByCollectionAndNameHandler,
            $this->createFieldHandler,
            $this->updateFieldHandler,
            $this->deleteFieldHandler,
            $this->class,
        );
    }

    protected function buildController(bool $authenticated = true, bool $admin = false): FieldsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        if ($authenticated) {
            $user = new class implements UserInterface {
                public function getRoles(): array { return ['ROLE_USER']; }
                public function eraseCredentials(): void {}
                public function getUserIdentifier(): string { return 'user@example.com'; }
            };
            $token = $this->createMock(TokenInterface::class);
            $token->method('getUser')->willReturn($user);
            $tokenStorage->method('getToken')->willReturn($token);
        } else {
            $tokenStorage->method('getToken')->willReturn(null);
        }

        $guard = new RequestGuard(tokenStorage: $tokenStorage, requestStack: $this->requestStack);

        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn($admin);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $container->method('get')->willReturnMap([
            ['security.authorization_checker', $authChecker],
        ]);

        $controller = new FieldsController(
            guard:                              $guard,
            getFieldsHandler:                  $this->getFieldsHandler,
            getFieldsByCollectionHandler:      $this->getFieldsByCollectionHandler,
            getFieldByCollectionAndNameHandler: $this->getFieldByCollectionAndNameHandler,
            createFieldHandler:                $this->createFieldHandler,
            updateFieldHandler:                $this->updateFieldHandler,
            deleteFieldHandler:                $this->deleteFieldHandler,
        );
        $controller->setContainer(container: $container);

        return $controller;
    }

    protected function buildUnauthenticatedController(): FieldsController
    {
        return $this->buildController(authenticated: false);
    }

    protected function buildAdminController(): FieldsController
    {
        return $this->buildController(authenticated: true, admin: true);
    }

    protected function makeFieldMeta(): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         'bbbbbbbb-0000-7000-8000-000000000001',
            collection: 'articles',
            field:      'title',
            type:       FieldType::STRING,
            label:      null,
            note:       null,
            required:   false,
            readonly:   false,
            hidden:     false,
            sortOrder:  0,
            interface:  null,
            options:    null,
            createdAt:  new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:  null,
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
