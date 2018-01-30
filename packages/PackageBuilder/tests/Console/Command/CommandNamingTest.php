<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console\Command;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class CommandNamingTest extends TestCase
{
    public function test(): void
    {
        $name = CommandNaming::classToName('SomeNameCommand');
        $this->assertSame('some-name', $name);
        $name = CommandNaming::classToName('AlsoNamespace\SomeNameCommand');
        $this->assertSame('some-name', $name);
    }
}
