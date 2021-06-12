<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeResolver;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use Symplify\Astral\Naming\SimpleNameResolver;

final class NullableTypeResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function resolveNormalType(Node $node): ?string
    {
        if ($node instanceof NullableType) {
            return $this->simpleNameResolver->getName($node->type);
        }

        return $this->simpleNameResolver->getName($node);
    }
}
