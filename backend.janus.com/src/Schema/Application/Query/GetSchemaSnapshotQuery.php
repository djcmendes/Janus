<?php

/**
 * @file GetSchemaSnapshotQuery.php
 *
 * Marker query that triggers a full schema snapshot export.
 *
 * @package App\Schema\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query;

/**
 * Marker query — carries no parameters because the snapshot always reflects
 * the full current state of all collections, fields, and relations.
 */
final class GetSchemaSnapshotQuery {}
