<?php

declare(strict_types=1);

namespace App\Policies\Application\Command;

final class DeletePolicyCommand
{
    public function __construct(public readonly string $id) {}
}
