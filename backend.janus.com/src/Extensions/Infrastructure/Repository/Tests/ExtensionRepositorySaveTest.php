<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionRepository::class)]
final class ExtensionRepositorySaveTest extends ExtensionRepositoryTest
{
    public function testSaveCallsPersistAndFlush(): void
    {
        $extension = $this->makeExtension();

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($extension);
    }
}
