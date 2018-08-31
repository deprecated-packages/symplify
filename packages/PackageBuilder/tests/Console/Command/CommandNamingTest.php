<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console\Command;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class CommandNamingTest extends TestCase
{
    /**
     * @dataProvider provideDataForClassToName()
     */
    public function test(string $commandClass, string $expectedCommandName): void
    {
        $this->assertSame($expectedCommandName, CommandNaming::classToName($commandClass));
    }

    public function provideDataForClassToName(): Iterator
    {
        yield ['SomeNameCommand', 'some-name'];
        yield ['AlsoNamespace\SomeNameCommand', 'some-name'];
        yield ['AlsoNamespace\ECSCommand', 'ecs'];
        yield ['AlsoNamespace\PHPStanCommand', 'php-stan'];
    }
}
