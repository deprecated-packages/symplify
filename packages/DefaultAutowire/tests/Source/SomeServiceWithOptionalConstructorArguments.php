<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

final class SomeServiceWithOptionalConstructorArguments
{
    /**
     * @var SomeService
     */
    private $someService;

    /**
     * @var array
     */
    private $arg = [];

    public function __construct(?SomeService $someService, array $arg = [])
    {
        $this->someService = $someService;
        $this->arg = $arg;
    }
}
