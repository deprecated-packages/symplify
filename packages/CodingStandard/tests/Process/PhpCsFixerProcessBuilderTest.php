<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Process;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Process\PhpCsFixerProcessBuilder;

final class PhpCsFixerProcessBuilderTest extends TestCase
{
    public function test()
    {
        $builder = new PhpCsFixerProcessBuilder('directory');

        $builder->setRules('fixers');

        $this->assertSame(
            "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--verbose' '--allow-risky=yes' '--rules=fixers'",
            $builder->getProcess()->getCommandLine()
        );

        $this->assertSame(
            "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--verbose' '--allow-risky=yes' '--rules=fixers'",
            $builder->getProcess()->getCommandLine()
        );

        $builder->setLevel('level5');
        $this->assertSame(
            "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--verbose' '--allow-risky=yes' '--rules=fixers' " .
            "'--rules=@level5'",
            $builder->getProcess()->getCommandLine()
        );

        $builder->enableDryRun();
        $this->assertSame(
            "'./vendor/bin/php-cs-fixer' 'fix' 'directory' '--verbose' '--allow-risky=yes' '--rules=fixers' " .
            "'--rules=@level5' '--dry-run' '--diff'",
            $builder->getProcess()->getCommandLine()
        );
    }
}
