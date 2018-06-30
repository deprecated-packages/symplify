<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Process;

use Symfony\Component\Process\Process;

final class SplitProcessInfo
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var string
     */
    private $localDirectory;

    /**
     * @var string
     */
    private $remoteRepository;

    public function __construct(Process $process, string $localDirectory, string $remoteRepository)
    {
        $this->process = $process;
        $this->localDirectory = $localDirectory;
        $this->remoteRepository = $remoteRepository;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getLocalDirectory(): string
    {
        return $this->localDirectory;
    }

    public function getRemoteRepository(): string
    {
        return $this->remoteRepository;
    }
}
