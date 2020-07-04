<?php

declare(strict_types=1);

namespace Symplify\ParameterNameGuard\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ParameterNameGuard\Guard\ParameterNameGuard;

final class ParameterNameGuardCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const CORRECT_TO_TYPOS = 'correct_to_typos';

    public function process(ContainerBuilder $containerBuilder): void
    {
        if (! $containerBuilder->hasParameter(self::CORRECT_TO_TYPOS)) {
            return;
        }

        $correctToTypos = $containerBuilder->getParameter(self::CORRECT_TO_TYPOS);

        $parameterNameGuard = new ParameterNameGuard($correctToTypos, $containerBuilder->getParameterBag());
        $parameterNameGuard->process();
    }
}
