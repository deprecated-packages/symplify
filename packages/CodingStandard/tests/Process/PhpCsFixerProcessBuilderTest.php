<?php

namespace Symplify\CodingStandard\Tests\Process;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Process\PhpCsFixerProcessBuilder;

final class PhpCsFixerProcessBuilderTest extends TestCase
{
    public function test()
    {
        $builder = new PhpCsFixerProcessBuilder('directory');

        $builder->setFixers('fixers');

        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--fixers=fixers'"
            ),
            $builder->getProcess()->getCommandLine()
        );

        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--fixers=fixers'"
            ),
            $builder->getProcess()->getCommandLine()
        );

        $builder->setLevel('level5');
        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--fixers=fixers' '--level=level5'"
            ),
            $builder->getProcess()->getCommandLine()
        );

        $builder->enableDryRun();
        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--fixers=fixers' '--level=level5' '--dry-run' '--diff'"
            ),
            $builder->getProcess()->getCommandLine()
        );
    }
}
