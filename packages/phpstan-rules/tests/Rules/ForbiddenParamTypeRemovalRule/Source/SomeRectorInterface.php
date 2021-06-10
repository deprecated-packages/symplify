<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Source;

interface SomeRectorInterface
{
    public function refactor(SomeNode $node);
}
