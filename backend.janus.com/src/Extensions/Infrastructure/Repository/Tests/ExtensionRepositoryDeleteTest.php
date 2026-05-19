<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionRepository::class)]
final class ExtensionRepositoryDeleteTest extends ExtensionRepositoryTest
{
    public function testDeleteCallsRemoveAndFlush(): void
    {
        $extension = $this->makeExtension();

        $this->entityManager->expects($this->once())->method('remove');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->delete($extension);
    }
}
