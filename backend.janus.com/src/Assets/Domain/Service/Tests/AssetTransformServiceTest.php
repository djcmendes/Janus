<?php

/**
 * @file AssetTransformServiceTest.php
 *
 * Abstract base providing setUp / tearDown, temp image fixtures, and the
 * real SUT instance for all AssetTransformService test cases.
 *
 * Strategy: AssetTransformService is `final` with no constructor dependencies
 * and operates directly on PHP GD resources and the local filesystem.
 * Tests use real in-memory GD images written to a temporary directory — no
 * mocking or stubbing is required anywhere in this suite.
 *
 * @package App\Assets\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Domain\Service\Tests;

use App\Assets\Domain\Service\AssetTransformService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and image fixtures for all AssetTransformService
 * test suites.
 */
#[CoversClass(className: AssetTransformService::class)]
abstract class AssetTransformServiceTest extends TestCase
{
    /**
     * The system under test — real AssetTransformService with no injected deps.
     * @var AssetTransformService
     */
    protected AssetTransformService $class;

    /**
     * Reflection of AssetTransformService for reading private properties.
     * @var ReflectionClass<AssetTransformService>
     */
    protected ReflectionClass $reflection;

    /**
     * Temporary directory holding all GD-generated test images.
     * @var string
     */
    protected string $tempDir;

    /**
     * Absolute path to a 16×9 JPEG created in setUp().
     * @var string
     */
    protected string $tempJpegPath;

    /**
     * Absolute path to a 16×9 PNG created in setUp().
     * @var string
     */
    protected string $tempPngPath;

    /**
     * Absolute path to a file containing arbitrary bytes (no valid image header).
     * @var string
     */
    protected string $corruptFilePath;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/janus_test_transform_' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);

        $img = imagecreatetruecolor(16, 9);
        $red = imagecolorallocate($img, 255, 0, 0);
        imagefilledrectangle($img, 0, 0, 15, 8, $red);

        $this->tempJpegPath = $this->tempDir . '/test.jpg';
        imagejpeg($img, $this->tempJpegPath, 85);

        $this->tempPngPath = $this->tempDir . '/test.png';
        imagepng($img, $this->tempPngPath);

        imagedestroy($img);

        $this->corruptFilePath = $this->tempDir . '/corrupt.jpg';
        file_put_contents($this->corruptFilePath, 'not an image');

        $this->class      = new AssetTransformService();
        $this->reflection = new ReflectionClass(AssetTransformService::class);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob($this->tempDir . '/*') ?: []);
            rmdir($this->tempDir);
        }

        unset($this->class, $this->reflection);
    }
}
