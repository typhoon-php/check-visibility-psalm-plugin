<?php

declare(strict_types=1);

namespace Typhoon\CheckVisibilityPsalmPlugin;

use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;

/**
 * @api
 */
final class UnspecifiedVisibility extends PluginIssue
{
    public function __construct(string $name, CodeLocation $code_location)
    {
        parent::__construct(\sprintf('%s must be either @api or @internal', $name), $code_location);
    }
}
