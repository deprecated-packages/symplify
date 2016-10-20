<?php

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class MissingArgumentsTypehintsFactory
{
    /**
     * @param string $valueWithoutType
     * @param SomeService|null $someService
     * @param int $value
     *
     * @return MissingArgumentsTypehints
     */
    public function create($valueWithoutType, SomeService $someService = null, $value = 1) : MissingArgumentsTypehints
    {
        return new MissingArgumentsTypehints($valueWithoutType, $someService, $value);
    }
}
