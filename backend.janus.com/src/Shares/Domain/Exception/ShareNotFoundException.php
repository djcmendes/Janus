<?php

declare(strict_types=1);

namespace App\Shares\Domain\Exception;

final class ShareNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Share '{$id}' not found.");
    }
}
