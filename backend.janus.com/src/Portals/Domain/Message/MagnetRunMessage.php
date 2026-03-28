<?php
declare(strict_types=1);
namespace App\Portals\Domain\Message;

final class MagnetRunMessage
{
    public function __construct(
        public readonly string $magnetId,
        public readonly string $magnetRunId,
    ) {}
}
