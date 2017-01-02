<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource;

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
}
