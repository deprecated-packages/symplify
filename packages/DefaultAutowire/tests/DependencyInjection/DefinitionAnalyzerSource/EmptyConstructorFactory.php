<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource;

final class EmptyConstructorFactory
{
    public function create(): EmptyConstructor
    {
        return new EmptyConstructor;
    }
}
