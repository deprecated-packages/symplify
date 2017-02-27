<?php declare(strict_types=1);

namespace Symplify\ServiceDefinitionDecorator\Tests\Adapter\Symfony\Source;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'some_event' => 'doThis'
        ];
    }

    public function doThis(): void
    {
    }
}
