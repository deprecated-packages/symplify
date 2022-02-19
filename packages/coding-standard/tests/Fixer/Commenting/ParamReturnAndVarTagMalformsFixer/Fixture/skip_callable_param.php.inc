<?php

use Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer\Source\Schedule;

final class SkipCallableParam
{
    /**
     * @param callable(int $stepCount): void $postFileCallback
     */
    public function findFirst(
        Schedule $schedule,
        callable $postFileCallback
    ) {
    }
}
