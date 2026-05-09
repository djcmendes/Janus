<?php

/**
 * @file ExtensionType.php
 *
 * Backed enum representing the integration kind of a registered extension.
 *
 * @package App\Extensions\Domain\Enum
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Domain\Enum;

/**
 * Identifies the type of a registered Janus extension.
 *
 * Note: the PHP keyword `interface` is avoided by using the `INTERFACE_` case name.
 */
enum ExtensionType: string
{
    /** Custom interface component extending the Admin UI. */
    case INTERFACE_  = 'interface';

    /** Custom API endpoint registered within the Janus router. */
    case ENDPOINT    = 'endpoint';

    /** Event hook — reacts to lifecycle events (create, update, delete). */
    case HOOK        = 'hook';

    /** Custom operation node usable inside Flows. */
    case OPERATION   = 'operation';

    /** Custom display component for rendering field values. */
    case DISPLAY     = 'display';

    /** Custom layout component for the content browser. */
    case LAYOUT      = 'layout';

    /** Custom top-level module added to the Admin navigation. */
    case MODULE      = 'module';

    /** Custom panel component for dashboards. */
    case PANEL       = 'panel';
}
