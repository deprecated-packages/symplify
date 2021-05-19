<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Source;

interface NoTypeInterface
{
    public function noType($node);
}
