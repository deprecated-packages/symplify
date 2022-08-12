<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Fixture;

use Symfony\Component\Process\Process;

final class SkipVendorLocated
{
    public function process()
    {
        $process = new Process('...');
        $process->run(null);

        $secondProcess = new Process('...');
        $secondProcess->run(null);
    }
}
