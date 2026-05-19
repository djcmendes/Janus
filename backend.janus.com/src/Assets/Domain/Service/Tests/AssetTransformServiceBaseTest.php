<?php

/**
 * @file AssetTransformServiceBaseTest.php
 *
 * Constructor and interface compliance tests for AssetTransformService.
 *
 * @package App\Assets\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Domain\Service\Tests;

use App\Assets\Domain\Service\AssetTransformService;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: AssetTransformService::class)]
final class AssetTransformServiceBaseTest extends AssetTransformServiceTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of AssetTransformService.
     */
    public function testIsInstanceOfAssetTransformService(): void
    {
        $this->assertInstanceOf(AssetTransformService::class, $this->class);
    }

    /**
     * Test that AssetTransformService can be instantiated with no constructor arguments.
     */
    public function testIsInstantiableWithNoArguments(): void
    {
        $this->assertNotNull(new AssetTransformService());
    }
}
