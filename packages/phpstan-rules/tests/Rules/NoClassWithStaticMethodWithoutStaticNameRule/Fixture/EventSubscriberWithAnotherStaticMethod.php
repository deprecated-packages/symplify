<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EventSubscriberWithAnotherStaticMethod implements EventSubscriberInterface
{
    public static function run(): void
    {
    }

    public static function getSubscribedEvents()
    {
    }
}
