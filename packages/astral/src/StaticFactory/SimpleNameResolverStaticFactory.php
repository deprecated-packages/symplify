<?php

declare(strict_types=1);

namespace Symplify\Astral\StaticFactory;

use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeNameResolver\ClassLikeNodeNameResolver;
use Symplify\Astral\NodeNameResolver\ClassMethodNodeNameResolver;
use Symplify\Astral\NodeNameResolver\FuncCallNodeNameResolver;
use Symplify\Astral\NodeNameResolver\IdentifierNodeNameResolver;

/**
 * This would be normally handled by standard Symfony or Nette DI,
 * but PHPStan does not use any of those, so we have to make it manually.
 */
final class SimpleNameResolverStaticFactory
{
    public static function create(): SimpleNameResolver
    {
        $nameResolvers = [
            new ClassLikeNodeNameResolver(),
            new IdentifierNodeNameResolver(),
            new ClassMethodNodeNameResolver(),
            new FuncCallNodeNameResolver(),
        ];
        return new SimpleNameResolver($nameResolvers);
    }
}
