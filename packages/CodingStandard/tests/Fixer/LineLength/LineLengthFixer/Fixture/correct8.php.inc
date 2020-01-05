<?php

declare(strict_types=1);

namespace Pehapkari\Website\Tests\Posts\Year2016\EventDispatcher;

final class EventDispatchingTest
{
    public function test(): void
    {
        $notifyMeOnVideoPublishedEventSubscriber = new NotifyMeOnVideoPublishedEventSubscriber;
        $eventDispatcher->addSubscriber($notifyMeOnVideoPublishedEventSubscriber);
    }
}
