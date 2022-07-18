<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Source\ReturnMethodStatic;

final class SkipStaticReturn
{
    public function run(ReturnMethodStatic $returnMethodStatic)
    {
        $returnMethodStatic->getStatic();
    }

    /**
     * @param CompilerPassInterface[] $compilerPasses
     */
    public function withContainerBuilder(ContainerBuilder $containerBuilder, array $compilerPasses): void
    {
        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->loadFromExtension('...', []);
        }
    }
}
