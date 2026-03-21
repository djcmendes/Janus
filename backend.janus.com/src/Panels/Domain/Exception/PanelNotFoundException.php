<?php

declare(strict_types=1);

namespace App\Panels\Domain\Exception;

final class PanelNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Panel '{$id}' not found.");
    }
}
