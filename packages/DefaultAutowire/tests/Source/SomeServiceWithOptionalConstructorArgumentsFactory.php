<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

class SomeServiceWithOptionalConstructorArgumentsFactory
{
    /**
     * @param SomeService|null $someService
     * @param array $arg
     *
     * @return SomeServiceWithOptionalConstructorArguments
     */
    public function create(
        SomeService $someService = null,
        array $arg = []
    ) : SomeServiceWithOptionalConstructorArguments {
        return new SomeServiceWithOptionalConstructorArguments($someService, $arg);
    }
}
