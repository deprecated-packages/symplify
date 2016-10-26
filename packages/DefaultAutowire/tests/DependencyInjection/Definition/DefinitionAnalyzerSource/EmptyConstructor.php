<?php

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

final class EmptyConstructor
{
    public function __construct()
    {
        $value = 1;
    }
}
