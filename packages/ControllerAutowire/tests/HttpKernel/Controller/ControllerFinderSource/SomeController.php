<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\HttpKernel\Controller\ControllerFinderSource;

class SomeController
{
    /**
     * @var SomeService
     */
    private $someService;

    public function __construct(SomeService $someService)
    {
        $this->someService = $someService;
    }

    public function getSomeService(): SomeService
    {
        return $this->someService;
    }
}
