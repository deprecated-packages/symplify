<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule\Fixture;

final class SkipArray
{
    public function run($errors)
    {
        foreach ($errors as $fileErrors) {
            foreach ([$fileErrors] as $fileError) {
                echo $fileError;
            }
        }
    }
}
