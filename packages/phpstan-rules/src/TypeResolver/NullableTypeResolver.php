<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeResolver;

use PhpParser\Node;
use PhpParser\Node\NullableType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class NullableTypeResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return class-string|null
     */
    public function resolveNormalType(Node $node): ?string
    {
        if ($node instanceof NullableType) {
            $class = $this->simpleNameResolver->getName($node->type);
        } else {
            $class = $this->simpleNameResolver->getName($node);
        }
        
        if ($class === null ) {
            return null;
        }

        if (!class_exists($class)) {
            throw new ShouldNotHappenException();
        }
        return $class;
    }
}
