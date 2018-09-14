<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function Safe\getcwd;

final class DetectParametersCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const OPTION_ROOT_DIRECTORY = 'root_directory';

    public function process(ContainerBuilder $containerBuilder): void
    {
        if (! $containerBuilder->hasParameter(self::OPTION_ROOT_DIRECTORY)) {
            $containerBuilder->setParameter(self::OPTION_ROOT_DIRECTORY, getcwd());
        }
    }
}
