<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class SkipNodeType
{
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    public function getNodeType(): array
    {
        return [ClassMethod::class];
    }
}