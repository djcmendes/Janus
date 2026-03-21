<?php

declare(strict_types=1);

namespace App\Shares\Domain\Exception;

final class ShareForbiddenException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('You do not have permission to manage this share.');
    }
}
