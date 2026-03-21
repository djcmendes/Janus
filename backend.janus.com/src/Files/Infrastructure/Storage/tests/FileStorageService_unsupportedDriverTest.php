<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Storage\tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorageService_unsupportedDriverTest extends FileStorageServiceTestCase
{
    public function test_store_with_s3_driver_throws_runtime_exception(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'janus_');
        file_put_contents($tmpFile, 'hello');

        $uploaded = new UploadedFile($tmpFile, 'test.txt', 'text/plain', null, true);

        $this->expectException(\RuntimeException::class);
        $this->storage->store($uploaded, 's3');
    }
}
