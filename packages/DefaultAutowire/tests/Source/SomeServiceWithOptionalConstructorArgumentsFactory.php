<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

final class SomeServiceWithOptionalConstructorArgumentsFactory
{
    /**
     * @param mixed[] $arg
     */
    public function create(?SomeService $someService, array $arg = []): SomeServiceWithOptionalConstructorArguments
    {
        return new SomeServiceWithOptionalConstructorArguments($someService, $arg);
    }
}
