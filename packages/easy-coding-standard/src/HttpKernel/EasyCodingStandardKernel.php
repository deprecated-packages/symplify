<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\HttpKernel;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\DependencyInjection\Extension\SymplifyCodingStandardExtension;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffConfig;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\EasyCodingStandardExtension;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use Symplify\Skipper\ValueObject\SkipperConfig;
use Symplify\SymfonyContainerBuilder\ContainerBuilderFactory;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;

final class EasyCodingStandardKernel implements LightKernelInterface
{
    private ContainerInterface|null $container = null;

    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';

        $compilerPasses = $this->createCompilerPasses();
        $extensions = $this->createExtensions();
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $configFiles[] = SkipperConfig::FILE_PATH;

        $containerBuilderFactory = new ContainerBuilderFactory();

        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();

        $this->container = $containerBuilder;

        return $containerBuilder;
    }

    public function getContainer(): ContainerInterface
    {
        if (! $this->container instanceof ContainerInterface) {
            throw new ShouldNotHappenException();
        }

        return $this->container;
    }

    /**
     * @return ExtensionInterface[]
     */
    private function createExtensions(): array
    {
        $extensions = [];

        $extensions[] = new SymplifyKernelExtension();
        $extensions[] = new EasyCodingStandardExtension();
        $extensions[] = new SymplifyCodingStandardExtension();

        return $extensions;
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
