<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

final class SkipAssignOfVariable
{
    public function run($autoloadDirectory)
    {
        $filePathCandidates = [
            $this->fileInfo->getPath() . DIRECTORY_SEPARATOR . $autoloadDirectory,
            // mostly tests
            getcwd() . DIRECTORY_SEPARATOR . $autoloadDirectory,
        ];
    }
}
