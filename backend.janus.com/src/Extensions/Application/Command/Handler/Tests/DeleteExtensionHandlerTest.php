<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\DeleteExtensionHandler;
use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeleteExtensionHandler::class)]
abstract class DeleteExtensionHandlerTest extends TestCase
{
    /** @var MockObject&ExtensionRepositoryInterface */
    protected MockObject $repository;
    protected DeleteExtensionHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ExtensionRepositoryInterface::class);
        $this->handler    = new DeleteExtensionHandler($this->repository);
    }

    protected function makeExtension(): Extension
    {
        return new Extension('my-hook', ExtensionType::HOOK, '1.0.0');
    }
}
