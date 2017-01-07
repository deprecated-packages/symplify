<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Console;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\PHP7_CodeSniffer\Console\ConsoleApplication;

final class ConsoleApplicationTest extends TestCase
{
    public function testConstruct()
    {
        $sniffDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $application = new ConsoleApplication($sniffDispatcher->reveal());

        $this->assertInstanceOf(
            EventDispatcherInterface::class,
            PHPUnit_Framework_Assert::getObjectAttribute($application, 'dispatcher')
        );

        $definition = $application->getDefinition();

        $this->assertCount(1, $definition->getArguments());
        $this->assertCount(1, $definition->getOptions());
    }
}
