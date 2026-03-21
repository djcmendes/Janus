<?php

declare(strict_types=1);

namespace App\Users\Application\Command\Handler\Test;

use App\Users\Application\Command\Handler\InviteUserHandler;
use App\Users\Application\Command\InviteUserCommand;
use App\Users\Application\DTO\UserDto;
use App\Users\Domain\Exception\UserAlreadyExistsException;
use App\Users\Domain\Message\InviteEmailMessage;
use App\Users\Domain\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class InviteUserHandlerTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private MessageBusInterface     $bus;
    private InviteUserHandler       $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->bus        = $this->createMock(MessageBusInterface::class);
        $this->handler    = new InviteUserHandler(
            $this->repository,
            $this->bus,
            'http://janus.test',
        );
    }

    public function testHandleCreatesInvitedUserAndReturnsDto(): void
    {
        $command = new InviteUserCommand(email: 'invitee@example.com');

        $this->repository->method('findByEmail')->willReturn(null);
        $this->repository->expects($this->once())->method('save');
        $this->bus->method('dispatch')->willReturn(new Envelope(new \stdClass()));

        $dto = $this->handler->handle($command);

        $this->assertInstanceOf(UserDto::class, $dto);
        $this->assertSame('invitee@example.com', $dto->email);
        $this->assertSame('invited', $dto->status);
        $this->assertNotEmpty($dto->inviteToken);
    }

    public function testHandleThrowsWhenEmailAlreadyExists(): void
    {
        $command = new InviteUserCommand(email: 'existing@example.com');

        $existingUser = new \App\Users\Domain\Entity\User('existing@example.com');
        $this->repository->method('findByEmail')->willReturn($existingUser);
        $this->repository->expects($this->never())->method('save');

        $this->expectException(UserAlreadyExistsException::class);
        $this->handler->handle($command);
    }

    public function testHandleDispatchesInviteEmailMessage(): void
    {
        $command = new InviteUserCommand(email: 'new@example.com');

        $this->repository->method('findByEmail')->willReturn(null);
        $this->repository->method('save');

        $this->bus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function (mixed $msg): bool {
                return $msg instanceof InviteEmailMessage
                    && $msg->recipientEmail === 'new@example.com'
                    && $msg->appBaseUrl === 'http://janus.test';
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $this->handler->handle($command);
    }

    public function testHandleSetsOptionalFields(): void
    {
        $command = new InviteUserCommand(
            email:     'full@example.com',
            firstName: 'Jane',
            lastName:  'Doe',
            roles:     ['ROLE_ADMIN'],
        );

        $this->repository->method('findByEmail')->willReturn(null);
        $this->repository->method('save');
        $this->bus->method('dispatch')->willReturn(new Envelope(new \stdClass()));

        $dto = $this->handler->handle($command);

        $this->assertSame('Jane', $dto->firstName);
        $this->assertSame('Doe', $dto->lastName);
        $this->assertContains('ROLE_ADMIN', $dto->roles);
    }
}
