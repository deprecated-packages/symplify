<?php declare(strict_types=1);

namespace Symplify\DefaultAutoconfigure\Tests\Source;

use Symfony\Component\Console\Command\Command;

final class SomeCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('some_command');
    }
}
