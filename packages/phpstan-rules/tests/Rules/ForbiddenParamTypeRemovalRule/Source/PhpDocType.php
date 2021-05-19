<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Source;

class PhpDocType
{
    /**
     * @param string|null $value
     */
    public function justPhpDocType($node = null)
    {
    }
}
