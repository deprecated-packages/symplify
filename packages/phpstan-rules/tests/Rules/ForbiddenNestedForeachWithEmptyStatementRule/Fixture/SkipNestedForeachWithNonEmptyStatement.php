<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule\Fixture;

class SkipNestedForeachWithNonEmptyStatement
{
    public function run(array $errors)
    {
        foreach ($errors as $fileErrors) {
            $errorCount = count($fileErrors);
            foreach ($fileErrors as $fileError) {
                echo $fileError;
            }
        }
    }
}
