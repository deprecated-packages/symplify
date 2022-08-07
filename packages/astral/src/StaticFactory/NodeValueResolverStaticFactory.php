<?php

declare(strict_types=1);

namespace Symplify\Astral\StaticFactory;

use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PackageBuilder\Php\TypeChecker;

/**
 * @api
 */
final class NodeValueResolverStaticFactory
{
    public static function create(): NodeValueResolver
    {
        return new NodeValueResolver(new TypeChecker());
    }
}
