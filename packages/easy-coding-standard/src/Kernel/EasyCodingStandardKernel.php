<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Kernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\ValueObject\CodingStandardConfig;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\ValueObject\EasyCodingStandardConfig;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use Symplify\Skipper\ValueObject\SkipperConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyCodingStandardKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';

        $compilerPasses = $this->createCompilerPasses();

        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = SkipperConfig::FILE_PATH;
        $configFiles[] = CodingStandardConfig::FILE_PATH;
        $configFiles[] = EasyCodingStandardConfig::FILE_PATH;

        return $this->create([], $compilerPasses, $configFiles);
    }

    /**
     * @return CompilerPassInterface[]
     */
    private function createCompilerPasses(): array
    {
        $compilerPasses = [];

        // cleanup
        $compilerPasses[] = new RemoveExcludedCheckersCompilerPass();
        $compilerPasses[] = new RemoveMutualCheckersCompilerPass();
        $compilerPasses[] = new ConflictingCheckersCompilerPass();

        // autowire
        $compilerPasses[] = new AutowireInterfacesCompilerPass([
            FixerInterface::class,
            Sniff::class,
            OutputFormatterInterface::class,
        ]);

        $compilerPasses[] = new FixerWhitespaceConfigCompilerPass();
        $compilerPasses[] = new AutowireArrayParameterCompilerPass();

        return $compilerPasses;
    }
}
