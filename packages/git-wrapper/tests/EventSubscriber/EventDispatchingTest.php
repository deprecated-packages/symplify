<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber;

use Symplify\GitWrapper\Tests\AbstractGitWrapperTestCase;

final class EventDispatchingTest extends AbstractGitWrapperTestCase
{
    public function test(): void
    {
        $eventSubscriber = $this->registerAndReturnEventSubscriber();
        $this->gitWrapper->version();

        $this->assertTrue($eventSubscriber->wasMethodCalled('onPrepare'));
        $this->assertTrue($eventSubscriber->wasMethodCalled('onSuccess'));
        $this->assertFalse($eventSubscriber->wasMethodCalled('onError'));
        $this->assertFalse($eventSubscriber->wasMethodCalled('onBypass'));
    }

    public function testError(): void
    {
        $eventSubscriber = $this->registerAndReturnEventSubscriber();
        $this->runBadCommand(true);

        $this->assertTrue($eventSubscriber->wasMethodCalled('onPrepare'));
        $this->assertFalse($eventSubscriber->wasMethodCalled('onSuccess'));
        $this->assertTrue($eventSubscriber->wasMethodCalled('onError'));
        $this->assertFalse($eventSubscriber->wasMethodCalled('onBypass'));
    }

    public function testGitBypass(): void
    {
        $this->createRegisterAndReturnBypassEventSubscriber();
        $eventSubscriber = $this->registerAndReturnEventSubscriber();

        $output = $this->gitWrapper->version();

        $this->assertTrue($eventSubscriber->wasMethodCalled('onPrepare'));
        $this->assertFalse($eventSubscriber->wasMethodCalled('onSuccess'));
        $this->assertFalse($eventSubscriber->wasMethodCalled('onError'));
        $this->assertTrue($eventSubscriber->wasMethodCalled('onBypass'));

        $this->assertEmpty($output);
    }
}
