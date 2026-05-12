<?php

/**
 * @file SaveVersionRequest.php
 *
 * Request DTO for the POST /versions endpoint carrying validated input fields.
 *
 * @package App\Versions\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deserialisation target and validation carrier for creating a new Version.
 */
final class SaveVersionRequest
{
    /** @var string */
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $collection = '';

    /** @var string */
    #[Assert\NotBlank]
    #[Assert\Length(max: 36)]
    public string $item = '';

    /** @var string */
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $key = 'main';

    /** @var mixed */
    #[Assert\NotNull]
    #[Assert\Type('array')]
    public mixed $data = null;

    /** @var mixed */
    public mixed $delta = null;
}
