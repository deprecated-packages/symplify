<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule\Fixture;

class SkipNotNestedForeach
{
    public function run(array $errors)
    {
        foreach ($errors as $fileErrors) {
            echo $fileErrors;
        }
    }
}
