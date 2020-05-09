<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;

/**
 * Bind parameters by default:
 * - from "%value_name%"
 * - to "$valueName"
 */
final class AutoBindParametersCompilerPass extends AutoBindParameterCompilerPass
{
    public function __construct()
    {
        trigger_error(sprintf(
            'Compiler pass "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" instead',
            self::class,
            AutoBindParameterCompilerPass::class
        ));

        sleep(3);
    }
}
