<?php

declare(strict_types=1);

namespace App\Translations\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateTranslationRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 16)]
    public string $language = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $key = '';

    #[Assert\NotBlank]
    public string $value = '';
}
