<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule\Source\AbstractCommand;

final class SkipCommandOptions extends AbstractCommand
{
    public function go(): void
    {
        $this->addArgument('...');

        $this->addOption('...');
    }
}
