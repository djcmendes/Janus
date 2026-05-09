<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\UpdateExtensionHandler;
use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(UpdateExtensionHandler::class)]
abstract class UpdateExtensionHandlerTest extends TestCase
{
    /** @var MockObject&ExtensionRepositoryInterface */
    protected MockObject $repository;
    protected UpdateExtensionHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ExtensionRepositoryInterface::class);
        $this->handler    = new UpdateExtensionHandler($this->repository);
    }

    protected function makeExtension(): Extension
    {
        return Extension::reconstitute(
            id:          'aaaaaaaa-0000-7000-8000-000000000001',
            name:        'my-hook',
            type:        ExtensionType::HOOK,
            version:     '1.0.0',
            enabled:     false,
            description: null,
            meta:        null,
            createdAt:   new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:   new \DateTimeImmutable('2024-01-01T00:00:00Z'),
        );
    }
}
