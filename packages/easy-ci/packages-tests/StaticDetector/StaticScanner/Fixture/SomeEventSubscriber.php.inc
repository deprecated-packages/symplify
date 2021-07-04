<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\StaticDetector\StaticScanner\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
    }
}
