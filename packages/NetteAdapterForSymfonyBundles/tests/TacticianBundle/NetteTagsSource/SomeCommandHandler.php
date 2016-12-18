<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\TacticianBundle\NetteTagsSource;

class SomeCommandHandler
{
    public function handle(SomeCommand $someCommand)
    {
        $someCommand->setState('changedState');
    }
}
