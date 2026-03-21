<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Storage\tests;

class FileStorageService_deleteTest extends FileStorageServiceTestCase
{
    public function test_delete_removes_existing_file(): void
    {
        $filename = 'testfile.txt';
        file_put_contents($this->tempDir . '/' . $filename, 'content');

        $this->storage->delete($filename);

        $this->assertFileDoesNotExist($this->tempDir . '/' . $filename);
    }

    public function test_delete_non_existent_file_does_not_throw(): void
    {
        $this->expectNotToPerformAssertions();
        $this->storage->delete('non-existent-file.txt');
    }
}
