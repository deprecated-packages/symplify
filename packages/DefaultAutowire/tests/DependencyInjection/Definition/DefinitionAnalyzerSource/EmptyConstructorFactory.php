<?php

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

final class EmptyConstructorFactory
{
    public function create() : EmptyConstructor
    {
        return new EmptyConstructor();
    }
}
