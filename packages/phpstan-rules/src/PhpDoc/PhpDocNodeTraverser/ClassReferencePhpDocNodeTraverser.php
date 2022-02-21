<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc\PhpDocNodeTraverser;

use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\PhpDocParser\PhpDocNodeTraverser;
use Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;
use Symplify\PHPStanRules\PhpDoc\ClassReferencePhpDocNodeVisitor;

final class ClassReferencePhpDocNodeTraverser
{
    public function __construct(
        private ClassReferencePhpDocNodeVisitor $classReferencePhpDocNodeVisitor
    ) {
    }

    public function decoratePhpDocNode(
        SimplePhpDocNode $simplePhpDocNode,
        ClassReflection $classReflection
    ): void {
        $phpDocNodeTraverser = new PhpDocNodeTraverser();

        $this->classReferencePhpDocNodeVisitor->configureClassName($classReflection->getName());
        $phpDocNodeTraverser->addPhpDocNodeVisitor($this->classReferencePhpDocNodeVisitor);

        $phpDocNodeTraverser->traverse($simplePhpDocNode);
    }
}
