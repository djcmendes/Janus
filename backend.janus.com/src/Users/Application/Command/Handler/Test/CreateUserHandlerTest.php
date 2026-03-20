<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler\Test;

use App\Users\Application\Command\CreateUserCommand;
use App\Users\Application\Command\Handler\CreateUserHandler;
use App\Users\Application\DTO\UserDto;
use App\Users\Domain\Exception\UserAlreadyExistsException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserHandlerTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private UserPasswordHasherInterface $hasher;
    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->hasher     = $this->createMock(UserPasswordHasherInterface::class);
        $this->handler    = new CreateUserHandler($this->repository, $this->hasher);
    }

    public function testHandleCreatesUserAndReturnsDto(): void
    {
        $command = new CreateUserCommand(
            email:     'john@example.com',
            password:  'plaintext',
            firstName: 'John',
            lastName:  'Doe',
        );

        $this->repository->method('findByEmail')->willReturn(null);
        $this->hasher->method('hashPassword')->willReturn('hashed_password');
        $this->repository->expects($this->once())->method('save');

        $dto = $this->handler->handle($command);

        $this->assertInstanceOf(UserDto::class, $dto);
        $this->assertSame('john@example.com', $dto->email);
        $this->assertSame('John', $dto->firstName);
        $this->assertSame('Doe', $dto->lastName);
        $this->assertSame('active', $dto->status);
        $this->assertContains('ROLE_USER', $dto->roles);
    }

    public function testHandleThrowsWhenEmailAlreadyExists(): void
    {
        $command = new CreateUserCommand(email: 'existing@example.com', password: 'pass');

        $existingUser = new \App\Users\Domain\Entity\User('existing@example.com');
        $this->repository->method('findByEmail')->willReturn($existingUser);

        $this->repository->expects($this->never())->method('save');

        $this->expectException(UserAlreadyExistsException::class);
        $this->handler->handle($command);
    }

    public function testHandleHashesPassword(): void
    {
        $command = new CreateUserCommand(email: 'user@example.com', password: 'my_plain_pass');

        $this->repository->method('findByEmail')->willReturn(null);

        $this->hasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn('$2y$hashed');

        $this->repository->method('save');

        $this->handler->handle($command);
    }

    public function testHandleAppliesRoles(): void
    {
        $command = new CreateUserCommand(
            email:   'admin@example.com',
            password: 'pass',
            roles:   ['ROLE_ADMIN'],
        );

        $this->repository->method('findByEmail')->willReturn(null);
        $this->hasher->method('hashPassword')->willReturn('hashed');
        $this->repository->method('save');

        $dto = $this->handler->handle($command);

        $this->assertContains('ROLE_ADMIN', $dto->roles);
    }

    public function testHandleWorksWithMinimalFields(): void
    {
        $command = new CreateUserCommand(email: 'min@example.com', password: 'pass');

        $this->repository->method('findByEmail')->willReturn(null);
        $this->hasher->method('hashPassword')->willReturn('hashed');
        $this->repository->method('save');

        $dto = $this->handler->handle($command);

        $this->assertNull($dto->firstName);
        $this->assertNull($dto->lastName);
    }
}
