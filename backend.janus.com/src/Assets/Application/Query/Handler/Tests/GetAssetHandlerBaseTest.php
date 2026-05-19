<?php

/**
 * @file GetAssetHandlerBaseTest.php
 *
 * Constructor and dependency-wiring tests for GetAssetHandler.
 *
 * @package App\Assets\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query\Handler\Tests;

use App\Assets\Application\Query\Handler\GetAssetHandler;
use App\Assets\Domain\Service\AssetTransformService;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

#[CoversClass(className: GetAssetHandler::class)]
final class GetAssetHandlerBaseTest extends GetAssetHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of GetAssetHandler.
     */
    public function testIsInstanceOfGetAssetHandler(): void
    {
        $this->assertInstanceOf(GetAssetHandler::class, $this->class);
    }

    /**
     * Test that the fileRepository property holds the injected mock repository.
     */
    public function testFileRepositoryIsWiredCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'fileRepository'))->getValue($this->class);

        $this->assertInstanceOf(FileRepositoryInterface::class, $value);
        $this->assertSame($this->fileRepository, $value);
    }

    /**
     * Test that the storage property holds the injected FileStorageService instance.
     */
    public function testStorageIsWiredCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'storage'))->getValue($this->class);

        $this->assertInstanceOf(FileStorageService::class, $value);
        $this->assertSame($this->storage, $value);
    }

    /**
     * Test that the transformer property holds the injected AssetTransformService instance.
     */
    public function testTransformerIsWiredCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'transformer'))->getValue($this->class);

        $this->assertInstanceOf(AssetTransformService::class, $value);
        $this->assertSame($this->transformer, $value);
    }
}
