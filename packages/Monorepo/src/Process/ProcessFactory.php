<?php declare(strict_types=1);

namespace Symplify\Monorepo\Process;

use Symfony\Component\Process\Process;

final class ProcessFactory
{
    /**
     * @var string
     */
    private $cwd;

    public function setCurrentWorkingDirectory(string $cwd): void
    {
        $this->cwd = $cwd;
    }

    public function createSubsplitInit(): Process
    {
        return new Process([realpath(BashFiles::SUBSPLIT), 'init', '.git'], $this->cwd);
    }
}
