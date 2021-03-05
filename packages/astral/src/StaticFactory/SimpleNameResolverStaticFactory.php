<?php

declare(strict_types=1);

namespace Symplify\Astral\StaticFactory;

use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeNameResolver\ArgNodeNameResolver;
use Symplify\Astral\NodeNameResolver\AttributeNodeNameResolver;
use Symplify\Astral\NodeNameResolver\ClassLikeNodeNameResolver;
use Symplify\Astral\NodeNameResolver\ClassMethodNodeNameResolver;
use Symplify\Astral\NodeNameResolver\ConstFetchNodeNameResolver;
use Symplify\Astral\NodeNameResolver\FuncCallNodeNameResolver;
use Symplify\Astral\NodeNameResolver\IdentifierNodeNameResolver;
use Symplify\Astral\NodeNameResolver\NamespaceNodeNameResolver;
use Symplify\Astral\NodeNameResolver\ParamNodeNameResolver;
use Symplify\Astral\NodeNameResolver\PropertyNodeNameResolver;

/**
 * This would be normally handled by standard Symfony or Nette DI, but PHPStan does not use any of those, so we have to
 * make it manually.
 */
final class SimpleNameResolverStaticFactory
{
    public static function create(): SimpleNameResolver
    {
        $nameResolvers = [
            new ArgNodeNameResolver(),
            new AttributeNodeNameResolver(),
            new ClassLikeNodeNameResolver(),
            new ClassMethodNodeNameResolver(),
            new ConstFetchNodeNameResolver(),
            new FuncCallNodeNameResolver(),
            new IdentifierNodeNameResolver(),
            new NamespaceNodeNameResolver(),
            new ParamNodeNameResolver(),
            new PropertyNodeNameResolver(),
        ];

        return new SimpleNameResolver($nameResolvers);
    }
}
