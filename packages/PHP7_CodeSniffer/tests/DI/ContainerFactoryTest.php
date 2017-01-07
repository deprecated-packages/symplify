<?php

namespace Symplify\PHP7_CodeSniffer\Tests\DI;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\DI\ContainerFactory;

final class ContainerFactoryTest extends TestCase
{
    public function testCreate()
    {
        $containerFactory = new ContainerFactory();
        $this->assertInstanceOf(Container::class, $containerFactory->create());
    }
}
