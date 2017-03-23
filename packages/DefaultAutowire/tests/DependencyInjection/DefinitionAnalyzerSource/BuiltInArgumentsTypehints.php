<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class BuiltInArgumentsTypehints
{
    /**
     * @var SomeService
     */
    private $someService;

    /**
     * @var int
     */
    private $int;

    public function __construct(SomeService $someService, int $int)
    {
        $this->someService = $someService;
        $this->int = $int;
    }

    public function getSomeService(): SomeService
    {
        return $this->someService;
    }

    public function getInt(): int
    {
        return $this->int;
    }
}
