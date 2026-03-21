<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Storage\tests;

use App\Files\Infrastructure\Storage\FileStorageService;
use PHPUnit\Framework\TestCase;

abstract class FileStorageServiceTestCase extends TestCase
{
    protected FileStorageService $storage;
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/janus_test_storage_' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);
        $this->storage = new FileStorageService($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob($this->tempDir . '/*') ?: []);
            rmdir($this->tempDir);
        }
        unset($this->storage);
    }
}
