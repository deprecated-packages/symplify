<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceFinderInterface;
use Symplify\EasyCodingStandard\Contract\Finder\ExtraFilesProviderInterface;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionCollector;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionFinder;

final class CustomDefinitionsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $sourceFinderDefinition = DefinitionFinder::getByType($containerBuilder, SourceFinder::class);

        dump($sourceFinderDefinition);

        $customFinder = DefinitionFinder::getByType($containerBuilder, CustomSourceFinderInterface::class);

        dump($customFinder);

        die;
    }
}
