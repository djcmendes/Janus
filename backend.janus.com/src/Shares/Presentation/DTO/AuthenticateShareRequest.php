<?php

declare(strict_types=1);

namespace App\Shares\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class AuthenticateShareRequest
{
    #[Assert\NotBlank]
    public string $token = '';

    public ?string $password = null;
}
