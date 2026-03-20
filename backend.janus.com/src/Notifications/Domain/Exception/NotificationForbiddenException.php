<?php

declare(strict_types=1);

namespace App\Notifications\Domain\Exception;

final class NotificationForbiddenException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('You do not have permission to access this notification.');
    }
}
