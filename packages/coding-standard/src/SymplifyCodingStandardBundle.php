<?php

declare(strict_types=1);

namespace Symplify\CodingStandard;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\CodingStandard\DependencyInjection\Extension\SymplifyCodingStandardExtension;

final class SymplifyCodingStandardBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new SymplifyCodingStandardExtension();
    }
}
