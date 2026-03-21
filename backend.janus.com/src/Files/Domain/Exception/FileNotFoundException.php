<?php

declare(strict_types=1);

namespace App\Files\Domain\Exception;

final class FileNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('File "%s" not found.', $id));
    }
}
