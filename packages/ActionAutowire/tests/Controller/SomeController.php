<?php

declare(strict_types=1);

namespace Symplify\ActionAutowire\Tests\Controller;

use Symplify\ActionAutowire\Tests\CompleteSource\SomeService;

final class SomeController
{
    public function someServiceAwareAction(SomeService $someService)
    {
    }
}
