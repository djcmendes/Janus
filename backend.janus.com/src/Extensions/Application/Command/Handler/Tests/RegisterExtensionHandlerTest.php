<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\RegisterExtensionHandler;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: RegisterExtensionHandler::class)]
abstract class RegisterExtensionHandlerTest extends TestCase
{
    /** @var MockObject&ExtensionRepositoryInterface */
    protected MockObject $repository;
    protected RegisterExtensionHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ExtensionRepositoryInterface::class);
        $this->handler    = new RegisterExtensionHandler($this->repository);
    }
}
