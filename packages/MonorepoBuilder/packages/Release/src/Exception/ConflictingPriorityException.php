<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Exception;

use Exception;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use function Safe\sprintf;

final class ConflictingPriorityException extends Exception
{
    public function __construct(ReleaseWorkerInterface $firstReleaseWorker, ReleaseWorkerInterface $secondReleaseWorker)
    {
        $message = sprintf(
            'There 2 workers with %d priority: %s and %s. Change value in getPriority() in one of them',
            $firstReleaseWorker->getPriority(),
            get_class($firstReleaseWorker),
            get_class($secondReleaseWorker)
        );

        parent::__construct($message);
    }
}
