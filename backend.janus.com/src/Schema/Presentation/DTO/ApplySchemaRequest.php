<?php

/**
 * @file ApplySchemaRequest.php
 *
 * Deserialization target and validation rules for the POST /schema/apply request body.
 *
 * @package App\Schema\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Carries the schema snapshot and optional force flag for a schema apply request.
 *
 * Deserialized by symfony/serializer from the JSON request body.
 * Validated by symfony/validator before the command is dispatched.
 */
final class ApplySchemaRequest
{
    /** @var mixed Full snapshot array (version + collections + relations). Must not be null. */
    #[Assert\NotNull]
    #[Assert\Type('array')]
    public mixed $snapshot = null;

    /** @var bool When true, collections/fields/relations absent from the snapshot are deleted. */
    public bool $force = false;
}
