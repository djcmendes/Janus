<?php

declare(strict_types=1);

namespace App\Notifications\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateNotificationRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 36)]
    public string $recipientId = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $subject = '';

    #[Assert\NotBlank]
    public string $message = '';

    public ?string $senderId   = null;
    public ?string $collection = null;
    public ?string $item       = null;
}
