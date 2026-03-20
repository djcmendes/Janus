<?php

declare(strict_types=1);

namespace App\Comments\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateCommentRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 65535)]
    public string $comment = '';
}
