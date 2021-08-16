<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Doctrine;

use Doctrine\ORM\Mapping\Entity;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;
use Symplify\SimplePhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;

final class EntityClassDetector
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private AttributeFinder $attributeFinder,
        private SimplePhpDocParser $simplePhpDocParser
    ) {
    }

    public function isInsideDoctrineEntity(Node $node): bool
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($node, Class_::class);
        if (! $class instanceof Class_) {
            return false;
        }

        if ($this->attributeFinder->hasAttribute($class, Entity::class)) {
            return true;
        }

        $simplePhpDocNode = $this->simplePhpDocParser->parseNode($class);
        if (! $simplePhpDocNode instanceof SimplePhpDocNode) {
            return false;
        }

        return (bool) $simplePhpDocNode->getTagsByName('@Entity')
            ?? $simplePhpDocNode->getTagsByName('@ORM\Entity')
            ?? $simplePhpDocNode->getTagsByName('@' . Entity::class);
    }
}
