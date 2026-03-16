<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Enum;

enum ApiVersion: string
{
    case V1 = 'v1';
    case V2 = 'v2';

    /**
     * Checks if this version is less than a given version.
     */
    public function isLessThan(self $other): bool
    {
        $order = array_column(self::cases(), 'value');
        return array_search($this->value, $order, true) < array_search($other->value, $order, true);
    }
}
