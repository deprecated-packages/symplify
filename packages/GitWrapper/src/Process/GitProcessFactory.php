<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Process;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

final class GitProcessFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createFromWrapperCommandAndCwd(
        GitWrapper $gitWrapper,
        GitCommand $gitCommand,
        ?string $cwd = null
    ): GitProcess {
        return new GitProcess($gitWrapper, $gitCommand, $this->eventDispatcher, $cwd);
    }
}
