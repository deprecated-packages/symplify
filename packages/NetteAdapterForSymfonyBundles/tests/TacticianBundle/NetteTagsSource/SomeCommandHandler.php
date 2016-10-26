<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\TacticianBundle\NetteTagsSource;

class SomeCommandHandler
{
    public function handle(SomeCommand $someCommand)
    {
        $someCommand->setState('changedState');
    }
}
