<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SkipIndirectRemoval extends PhpFileLoader
{
    /**
     * @param string|null $type
     */
    public function load($resource, $type = null)
    {
    }
}
