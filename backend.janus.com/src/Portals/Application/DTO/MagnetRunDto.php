<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;

use App\Portals\Domain\Entity\MagnetRun;

final class MagnetRunDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $magnetId,
        public readonly string  $startedAt,
        public readonly ?string $finishedAt,
        public readonly int     $itemsImported,
        public readonly array   $errors,
    ) {}

    public static function fromEntity(MagnetRun $run): self
    {
        return new self(
            id:            $run->getId(),
            magnetId:      $run->getMagnetId(),
            startedAt:     $run->getStartedAt()->format(\DateTimeInterface::ATOM),
            finishedAt:    $run->getFinishedAt()?->format(\DateTimeInterface::ATOM),
            itemsImported: $run->getItemsImported(),
            errors:        $run->getErrors(),
        );
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'magnet_id'      => $this->magnetId,
            'started_at'     => $this->startedAt,
            'finished_at'    => $this->finishedAt,
            'items_imported' => $this->itemsImported,
            'errors'         => $this->errors,
        ];
    }
}
