<?php declare(strict_types=1);

namespace Symplify\DefaultAutoconfigure\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symplify\DefaultAutoconfigure\Tests\Source\SomeCommand;

final class CompleteTest extends TestCase
{
    public function test(): void
    {
        $kernel = new AppKernel;
        $kernel->boot();

        $consoleApplication = new Application($kernel);

        /** @var SomeCommand $someCommand */
        $someCommand = $consoleApplication->get('some_command');
        $this->assertInstanceOf(SomeCommand::class, $someCommand);
    }
}
