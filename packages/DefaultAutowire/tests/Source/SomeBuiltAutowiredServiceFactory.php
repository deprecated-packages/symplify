<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SomeBuiltAutowiredServiceFactory
{
    /**
     * @param SomeService $someService
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return SomeBuiltAutowiredService
     */
    public function create(
        SomeService $someService,
        EventDispatcherInterface $eventDispatcher
    ): SomeBuiltAutowiredService {
        return new SomeBuiltAutowiredService($someService, $eventDispatcher);
    }
}
