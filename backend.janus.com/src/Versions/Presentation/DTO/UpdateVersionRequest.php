<?php

/**
 * @file UpdateVersionRequest.php
 *
 * Request DTO for the PATCH /versions/{id} endpoint carrying optional mutable fields.
 *
 * @package App\Versions\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\DTO;

/**
 * Deserialisation target for partial updates to an existing Version record.
 *
 * Fields left absent in the request body remain at the UNCHANGED sentinel
 * and are not written by UpdateVersionHandler.
 */
final class UpdateVersionRequest
{
    /** @var mixed */
    public mixed $key   = '__UNCHANGED__';

    /** @var mixed */
    public mixed $delta = '__UNCHANGED__';
}
