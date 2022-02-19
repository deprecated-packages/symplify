<?php

use PhpParser\Node;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer\Source\Schedule;

final class SkipCallableParam
{
    /**
     * @param callable(int $stepCount): void $postFileCallback Used for progress bar jump
     * @return mixed[]
     */
    public function findFirst(
        Schedule $schedule,
        string $mainScript,
        callable $postFileCallback,
        InputInterface $input
    ): array {
        return [];
    }
}
