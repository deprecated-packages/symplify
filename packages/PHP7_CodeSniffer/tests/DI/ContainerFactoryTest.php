<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\DI;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\DI\ContainerFactory;

final class ContainerFactoryTest extends TestCase
{
    public function test()
    {
        $containerFactory = new ContainerFactory();
        $this->assertInstanceOf(Container::class, $containerFactory->create());
    }
}
