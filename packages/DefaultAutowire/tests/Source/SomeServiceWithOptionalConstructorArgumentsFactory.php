<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

final class SomeServiceWithOptionalConstructorArgumentsFactory
{
    public function create(?SomeService $someService, array $arg = []): SomeServiceWithOptionalConstructorArguments
    {
        return new SomeServiceWithOptionalConstructorArguments($someService, $arg);
    }
}
