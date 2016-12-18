<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Process;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Process\PhpCsProcessBuilder;

final class PhpCsProcessBuilderTest extends TestCase
{
    public function test()
    {
        $builder = new PhpCsProcessBuilder('directory');
        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/phpcs' 'directory' '--colors' '-p' '-s'"
            ),
            $builder->getProcess()->getCommandLine()
        );

        $builder->setExtensions('php5');
        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/phpcs' 'directory' '--colors' '-p' '-s' '--extensions=php5'"
            ),
            $builder->getProcess()->getCommandLine()
        );

        $builder->setStandard('standard');
        $this->assertSame(
            WindowsCompatibilityHelper::makeWindowsOsCompatible(
                "'./vendor/bin/phpcs' 'directory' '--colors' '-p' '-s' '--extensions=php5' '--standard=standard'"
            ),
            $builder->getProcess()->getCommandLine()
        );
    }
}
