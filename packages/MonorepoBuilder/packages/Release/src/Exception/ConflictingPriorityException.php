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
            'There 2 workers with %d priority:%s-%s%s-%s.%sChange value in "getPriority()" method in one of them',
            $firstReleaseWorker->getPriority(),
            PHP_EOL,
            get_class($firstReleaseWorker),
            PHP_EOL,
            get_class($secondReleaseWorker),
            PHP_EOL
        );

        parent::__construct($message);
    }
}
