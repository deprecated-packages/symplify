<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Contract\Process;

use Symfony\Component\Process\Process;

interface ProcessBuilderInterface
{
    public function getProcess() : Process;
}
