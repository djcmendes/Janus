<?php

declare(strict_types=1);

namespace App\Notifications\Application\DTO;

use App\Notifications\Domain\Entity\Notification;

final class NotificationDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $recipientId,
        public readonly string  $subject,
        public readonly string  $message,
        public readonly ?string $senderId,
        public readonly ?string $collection,
        public readonly ?string $item,
        public readonly bool    $read,
        public readonly string  $timestamp,
    ) {}

    public static function fromEntity(Notification $notification): self
    {
        return new self(
            id:          $notification->getId(),
            recipientId: $notification->getRecipientId(),
            subject:     $notification->getSubject(),
            message:     $notification->getMessage(),
            senderId:    $notification->getSenderId(),
            collection:  $notification->getCollection(),
            item:        $notification->getItem(),
            read:        $notification->isRead(),
            timestamp:   $notification->getTimestamp()->format(\DateTimeInterface::ATOM),
        );
    }
}
