<?php

declare(strict_types=1);

namespace App\Shares\Domain\Exception;

final class ShareInvalidException extends \RuntimeException
{
    public function __construct(string $reason = 'This share link is no longer valid.')
    {
        parent::__construct($reason);
    }
}
