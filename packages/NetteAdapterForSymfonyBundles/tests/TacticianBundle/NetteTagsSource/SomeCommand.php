<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\TacticianBundle\NetteTagsSource;

class SomeCommand
{
    /**
     * @var string
     */
    private $state;

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
