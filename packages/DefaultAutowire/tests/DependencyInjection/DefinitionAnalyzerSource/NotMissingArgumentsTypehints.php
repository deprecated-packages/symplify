<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource;

use Symplify\DefaultAutowire\Tests\Source\SomeService;

final class NotMissingArgumentsTypehints
{
    /**
     * @var SomeService
     */
    private $someService;

    /**
     * @var SomeService
     */
    private $anotherService;

    public function __construct(SomeService $someService, SomeService $anotherService)
    {
        $this->someService = $someService;
        $this->anotherService = $anotherService;
    }

    public function getSomeService(): SomeService
    {
        return $this->someService;
    }

    public function getAnotherService(): SomeService
    {
        return $this->anotherService;
    }
}
