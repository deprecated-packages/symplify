<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Source\PhpDocType;

final class SkipPhpDocType extends PhpDocType
{
    /**
     * @param string|null $node
     */
    public function justPhpDocType($node = null)
    {
    }
}
