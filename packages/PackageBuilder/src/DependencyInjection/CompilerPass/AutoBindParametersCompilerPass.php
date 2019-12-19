<?php declare(strict_types=1);

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
            'Compiler pass "%s" is deprecated. Use instead "%s"',
            self::class,
            AutoBindParameterCompilerPass::class
        ));

        sleep(3);
    }
}
