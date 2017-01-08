<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

final class EmptyConstructorFactory
{
    public function create() : EmptyConstructor
    {
        return new EmptyConstructor();
    }
}
