<?php

declare(strict_types=1);

namespace App\Files\Domain\Exception;

final class FolderNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Folder "%s" not found.', $id));
    }
}
