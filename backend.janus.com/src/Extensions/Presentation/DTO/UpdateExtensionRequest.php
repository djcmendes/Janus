<?php

/**
 * @file UpdateExtensionRequest.php
 *
 * Presentation-layer DTO for deserialising the PATCH /extensions/{id} body.
 *
 * @package App\Extensions\Presentation\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Presentation\DTO;

/**
 * Carries partial-update input for an existing extension.
 *
 * Fields left as the UNCHANGED sentinel are skipped by UpdateExtensionHandler.
 */
final class UpdateExtensionRequest
{
    /** @var bool|string New enabled state, or UNCHANGED to skip. */
    public bool|string  $enabled = '__UNCHANGED__';

    /** @var string|null New version string, or UNCHANGED to skip. */
    public string|null  $version = '__UNCHANGED__';

    /** @var mixed New meta array, null to clear, or UNCHANGED to skip. */
    public mixed        $meta    = '__UNCHANGED__';
}
