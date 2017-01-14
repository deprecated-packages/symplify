<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony\Event;

use Symfony\Component\EventDispatcher\Event;

final class SomeEvent extends Event
{
    /**
     * @var string
     */
    const NAME = 'some_event';

    /**
     * @var string
     */
    private $state = 'off';

    public function changeState(string $state)
    {
        $this->state = $state;
    }

    public function getState() : string
    {
        return $this->state;
    }
}
