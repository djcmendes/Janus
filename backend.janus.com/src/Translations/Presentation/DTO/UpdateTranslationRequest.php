<?php

declare(strict_types=1);

namespace App\Translations\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateTranslationRequest
{
    #[Assert\NotBlank]
    public string $value = '';
}
