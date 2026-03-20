<?php

declare(strict_types=1);

namespace App\Activity\Domain\Exception;

final class ActivityNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Activity "%s" not found.', $id));
    }
}
