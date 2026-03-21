<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Storage\tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorageService_storeTest extends FileStorageServiceTestCase
{
    public function test_store_returns_string_filename(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'janus_');
        file_put_contents($tmpFile, 'hello');

        $uploaded = new UploadedFile($tmpFile, 'test.txt', 'text/plain', null, true);
        $result   = $this->storage->store($uploaded);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_store_creates_file_on_disk(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'janus_');
        file_put_contents($tmpFile, 'hello');

        $uploaded = new UploadedFile($tmpFile, 'test.txt', 'text/plain', null, true);
        $stored   = $this->storage->store($uploaded);

        $this->assertFileExists($this->tempDir . '/' . $stored);
    }
}
